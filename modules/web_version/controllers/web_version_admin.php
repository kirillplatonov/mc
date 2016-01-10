<?php

/**
 * Web version 
 *
 * @package
 * @author Platonov Kirill <platonov-kd@ya.ru>
 * @link http://twitter.com/platonov_kd
 */

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

/**
 * Контроллер управления темами
 */
class Web_Version_Admin_Controller extends Controller {
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
		$this->action_config();
	}
	
	/**
	 * Выбор web темы
	 */
  public function action_config() {
		$_config = $this->config['system'];
		
		echo $config['web_theme'];

		if (isset($_POST['submit'])) {
			main::is_demo();
			$_config = $_POST;

			foreach ($_config as $key => $value) {
				if ($key == 'submit') continue;
				$sql  = "UPDATE #__config SET \n";
				$sql .= "`value` = '".mysqli_real_escape_string($this->db->db_link, stripslashes($value))."'\n";
				$sql .= "WHERE `key` = '".$key."'";
				$this->db->query($sql);
			}

			# Чистим кеш главной
			@unlink(ROOT.'cache/file_cache/'.md5('index_page'));

			a_notice('Данные успешно изменены!', a_url('web_version/admin/config'));
		}

		if (!isset($_POST['submit']) || $error) {
			# Получаем темы
			$web_theme = array();
			$dir = opendir(ROOT.'views');
			while ($theme = readdir($dir)) {
				if ($theme == '.' || $theme == '..' || !preg_match('/^web_/i', $theme)) continue;
				if (file_exists(ROOT.'views/'.$theme.'/theme.ini')) {
					$theme_info = parse_ini_file(ROOT.'views/'.$theme.'/theme.ini');
					if (!empty($theme_info['title'])) {
						if (strpos($theme, 'admin') === 0) $admin_themes[] = $theme_info;
						else $web_theme[] = $theme_info;
					}
				}
			}

			$this->tpl->assign(array(
				'_config' => $_config,
				'web_theme' => $web_theme
			));

			$this->tpl->display('config');
		}
	}   	

	/**
	 * Загрузка новой темы
	 */
	public function action_upload_theme() {
		if(!is_writable(ROOT .'views')) {
					a_error("Папка тем не доступна для записи, установите права 777 на папку <b>views</b>");
		}

		if(isset($_POST['submit'])) {
			main::is_demo();
			if(empty($_FILES['theme']['tmp_name'])) {
				$this->error .= 'Укажите zip файл темы!<br />';
			}

			if(!$this->error) {
				# Подключаем библиотеки для работы с архивами
				a_import('libraries/pclzip.lib');

				$archive = new PclZip($_FILES['theme']['tmp_name']);
				$theme_ini = $archive->extract(PCLZIP_OPT_BY_NAME, 'theme.ini', PCLZIP_OPT_EXTRACT_AS_STRING);
				$ini_string = $theme_ini[0]['content'];
				if(empty($ini_string))
					a_error("Не найден файл <b>theme.ini</b> загружаемой темы");

				$theme = parse_ini_string($ini_string);
				$theme['name'] = trim($theme['name']);
				if(empty($theme['name']))
					a_error("Не указано имя темы в файле theme.ini!");

				if(is_dir(ROOT .'views/'. $theme['name']))
					a_error("Папка с именем новой темы уже существует, возможно, тема была загружена ранее!");

				if(!preg_match("~^[0-9a-z_]*$~", $theme['name']))
					a_error("Имя темы имеет не правильный формат, оно должно состоять только из латинских букв в нижнем регистре, цифр и подчеркивания");
					
				if (!preg_match('/^web_/i', $theme['name']))
		  a_error("Загружена не web тема. Отсутствует префикс &quot;web_&quot; в её названии!"); 

				# Создаем папку темы
				$theme_path = ROOT .'views/'. $theme['name'];
				mkdir($theme_path);

				# Извлекаем содержимое архива в папку модуля
				$result = $archive->extract(PCLZIP_OPT_PATH, $theme_path);

				if($result[0]['status'] == 'ok') {
									a_notice("Тема успешно загружена, теперь перейдите в раздел &quot;Выбор web темы&quot; и активируйте её.", a_url('web_version/admin/config'));
				} else {
									a_notice("При извлечении архива произошла ошибка", a_url('web_version/admin'));
				}
			}
		}
		if(!isset($_POST['submit']) || $this->error) {
			$this->tpl->assign(array(
				'error' => $this->error
			));
	
			$this->tpl->display('upload_theme');
		}
	}
}
?>
