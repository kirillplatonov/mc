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

// Начало подсчета времени генерации страницы
$start_time = microtime(true);

defined('ROOT') or define('ROOT', str_replace('\\', '/', realpath(dirname(__FILE__))).'/');
define('IN_SYSTEM', TRUE);

require ROOT .'PositiveCode/ClassLoader.php';
$loader = new PositiveCode\ClassLoader(ROOT);
$loader->withPathes([
   'File_Cache' => 'libraries\file_cache',
   'PclZip' => 'libraries\pclzip.lib',
   'smiles' => 'modules/smiles/helpers/smiles',
    'main_ftp' => 'modules/main/helpers/main_ftp',
    'main_form' => 'modules/main/helpers/main_form'
]);
$loader->register();
// Конфигурация системы
if (file_exists(ROOT.'data_files/config.php')) {
	require_once(ROOT.'data_files/config.php');
}
else {
	header('Location: ./install/index.php');
	exit;
}

// Подключаем главные функции ядра
include_once(ROOT.'kernel/general_functions.php');
// Конфигурация php
include_once(ROOT.'kernel/ini_set.php');
// Подключаем Registry
a_import('libraries/registry');

session_name('sid');
session_start();

// Легкий XSS clean =)
$_GET = array_map('htmlspecialchars_array', $_GET);
Registry::set('classLoader', $loader);
// Подключаем MySQL класс
a_import('libraries/mysql');
$db = new MySQL();
$db->connect();
$db->charset('utf8');

// Добавяем $db в Registry
Registry::set('db', $db);

// Загрузка конфигурации системы
$CONFIG = array();
$result = $db->query("SELECT * FROM #__config");
while ($item = $db->fetch_array($result)) {
	$CONFIG[$item['module']][$item['key']] = $item['value'];
}

define('MAIN_MENU', $CONFIG['system']['main_menu']);
define('EXT', $CONFIG['system']['ext']);
define('DEFAULT_MODULE', $CONFIG['system']['default_module']);

// Добавяем $CONFIG в Registry
Registry::set('config', $CONFIG);

// Показ ошибок
if ($CONFIG['system']['display_errors']) {
	ini_set('display_errors', 'On');
} else {
	ini_set('display_errors', 'Off');
}

// Мини роутинг
a_import('libraries/route');
$route = new Route;

// Загрузка основного хелпера основного модуля
a_import('modules/main/helpers/main');
// Загрузка хелпера модулей
a_import('modules/modules/helpers/modules');

// Ежедневные действия в системе
a_import('kernel/everyday');

// Подключаем и инициализируем контроллер
a_import('libraries/controller');
$controller = a_load_class(ROUTE_CONTROLLER_PATH, 'controller');

// Выполняем метод контроллера
if ( ! empty($route->action)) {
	$action_method = 'action_'. $route->action;
    
	if (method_exists($controller, $action_method)) {
		$controller->$action_method();
	}
	else header('Location: '. a_url('main/page_not_found', '', true));
}
else {
	if (method_exists($controller, 'action_index')) {
		$controller->action_index();
	}
	else header('Location: '. a_url('main/page_not_found', '', true));
}

// Вывод профайлера
if ($CONFIG['system']['profiler'] == 'on' && ACCESS_LEVEL == 10) a_profiler($start_time);
?>