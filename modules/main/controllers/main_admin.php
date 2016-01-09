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
 * Основные функции админ панели
 */
class Main_Admin_Controller extends Controller {
	/**
	 * Уровень пользовательского доступа
	 */
	protected $access_level = 10;
	/**
	 * Тема
	 */
	protected $template_theme = 'admin';

	/**
	 * Метод по умолчанию
	 */
	public function action_index() {
		$this->action_config();
	}

	/**
	 * Конфигурация системы
	 */
	public function action_config() {
		$_config = $this->config['system'];

		if (isset($_POST['submit'])) {
			main::is_demo();
			$_config = $_POST;

			foreach ($_config as $key => $value) {
				if ($key == 'submit') continue;
				$sql  = "UPDATE #__config SET \n";
				$sql .= "`value` = '".mysqli_real_escape_string($this->db, stripslashes($value))."'\n";
				$sql .= "WHERE `key` = '".$key."'";
				$this->db->query($sql);
			}

			# Чистим кеш главной
			@unlink(ROOT.'cache/file_cache/'.md5('index_page'));

			a_notice('Данные успешно изменены!', a_url('main/admin/config'));
		}

		if (!isset($_POST['submit']) || $error) {
			# Получаем темы
			$default_themes = array();
			$admin_themes = array();
			$dir = opendir(ROOT.'views');
			while ($theme = readdir($dir)) {
				if ($theme == '.' || $theme == '..' || $theme == '.htaccess' || $theme == '.gitignore') continue;
				if (file_exists(ROOT.'views/'.$theme.'/theme.ini')) {
					$theme_info = parse_ini_file(ROOT.'views/'.$theme.'/theme.ini');
					if (!empty($theme_info['title'])) {
						if (strpos($theme, 'admin') === 0) $admin_themes[] = $theme_info;
						else $default_themes[] = $theme_info;
					}
				}
			}

			$mainpage_modules = array();
			$dir = opendir(ROOT.'modules');
			while ($module = readdir($dir)) {
				if ($module == '.' || $module == '..' || $module == '.htaccess' || $module == '.gitignore') continue;
				if (file_exists(ROOT.'modules/'.$module.'/module.ini')) {
					$module_info = parse_ini_file(ROOT.'modules/'.$module.'/module.ini');
					if ($module_info['mainpage']) $mainpage_modules[] = $module_info;
				}
			}

			$this->tpl->assign(array(
				'_config' => $_config,
				'admin_themes' => $admin_themes,
				'default_themes' => $default_themes,
				'mainpage_modules' => $mainpage_modules
			));

			$this->tpl->display('config');
		}
	}
	
	/**
	 * Конфигурация модуля
	 */
	public function action_ftp_config() {
		$_config = $this->config['ftp'];

		if (isset($_POST['submit'])) {
			main::is_demo();
			$_config = $_POST;
			
			if (!class_exists('main_ftp')) a_import('modules/main/helpers/main_ftp');
			
			# Тестируем подключение и папку скрипта
			$connect_data = array(
				'server' => $_POST['server'],
				'port' => $_POST['port'],
				'login' => $_POST['login'],
				'password' => $_POST['password']
			);
			
			if (!$ftp_handle = main_ftp::connect($connect_data))
				$this->error .= 'Не удалось подключиться к указанному фтп серверу, проверьте правильность указанных данных<br />';
			
			if (!$this->error) {	
				$test_file = 'tmp/'.main::get_unique_code();
				
				file_put_contents(ROOT.$test_file, 'ftp test');
				
				if (!@ftp_get($ftp_handle, ROOT.'tmp/'.main::get_unique_code(), $_POST['path'].'/'.$test_file, FTP_ASCII))
					$this->error .= 'Фтп папка скрипта указана не верно!';
				
				if (!$this->error) {
					main::config($_config, 'ftp', $this->db);
		
					a_notice('Данные успешно изменены!', a_url('main/admin/ftp_config'));
				}
			}
		}

		if (!isset($_POST['submit']) || $this->error) {
			$this->tpl->assign(array(
				'error' => $this->error,
				'_config' => $_config
			));

			$this->tpl->display('ftp_config');
		}
	}
	
