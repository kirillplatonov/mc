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

// Папка с файлами загрузок
define('DOWNLOADS_DIRECTORY', 'files/downloads/');
// Максимальный размер файла для скачивания через force_download
define('FORCE_DOWNLOAD_MAX_FILESIZE', 0);

/**
 * Контроллер пользовательской части загруз-центра
 */
class Downloads_Controller extends Controller {
	/**
	 * Construct
	 */
	public function __construct() {
		parent::__construct();
		if (isset($_GET['preview'])) {
			if ($_GET['preview'] == 0 || $_GET['preview'] == 20 || $_GET['preview'] == 60 || $_GET['preview'] == 100)
				$_SESSION['downloads_preview'] = $_GET['preview'];
		}
		if (isset($_GET['order_by'])) {
			if ($_GET['order_by'] == 'name' || $_GET['order_by'] == 'time' || $_GET['order_by'] == 'downloads')
				$_SESSION['order_by'] = $_GET['order_by'];
		}
		# Хелпер загрузок
		a_import('modules/downloads/helpers/downloads');
	}

	/**
	 * Метод по умолчанию
	 */
	public function action_index() {
		$this->action_list_files();
	}

	/**
	 * Выбор превьюшек
	 */
	public function change_previews() {
		$this->tpl->display('preview');
		exit;
	}

