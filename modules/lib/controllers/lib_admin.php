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
 * Контроллер управления библиотекой
 */
class Lib_Admin_Controller extends Controller
{

    /**
     * Уровень пользовательского доступа
     */
    public $access_level = 8;

    /**
     * Тема
     */
    public $template_theme = 'admin';

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        # Хелпер библиотеки
        a_import('modules/lib/helpers/lib');
    }

    /**
     * Метод по умолчанию
     */
    public function action_index()
    {
        $this->action_list_books();
    }

    /**
     * Загрузка книг
     */
    public function action_add_books()
    {
        # Получем данные о папке для загрузки
        if (empty($_GET['directory_id']) OR ! is_numeric($_GET['directory_id']))
            $directory_id = 0;
        else
            $directory_id = intval($_GET['directory_id']);

        if ($directory_id != 0 && !$directory = $this->db->get_row("SELECT * FROM #__lib_directories WHERE directory_id = '" . $directory_id . "'")) {
            a_error('Папка для загрузки не найдена!');
        }

        # Определяем как загружать
        switch ($_GET['type']) {
            case 'textes':
                $type = 'textes';
                break;
            case 'import':
                $type = 'import';
                break;
            case 'upload':
            default:
                $type = 'upload';
                break;
        }

        if (isset($_POST['submit'])) {
            main::is_demo();
            if (!$this->error) {
                # Определяем путь для загрузки
                $directory_path = lib::get_path($directory_id, $this->db);
                $realpath = lib::get_realpath($directory_path);
                $path_to_file = ($realpath != '' ? $realpath . '/' : '') . ($directory_id == 0 ? '' : $directory_id . '/');

                $c = 0;
                for ($i = 1; $i <= 10; $i++) {
                    if (!empty($_POST['name_' . $i])) {
                        # Добавляем файл в базу
                        $this->db->query("INSERT INTO #__lib_books SET
	    					directory_id = '$directory_id',
	    					name = '" . a_safe($_POST['name_' . $i]) . "',
	    					path_to_file = '/" . $path_to_file . "',
	    					time = UNIX_TIMESTAMP()
						");

                        $book_id = $this->db->insert_id();

                        switch ($type) {
                            # Загрузка из текста
                            case 'textes':
                                if (!empty($_POST['text_' . $i])) {
                                    file_put_contents(ROOT . 'files/lib/' . $path_to_file . $book_id . '.txt', $_POST['text_' . $i]);
                                    $c++;
                                }
                                break;
                            # Импорт
                            case 'import':
                                if (!empty($_POST['link_' . $i])) {
                                    copy($_POST['link_' . $i], ROOT . 'files/lib/' . $path_to_file . $book_id . '.txt');
                                    $c++;
                                }
                                break;
                            # Upload
                            case 'upload':
                            default:
                                if (!empty($_FILES['file_' . $i]['tmp_name'])) {
                                    copy($_FILES['file_' . $i]['tmp_name'], ROOT . 'files/lib/' . $path_to_file . $book_id . '.txt');
                                    $c++;
                                }
                                break;
                        }
                    }
                }

                a_notice('Загружено книг: ' . $c, a_url('lib/admin/list_books', 'directory_id=' . $directory_id));
            }
        }
        if (!isset($_POST['submit']) OR $this->error) {
            $this->tpl->assign(array(
                'error' => $this->error,
                'type' => $type
            ));

            $this->tpl->display('add_books');
        }
    }

    /**
     * Список книг и папок
     */
    public function action_list_books()
    {
        $this->per_page = 20;

        if (empty($_GET['directory_id']) OR ! is_numeric($_GET['directory_id']))
            $directory_id = 0;
        else
            $directory_id = intval($_GET['directory_id']);

        if ($directory_id != 0 && !$directory = $this->db->get_row("SELECT * FROM #__lib_directories WHERE directory_id = '$directory_id'")) {
            a_error('Папка не найдена!');
        } else {
            # Определяем папка с файлами или папками
            if ($this->db->get_one("SELECT directory_id FROM #__lib_directories WHERE parent_id = $directory_id")) {
                $files_directory = FALSE;
                $this->per_page = 100;
            } else {
                $files_directory = TRUE;
            }
        }

        $directory_path = lib::get_path($directory_id, $this->db);
        $namepath = lib::get_namepath($directory_path, '/', TRUE);

        # Получаем список папок и файлов
        $sql = "SELECT SQL_CALC_FOUND_ROWS
        			directory_id AS book_id,
        			name,
        			(SELECT 'directory') AS type,
        			(SELECT 0) AS description,
                    position
        			FROM #__lib_directories WHERE parent_id = '$directory_id' " . PHP_EOL;
        $sql .= "UNION ALL " . PHP_EOL;
        $sql .= "SELECT
        			book_id,
        			name,
        			(SELECT 'book') AS type,
        			description,
                    (SELECT 0) AS position
        			FROM #__lib_books WHERE directory_id = '$directory_id' " . PHP_EOL;

        $sql .= "ORDER BY type DESC, position ASC, book_id DESC LIMIT $this->start, $this->per_page";

        $result = $this->db->query($sql);
        $total = $this->db->get_one("SELECT FOUND_ROWS()");

        $min_p = $this->db->get_one("SELECT MIN(position) FROM #__lib_directories WHERE parent_id = '$directory_id'");
        $max_p = $this->db->get_one("SELECT MAX(position) FROM #__lib_directories WHERE parent_id = '$directory_id'");

        $books = array();
        while ($book = $this->db->fetch_array($result)) {
            if ($book['type'] == 'directory') {
                if ($book['position'] != $min_p)
                    $book['up'] = '<a href="' . a_url('lib/admin/directory_up', 'directory_id=' . $book['book_id']) . '">up</a>';
                else
                    $book['up'] = 'up';

                if ($book['position'] != $max_p)
                    $book['down'] = '<a href="' . a_url('lib/admin/directory_down', 'directory_id=' . $book['book_id']) . '">down</a>';
                else
                    $book['down'] = 'down';
            } else {
                $book['up'] = '-';
                $book['down'] = '-';
            }

            $books[] = $book;
        }

        # Пагинация
        $pg_conf['base_url'] = a_url('lib/admin/list_books', 'directory_id=' . intval($_GET['directory_id']) . '&amp;start=');
        $pg_conf['total_rows'] = $total;
        $pg_conf['per_page'] = $this->per_page;

        a_import('libraries/pagination');
        $pg = new CI_Pagination($pg_conf);

        $this->tpl->assign(array(
            'books' => $books,
            'total' => $total,
            'namepath' => $namepath,
            'pagination' => $pg->create_links(),
            'directory' => $directory
        ));

        $this->tpl->display('list_books');
    }

    /**
     * Создание / редактирование папки
     */
    public function action_directory_edit()
    {
        if (is_numeric($_GET['directory_id'])) {
            $directory_id = intval($_GET['directory_id']);
            if (!$directory = $this->db->get_row("SELECT * FROM #__lib_directories WHERE directory_id = '$directory_id'")) {
                a_error('Папка не найдена!');
            }
            $parent_directory = $this->db->get_row("SELECT * FROM #__lib_directories WHERE directory_id = '" . $directory['parent_id'] . "'");
            $action = 'edit';
        } else {
            if ($_GET['parent_id'] != '' && !$parent_directory = $this->db->get_row("SELECT * FROM #__lib_directories WHERE directory_id = '" . intval($_GET['parent_id']) . "'"))
                a_error('Папка предок не найдена!');
            $directory = array();
            $action = 'add';
        }

        if (isset($_POST['submit'])) {
            main::is_demo();
            if (empty($_POST['name'])) {
                $this->error .= 'Укажите название папки!<br />';
            }

            if (!$this->error) {
                # Создаем нувую папку
                if ($action == 'add') {
                    # Получаем позицию папки
                    $position = $this->db->get_one("SELECT MAX(position) FROM #__lib_directories WHERE parent_id = '" . $parent_directory['directory_id'] . "'") + 1;

                    $this->db->query("INSERT INTO #__lib_directories SET
	           			name = '" . a_safe($_POST['name']) . "',
	           			parent_id = '" . @$parent_directory['directory_id'] . "',
	           			position = '$position'
           			");

                    $directory_id = $this->db->insert_id();

                    # Создаем папку в файловой системе
                    # Получаем директорию для папки
                    $directory_path = lib::get_path($directory_id, $this->db);
                    $realpath = lib::get_realpath($directory_path);

                    mkdir(ROOT . 'files/lib/' . $realpath . '/' . $directory_id);
                    chmod(ROOT . 'files/lib/' . $realpath . '/' . $directory_id, 0777);

                    a_notice('Папка успешно создана!', a_url('lib/admin/list_books', 'directory_id=' . $parent_directory['directory_id']));
                } elseif ($action == 'edit') {
                    # Изменяем имя папки
                    $this->db->query("UPDATE #__lib_directories SET
	           			name = '" . a_safe($_POST['name']) . "'
	           			WHERE
	           			directory_id = '" . $directory_id . "'
	           		");

                    a_notice('Папка успешно изменена!', a_url('lib/admin/list_books', 'directory_id=' . $parent_directory['directory_id']));
                }
            }
        }
        if (!isset($_POST['submit']) || $this->error) {
            $this->tpl->assign(array(
                'error' => $this->error,
                'directory' => $directory,
                'action' => $action
            ));
            $this->tpl->display('directory_edit');
        }
    }

    /**
     * Увеличение позиции папки
     */
    public function action_directory_up()
    {
        main::is_demo();
        if (!$directory = $this->db->get_row("SELECT * FROM #__lib_directories WHERE directory_id = " . intval($_GET['directory_id'])))
            a_error('Папка не найдена!');

        # Меняем позиции
        $this->db->query("UPDATE #__lib_directories SET position = " . $directory['position'] . " WHERE parent_id = '" . $directory['parent_id'] . "' AND position = " . ($directory['position'] - 1));
        $this->db->query("UPDATE #__lib_directories SET position = " . ($directory['position'] - 1) . " WHERE directory_id = " . intval($_GET['directory_id']));

        header("Location: " . a_url('lib/admin', 'directory_id=' . $directory['parent_id'], TRUE));
        exit;
    }

    /**
     * Уменьшение позиции папки
     */
    public function action_directory_down()
    {
        main::is_demo();
        if (!$directory = $this->db->get_row("SELECT * FROM #__lib_directories WHERE directory_id = " . intval($_GET['directory_id'])))
            a_error('Папка не найдена!');

        # Меняем позиции
        $this->db->query("UPDATE #__lib_directories SET position = " . $directory['position'] . " WHERE parent_id = '" . $directory['parent_id'] . "' AND position = " . ($directory['position'] + 1));
        $this->db->query("UPDATE #__lib_directories SET position = " . ($directory['position'] + 1) . " WHERE directory_id = " . intval($_GET['directory_id']));

        header("Location: " . a_url('lib/admin', 'directory_id=' . $directory['parent_id'], TRUE));
        exit;
    }

    /**
     * Удаление книги
     */
    public function action_book_delete()
    {
        main::is_demo();
        if (!$book = $this->db->get_row("SELECT * FROM #__lib_books WHERE book_id = '" . intval($_GET['book_id']) . "'"))
            a_error("Книга не найдена!");

        # Удаляем книгу из ФС
        unlink(ROOT . 'files/lib' . $book['path_to_file'] . $book['book_id'] . '.txt');

        # Удаляем книгу из БД
        $this->db->query("DELETE FROM #__lib_books WHERE book_id = '" . $book['book_id'] . "'");

        a_notice('Книга удалена!', a_url('lib/admin/list_books', 'directory_id=' . $book['directory_id']));
    }

    /**
     * Удаление папки
     */
    public function action_directory_delete()
    {
        main::is_demo();
        $directory_id = intval($_GET['directory_id']);

        if (!$directory = $this->db->get_row("SELECT * FROM #__lib_directories WHERE directory_id = '$directory_id'")) {
            a_error('Папка не найдена!');
        }

        if ($this->db->get_one("SELECT directory_id FROM #__lib_directories WHERE parent_id = '$directory_id'") OR
                $this->db->get_one("SELECT book_id FROM #__lib_books WHERE directory_id = '$directory_id'")) {
            a_error('Папку не возможно удалить, так как она не пуста!');
        }

        # Удаление из ФС
        $directory_path = lib::get_path($directory_id, $this->db);
        $realpath = lib::get_realpath($directory_path);
        rmdir(ROOT . 'files/lib/' . $realpath . '/' . $directory_id);

        # Удаление папки из базы
        $this->db->query("DELETE FROM #__lib_directories WHERE directory_id = '$directory_id'");

        # Меняем позиции
        $this->db->query("UPDATE #__lib_directories SET position = position - 1 WHERE parent_id = '" . $directory['parent_id'] . "' AND position > '" . $directory['position'] . "'");

        a_notice('Папка успешно удалена!', a_url('lib/admin/list_books', 'directory_id=' . $directory['parent_id']));
    }

    /**
     * Удаление всех файлов в папке
     */
    public function action_directory_clear()
    {
        main::is_demo();
        $directory_id = empty($_GET['directory_id']) ? 0 : intval($_GET['directory_id']);

        # Получаем информацию о папке
        if ($directory_id !== 0 && !$this->db->get_one("SELECT directory_id FROM #__lib_directories WHERE directory_id = '" . intval($directory_id) . "'")) {
            a_error('Папка не найдена!');
        }

        # Удаляем файлы из ФС
        $result = $this->db->query("SELECT * FROM #__lib_books WHERE directory_id = '$directory_id'");
        while ($book = $this->db->fetch_array($result)) {
            # Удаляем книгу из ФС
            unlink(ROOT . 'files/lib' . $book['path_to_file'] . $book['book_id'] . '.txt');
            # Удаляем книгу из БД
            $this->db->query("DELETE FROM #__lib_books WHERE book_id = '" . $book['book_id'] . "'");
        }

        a_notice('Папка успешно очищена', a_url('lib/admin/list_books', 'directory_id=' . $directory_id));
    }

}

?>