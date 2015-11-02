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
 * Контроллер управления загруз центром
 */
class Downloads_Admin_Controller extends Controller {
	/**
	 * Доступ
	 */
	public $access_level = 8;
	/**
	 * Тема
	 */
	public $template_theme = 'admin';
	/**
	 * Папка для загрузки по фтп
	 */
	public $ftp_directory = '_ftp_upload/';

	/**
	 * Construct
	 */
	public function __construct() {
		parent::__construct();

		define('DOWNLOADS_DIRECTORY', 'files/downloads/');
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
	 * Конфигурация модуля
	 */
	public function action_config() {
		$_config = $this->config['downloads'];

		if (isset($_POST['submit'])) {
			main::is_demo();
			$_config = $_POST;

			main::config($_config, 'downloads', $this->db);

			a_notice('Данные успешно изменены!', a_url('downloads/admin/config'));
		}

		if (!isset($_POST['submit']) || $this->error) {
			$this->tpl->assign(array(
				'_config' => $_config
			));

			$this->tpl->display('config');
		}
	}

	/**
	 * Список файлов и папок
	 */
	public function action_list_files() {
		if (empty($_GET['directory_id']) or !is_numeric($_GET['directory_id'])) $directory_id = 0;
		else $directory_id = intval($_GET['directory_id']);

		if ($directory_id != 0 && !$directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = '$directory_id'")) {
			a_error('Папка не найдена!');
		}
		else {
			# Определяем папка с файлами или папками
			if ($this->db->get_one("SELECT directory_id FROM #__downloads_directories WHERE parent_id = $directory_id")) {
				$files_directory = false;
				$this->per_page = 100;
			}
			else {
				$files_directory = true;
				$this->per_page = 20;
			}
		}

		$directory_path = downloads::get_path($directory_id, $this->db);
		$namepath = downloads::get_namepath($directory_path, '/', TRUE);

		# Получаем список папок и файлов
		$sql = "SELECT SQL_CALC_FOUND_ROWS
        			directory_id AS file_id,
        			name,
        			'directory' AS type,
        			0 AS real_name,
        			0 AS path_to_file,
				position
        			FROM #__downloads_directories
        			WHERE parent_id = '$directory_id'".PHP_EOL;
		$sql .= "UNION ALL".PHP_EOL;
		$sql .= "SELECT
        			file_id,
        			name,
        			'file' AS type,
        			real_name,
        			path_to_file,
				0 AS position
        			FROM #__downloads_files
        			WHERE directory_id = '$directory_id' AND status = 'active' AND real_name != ''".PHP_EOL;

		$sql .= "ORDER BY type ASC, position ASC, file_id DESC LIMIT $this->start, $this->per_page";

		$result = $this->db->query($sql);
		$total = $this->db->get_one("SELECT FOUND_ROWS()");

		$min_p = $this->db->get_one("SELECT MIN(position) FROM #__downloads_directories WHERE parent_id = '$directory_id'");
 		$max_p = $this->db->get_one("SELECT MAX(position) FROM #__downloads_directories WHERE parent_id = '$directory_id'");

		$files = array();
		while ($file = $this->db->fetch_array($result)) {
			if ($file['type'] == 'directory') {
				if ($file['position'] != $min_p) $file['up'] = '<a href="'.a_url('downloads/admin/directory_up', 'directory_id='.$file['file_id']).'"><img src="'.URL.'views/admin/images/up.png" alt="" /></a>';
				else $file['up'] = '';
	
				if ($file['position'] != $max_p) $file['down'] = '<a href="'.a_url('downloads/admin/directory_down', 'directory_id='.$file['file_id']).'"><img src="'.URL.'views/admin/images/down.png" alt="" /></a>';
				else $file['down'] = '';
			}
			else {
				$file['up'] = '-';
				$file['down'] = '-';
			}

			$files[] = $file;
		}

		# Пагинация
		$pg_conf['base_url'] = a_url('downloads/admin/list_files', 'directory_id='.intval($_GET['directory_id']).'&amp;start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'files' => $files,
			'total' => $total,
			'namepath' => $namepath,
			'pagination' => $pg->create_links(),
			'directory' => $directory
		));

		$this->tpl->display('list_files');
	}