	/**
	 * Листинг файлов
	 */
	public function action_list_files() {
		if (!isset($_SESSION['downloads_preview'])) $_SESSION['downloads_preview'] = 60;

		if (empty($_GET['directory_id']) OR !is_numeric($_GET['directory_id'])) {
			$directory_id = 0;
		}
		else {
			$directory_id = intval($_GET['directory_id']);
			if (!$directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = '$directory_id'")) {
				a_error('Папка не найдена!');
			}
		}

		switch ($_GET['action']) {
			# Поиск файлов
			case 'search':
				$action = 'search';
				$title = 'Результаты поиска';
				$sql = "SELECT SQL_CALC_FOUND_ROWS
						file_id,
						name,
						'file' AS type,
						file_ext,
						0 AS count_files,
						0 AS new_day,
						real_name,
						filesize,
						time,
						path_to_file,
						downloads,
						screen1,
						about,
						previews,
						(SELECT 0) AS position
						FROM #__downloads_files
						WHERE status = 'active' AND
						name LIKE '%". a_safe($_GET['search_word'])."%' ";

				if ($directory_id != 0) $sql .= " AND path_to_file LIKE '%/".$directory_id."/%' ";
				if (empty($_GET['search_word'])) $sql .= " AND 1 = 0 ";

				$sql .= " LIMIT $this->start, $this->per_page";
				break;
			
			# Список новых файлов
			case 'new_files':
				$action = 'new_files';
				$title = 'Новые файлы';
				$sql = "SELECT SQL_CALC_FOUND_ROWS
						file_id,
						name,
						'file' AS type,
						file_ext,
						0 AS count_files,
						0 AS new_day,
						real_name,
						filesize,
						time,
						path_to_file,
						downloads,
						screen1,
						about,
						previews,
						(SELECT 0) AS position
						FROM #__downloads_files
						WHERE status = 'active' AND 1 = 1 ";

				if ($directory_id != 0) $sql .= " AND path_to_file LIKE '%/".$directory_id."/%' ";

				$sql .= " ORDER BY time DESC ";
				$sql .= " LIMIT $this->start, $this->per_page";
				break;

			# Список папок и файлов
			default:
				$action = 'list';
				$title = 'Загрузки'.(!empty($directory['name']) ? ' | '.$directory['name'] : '');
				# Определяем папка с файлами или папками
				if ($this->db->get_one("SELECT file_id FROM #__downloads_files WHERE directory_id = $directory_id AND real_name != ''")) {
					$is_files_directory = true;
					$this->per_page = $this->config['downloads']['files_per_page'];
				} else {
					$is_files_directory = false;
					$this->per_page = $this->config['downloads']['directories_per_page'];
				}

				$this->tpl->assign('is_files_directory', $is_files_directory);

				if ($directory['images'] == 'yes' && !isset($_GET['preview']) && !isset($_GET['start'])) {
					$this->change_previews();
				}

				$directory_path = downloads::get_path($directory_id, $this->db);
				$namepath = downloads::get_namepath($directory_path, ' » ');


				# Получаем список папок и файлов
				$sql = "SELECT SQL_CALC_FOUND_ROWS
						dd.directory_id AS file_id,
						dd.name,
						'directory' AS type,
						'directory' AS file_ext,
						0 AS real_name,
						0 AS filesize,
						0 AS time,
						0 AS path_to_file,
						0 AS downloads,
						0 AS screen1,
						0 AS about,
						0 AS previews,
						dd.position
						FROM #__downloads_directories AS dd
						WHERE dd.parent_id = '$directory_id'".PHP_EOL;
				$sql .= "UNION ALL".PHP_EOL;
				$sql .= "SELECT
						file_id,
						name,
						'file' AS type,
						file_ext,
						real_name,
						filesize,
						time,
						path_to_file,
						downloads,
						screen1,
						about,
						previews,
						(SELECT 0) AS position
						FROM #__downloads_files
						WHERE
						directory_id = '$directory_id' AND
						status = 'active' AND
						real_name != ''".PHP_EOL;

				$sql .= "ORDER BY type ASC, ";

				# Сортировка
				if ($is_files_directory) {
					switch ($_SESSION['order_by']) {
						case 'name':
							$sql .= "name ASC ";
							break;
						case 'downloads':
							$sql .= "downloads DESC ";
							break;
						default:
							$sql .= "time DESC ";
							break;
					}
				} else {
					$sql .= "position ASC ";
				}

				$sql .= " LIMIT $this->start, $this->per_page";
				break;
		}
		
		# Работаем с кешем
		if ($action == 'list' && !$is_files_directory && $this->config['downloads']['cache_time'] > 0) {
			$using_cache = true;
			
			$cache_key = 'downloads_'.$directory_id.'_'.$this->start;
			$files = $this->cache->get($cache_key, $this->config['downloads']['cache_time'] * 60);
		}
		
		if (empty($files)) {
			$result = $this->db->query($sql);
			$total = $this->db->get_one("SELECT FOUND_ROWS()");
	
			$files = array();
			while ($file = $this->db->fetch_array($result)) {
				# Получаем счетчики файлов в папках
				if ($file['type'] == 'directory') {
					$counts = $this->db->get_row("SELECT
						COUNT(CASE WHEN status = 'active' THEN 1 END) AS count_files,
						COUNT(CASE WHEN time > UNIX_TIMESTAMP( ) - 86400 THEN 1 END) AS new_day
						FROM #__downloads_files
						WHERE path_to_file LIKE '%/". $file['file_id']."/%'
					");
					
					$file['count_files'] = $counts['count_files'];
					$file['new_day'] = $counts['new_day'];
				}
				
				$files[] = $file;
			}
			
			$files['total'] = $total;
			
			if ($using_cache) $this->cache->set($cache_key, $files);
		}

		# Пагинация
		$http_query = http_build_query(array(
			'directory_id' => $_GET['directory_id'],
			'action' => $_GET['action'],
			'search_word' => $_GET['search_word']
		), '', '&amp;');

		$pg_conf['base_url'] = $_GET['directory_id'] == 0 ? a_url('downloads/list_files', $http_query.'&amp;start=', true) : a_url('downloads/list_files', $http_query.'&amp;start=');
		$pg_conf['total_rows'] = $files['total'];
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);
		
		# Удаляем лишние данные
		unset($files['total']);

		$this->tpl->assign(array(
			'files' => $files,
			'total' => $total,
			'action' => $action,
			'title' => $title,
			'navigation' => $namepath,
			'pagination' => $pg->create_links(),
			'directory' => $directory,
			'_config' => $this->config['downloads'],
		));

		$this->tpl->display('list_files');
	}