	/**
	 * Загрузка модулей, тем и обновлений по фтп
	 */
	public function action_upload() {
		switch ($_GET['action']) {
			case 'module':
				$action = 'module';
				$title = 'Загрузить модуль';
				$notice_message = 'Модуль успешно загружен, теперь перейдите в управление модулями и инсталлируйте его';
				$notice_url = a_url('modules/admin');
				break;
			
			case 'theme':
				$action = 'theme';
				$title = 'Загрузить тему';
				$notice_message = 'Тема успешно загружена, теперь перейдите в раздел &quot;Конфигурация системы&quot; и активируйте её.';
				$notice_url = a_url('main/admin/config');
				break;
			
			case 'update':
			default:
				$action = 'update';
				$title = 'Загрузить обновление';
				$notice_message = 'Обновление успешно загружено!';
				$notice_url = a_url('user/admin');
				break;
		}
		
		if (isset($_POST['submit'])) {
			if (empty($_FILES['file']['tmp_name']))
				$this->error .= "Укажите файл для загрузки<br />";
		
			if (!$this->error) {
				# Создаем объект архива
				a_import('libraries/pclzip.lib');
				$archive = new PclZip($_FILES['file']['tmp_name']);
				
				# Подключаемся по фтп
				if (!class_exists('main_ftp')) a_import('modules/main/helpers/main_ftp');
			
				$connect_data = array(
					'server' => $this->config['ftp']['server'],
					'port' => $this->config['ftp']['port'],
					'login' => $this->config['ftp']['login'],
					'password' => $this->config['ftp']['password']
				);
				
				if (!$ftp_handle = main_ftp::connect($connect_data))
					a_notice('Не удалось подключиться к указанному фтп серверу, проверьте настройки FTP', a_url('main/admin/ftp_config'));
				
				$test_file = 'tmp/'.main::get_unique_code();
				
				file_put_contents(ROOT.$test_file, 'ftp test');
				
				if (!@ftp_get($ftp_handle, ROOT.'tmp/'.main::get_unique_code(), $this->config['ftp']['path'].'/'.$test_file, FTP_ASCII))
					a_notice('Не удалось проверить корневую папку MobileCMS, проверьте настройки FTP', a_url('main/admin/ftp_config'));
				
				switch ($action) {
					# Подготовка к загрузке для модуля
					case 'module':
						$module_ini = $archive->extract(PCLZIP_OPT_BY_NAME, 'module.ini', PCLZIP_OPT_EXTRACT_AS_STRING);
						$ini_string = $module_ini[0]['content'];
						if (empty($ini_string))
							a_error("Не найден файл <b>module.ini</b> загружаемого модуля!");
		
						$module = parse_ini_string($ini_string);
						$module['name'] = trim($module['name']);
						if (empty($module['name']))
							a_error("Не указано имя модуля в файле module.ini!");
		
						if (is_dir(ROOT.'modules/'.$module['name']))
							a_error("Папка с именем нового модуля уже существует, возможно, модуль быз загружен ранее!");
		
						if (!preg_match("~^[0-9a-z_]*$~", $module['name']))
							a_error("Имя модуля имеет не правильный формат, оно должно состоять только из латинских букв в нижнем регистре, цифр и подчеркивания");
							
						# Создаем папку модуля
						if (!@ftp_mkdir($ftp_handle, $this->config['ftp']['path'].'/modules/'.$module['name']))
							a_error("Не удалось создать папку модуля");
							
						$upload_path = $this->config['ftp']['path'].'/modules/'.$module['name'];
						break;
					
					# Подготовка к загрузке для темы
					case 'theme':
						$theme_ini = $archive->extract(PCLZIP_OPT_BY_NAME, 'theme.ini', PCLZIP_OPT_EXTRACT_AS_STRING);
						$ini_string = $theme_ini[0]['content'];
						if (empty($ini_string))
							a_error("Не найден файл <b>theme.ini</b> загружаемой темы!");
		
						$theme = parse_ini_string($ini_string);
						$theme['name'] = trim($theme['name']);
						if (empty($theme['name']))
							a_error("Не указано имя темы в файле theme.ini!");
		
						if (is_dir(ROOT.'views/'.$theme['name']))
							a_error("Папка с именем новой темы уже существует, возможно, тема была загружена ранее!");
		
						if (!preg_match("~^[0-9a-z_]*$~", $theme['name']))
							a_error("Имя темы имеет не правильный формат, оно должно состоять только из латинских букв в нижнем регистре, цифр и подчеркивания");
							
						# Создаем папку темы
						if (!@ftp_mkdir($ftp_handle, $this->config['ftp']['path'].'/views/'.$theme['name']))
							a_error("Не удалось создать папку темы");
							
						$upload_path = $this->config['ftp']['path'].'/views/'.$theme['name'];
						break;
					
					# Подготовка к загрузке для обновления
					case 'update':
						$upload_path = $this->config['ftp']['path'];
						break;
				}
				
				# Распаковываем архив во временную папку
				$tmp_path = ROOT.'tmp/'.main::get_unique_code();
				mkdir($tmp_path);
				$result = $archive->extract(PCLZIP_OPT_PATH, $tmp_path);
				
				main_ftp::copy_local_dir($ftp_handle, $tmp_path, $upload_path);
				
				a_notice($notice_message, $notice_url);
			}
		}
		if (!isset($_POST['submit']) or $this->error) {
			$this->tpl->assign(array(
				'error' => $this->error,
				'action' => $action,
				'title' => $title
			));
			
			$this->tpl->display('upload');
		}
	}
	
