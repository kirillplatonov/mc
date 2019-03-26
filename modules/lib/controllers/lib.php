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
 * Контроллер пользовательской части библиотеки
 */
class Lib_Controller extends Controller {
	/**
	 * Уровень пользовательского доступа
	 */
	public $access_level = 0;

	/**
	 * Construct
	 */
	public function __construct() {
		parent::__construct();

		# Хелпер библиотеки
		a_import('modules/lib/helpers/lib');
	}

	/**
	 * Метод по умолчанию
	 */
	public function action_index() {
		$this->action_list_books();
	}

	/**
	 * Чтение книги
	 */
	public function action_read_book() {
		$this->per_page = 300;

		# Обновляем количество просмотров
		if ($this->start == 0)
			$this->db->query("UPDATE #__lib_books SET `reads` = `reads` + 1 WHERE book_id = '".intval($_GET['book_id'])."'");

		if (!$book = $this->db->get_row("SELECT b.*, d.name AS directory_name,
			(SELECT COUNT(*) FROM #__comments_posts WHERE module = 'lib' AND item_id = b.book_id) AS comments
			FROM #__lib_books AS b LEFT JOIN #__lib_directories AS d USING(directory_id) WHERE b.book_id = '". intval($_GET['book_id'])."'"))
			a_error("Книга не найдена!");

		$directory_path = lib::get_path($book['directory_id'], $this->db);
		$namepath = lib::get_namepath($directory_path, '/');

		# Получаем навигацию
		$navigation = '<a href="'.a_url('lib').'">Библиотека</a> ';
		if (!empty($namepath))
			$navigation .= '» '.$namepath;
		if ($book['directory_id'] > 0)
			$navigation .= '» <a href="'.a_url('lib/list_books', 'directory_id='.$book['directory_id']).'">'.$book['directory_name'].'</a>';

		# Получаем контент и делаем разбивку по страницам
		$text = file_get_contents(ROOT.'files/lib'.$book['path_to_file'].$book['book_id'].'.txt');
		$ex = explode(' ', $text);
		$total = count($ex);
		$text_page = '';
		for ($i = $this->start; $i < $this->start + $this->per_page && $i <= $total; $i++) {
			$text_page .= $ex[$i].' ';
		}
		$text_page = htmlspecialchars(stripslashes($text_page));
		$text_page = nl2br($text_page);
		$text_page = main::bbcode($text_page);

		# Пагинация
		$pg_conf['base_url'] = a_url('lib/read_book', 'book_id='.$book['book_id'].'&amp;start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'book' => $book,
			'text_page' => $text_page,
			'navigation' => $navigation,
			'pagination' => $pg->create_links()
  		));

  		$this->tpl->display('read_book');
	}

	/**
	 * Скачать книгу
	 */
	public function action_download_book() {
		$this->per_page = 7;

		if(!$book = $this->db->get_row("SELECT b.* FROM #__lib_books AS b WHERE b.book_id = '". intval($_GET['book_id']) ."'")) {
					a_error("Книга не найдена!");
		}

		$file_path = ROOT .'files/lib'. $book['path_to_file'];
		$jar_path = ROOT .'modules/lib/jar_data';

		# Подключаем библиотеки для работы с архивами
		a_import('libraries/pclzip.lib');

		switch($_GET['type']) {
			case 'jar':
				$filename = main::detranslite($book['name']) .'.jar';
				$mime = 'application/java-archive';
				$tmp_dir = ROOT .'tmp/'. main::get_unique_code(32);
				
				# Копируем основные файлы книги
				lib::r_copy($jar_path, $tmp_dir);
				
				#Правим манифест
				$manifest = file_get_contents($jar_path .'/META-INF/MANIFEST.MF');
				$manifest = str_replace('{NAME}', main::detranslite($book['name']), $manifest);
				file_put_contents($tmp_dir .'/META-INF/MANIFEST.MF', $manifest);
				
				# Добавляем книгу
				$book = file_get_contents($file_path . $book['book_id'] .'.txt');
				file_put_contents($tmp_dir .'/textfile.txt', main::wtext($book));
				
				# Создаем архив
				$archive = new PclZip($tmp_dir .'/tmp.jar');
				
				# Добавляем основные файлы книги
				$list = $archive->create($tmp_dir, PCLZIP_OPT_REMOVE_PATH, $tmp_dir);
				$content = file_get_contents($tmp_dir .'/tmp.jar');
				main::delete_dir($tmp_dir);
				break;
			case 'zip':
				$filename = main::detranslite($book['name']) .'.zip';
				$mime = 'application/zip';
				$tmp_name = ROOT .'tmp/'. main::get_unique_code(32);
				$archive = new PclZip($tmp_name);
				$list = $archive->create($file_path . $book['book_id'] .'.txt',
						PCLZIP_OPT_REMOVE_PATH, $file_path);
				$content = file_get_contents($tmp_name);
				unlink($tmp_name);
				break;
			case 'txt':
			default:
				$filename = main::detranslite($book['name']) .'.txt';
				$mime = 'text/plain';
				$content = file_get_contents($file_path . $book['book_id'] .'.txt');
				break;
		}

		header('Content-Type: '. $mime);
		header('Content-Disposition: attachment; filename='. $filename);
		header("Content-Transfer-Encoding: binary");
		header('Expires: 0');
		header('Pragma: no-cache');
		header("Content-Length: ". strlen($content));

		echo $content;
	}

	/**
	 * Список книг и папок
	 */
	public function action_list_books() {
		switch ($_GET['type']) {
			# Самые популярные
			case 'top':
				$sql = "SELECT *, (SELECT 'book') AS type FROM #__lib_books ORDER BY `reads` DESC LIMIT $this->start, $this->per_page";
				$navigation = '<a href="'.a_url('lib').'">Библиотека</a>';
				$type = 'top';
				break;

			# Поиск
			case 'search':
				$sql = "SELECT *, (SELECT 'book') AS type FROM #__lib_books WHERE name LIKE '%".a_safe($_GET['search_word'])."%' ORDER BY `reads` DESC LIMIT $this->start, $this->per_page";
				$navigation = '<a href="'.a_url('lib').'">Библиотека</a>';
				$type = 'search';
				break;

			# Обычный листинг
			default:
				if (empty($_GET['directory_id']) OR !is_numeric($_GET['directory_id'])) $directory_id = 0;
				else $directory_id = intval($_GET['directory_id']);

				if ($directory_id != 0 && !$directory = $this->db->get_row("SELECT * FROM #__lib_directories WHERE directory_id = '$directory_id'")) {
					a_error('Папка не найдена!');
				}
				else {
					# Определяем папка с файлами или папками
					if ($this->db->get_one("SELECT directory_id FROM #__lib_directories WHERE parent_id = $directory_id")) {
						$files_directory = FALSE;
						$this->per_page = 100;
					}
					else {
						$files_directory = TRUE;
					}
				}

				$directory_path = lib::get_path($directory_id, $this->db);
				$namepath = lib::get_namepath($directory_path, '/', TRUE);

				# Получаем навигацию
				if ($directory_id > 0) {
					$navigation = '<a href="'.a_url('lib').'">Библиотека</a> ';
					if (!empty($namepath))
						$navigation .= '» '.$namepath;
					if ($directory['directory_id'] > 0)
						$navigation .= '» <a href="'.a_url('lib/list_books', 'directory_id='.$directory['directory_id']).'">'.$directory['name'].'</a>';
				}

				# Получаем список папок и файлов
				$sql = "SELECT SQL_CALC_FOUND_ROWS
		        			directory_id AS book_id,
		        			name,
		        			(SELECT 'directory') AS type,
		        			(SELECT 0) AS description,
						position,
						(SELECT 0) AS `reads`,
						(SELECT 0) AS time,
						(SELECT COUNT(*) FROM #__lib_books AS lb WHERE lb.path_to_file LIKE CONCAT('%/', ld.directory_id, '/%')) AS count_books,
						(SELECT COUNT(*) FROM #__lib_books AS lb WHERE lb.path_to_file LIKE CONCAT('%/', ld.directory_id, '/%') AND time > UNIX_TIMESTAMP() - 86400) AS new_books
		        			FROM #__lib_directories AS ld WHERE parent_id = '$directory_id' ".PHP_EOL;
				$sql .= "UNION ALL ".PHP_EOL;
				$sql .= "SELECT
		        			book_id,
		        			name,
		        			(SELECT 'book') AS type,
		        			description,
						(SELECT 0) AS position,
						`reads`,
						time,
						(SELECT 0) AS count_books,
						(SELECT 0) AS new_books
		        			FROM #__lib_books WHERE directory_id = '$directory_id' ".PHP_EOL;

				$sql .= "ORDER BY type DESC, position ASC, book_id DESC LIMIT $this->start, $this->per_page";

				$type = 'default_listing';
				break;
		}

		$result = $this->db->query($sql);
		$total = $this->db->get_one("SELECT FOUND_ROWS()");
		
		$books = array();
		while ($book = $this->db->fetch_array($result)) {
			$books[] = $book;
		}

		# Пагинация
		$pg_conf['base_url'] = a_url('lib/list_books', 'type='.$_GET['type'].'&amp;search_word='.$_GET['search_word'].'&amp;directory_id='.intval($_GET['directory_id']).'&amp;start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		 $this->tpl->assign(array(
			'books' => $books,
			'total' => $total,
			'type' => $type,
			'navigation' => $navigation,
			'pagination' => $pg->create_links(),
			'directory' => $directory
		));

		$this->tpl->display('list_books');
	}
}
?>