	/**
	 * Форма поиска
	 */
	public function action_search_form() {
		if (empty($_GET['directory_id']) || !is_numeric($_GET['directory_id'])) {
			$directory_id = 0;
		} else {
			$directory_id = intval($_GET['directory_id']);

			if (!$directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = '$directory_id'")) a_error('Папка не найдена');
		}
		
		$directory_path = downloads::get_path($directory['directory_id'], $this->db);
		$navigation = downloads::get_namepath($directory_path, ' &raquo; ');

		if ($directory['directory_id'] > 0) $navigation .= (!empty($navigation) ? ' &raquo; ' : '').'<a href="'.URL.'downloads/'.$directory['directory_id'].'">'.$directory['name'].'</a>';

		if ($_GET['send']) {
			if (empty($_GET['search_word'])) $this->error .= 'Укажите запрос<br />';

			if (!$this->error) {
				header('Location: '.a_url('downloads/list_files', 'action=search&directory_id='.($directory_id > 0 ? $directory_id : '').'&search_word='.urlencode($_GET['search_word'])));
				exit;
			}
		}

		$this->tpl->assign(array(
			'error' => $this->error,
			'directory' => $directory,
			'directory_id' => $directory_id,
			'navigation' => $navigation,
		));

		$this->tpl->display('search_form');
	}

	/**
	 * Скачивание файла
	 */
	public function action_download_file() {
		$file_id = intval($_GET['file_id']);

		if (!$file = $this->db->get_row("SELECT * FROM #__downloads_files WHERE file_id = '$file_id'"))
			a_error('Файл не найден!');

		# Обновляем количество закачек файла
		$this->db->query("UPDATE #__downloads_files SET downloads = downloads + 1 WHERE file_id = '$file_id'");

		if (!file_exists(ROOT.$file['path_to_file'].'/'.$file['real_name']))
			a_error('Файл отсутствует!');

		if ($file['filesize'] > FORCE_DOWNLOAD_MAX_FILESIZE) {
			header('location: '.URL.$file['path_to_file'].'/'.$file['real_name']);
			exit;
		}
		else {
			$file_content = file_get_contents(ROOT.$file['path_to_file'].'/'.$file['real_name']);
			downloads::force_download($file['real_name'], $file_content, $file_id.'_'.$CONFIG['downloads_prefix'].'_', FALSE);
		}
	}

	/**
	 * Получение jad из jar
	 */
	public function action_get_jad() {
		if(!$file = $this->db->get_row("SELECT * FROM #__downloads_files WHERE file_id = '". intval($_GET['file_id']) ."'"))
			a_error("Файл не найден!");

		if(is_numeric($_GET['add_file'])) {
			if(!empty($file['add_file_real_name_'. $_GET['add_file']])) {
				$jar_name = $file['add_file_real_name_'. $_GET['add_file']];
				$file_ext = array_pop(explode('.', $jar_name));
			}
			else a_error("Дополнительный файл не найтен!");
		}
		else {
			$jar_name = $file['real_name'];
			$file_ext = $file['file_ext'];
		}
	
		if ($file_ext != 'jar') a_error("Это не JAR файл!");

		# Увеличиваем количество скачиваний файла
		$this->db->query("UPDATE a_downloads_files SET downloads = downloads + 1 WHERE file_id = '".$file['file_id']."'");
	
		if (!class_exists('PclZip')) a_import('libraries/pclzip.lib');
		a_import('libraries/j2me_tools');
	
		$jar_path = ROOT.$file['path_to_file'].'/'.$jar_name;
		$jar_url = URL.$file['path_to_file'].'/'.$jar_name;
		$jad_contents = j2me_tools::get_jad($jar_path, $jar_url);
	
		header('Content-type: text/vnd.sun.j2me.app-descriptor;charset=UTF-8');
		echo $jad_contents;
	}

	/**
	 * Просмотр деталей файла
	 */
	public function action_view_file() {
		# Инфо о файле
		if (!$file = $this->db->get_row("SELECT *,
		 	(SELECT username FROM #__users AS u WHERE u.user_id = df.user_id) AS username,
		 	(SELECT COUNT(*) FROM #__comments_posts WHERE module = 'downloads' AND item_id = df.file_id) comments
		 	FROM #__downloads_files AS df WHERE df.file_id = '". intval($_GET['file_id'])."'"))
			a_error('Файл не найден!');

		$directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = '".$file['directory_id']."'");
		if ($this->db->get_one("SELECT id FROM a_rating_logs WHERE ip = '".a_safe($_SERVER['REMOTE_ADDR'])."' AND module = 'downloads' AND item_id = '".$file['file_id']."'"))
			$file['rated'] = true;
		else $file['rated'] = false;

		# Получаем скриншоты файла
		$file['screens'] = '';
		if (!empty($file['screen1']) && empty($_GET['s']))
			$file['screens'] .= '<img src="'.URL.$file['path_to_file'].'/'.$file['screen1'].'" alt="" /><br />';
		elseif (!empty($file['screen2']) && $_GET['s'] == 2)
			$file['screens'] .= '<img src="'.URL.$file['path_to_file'].'/'.$file['screen2'].'" alt="" /><br />';
		elseif (!empty($file['screen3']) && $_GET['s'] == 3)
			$file['screens'] .= '<img src="'.URL.$file['path_to_file'].'/'.$file['screen3'].'" alt="" /><br />';

		if (!empty($file['screen2'])) {
			$file['screens'] .= empty($_GET['s']) ? '<b>1</b>' : '<a href="'.a_url('downloads/view_file', 'file_id='.$file['file_id']).'">1</a>';
			$file['screens'] .= ', '.(($_GET['s'] == 2) ? '<b>2</b>' : '<a href="'.a_url('downloads/view_file', 'file_id='.$file['file_id'].'&amp;s=2').'">2</a>');
			if (!empty($file['screen3']))
				$file['screens'] .= ', '.(($_GET['s'] == 3) ? '<b>3</b>' : '<a href="'.a_url('downloads/view_file', 'file_id='.$file['file_id'].'&amp;s=3').'">3</a>');
		}

		if (!empty($file['screens'])) $file['screens'] .= '<br /><br />';

		$file['about'] = nl2br(main::bbcode($file['about']));
		$file['rating_stars'] = file_get_contents(URL.'main/rating?rate='.$file['rating']);

		$directory_path = downloads::get_path($file['directory_id'], $this->db);
		$navigation = downloads::get_namepath($directory_path, ' » ');

		if ($directory['directory_id'] > 0)
			$navigation .= (!empty($navigation) ? ' » ' : '').'<a href="'.a_url('downloads', 'directory_id='.$directory['directory_id']).'">'.$directory['name'].'</a>';

		$this->tpl->assign(array(
			'file' => $file,
			'directory' => $directory,
			'navigation' => $navigation
		));
	
		$this->tpl->display('view_file');
	}

	/**
	 * Изменение рейтинга файла
	 */
	public function action_rating_change() {
		if (!$file = $this->db->get_row("SELECT file_id, user_id FROM a_downloads_files WHERE file_id = '".intval($_GET['file_id'])."'"))
			a_error("Файл не найден!");

		if ($this->db->get_one("SELECT id FROM a_rating_logs WHERE module = 'downloads' AND ip = '".a_safe($_SERVER['REMOTE_ADDR'])."' AND item_id = '".$file['file_id']."'"))
			a_error("Вы голосовали за данный файл ранее!");

		if ($file['user_id'] == USER_ID)
			a_error("Голосовать за свой файл нельзя!");

		$est = intval($_GET['est']);
		if ($est != 1 && $est != 2 && $est != 3 && $est != 4 && $est != 5)
			a_error("Оценка не определена!");

		# Увеличиваем количество голосов
		$this->db->query("UPDATE a_downloads_files SET
			rating = (rating * rating_voices + $est) / (rating_voices + 1),
			rating_voices = rating_voices + 1
			WHERE file_id = '".$file['file_id']."'
		");

		# Добавляем голос в логи
		$this->db->query("INSERT INTO a_rating_logs SET
			module = 'downloads',
			ip = '". a_safe($_SERVER['REMOTE_ADDR'])."',
			item_id = '". $file['file_id']."',
			time = UNIX_TIMESTAMP()
		");

		a_notice("Оценка принята!", URL.'downloads/view/'.$file['file_id']);
	}

	/**
	 * Управление файлами пользователей
	 */
	public function action_user_files() {
		// Запрет выгрузки гостям
		if (USER_ID == -1) a_error('Для выгрузки файлов на сайт необходимо <a href="'.a_url('user/login').'">войти</a> или <a href="'.a_url('user/registration').'">зарегистрироваться</a>');
		
		// Запрет выгрузки файлов пользователями
		if ($this->config['downloads']['user_upload'] == 0) a_error('Выгрузка файлов на сайт пользователями запрещена');
		
		// Проверка действия
		if ($_GET['action'] != 'add' && $_GET['action'] != 'edit' && $_GET['action'] != 'delete') a_error('Не выбрано действие');
		
		// Проверка директории
		if ($_GET['action'] == 'add' && !$directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = '".intval($_GET['directory_id'])."' AND user_files = 'yes'")) a_error('Папка не найдена, либо не предназначена для выгрузки файлов пользователями в нее');
		
		switch ($_GET['action']) {
			case 'add':
				$action = 'add';
				$title = 'Добавление файла';
				
				$directory_path = downloads::get_path($directory['directory_id'], $this->db);
				$navigation = downloads::get_namepath($directory_path, ' &raquo; ');

				if ($directory['directory_id'] > 0) $navigation .= (!empty($navigation) ? ' &raquo; ' : '').'<a href="'.URL.'downloads/'.$directory['directory_id'].'">'.$directory['name'].'</a>';
			
				// Обработка формы выгрузки файла
				if ($_POST['submit']) {
					// Фильтрация данных
					$name = htmlspecialchars(trim($_POST['name']));
					$about = htmlspecialchars(trim($_POST['about']));
				
					// Массивс информацией о файле
					$file = array();
					
					// Проверка корректности данных
					if (!empty($name) && main::strlen($name) > 32) $this->error .= 'Имя файла не должно быть длинее 32 символов<br />';
					
					if (!empty($about) && main::strlen($about) > 5000) $this->error .= 'Описание не должно быть длинее 5000 символов<br />';
					
					// Получение информации о файле
					if (!empty($_FILES['file_upload']['tmp_name'])) {
						$type = 'upload';
						$file['real_name'] = $_FILES['file_upload']['name'];
						$file['file_ext'] = array_pop(explode('.', $file['real_name']));
						$file['filesize'] = filesize($_FILES['file_upload']['tmp_name']);
					} else if (!empty($_POST['file_import']) && $_POST['file_import'] != 'http://') {
						$type = 'import';
						$file['real_name'] = basename($_POST['file_import']);
						$file['file_ext'] = array_pop(explode('.', $file['real_name']));
						$file['filesize'] = downloads::get_filesize($_POST['file_import']);
					} else {
						$this->error = 'Укажите загружаемый файл<br />';
					}
					
					// Проверка типа файла
					if ( ! strstr(';'. $this->config['downloads']['allowed_filetypes'] .';', ';'. $file['file_ext'] .';') && $type) {
						$this->error .= 'Вы пытаетесь загрузить запрещенный тип файла<br />';
					}

					// Проверка размера файла
					if (($file['filesize'] > $this->config['downloads']['max_filesize'] * 1048576) || $file['filesize'] === FALSE) {
						$this->error .= 'Размер загружаемого файла превышает допустимый размер ('. $this->config['downloads']['max_filesize'] .' Mb)<br />';
					}
					
					// Сохранение файла
					if ( ! $this->error) {
						// Получение ID файла
						$this->db->query("INSERT INTO #__downloads_files SET file_id = 'NULL'");
						$file_id = $this->db->insert_id();

						$directory_path = downloads::get_path($directory['directory_id'], $this->db);
		   				$realpath = downloads::get_realpath($directory_path);
						$realpath = ($realpath != '' ? $realpath . '/' :  '') . ($directory['directory_id'] == 0 ? '' : $directory['directory_id'] . '/');

						// Создание папки для файла
						mkdir(ROOT . DOWNLOADS_DIRECTORY . $realpath . $file_id);
   						chmod(ROOT . DOWNLOADS_DIRECTORY . $realpath . $file_id, 0777);

   						$path_to_file = DOWNLOADS_DIRECTORY . ($realpath != '' ? $realpath : '') . $file_id;

   						if ($type == 'upload') {
   							$file_path = ROOT . $path_to_file .'/'. $_FILES['file_upload']['name'];
							copy($_FILES['file_upload']['tmp_name'], $file_path);
						} else {
							$file_path = ROOT . $path_to_file .'/'. basename($_POST['file_import']);
							copy($_POST['file_import'], $file_path);
						}
						
						// Работа со скриншотом к основному файлу
						if ( ! empty($_FILES['screen1']['tmp_name'])) {
							$screen_path = ROOT . $path_to_file .'/'. $_FILES['screen1']['name'];

							if (copy($_FILES['screen1']['tmp_name'], $screen_path)) {
								if ($this->config['downloads']['screens_width'] > 0) {
									main::image_resize($screen_path, $screen_path, intval($this->config['downloads']['screens_width']));
								}

								$file['screen1'] = $_FILES['screen1']['name'];
							}
						} else if (!empty($_POST['screen1']) && $_POST['screen1'] != 'http://') {
							$import_file_path = fm::get_real_file_path($_POST['screen_1']);
							$import_file_name = basename($import_file_path);
							$screen_path = ROOT.$path_to_file.'/'.$import_file_name;

							if (copy($import_file_path, $screen_path)) {
								if ($this->config['downloads']['screens_width'] > 0) {
									main::image_resize($screen_path, $screen_path, intval($this->config['downloads']['screens_width']));
								}

								$file['screen1'] = $import_file_name;
							}
						}
						
						$file['name'] = $_POST['name'];
						$file['about'] = $_POST['about'];
						if ($this->config['downloads']['moderation'] == 1) {
							$file['status'] = 'moderate';
						} else {
							$file['status'] = 'active';
						}
						$file['user_id'] = USER_ID;
						$file['path_to_file'] = $path_to_file;
						$file['directory_id'] = $directory['directory_id'];

						// Выполнение действий над определенными типами файлов
						$file = downloads::filetype_actions($file);

						// Изменение файла в базе
						downloads::update_file($this->db, $file_id, $file);

						a_notice($this->config['downloads']['moderation'] == 1 ? 'Файл успешно загружен. Он будет доступен для скачиваниями другими пользователями после прохождения модерации' : 'Файл успешно загружен', URL.'downloads/view/'.$file_id);
					}
				}
			break;
			
			case 'edit':
				$action = 'edit';
				$title = 'Изменение файла';
			
				// Проверка файла
				if ( ! $file = $this->db->get_row("SELECT * FROM #__downloads_files WHERE file_id = '". intval($_GET['file_id']) ."' AND user_id = '". USER_ID ."' AND path_to_file != ''")) a_error('Файл не найден');
			    
				$directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = '$file[directory_id]'");
			    
				$directory_path = downloads::get_path($directory['directory_id'], $this->db);
				$navigation = downloads::get_namepath($directory_path, ' &raquo; ');

				if ($directory['directory_id'] > 0) $navigation .= ( ! empty($navigation) ? ' &raquo; ' : '') .'<a href="'. URL .'downloads/'. $directory['directory_id'] .'">'. $directory['name'] .'</a>';
			    
				// Обработка формы выгрузки файла
				if ($_POST['submit']) {
					// Фильтрация данных
					$name = htmlspecialchars(trim($_POST['name']));
					$about = htmlspecialchars(trim($_POST['about']));

					// Проверка корректности данных
					if ( ! empty($name) && main::strlen($name) > 32) {
						$this->error .= 'Имя файла не должно быть длинее 32 символов<br />';
					}

					if ( ! empty($about) && main::strlen($about) > 5000) {
						$this->error .= 'Описание не должно быть длинее 5000 символов<br />';
					}

					// Получение информации о файле
					if ( ! empty($_FILES['file_upload']['tmp_name'])) {
						@unlink(ROOT . $file['path_to_file'] .'/'. $file['real_name']);
						$type = 'upload';
						$file['real_name'] = $_FILES['file_upload']['name'];
						$file['file_ext'] = array_pop(explode('.', $file['real_name']));
						$file['filesize'] = filesize($_FILES['file_upload']['tmp_name']);
					} else if ( ! empty($_POST['file_import']) && $_POST['file_import'] != 'http://') {
						@unlink(ROOT . $file['path_to_file'] .'/'. $file['real_name']);
						$type = 'import';
						$file['real_name'] = basename($_POST['file_import']);
						$file['file_ext'] = array_pop(explode('.', $file['real_name']));
						$file['filesize'] = downloads::get_filesize($_POST['file_import']);
					}

					// Проверка типа файла
					if ( ! strstr(';'. $this->config['downloads']['allowed_filetypes'] .';', ';'. $file['file_ext'] .';') && $type) {
						$this->error .= 'Вы пытаетесь загрузить запрещенный тип файла<br />';
					}

					// Проверка размера файла
					if (($file['filesize'] > $this->config['downloads']['max_filesize'] * 1048576) || $file['filesize'] === FALSE) {
						$this->error .= 'Размер загружаемого файла превышает допустимый размер ('. $this->config['downloads']['max_filesize'] .' Mb)<br />';
					}

					// Сохранение файла
					if ( ! $this->error) {
						// Получение ID файла
						$file_id = $file['file_id'];

   						$path_to_file = $file['path_to_file'];

   						if ($type && $type == 'upload') {
			 				$file_path = ROOT . $path_to_file .'/'. $_FILES['file_upload']['name'];
							copy($_FILES['file_upload']['tmp_name'], $file_path);
						} elseif ($type && $type == 'import') {
							$file_path = ROOT . $path_to_file .'/'. basename($_POST['file_import']);
							copy($_POST['file_import'], $file_path);
						}

						// Работа со скриншотом к основному файлу
						if ( ! empty($_FILES['screen1']['tmp_name'])) {
							@unlink(ROOT . $path_to_file .'/'. $file['screen1']);
						
							$screen_path = ROOT . $path_to_file .'/'. $_FILES['screen1']['name'];

							if (copy($_FILES['screen1']['tmp_name'], $screen_path)) {
								if ($this->config['downloads']['screens_width'] > 0) {
									main::image_resize($screen_path, $screen_path, intval($this->config['downloads']['screens_width']));
								}

								$file['screen1'] = $_FILES['screen1']['name'];
							}
						} else if ( ! empty($_POST['screen1']) && $_POST['screen1'] != 'http://') {
							@unlink(ROOT . $path_to_file .'/'. $file['screen1']);
						
							$import_file_path = fm::get_real_file_path($_POST['screen_1']);
							$import_file_name = basename($import_file_path);
							$screen_path = ROOT . $path_to_file .'/'. $import_file_name;

							if (copy($import_file_path, $screen_path)) {
								if ($this->config['downloads']['screens_width'] > 0) {
									main::image_resize($screen_path, $screen_path, intval($this->config['downloads']['screens_width']));
								}

								$file['screen1'] = $import_file_name;
							}
						}

						$file['name'] = $_POST['name'];
						$file['about'] = $_POST['about'];
						if ($this->config['downloads']['moderation'] == 1) {
							$file['status'] = 'moderate';
						} else {
							$file['status'] = 'active';
						}
						$file['user_id'] = USER_ID;
						$file['path_to_file'] = $path_to_file;
						$file['directory_id'] = $directory['directory_id'];

						// Выполнение действий над определенными типами файлов
						$file = downloads::filetype_actions($file);

						// Изменение файла в базе
						downloads::update_file($this->db, $file_id, $file);

						a_notice($this->config['downloads']['moderation'] == 1 ? 'Файл успешно изменен. Он будет доступен для скачиваниями другими пользователями после прохождения модерации' : 'Файл успешно загружен', URL.'downloads/view/'.$file_id);
					}
				}
			break;
			
			case 'delete':
				$action = 'delete';
				$title = 'Удаление файла';
			
				// Проверка файла
				if ( ! $file = $this->db->get_row("SELECT * FROM #__downloads_files WHERE file_id = '". intval($_GET['file_id']) ."' AND user_id = '". USER_ID ."' AND path_to_file != ''")) a_error('Файл не найден');
			    
				if ( ! empty($_GET['confirm'])) {
					// Удаление папки из ФС
					main::delete_dir(ROOT . $file['path_to_file']);

					// Удаление файла из БД
					$this->db->query("DELETE FROM #__downloads_files WHERE file_id = '". $file['file_id'] ."'");

					a_notice('Файл успешно удален', URL .'downloads/'. $file['directory_id']);
				} else {
					a_confirm('Вы подтверждаете удаление данного файла?', a_url('downloads/user_files', 'action=delete&amp;file_id='. $file['file_id'] .'&amp;confirm=ok'), URL .'downloads/view/'. $file['file_id']);
				}
			break;
		}
		
		$this->tpl->assign(array(
			'error' => $this->error,
			'directory' => $directory,
			'action' => $action,
			'title' => $title,
			'file' => $file,
			'navigation' => $navigation,
		));

		$this->tpl->display('user_files');
	}
	
	/**
	 * Файлы и комментарии пользователя
	 */
	public function action_my() {
		// Запрет доступа гостям
		if (USER_ID == -1) a_error('Для доступа к странице необходимо <a href="'. a_url('user/login') .'">войти</a> или <a href="'. a_url('user/registration') .'">зарегистрироваться</a>');

		// Проверка действия
		if ($_GET['action'] != 'files' && $_GET['action'] != 'comments') a_error('Не выбрано действие');
		
		switch($_GET['action']) {
			case 'files':
				$action = 'files';
				$title = 'Мои файлы';

				$result = $this->db->query("SELECT * FROM #__downloads_files WHERE user_id = '". USER_ID ."' ORDER BY time DESC LIMIT $this->start, $this->per_page");
				$total = $this->db->get_one("SELECT COUNT(*) FROM #__downloads_files WHERE user_id = '". USER_ID ."'");

				$files = array();
				while ($file = $this->db->fetch_array($result)) {
					$files[] = $file;
				}

				$files['total'] = $total;

				$pg_conf['base_url'] = a_url('downloads/my', 'action=files&amp;start=');
				$pg_conf['total_rows'] = $files['total'];
				$pg_conf['per_page'] = $this->per_page;

				a_import('libraries/pagination');
				$pg = new CI_Pagination($pg_conf);

				// Удаляем лишние данные
				unset($files['total']);

				$this->tpl->assign(array(
					'title' => $title,
					'action' => $action,
					'pagination' => $pg->create_links(),
					'total' => $total,
					'files' => $files,
					'start' => $this->start,
					'_config' => $this->config['downloads'],
				));

				$this->tpl->display('my_files');
			break;
			
			case 'comments':
			break;
		}
	}
}
?>