	/**
	 * MySQL запросы
	 */
	public function action_mysql() {
	
	# Парсер SQL запросов
  function parse_sql($sql) {
	$queries = array();
	$strlen = strlen($sql);
	$position = 0;
	$query = '';

	for (;$position<$strlen;++$position) {
	  $char = $sql{$position};

	  switch ($char) {
		case '-':
		  if (substr($sql, $position, 3) !== '-- ') {
			$query .= $char;
            
			break;
		  }

		case '#':
		  while ($char !== "\r" && $char !== "\n" && $position < $strlen - 1) $char = $sql{++$position};
		break;

		case '`':
		case '\'':
		case '"':
		  $quote  = $char;
		  $query .= $quote;

		  while ($position < $strlen - 1) {
			$char = $sql{++$position};

			if ($char === '\\') {
			  $query .= $char;

			  if ($position < $strlen - 1) {
				$char = $sql{++$position};
				$query .= $char;

				if ($position < $strlen - 1) $char = $sql{++$position};
			  } else {
				break;
			  }
			}

			if ($char === $quote) break;

			$query .= $char;
		  }

		  $query .= $quote;
          
		  break;

		case ';':
		  $query = trim($query);
                    
		  if ($query) $queries[] = $query;
          
		  $query = '';
		break;

		default:
		  $query .= $char;
		break;
	  }
	}

	$query = trim($query);
        
	if ($query) $queries[] = $query;

	return $queries;
  }
	
	if (isset($_POST['submit'])) {
	  main::is_demo();
    
	  if (empty($_POST['queries']))
		a_error('Запросы отсутствуют');
        
	  if (!$error) {
		$sql = parse_sql($_POST['queries']);
        
		# Кол-во выполненных запросов
		$num_queries = 0;
        
		# Выполнение запросов
		for ($i=0;$i<count($sql);$i++) {
		  if ($sql[$i] != '') {
			if ($this->db->query($sql[$i])) {
			  $num_queries++;
			}
		  }
		}
	  }  

		  a_notice('Выполнено '. $num_queries .' запросов', a_url('main/admin/mysql'));
	}

	if(!isset($_POST['submit']) || $error) {
	  $this->tpl->assign(array(
			 'error' => $this->error,
			 'title' => 'MySQL запросы'
		  ));
			
		  $this->tpl->display('mysql'); 
	} 
	}
}

?>
