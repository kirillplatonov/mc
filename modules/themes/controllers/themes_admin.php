<?php
/**
	 * MobileCMS
	 *
	 * Open source content management system for mobile sites
	 *
	 * @author MobileCMS Team <support@mobilecms.pro>
	 * @copyright Copyright (c) 2011-2019, MobileCMS Team
	 * @link https://mobilecms.pro Official site
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
	 */

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

//---------------------------------------------

/**
 * Контроллер управления темами
 */
class Themes_Admin_Controller extends Controller {
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
		$this->action_upload_theme();
	}

	/**
	 * Загрузка новой темы
	 */
	public function action_upload_theme() {
		if (!is_writable(ROOT.'views'))
			a_error("Папка тем не доступна для записи, установите права 777 на папку <b>views</b>");

		if (isset($_POST['submit'])) {
			main::is_demo();
			if (empty($_FILES['theme']['tmp_name'])) {
				$this->error .= 'Укажите zip файл темы!<br />';
			}

			if (!$this->error) {
				# Подключаем библиотеки для работы с архивами
				a_import('libraries/pclzip.lib');

				$archive = new PclZip($_FILES['theme']['tmp_name']);
				$theme_ini = $archive->extract(PCLZIP_OPT_BY_NAME, 'theme.ini', PCLZIP_OPT_EXTRACT_AS_STRING);
				$ini_string = $theme_ini[0]['content'];
				if (empty($ini_string))
					a_error("Не найден файл <b>theme.ini</b> загружаемой темы");

				$theme = parse_ini_string($ini_string);
				$theme['name'] = trim($theme['name']);
				if (empty($theme['name']))
					a_error("Не указано имя темы в файле theme.ini!");

				if (is_dir(ROOT.'views/'.$theme['name']))
					a_error("Папка с именем новой темы уже существует, возможно, тема была загружена ранее!");

				if (!preg_match("~^[0-9a-z_]*$~", $theme['name']))
					a_error("Имя темы имеет не правильный формат, оно должно состоять только из латинских букв в нижнем регистре, цифр и подчеркивания");

				# Создаем папку темы
				$theme_path = ROOT.'views/'.$theme['name'];
				mkdir($theme_path);

				# Извлекаем содержимое архива в папку модуля
				$result = $archive->extract(PCLZIP_OPT_PATH, $theme_path);

				if ($result[0]['status'] == 'ok')
					a_notice("Тема успешно загружена, теперь перейдите в раздел &quot;Конфигурация системы&quot; и активируйте её.", a_url('main/admin/config'));
				else
					a_notice("При извлечении архива произошла ошибка", a_url('themes/admin'));
			}
		}
		if (!isset($_POST['submit']) || $this->error) {
			$this->tpl->assign(array(
				'error' => $this->error
			));
	
			$this->tpl->display('upload_theme');
		}
	}
}
?>