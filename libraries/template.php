<?php
/**
	 * MobileCMS
	 *
	 * Open source content management system for mobile sites
	 *
	 * @author MobileCMS Team <support@mobilecms.pro>
	 * @copyright Copyright (c) 2011-2019, MobileCMS Team
	 * @link https://mobilecms.pro Official site
	 * @license MIT license
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
		if(!strstr($filename, '.tpl')) {
			$filename .= '.tpl';
		}

		# Определение дополнительных параметров
		foreach($params AS $var => $var_value) {
			$this->vars[$var] = $var_value;
		}

		if(strpos($this->theme, 'admin') === 0) {
			$alternative_theme = 'admin';
		} else {
			$alternative_theme = 'default';
		}
		
		# Определяем имя файла шаблона
		if(strstr($filename, '{THEME}')) {
			if(file_exists(ROOT . str_replace('{THEME}', THEME, $filename))) {
				$this->template_file = ROOT . str_replace('{THEME}', THEME, $filename);
			} elseif(file_exists(ROOT . str_replace('{THEME}', $alternative_theme, $filename))) {
				$this->template_file = ROOT . str_replace('{THEME}', $alternative_theme, $filename);
			} else {
				die('Файл <b>'. $filename .'</b> не является шаблоном или не найден.');
			}
		} else {
			if(file_exists(ROOT . 'modules/'. ROUTE_MODULE .'/views/'. $this->theme .'/'. $filename)) {
				$this->template_file = ROOT . 'modules/'. ROUTE_MODULE .'/views/'. $this->theme .'/'. $filename;
			} elseif(file_exists(ROOT . 'modules/'. ROUTE_MODULE .'/views/'. $alternative_theme .'/'. $filename)) {
				$this->template_file = ROOT . 'modules/'. ROUTE_MODULE .'/views/'. $alternative_theme .'/'. $filename;
			} elseif(file_exists(ROOT .'/views/'. $this->theme .'/'. $filename)) {
				$this->template_file = ROOT .'/views/'. $this->theme .'/'. $filename;
			} else {
				die('Файл <b>'. $filename .'</b> не является шаблоном или не найден.');
			}
		}

		# Создаем ссылки на переменные из общего массива, чтобы они были видны в шаблоне
		extract($this->vars, EXTR_REFS);

		ob_start();
		include($this->template_file);     
		$page_content = ob_get_clean();
		return $page_content;
	}

	/**
	* Вывод кода страницы
	* @param string $filename
	*/
	public function display($filename, $params = array()) {
		echo $this->parse($filename, $params);
	}

	/**
	 * Assign переменных
	 */
	public function assign($param1, $param2 = NULL) {
		if (!$param2 && is_array($param1)) {
			foreach ($param1 AS $key => $value) {
				$this->vars[$key] = $value;
			}
			return TRUE;
		}
		elseif ($param2) {
			$this->vars[$param1] = $param2;
			return TRUE;
		}
		return FALSE;
	}
}

?>