	/**
	 * Создание / редактирование папки
	 */
	public function action_directory_edit() {
		if (is_numeric(@$_GET['directory_id'])) {
			$directory_id = intval($_GET['directory_id']);
 			if (!$directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = '$directory_id'")) {
				a_error('Папка не найдена!');
			}
			$parent_directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = '".$directory['parent_id']."'");
			$action = 'edit';
		}
		else {
			if ($_GET['parent_id'] != '' && !$parent_directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = '".intval($_GET['parent_id'])."'"))
				a_error('Директория предок не найдена!');
			$directory = array();
			$action = 'add';
		}

		if (isset($_POST['submit'])) {
			main::is_demo();
			if (empty($_POST['name'])) {
				$this->error .= 'Укажите название папки!<br />';
			}

			if (!$this->error) {
				# Определяем, является ли папка c изображениями
				if (isset($_POST['images'])) $images = 'yes';
				else $images = 'no';
	
				# Определяем, является ли папка c файлами пользователей
				if (isset($_POST['user_files']) OR $parent_directory['user_files'] == 'yes') $user_files = 'yes';
				else $user_files = 'no';

				# Создаем нувую папку
				if ($action == 'add') {
					# Получаем позицию папки
					if (!isset($_POST['in_up'])) {
						$position = $this->db->get_one("SELECT MAX(position) FROM #__downloads_directories WHERE parent_id = '".$parent_directory['directory_id']."'") + 1;
					}
					else {
						$position = 1;
						$this->db->query("UPDATE #__downloads_directories SET position = position + 1 WHERE parent_id = '".$parent_directory['directory_id']."'");
					}
	
					$this->db->query("INSERT INTO #__downloads_directories SET
						name = '". a_safe($_POST['name'])."',
						images = '". $images."',
						user_files = '". $user_files."',
						parent_id = '". @$parent_directory['directory_id']."',
						position = '$position'
					");
	
					$directory_id = $this->db->insert_id();

					# Создаем папку в файловой системе
					# Получаем директорию для папки
					$directory_path = downloads::get_path($directory_id, $this->db);
					$realpath = downloads::get_realpath($directory_path);

					mkdir(ROOT.DOWNLOADS_DIRECTORY.$realpath.'/'.$directory_id);
	 				chmod(ROOT.DOWNLOADS_DIRECTORY.$realpath.'/'.$directory_id, 0777);

					a_notice('Папка успешно создана!', a_url('downloads/admin/list_files', 'directory_id='.@$parent_directory['directory_id']));
	 			}
				elseif ($action == 'edit') {
					# Изменяем имя папки
					$this->db->query("UPDATE #__downloads_directories SET
						name = '". a_safe($_POST['name'])."',
						images = '". $images."',
						user_files = '". $user_files."'
						WHERE
						directory_id = '". $directory_id."'
					");

					a_notice('Папка успешно изменена!', a_url('downloads/admin/list_files', 'directory_id='.$parent_directory['directory_id']));
				}
			}
		}
		if (!isset($_POST['submit']) || $this->error) {
			$this->tpl->assign(array(
				'error' => $this->error,
				'directory' => $directory,
				'action' => $action,
				'_config' => $this->config['downloads'],
			));
			$this->tpl->display('directory_edit');
		}
	}

	/**
	 * Модерация файлов пользователей
	 */
	public function action_list_moderate_files() {
		$sql = "SELECT SQL_CALC_FOUND_ROWS *, u.username, (SELECT name FROM #__downloads_directories WHERE directory_id = f.directory_id) AS directory_name
			FROM #__downloads_files AS f LEFT JOIN #__users AS u USING(user_id) WHERE f.status = 'moderate'";
		$files = $this->db->get_array($sql);
		$total = $this->db->get_one("SELECT FOUND_ROWS()");

		# Пагинация
		$pg_conf['base_url'] = a_url('downloads/admin/list_moderate_files', 'start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'files' => $files,
			'directory' => $directory
		));

		$this->tpl->display('list_moderate_files');
	}

	/**
	 * Удаление папки
	 */
	public function action_directory_delete() {
		main::is_demo();
		$directory_id = intval($_GET['directory_id']);

		if (!$directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = '$directory_id'")) {
			a_error('Папка не найдена!');
		}

		# Удаление из ФС
		$directory_path = downloads::get_path($directory_id, $this->db);
		$realpath = downloads::get_realpath($directory_path);
		main::delete_dir(ROOT.DOWNLOADS_DIRECTORY.$realpath.'/'.$directory_id);

		# Удаляем все файлы из базы
		$this->db->query("DELETE FROM #__downloads_files WHERE path_to_file LIKE CONCAT('%/', $directory_id, '/%')");
		# Удаляем всех потомков данной папки
		downloads::delete_directories($directory_id);

		# Удаление папки из базы
		$this->db->query("DELETE FROM #__downloads_directories WHERE directory_id = '$directory_id'");

		# Меняем позиции
		$this->db->query("UPDATE #__downloads_directories SET position = position - 1 WHERE parent_id = '". $directory['parent_id'] ."' AND position > '". $directory['position'] ."'");

		a_notice('Папка успешно удалена!', a_url('downloads/admin/list_files', 'directory_id='. $directory['parent_id']));
	}

	/**
	 * Загрузка файла
	 */
	public function action_file_upload() {
		if(function_exists('set_time_limit')) {
			set_time_limit(0);
		}
		# Редактирование файла
		if(is_numeric(@$_GET['file_id'])) {
			if(!$file = $this->db->get_row("SELECT * FROM #__downloads_files WHERE file_id = '". intval($_GET['file_id']) ."'")) {
				a_error('Файл не найден!');
			}
			$action = 'edit';
			$file_id = intval($_GET['file_id']);
			$directory_id = $file['directory_id'];
			$new_file = false;
		}
		# Добавление файла
		else {
			$file = array();
			$action = 'add';
			$new_file = true;

			# Получем данные о папке для загрузки
			if (empty($_GET['directory_id']) OR !is_numeric($_GET['directory_id'])) $directory_id = 0;
			else $directory_id = intval($_GET['directory_id']);

			if ($directory_id != 0 && !$directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = '".$directory_id."'")) {
	  			a_error('Папка для загрузки не найдена!');
	  		}
		}

		if (isset($_POST['submit'])) {
			main::is_demo();
			if (!$this->error) {
				# Получаем путь к папке
				$directory_path = downloads::get_path($directory_id, $this->db);
		   		$realpath = downloads::get_realpath($directory_path);
				$realpath = ($realpath != '' ? $realpath.'/' : '').($directory_id == 0 ? '' : $directory_id.'/');

				if ($action == 'add') {
					# Создаем пустую строку в базе для файла (чтобы получить его ID)
					$this->db->query("INSERT INTO #__downloads_files SET file_id = 'NULL'");
					$file_id = $this->db->insert_id();

					# Создаем папку для файла
					mkdir(ROOT.DOWNLOADS_DIRECTORY.$realpath.$file_id);
					chmod(ROOT.DOWNLOADS_DIRECTORY.$realpath.$file_id, 0777);
				}

				$path_to_file = DOWNLOADS_DIRECTORY.($realpath != '' ? $realpath : '').$file_id;

				# Загружаем основной файл
				if (!empty($_FILES['file_upload']['tmp_name'])) {
					if (copy($_FILES['file_upload']['tmp_name'], ROOT.$path_to_file.'/'.$_FILES['file_upload']['name'])) {
						# Удаляем файл, если он был перезагружен
						if ($action == 'edit') {
							if ($file['real_name'] != $_FILES['file_upload']['name']) {
								@unlink(ROOT.$path_to_file.'/'.$file['real_name']);
							}
						}
						$file['real_name'] = $_FILES['file_upload']['name'];
					}
				}
				elseif (!empty($_POST['file_import']) && $_POST['file_import'] != 'http://') {
					$import_file_path = downloads::get_real_file_path($_POST['file_import']);
					$import_file_name = basename($import_file_path);
					if (copy($import_file_path, ROOT.$path_to_file.'/'.$import_file_name)) {
						# Удаляем файл, если он был перезагружен
						if ($action == 'edit') {
							if ($file['real_name'] != $import_file_name) {
								@unlink(ROOT.$path_to_file.'/'.$file['real_name']);
							}
						}
						$file['real_name'] = $import_file_name;
					}
				}

				# Работа со скринами
				for ($i = 1; $i <= 3; $i++) {
					if (!empty($_FILES['screen'.$i]['tmp_name'])) {
						$screen_path = ROOT.$path_to_file.'/'.$_FILES['screen'.$i]['name'];
						if (copy($_FILES['screen'.$i]['tmp_name'], $screen_path)) {
							# Изменяем скриншот
							if ($this->config['downloads']['screens_width'] > 0) {
								main::image_resize($screen_path, $screen_path, intval($this->config['downloads']['screens_width']), 0, 90);
							}

							# Удаляем файл, если он был перезагружен
							if ($action == 'edit') {
								if ($file['screen'.$i] != '' && $file['screen'.$i] != $_FILES['screen'.$i]['name']) {
									@unlink(ROOT.$path_to_file.'/'.$file['screen'.$i]);
								}
							}
							$file['screen'.$i] = $_FILES['screen'.$i]['name'];
						}
					}
					elseif (!empty($_POST['screen'.$i]) && $_POST['screen'.$i] != 'http://') {
						$import_file_path = downloads::get_real_file_path($_POST['screen'.$i]);
						$import_file_name = basename($import_file_path);
						if (copy($import_file_path, ROOT.$path_to_file.'/'.$import_file_name)) {
							# Удаляем файл, если он был перезагружен
							if ($action == 'edit') {
								if ($file['screen'.$i] != '' && $file['screen'.$i] != $import_file_name) {
									@unlink(ROOT.$path_to_file.'/'.$file['screen'.$i]);
								}
							}
							$file['screen'.$i] = $import_file_name;
						}
					}
				}

				# Дополнительные файлы
				for ($i = 1; $i <= 5; $i++) {
					if (!empty($_FILES['add_file_file_'.$i]['tmp_name'])) {
						if (copy($_FILES['add_file_file_'.$i]['tmp_name'], ROOT.$path_to_file.'/'.$_FILES['add_file_file_'.$i]['name'])) {
							# Удаляем файл, если он был перезагружен
							if ($action == 'edit') {
								if ($file['add_file_file_'.$i] != '' && $file['add_file_file_'.$i] != $_FILES['add_file_file_'.$i]['name']) {
									@unlink(ROOT.$path_to_file.'/'.$file['add_file_file_'.$i]);
								}
							}
							$file['add_file_real_name_'.$i] = $_FILES['add_file_file_'.$i]['name'];
						}
					}
					elseif (!empty($_POST['add_file_file_'.$i]) && $_POST['add_file_file_'.$i] != 'http://') {
						$import_file_path = downloads::get_real_file_path($_POST['add_file_file_'.$i]);
						$import_file_name = basename($import_file_path);
						if (copy($import_file_path, ROOT.$path_to_file.'/'.$import_file_name)) {
							# Удаляем файл, если он был перезагружен
							if ($action == 'edit') {
								if ($file['add_file_real_name_'.$i] != '' && $file['add_file_real_name_'.$i] != $import_file_name) {
									@unlink(ROOT.$path_to_file.'/'.$file['add_file_real_name_'.$i]);
								}
							}
							$file['add_file_real_name_'.$i] = $import_file_name;
						}
					}
				}

				if (!$this->error) {
					# Получаем размер файла
					$file['filesize'] = filesize(ROOT.$path_to_file.'/'.$file['real_name']) OR a_error('Ошибка в определении размера файла, возможно, он не был загружен!');
					# Получаем расширение файла
					$file['file_ext'] = array_pop(explode('.', $file['real_name']));

					$file['path_to_file'] = $path_to_file;
					$file['directory_id'] = $directory_id;
					$file['about'] = $_POST['about'];
					$file['status'] = $_POST['status'];
					$file['name'] = $_POST['name'];

					# Выполняем действия над определенными типами файлов
					$file = downloads::filetype_actions($file);

					# Изменяем файл в базе
					downloads::update_file($this->db, $file_id, $file, $new_file);

					if ($action == 'add') $message = 'Файл успешно добавлен!';
					if ($action == 'edit') $message = 'Файл успешно изменен!';

					a_notice($message, a_url('downloads/admin/list_files', 'directory_id='.$directory_id));
				}
			}
		}
		if (!isset($_POST['submit']) || $this->error) {
  			$this->tpl->assign(array(
				'error' => $this->error,
				'error_file' => $error_file,
				'file' => $file,
				'action' => $action
  			));
  			$this->tpl->display('file_upload');
		}
	}

	/**
	 * Удаление файла
	 */
	public function action_file_delete() {
		main::is_demo();
		# Получаем информацию о файле
		if(!$file = $this->db->get_row("SELECT * FROM #__downloads_files WHERE file_id = '". intval($_GET['file_id']) ."'")) {
			a_error('Удаляемый файл не найден!');
		}

		# Удаление папки из ФС
		main::delete_dir(ROOT . $file['path_to_file']);

		# Удаляем файл из БД
		$this->db->query("DELETE FROM #__downloads_files WHERE file_id = '". $file['file_id'] ."'");

		a_notice('Файл успешно удален!', a_url('downloads/admin/list_files', 'directory_id='. $file['directory_id']));
	}

	/**
	 * Переименование файла
	 */
	public function action_file_rename() {
		if(!$file = $this->db->get_row("SELECT * FROM #__downloads_files WHERE file_id = '". intval($_GET['file_id']) ."'")) {
					a_error("Файл не найден!");
		}

		$fields = array('real_name', 'add_file_real_name_1', 'add_file_real_name_2', 'add_file_real_name_3', 'add_file_real_name_4', 'add_file_real_name_5');
		if(!in_array($_GET['field_name'], $fields)) {
					a_error("Неверное имя поля!");
		}

		if(empty($file[$_GET['field_name']])) {
					a_error("Данный файл не был загружен!");
		}

		if(isset($_POST['submit'])) {
			if(empty($_POST['new_name'])) {
							$this->error .= 'Имя файла необходимо указывать!<br />';
			}

			if(!$this->error) {
				$this->db->query("UPDATE #__downloads_files SET ". a_safe($_GET['field_name']) ." = '". a_safe($_POST['new_name']) ."' WHERE file_id = '". $file['file_id'] ."'");
				rename(ROOT . $file['path_to_file'] .'/'. $file[$_GET['field_name']], ROOT . $file['path_to_file'] .'/'. $_POST['new_name']);
				header("Location: ". a_url('downloads/admin/file_upload', 'file_id='. $file['file_id']));
				exit;
			}
		}
		if(!isset($_POST['submit']) OR $this->error) {
			$this->tpl->assign(array(
				'error' => $this->error,
				'file' => $file
			));

			$this->tpl->display('file_rename');
		}
	}

	/**
	 * Загрузка файлов с фтп
	 */
	public function action_ftp_upload() {
		set_time_limit(0);
		ignore_user_abort(TRUE);

		if (!$directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = '".intval($_GET['directory_id'])."'"))
			a_error('Папка не найдена!');

		if (isset($_POST['submit'])) {
			if (!file_exists(ROOT.DOWNLOADS_DIRECTORY.$this->ftp_directory.$_POST['from_directory'])) {
				$this->error .= 'ФТП папка не найдена!<br />';
			}

		  	if (!$this->error) {
		  		$translite = isset($_POST['translite']) ? TRUE : FALSE;
				$this->_ftp_upload_copy_files($directory['directory_id'], $_POST['from_directory'], $translite);

				a_notice("Копирование файлов завершено!", a_url('downloads/admin/list_files', 'directory_id='.$directory['directory_id']));
		  	}
		}
		if (!isset($_POST['submit']) OR $this->error) {
			$this->tpl->assign(array(
				'error' => $this->error,
				'directory' => $directory
			));
			$this->tpl->display('ftp_upload');
		}
	}

	/**
	 * Увеличение позиции папки
	 */
	public function action_directory_up() {
		if(!$directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = ". intval($_GET['directory_id'])))
			a_error('Папка не найдена!');

		# Меняем позиции
		$this->db->query("UPDATE #__downloads_directories SET position = ". $directory['position'] ." WHERE parent_id = '". $directory['parent_id'] ."' AND position = ". ($directory['position'] - 1));
		$this->db->query("UPDATE #__downloads_directories SET position = ". ($directory['position'] - 1) ." WHERE directory_id = ". intval($_GET['directory_id']));
	
		header("Location: ". a_url('downloads/admin', 'directory_id='. $directory['parent_id'], TRUE));
		exit;
	}

	/**
	 * Уменьшение позиции папки
	 */
	public function action_directory_down() {
		if(!$directory = $this->db->get_row("SELECT * FROM #__downloads_directories WHERE directory_id = ". intval($_GET['directory_id'])))
			a_error('Папка не найдена!');

		# Меняем позиции
		$this->db->query("UPDATE #__downloads_directories SET position = ". $directory['position'] ." WHERE parent_id = '". $directory['parent_id'] ."' AND position = ". ($directory['position'] + 1));
		$this->db->query("UPDATE #__downloads_directories SET position = ". ($directory['position'] + 1) ." WHERE directory_id = ". intval($_GET['directory_id']));
	
		header("Location: ". a_url('downloads/admin', 'directory_id='. $directory['parent_id'], TRUE));
		exit;
	}

	/**
	 * Получение списка файлов для загрузки файлов по фтп
	 */
	public function action_ftp_upload_get_directories() {
		$directories = array();
		$directory = (!empty($_GET['directory']) ? $_GET['directory'].'/' : '');
		$directory_for_scan = ROOT.DOWNLOADS_DIRECTORY.$this->ftp_directory.'/';
		$dir = opendir($directory_for_scan.$directory);
		while ($directory1 = readdir($dir)) {
			if ($directory1 == '.' || $directory1 == '..') continue;
			if (is_dir($directory_for_scan.$directory.$directory1)) {
				$directories[$directory.$directory1] = $directory1;
			}
		}

		header('Content-Type: text/javascript; charset=utf-8');
 		echo json_encode($directories);
	}

	/**
	 * Копирование фтп файлов в ЗЦ
	 *
	 * @access private
	 */
	private function _ftp_upload_copy_files($directory_id, $ftp_directory, $translite = TRUE) {
		# Получаем путь к папке
		$directory_path = downloads::get_path($directory_id, $this->db);
		$directory_realpath = downloads::get_realpath($directory_path) .'/'. $directory_id;

		# Сканируем фтп папку
		$ftp_directory_full = ROOT . DOWNLOADS_DIRECTORY . $this->ftp_directory . $ftp_directory;
		$dir = opendir($ftp_directory_full);
		while($f = readdir($dir)) {
			if($f == '.' || $f == '..') {
				continue;
			}

			# Если это папка
			if(is_dir($ftp_directory_full .'/'. $f)) {
			  	# Создаем новую папку в ЗЦ
			  	$this->db->query("INSERT INTO #__downloads_directories SET
           			name = '". a_safe(main::translite($f)) ."',
           			parent_id = '". $directory_id ."'
          		");

		  		$new_directory_id = $this->db->insert_id();

		   		# Создаем папку в файловой системе
				mkdir(ROOT . DOWNLOADS_DIRECTORY . $directory_realpath .'/'. $new_directory_id);
 				chmod(ROOT . DOWNLOADS_DIRECTORY . $directory_realpath .'/'. $new_directory_id, 0777);

 				# Запускаем сканер для новой папки
 				$this->_ftp_upload_copy_files($new_directory_id, $ftp_directory .'/'. $f, $translite);
			}

			# Если это файл
			if(is_file($ftp_directory_full .'/'. $f)) {
				# Создаем пустую строку в базе для файла (чтобы получить его ID)
				$this->db->query("INSERT INTO #__downloads_files SET file_id = 'NULL'");
				$new_file_id = $this->db->insert_id();

				# Создаем папку для файла
				mkdir(ROOT . DOWNLOADS_DIRECTORY . $directory_realpath .'/'. $new_file_id);
				chmod(ROOT . DOWNLOADS_DIRECTORY . $directory_realpath .'/'. $new_file_id, 0777);

				# Копируем файл в новую папку
				copy($ftp_directory_full .'/'. $f, ROOT . DOWNLOADS_DIRECTORY . $directory_realpath .'/'. $new_file_id .'/'. $f);

				# Получаем размер файла
				$file['filesize'] = filesize(ROOT . DOWNLOADS_DIRECTORY . $directory_realpath .'/'. $new_file_id .'/'. $f) OR a_error('Ошибка в определении размера файла, возможно, он не был загружен!');
				# Получаем расширение файла
				$file['file_ext'] = array_pop(explode('.', $f));
				# Определяем имя файла
				$file['name'] = str_replace('.'. $file['file_ext'], '', $f);
				if($translite) {
					$file['name'] = main::translite($file['name']);
				}
				$file['directory_id'] = $directory_id;
				$file['real_name'] = $f;
				$file['path_to_file'] = DOWNLOADS_DIRECTORY . $directory_realpath .'/'. $new_file_id;
				$file['status'] = 'active';

				# Выполняем действия над определенными типами файлов
				$file = downloads::filetype_actions($file);

				# Изменяем файл в базе
				downloads::update_file($this->db, $new_file_id, $file);
				$file = '';
			}
		}
	}
}
?>