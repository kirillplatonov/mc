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

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа</b>');

/**
* Route class
*/
class Route {
	/**
	 * Путь к контроллеру
	 */
	public $controller_path = NULL;
	/**
	 * Название контроллера
	 */
	public $controller_name = NULL;
	/**
	 * Получаемый action
	 */
	public $action = NULL;
	/**
	 * Флаг существования контроллера
	 */
	public $controller_exists = TRUE;
	/**
	 * Модуль
	 */
	public $module;
	/**
	 * Сегменты
	 */
	public $segment1;
	public $segment2;
	public $segment3;

	/**
	 * Конструктор
	 */
	public function __construct() {
		$this->parse_query();
		$this->route();
	}

	/**
	 * Парсинг запроса
	 */
	public function parse_query() {
		$query = str_replace(URL, '', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$ex = explode('?', $query);
		$query = $ex[0];
		
		# Считываем все файлы с правилами роутинга в строку для парсинга
		$rules = '';
		$rules_dir = ROOT.'data_files/route_rules';
		$dir = opendir($rules_dir);
		while ($f = readdir($dir)) {
			if (strstr($f, '.rules')) $rules .= trim(file_get_contents($rules_dir.'/'.$f)).PHP_EOL;
		}
		
		$rules .= '([A-z0-9_\-]*)([\.A-z0-9]*)#segment1=$1'.PHP_EOL;
		$rules .= '([A-z0-9_\-]*)/([A-z0-9_\-]*)([\.A-z0-9]*)#segment1=$1&segment2=$2'.PHP_EOL;
		$rules .= '([A-z0-9_\-]*)/([A-z0-9_\-]*)/([A-z0-9_\-]*)([\.A-z0-9]*)#segment1=$1&segment2=$2&segment3=$3'.PHP_EOL;
		
		$rules = explode("\n", $rules);
	
		foreach ($rules as $rule) {
			$rule = trim($rule);
			if (strpos($rule, '#') == 0 or $rule == '') continue;
			$ex = explode('#', $rule);
			if (preg_match('~^'.$ex[0].'$~', $query)) {
				$result = preg_replace('~^'.$ex[0].'$~', $ex[1], $query, 1);
				$vars = explode('&', $result);
				foreach ($vars as $var) {
					$_ex = explode('=', $var);
					$_GET[$_ex[0]] = $_ex[1];
				}
				break;
			}
		}
	}

	/**
	 * Функция определяет контроллер и action
	 */
	public function route() {
		# Определение сегментов
		if ($this->_check_segments()) {
			# Если нет сегментов, подключаем модуль по умолчанию
			if (empty($this->segment1)) {
				if (file_exists(ROOT.'modules/'.DEFAULT_MODULE.'/controllers/'.DEFAULT_MODULE.'.php')) {
					$this->controller_path = 'modules/'.DEFAULT_MODULE.'/controllers/'.DEFAULT_MODULE.'.php';
					$this->controller_name = DEFAULT_MODULE;
					$this->action = '';
					$this->module = DEFAULT_MODULE;
				} else {
					$this->controller_exists = FALSE;
				}
			}
			# Если указаны все 3 сегмента
			if (!empty($this->segment1) && !empty($this->segment2) && !empty($this->segment3)) {
				if (file_exists(ROOT.'modules/'.$this->segment1.'/controllers/'.$this->segment1.'_'.$this->segment2.'.php')) {
					$this->controller_path = 'modules/'.$this->segment1.'/controllers/'.$this->segment1.'_'.$this->segment2.'.php';
					$this->controller_name = $this->segment1.'_'.$this->segment2;
					$this->action = $this->segment3;
					$this->module = $this->segment1;
				} else {
					$this->controller_exists = FALSE;
				}
			}
			# Если указаны 2 сегмента
			elseif (!empty($this->segment1) && !empty($this->segment2)) {
				if (file_exists(ROOT.'modules/'.$this->segment1.'/controllers/'.$this->segment1.'_'.$this->segment2.'.php')) {
					$this->controller_path = 'modules/'.$this->segment1.'/controllers/'.$this->segment1.'_'.$this->segment2.'.php';
					$this->controller_name = $this->segment1.'_'.$this->segment2;
					$this->action = '';
					$this->module = $this->segment1;
				}
				elseif (file_exists(ROOT.'modules/'.$this->segment1.'/controllers/'.$this->segment1.'.php')) {
					$this->controller_path = 'modules/'.$this->segment1.'/controllers/'.$this->segment1.'.php';
					$this->controller_name = $this->segment1;
					$this->action = $this->segment2;
					$this->module = $this->segment1;
				}
				else $this->controller_exists = FALSE;
			}
			# Если указан только 1 сегмент
			elseif (!empty($this->segment1)) {
				if (file_exists(ROOT.'modules/'.$this->segment1.'/controllers/'.$this->segment1.'.php')) {
					$this->controller_path = 'modules/'.$this->segment1.'/controllers/'.$this->segment1.'.php';
					$this->controller_name = $this->segment1;
					$this->action = '';
					$this->module = $this->segment1;
				} else {
					$this->controller_exists = FALSE;
				}
			}

			if ($this->controller_exists) {
				define('ROUTE_CONTROLLER_PATH', $this->controller_path);
				define('ROUTE_CONTROLLER_NAME', $this->controller_name);
				define('ROUTE_ACTION', $this->action);
				define('ROUTE_MODULE', $this->module);
			}
			else header('Location: '.a_url('main/page_not_found', '', true));
		}
	}

	/**
	 * Проверка правильности сегментов
	 */
	protected function _check_segments() {
		$check_segments = true;
		if (!empty($_GET['segment1'])) {
			if (preg_match('~^[0-9A-z_-]*$~', $_GET['segment1'])) $this->segment1 = $_GET['segment1'];
			else $check_segments = false;
		}
		if (!empty($_GET['segment2'])) {
			if (preg_match('~^[0-9A-z_-]*$~', $_GET['segment2'])) $this->segment2 = $_GET['segment2'];
			else $check_segments = false;
		}
		if (!empty($_GET['segment3'])) {
			if (preg_match('~^[0-9A-z_-]*$~', $_GET['segment3'])) $this->segment3 = $_GET['segment3'];
			else $check_segments = false;
		}
		if (!$check_segments) a_error('Ошибка регистрации сегментов!');
		return true;
	}
}
?>
