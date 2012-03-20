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

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

/**
 * Управление модулями
 */
class Modules_Admin_Controller extends Controller {
	/**
	* Уровень пользовательского доступа
	*/
	public $access_level = 10;
	/**
	* Тема
	*/
	public $template_theme = 'admin';

	/**
	* Метод по умолчанию
	*/
	public function action_index() {
		$this->action_list_modules();
	}

	/**
	* Список модулей
	*/
	public function action_list_modules() {
		$modules = modules::get_modules();
	
		$this->tpl->assign(array(
			'modules' => $modules
		));
	
		$this->tpl->display('list_modules');
	}

	/**
	* Загрузка нового модуля
	*/
	public function action_upload_module() {
		if(empty($this->config['ftp']['server']))
			a_notice('Не настроены фтп данные системы, перейдите по ссылке "Продолжить" для настройки.', a_url('main/admin/ftp_config'));

		if(isset($_POST['submit'])) {
			main::is_demo();
			if(empty($_FILES['module']['tmp_name'])) {
				$this->error .= 'Укажите zip файл модуля!<br />';
			}

			if(!$this->error) {
				# Подключаем библиотеки для работы с архивами
				a_import('libraries/pclzip.lib');

				$archive = new PclZip($_FILES['module']['tmp_name']);
				$module_ini = $archive->extract(PCLZIP_OPT_BY_NAME, 'module.ini', PCLZIP_OPT_EXTRACT_AS_STRING);
				$ini_string = $module_ini[0]['content'];
				if(empty($ini_string))
					a_error("Не найден файл <b>module.ini</b> загружаемого модуля!");

				$module = parse_ini_string($ini_string);
				$module['name'] = trim($module['name']);
				if(empty($module['name']))
					a_error("Не указано имя модуля в файле module.ini!");

				if(is_dir(ROOT .'modules/'. $module['name']))
					a_error("Папка с именем нового модуля уже существует, возможно, модуль быз загружен ранее!");

				if(!preg_match("~^[0-9a-z_]*$~", $module['name']))
					a_error("Имя модуля имеет не правильный формат, оно должно состоять только из латинских букв в нижнем регистре, цифр и подчеркивания");

				# Создаем папку модуля
				$module_path = ROOT .'modules/'. $module['name'];
				mkdir($module_path);

				# Извлекаем содержимое архива в папку модуля
				$result = $archive->extract(PCLZIP_OPT_PATH, $module_path);

				if($result[0]['status'] == 'ok')
					a_notice("Модуль успешно загружен, теперь перейдите в управление модулями и инсталлируйте его", a_url('modules/admin'));
				else
					a_notice("При извлечении архива произошла ошибка", a_url('modules/admin'));
			}
		}
		if(!isset($_POST['submit']) || $this->error) {
			$this->tpl->assign(array(
				'error' => $this->error
			));
	
			$this->tpl->display('upload_module');
		}
	}

	/**
	* Активация / деактивация модуля
	*/
	public function action_activate() {
		main::is_demo();
		if(empty($_GET['module']))
			a_error("Укажите модуль!");

		if(!$module_id = $this->db->get_one("SELECT id FROM #__modules WHERE name = '". a_safe($_GET['module']) ."'"))
			a_error("Модуль ". $_GET['module'] ." не установлен!");

		$status = @$_GET['status'] == 'on' ? 'on' : 'off';

		# Меняем статус модуля
		$this->db->query("UPDATE #__modules SET status = '$status' WHERE id = '$module_id'");

		header('Location: '. a_url('modules/admin', '', TRUE));
	}

	/**
	* Устанавка модуля
	*/
	public function action_install() {
		main::is_demo();
		$module_name = trim($_GET['module']);

		if(empty($module_name))
			a_error("Укажите модуль!");

		if($this->db->get_one("SELECT name FROM #__modules WHERE name = '". a_safe($module_name) ."'"))
			a_error("Модуль ". $module_name ." был установлен ранее!");

		if(!file_exists(ROOT .'modules/'. $module_name .'/module.ini'))
			a_error("Не найден файл информации о модуле!");

		# Получаем информацию о модуле
		$module = parse_ini_file(ROOT .'modules/'. $module_name .'/module.ini');

		# Добавляем модуль в таблицу модулей
		$this->db->query("INSERT INTO #__modules SET
			`name` = '". $module['name'] ."',
			`title` = '". $module['title'] ."',
			`admin_link` = '". $module['admin_link'] ."',
			`description` = '". $module['description'] ."',
			`installed` = 1,
			`status` = 'off'
		");

		# Если имеется инсталлятор, выполняем его
		if(file_exists(ROOT .'modules/'. $module_name .'/helpers/'. $module_name .'_installer.php')) {
			a_import('modules/'. $module_name .'/helpers/'. $module_name .'_installer');
			$installer_class = $module_name .'_installer';
			call_user_func(array($installer_class, 'install'), &$this->db);
		}

		header('Location: '. a_url('modules/admin', '', TRUE));
	}

	/**
	* Деинсталляция модуля
	*/
	public function action_uninstall() {
		main::is_demo();
		$module_name = trim($_GET['module']);

		if(empty($module_name))
			a_error("Укажите модуль!");

		if(!$this->db->get_one("SELECT name FROM #__modules WHERE name = '". a_safe($module_name) ."'"))
			a_error("Модуль ". $_GET['module'] ." не найден либо был  деинсталлирован ранее!");

		# Удаляем модуль из таблицы модулей
		$this->db->query("DELETE FROM #__modules WHERE name = '". a_safe($module_name) ."'");

		# Удаляем строки модуля из конфига
		$this->db->query("DELETE FROM #__config WHERE module = '". a_safe($module_name) ."'");

		# Если имеется инсталлятор, выполняем деинсталляцию
		if(file_exists(ROOT .'modules/'. $module_name .'/helpers/'. $module_name .'_installer.php')) {
			a_import('modules/'. $module_name .'/helpers/'. $module_name .'_installer');
			$installer_class = $module_name .'_installer';
			call_user_func(array($installer_class, 'uninstall'), $this->db);
		}

		# Удаляем файлы модуля и папку модуля
		main::delete_dir(ROOT .'modules/'. $module_name);
		if(is_dir(ROOT .'modules/'. $module_name))
			$message = "Не удалось удалить папку модуля, если это необходимо, попробуйте удалить её вручную по фтп, папка: modules/$module_name";
		else $message = "Модуль успешно удален!";

		a_notice($message, a_url('modules/admin'));
	}
}
?>