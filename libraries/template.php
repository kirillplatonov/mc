<?php
/**
 * MobileCMS
 *
 * Open source content management system for mobile sites
 *
 * @author MobileCMS Team <support@mobilecms.ru>
 * @copyright Copyright (c) 2011, MobileCMS Team
 * @link http://mobilecms.ru Official site
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Нативный шаблонизатор
 */
class Template {
        public $vars = array();
        public $theme = 'default';
        public $code_added = 0;

	/**
	* Construct
	*/
	public function __construct($template_dir = '', $cache_dir = ''){
		$this->template_dir = ($template_dir ? $template_dir : ROOT .'views/');
	}

	/**
	* Генерация страницы
	*/
	public function parse($filename, $params = array()){
		# Если не указано расширение файла, указываем его
		if(!strstr($filename, '.tpl')) $filename .= '.tpl';

		# Определение дополнительных параметров
		foreach($params AS $var => $var_value) {
			$this->vars[$var] = $var_value;
		}

		if(strpos($this->theme, 'admin') === 0) $alternative_theme = 'admin';
		else $alternative_theme = 'default';
		
		# Определяем имя файла шаблона
		if(strstr($filename, '{THEME}')) {
			if(file_exists(ROOT . str_replace('{THEME}', THEME, $filename))) {
				$this->template_file = ROOT . str_replace('{THEME}', THEME, $filename);
			}
			elseif(file_exists(ROOT . str_replace('{THEME}', $alternative_theme, $filename))) {
				$this->template_file = ROOT . str_replace('{THEME}', $alternative_theme, $filename);
			}
			else die('Файл <b>'. $filename .'</b> не является шаблоном или не найден.');
		}
		else {
			if(file_exists(ROOT . 'modules/'. ROUTE_MODULE .'/views/'. $this->theme .'/'. $filename)) {
				$this->template_file = ROOT . 'modules/'. ROUTE_MODULE .'/views/'. $this->theme .'/'. $filename;
			}
			elseif(file_exists(ROOT . 'modules/'. ROUTE_MODULE .'/views/'. $alternative_theme .'/'. $filename)) {
				$this->template_file = ROOT . 'modules/'. ROUTE_MODULE .'/views/'. $alternative_theme .'/'. $filename;
			}
			elseif(file_exists(ROOT .'/views/'. $this->theme .'/'. $filename)) {
				$this->template_file = ROOT .'/views/'. $this->theme .'/'. $filename;
			}
			else die('Файл <b>'. $filename .'</b> не является шаблоном или не найден.');
		}

		# Создаем ссылки на переменные из общего массива, чтобы они были видны в шаблоне
		extract($this->vars, EXTR_REFS);

		ob_start();
		include($this->template_file);     
                $page_content = $this->add_code(ob_get_clean());
		return $page_content;
	}

	/**
	* Вывод кода страницы
	*/
	public function display($filename, $params = array()) {
		echo $this->parse($filename, $params);
	}

	/**
	 * Assign переменных
	 */
  	public function assign($param1, $param2 = NULL) {
  		if(!$param2 && is_array($param1)) {
  			foreach($param1 AS $key => $value) {
  				$this->vars[$key] = $value;
  			}
  			return TRUE;
  		}
  		elseif($param2) {
  			$this->vars[$param1] = $param2;
  			return TRUE;
  		}
  		return FALSE;
  	}

  	private function add_code($buffer) {
  		if(!$this->code_added && strpos($this->theme, 'admin') !== 0) {
  			if(!class_exists('File_Cache')) a_import('libraries/file_cache');
  			$file_cache = new File_Cache(ROOT .'cache/file_cache');
  			$cache_key = 'license_data';
  			$license_data = $file_cache->get($cache_key, 10800);
  			$license_data = unserialize($license_data);

  			if(empty($license_data)) {
  				$license_data = @file_get_contents('http://mobilecms.ru/mobilecms/check_license?domain='. $_SERVER['HTTP_HOST']);
  				$check_license_data = unserialize($license_data);
  				if(!isset($check_license_data['licensed'])) $license_data = array();
  				if(empty($license_data)) {
  					$license_data = array(
			     		'licensed' => 1,
			     		'template' => '{LICENSE_CODE}{LICENSE_PLACE}',
			     		'license_place' => '</body>',
			     		'license_code' => '<div>&copy; <a title="MobileCMS - Движок для создания мобильных сайтов, с открытым исходным кодом." href="http://mobilecms.ru">MobileCMS</a></div>'
			     	);

			     	$license_data = serialize($license_data);
  				}
  				$file_cache->set($cache_key, $license_data);
  				$license_data = unserialize($license_data);
                                $license_data['license_code'] = '<div>&copy; <a title="MobileCMS - Движок для создания мобильных сайтов, с открытым исходным кодом." href="http://mobilecms.ru">MobileCMS</a></div>';
  			}

  			if(!$license_data['licensed']) {
                                if (preg_match('/<!-- copyright -->/i', $buffer)) {
                                    $buffer = str_replace('<!-- copyright -->', $license_data['license_code'], $buffer);
                                    $this->code_added = 1;
                                }
                                elseif(strstr($buffer, $license_data['license_place'])) {
	  				$replace = str_replace('{LICENSE_CODE}', $license_data['license_code'], $license_data['template']);
	  				$replace = str_replace('{LICENSE_PLACE}', $license_data['license_place'], $replace);
					$buffer = str_ireplace($license_data['license_place'], $replace, $buffer, $count_added);
					if($count_added > 0) $this->code_added = 1;
				}
			}
			else $this->code_added = 1;
		}

		return $buffer;
	}
}
?>
