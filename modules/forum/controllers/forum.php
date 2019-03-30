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
defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

/**
 * Контроллер форума, пользовательская часть
 */
class Forum_Controller extends Controller {

    /**
     * Метод по умолчанию
     */
    public function action_index() {
        $this->action_list_sections();
    }

    /**
     * Список разделов
     */
    public function action_list_sections() {
        $sections = array();
        $result = $this->db->query("SELECT * FROM #__forum_sections ORDER BY position");
        while ($section = $this->db->fetch_array($result)) {
            if ($this->config['forum']['show_forums_in_list_sections'] || $section['section_id'] == @$_GET['section_id']) {
                $section['forums'] = array();
                $result1 = $this->db->query("SELECT * FROM #__forum_forums WHERE section_id = '" . $section['section_id'] . "' ORDER BY position");
                while ($forum = $this->db->fetch_array($result1))
                    $section['forums'][] = $forum;
            }
            $sections[] = $section;
        }

        $this->tpl->assign(array(
            'sections' => $sections
        ));

        $this->tpl->display('list_sections');
    }

    /**
     * Просмотр форума
     */
    public function action_viewforum() {
        $this->per_page = $this->config['forum']['topics_per_page'];

        if ($_GET['type'] != 'new') {
            if (!$forum = $this->db->get_row("SELECT * FROM #__forum_forums WHERE forum_id = '" . intval($_GET['forum_id']) . "'"))
                a_error("Форум не найден!");
        }

        # Получение данных
        switch ($_GET['type']) {
            case 'new':
                $sql = "SELECT SQL_CALC_FOUND_ROWS ft.*, u.username AS last_username
		  			FROM #__forum_topics AS ft
		  			INNER JOIN #__users AS u ON ft.last_user_id = u.user_id
		  			ORDER BY ft.time DESC
		  			LIMIT $this->start, $this->per_page";
                break;
            default:
                $sql = "SELECT SQL_CALC_FOUND_ROWS ft.*, u.username AS last_username
		  			FROM #__forum_topics AS ft
		  			INNER JOIN #__users AS u ON ft.last_user_id = u.user_id
		  			WHERE ft.forum_id = '" . $forum['forum_id'] . "'
		  			ORDER BY ft.is_top_topic DESC, ft.last_message_time DESC
		  			LIMIT $this->start, $this->per_page";
                break;
        }

        $topics = $this->db->get_array($sql);
        $total = $this->db->get_one("SELECT FOUND_ROWS()");

        # Пагинация
        $pg_conf['base_url'] = a_url('forum/viewforum', 'forum_id=' . $_GET['forum_id'] . '&amp;type=' . $_GET['type'] . '&amp;start=');
        $pg_conf['total_rows'] = $total;
        $pg_conf['per_page'] = $this->per_page;

        a_import('libraries/pagination');
        $pg = new CI_Pagination($pg_conf);

        $this->tpl->assign(array(
            'topics' => $topics,
            'forum' => $forum,
            'total' => $total,
            'pagination' => $pg->create_links(),
            'section' => $this->db->get_row("SELECT * FROM #__forum_sections WHERE section_id = '" . $forum['section_id'] . "'"),
            'messages_per_page' => $this->config['forum']['messages_per_page']
        ));

        $this->tpl->display('viewforum');
    }

    /**
     * Просмотр темы
     */
    public function action_viewtopic() {
        $this->per_page = $this->config['forum']['messages_per_page'];

        if (!$topic = $this->db->get_row("SELECT * FROM #__forum_topics WHERE topic_id = '" . intval($_GET['topic_id']) . "'"))
            a_error("Тема не найдена!");

        # Получение данных
        $result = $this->db->query("SELECT SQL_CALC_FOUND_ROWS fm.*, u.username AS username, u.status AS user_status, up.avatar AS avatar_exists, u.last_visit, ff.file_id, ff.file_size, ff.file_downloads, ff.file_name
  			FROM #__forum_messages AS fm
  			INNER JOIN #__users AS u USING(user_id)
  			LEFT JOIN #__users_profiles AS up USING(user_id)
			LEFT JOIN #__forum_files AS ff USING(message_id)
  			WHERE fm.topic_id = '" . $topic['topic_id'] . "'
  			ORDER BY fm.message_id ASC
  			LIMIT $this->start, $this->per_page
  		");

        $messages = array();
        $num = $this->start;
        if (!class_exists('smiles'))
            a_import('modules/smiles/helpers/smiles');
        while ($message = $this->db->fetch_array($result)) {
            $message['num'] = ++$num;
            $message['message'] = main::bbcode($message['message']);
            $message['message'] = smiles::smiles_replace($message['message']);
            $message['message'] = nl2br($message['message']);
            $messages[] = $message;
        }

        $total = $this->db->get_one("SELECT FOUND_ROWS()");

        # Пагинация
        $pg_conf['base_url'] = a_url('forum/viewtopic', 'topic_id=' . $_GET['topic_id'] . '&amp;start=');
        $pg_conf['total_rows'] = $total;
        $pg_conf['per_page'] = $this->per_page;

        a_import('libraries/pagination');
        $pg = new CI_Pagination($pg_conf);

        $this->tpl->assign(array(
            'messages' => $messages,
            'topic' => $topic,
            'total' => $total,
            'pagination' => $pg->create_links(),
            'forum' => $this->db->get_row("SELECT * FROM #__forum_forums WHERE forum_id = '" . $topic['forum_id'] . "'")
        ));

        $this->tpl->display('viewtopic');
    }

    /**
     * Закрепление / открепление темы
     */
    public function action_topic_top() {
        if (!$topic = $this->db->get_row("SELECT * FROM #__forum_topics WHERE topic_id = '" . intval($_GET['topic_id']) . "'"))
            a_error("Тема не найдена!");

        if (ACCESS_LEVEL < 8)
            a_error('У вас нет прав на выполнение этой операции!');

        $status = $_GET['a'] == 'top' ? 1 : 0;
        $this->db->query("UPDATE #__forum_topics SET is_top_topic = '$status' WHERE topic_id = '" . $topic['topic_id'] . "'");

        header("Location: " . a_url('forum/viewforum', 'forum_id=' . $topic['forum_id'] . '&start=' . @$_GET['start'], TRUE));
        exit;
    }

    /**
     * Закрытие / окрытие темы
     */
    public function action_topic_close() {
        if (!$topic = $this->db->get_row("SELECT * FROM #__forum_topics WHERE topic_id = '" . intval($_GET['topic_id']) . "'"))
            a_error("Тема не найдена!");

        if (ACCESS_LEVEL < 8)
            a_error('У вас нет прав на выполнение этой операции!');

        $status = $_GET['a'] == 'close' ? 1 : 0;
        $this->db->query("UPDATE #__forum_topics SET is_close_topic = '$status' WHERE topic_id = '" . $topic['topic_id'] . "'");

        header("Location: " . a_url('forum/viewforum', 'forum_id=' . $topic['forum_id'] . '&start=' . @$_GET['start'], TRUE));
        exit;
    }

    /**
     * Закрытие / окрытие темы
     */
    public function action_topic_delete() {
        if (!$topic = $this->db->get_row("SELECT * FROM #__forum_topics WHERE topic_id = '" . intval($_GET['topic_id']) . "'")) {
            a_error("Тема не найдена!");
        }

        if (ACCESS_LEVEL < 8) {
            a_error('У вас нет прав на выполнение этой операции!');
        }

        if (!empty($_GET['confirm'])) {
            # удаляем тему
            $this->db->query("DELETE FROM #__forum_topics WHERE topic_id = '" . $topic['topic_id'] . "'");
            # удаляем сообщения в теме
            $this->db->query("DELETE FROM #__forum_messages WHERE topic_id = '" . $topic['topic_id'] . "'");
            # обновляем счетчик тем и сообщений в форуме
            $this->db->query("UPDATE #__forum_forums SET
				topics = topics - 1,
				messages = messages - " . $topic['messages'] . " - 1
				WHERE forum_id = '" . $topic['forum_id'] . "'
			");

            header("Location: " . a_url('forum/viewforum', 'forum_id=' . $topic['forum_id'] . '&start=' . @$_GET['start'], TRUE));
            exit;
        } else {
            a_confirm('Действительно хотите удалить тему &laquo;' . $topic['name'] . '&raquo; со всеми сообщениями?', a_url('forum/topic_delete', 'confirm=yes&amp;topic_id=' . $topic['topic_id'] . '&amp;start=' . @$_GET['start']), a_url('forum/viewforum', 'forum_id=' . $topic['forum_id'] . '&amp;start=' . @$_GET['start']));
        }
    }

    /**
     * Удаление сообщения
     */
    public function action_message_delete() {
        if (!$message = $this->db->get_row("SELECT m.*,
			(SELECT status FROM #__users AS u WHERE u.user_id = m.user_id) AS user_status
			FROM #__forum_messages AS m
			WHERE message_id = '" . intval($_GET['message_id']) . "'")) {
            a_error("Сообщение не найдено!");
        }

        if (!a_check_rights($message['user_id'], $message['user_status']) || !$message['is_last_message']) {
            a_error('У вас нет права удалять данное сообщение!');
        }

        if (!empty($_GET['confirm'])) {
            # Удаляем сообщение
            $this->db->query("DELETE FROM #__forum_messages WHERE message_id = '" . $message['message_id'] . "'");
            # Обновляем счетчики сообщений
            $this->db->query("UPDATE #__forum_topics SET messages = messages - 1 WHERE topic_id = '" . $message['topic_id'] . "'");
            $this->db->query("UPDATE #__forum_forums SET messages = messages - 1 WHERE forum_id = '" . $message['forum_id'] . "'");

            # Отнимаем рейтинг
            user::rating_update(-1, $message['user_id']);

            header("Location: " . a_url('forum/viewtopic', 'topic_id=' . $message['topic_id'] . '&start=' . @$_GET['start'], TRUE));
            exit;
        } else {
            a_confirm('Действительно хотите удалить данное сообщение?', a_url('forum/message_delete', 'confirm=yes&amp;message_id=' . $message['message_id'] . '&amp;start=' . @$_GET['start']), a_url('forum/viewtopic', 'topic_id=' . $message['topic_id'] . '&amp;start=' . @$_GET['start']));
        }
    }

    /**
     * Постинг
     */
    public function action_posting() {
        if (!empty($_GET['new_topic'])) {
            if (!$forum = $this->db->get_row("SELECT * FROM #__forum_forums WHERE forum_id = '" . intval($_GET['forum_id']) . "'")) {
                a_error("Форум не найден!");
            }
            $action = 'new_topic';
            $message = array();
            $title = "Новая тема";

            if (USER_ID == -1 && !$this->config['forum']['guests_create_topics']) {
                a_error("Гости не имеют права создвать темы!<br />Зарегистрируйтесь или войдите под своим именем.");
            }
        } else {
            if (is_numeric($_GET['message_id'])) {
                if (!$message = $this->db->get_row("SELECT * FROM #__forum_messages WHERE message_id = '" . intval($_GET['message_id']) . "'")) {
                    a_error("Сообщение не найдено!");
                }

                if (ACCESS_LEVEL < 8 && $message['user_id'] != USER_ID) {
                    a_error("У вас нет прав редактировать данное сообщение!");
                }

                if ($message['is_first_message'] == 1) {
                    $action = 'edit_first_message';
                } else {
                    $action = 'edit_message';
                }

                $title = "Редактировать сообщение";
                $message_text = $message['message'];
                $topic_id = $message['topic_id'];
            } else {
                $action = 'new_message';
                $message = array();
                $title = "Новое сообщение";
                $topic_id = $_GET['topic_id'];

                $message_text = '';

                if (!empty($_GET['replay'])) {
                    $message_text .= '[b]' . $_GET['replay'] . '[/b], ';
                } elseif (is_numeric($_GET['q'])) {
                    if (!$q_post = $this->db->get_row("SELECT * FROM #__forum_messages LEFT JOIN #__users USING(user_id) WHERE message_id = '" . intval($_GET['q']) . "'"))
                        a_error("Не найден пост для цитирования");

                    $message_text .= '[q]' . $q_post['username'] . ' (' . date('d.m.Y в H:i', $q_post['time']) . ')' . PHP_EOL;
                    $message_text .= $q_post['message'] . '[/q]' . PHP_EOL;
                }

                if (USER_ID == -1 && !$this->config['forum']['guests_write_messages'])
                    a_error("Гости не имеют отвечать на темы!<br />Зарегистрируйтесь или войдите под своим именем.");
            }

            if (!$topic = $this->db->get_row("SELECT * FROM #__forum_topics WHERE topic_id = '" . intval($topic_id) . "'"))
                a_error("Тема не найдена!");

            # Определяем можно ли постить в теме
            if (ACCESS_LEVEL < 8 && $topic['is_close_topic'])
                a_error("Тема закрыта, вы не имеете права писать и редактировать сообщения!");
        }

        if (isset($_POST['submit'])) {
            if ($action == 'new_topic' || $action == 'edit_first_message') {
                if (empty($_POST['topic_name'])) {
                    $this->error .= 'Укажите название темы!<br />';
                }
            }
            if (empty($_POST['message'])) {
                $this->error .= 'Укажите сообщение!<br />';
            }
            # Проверка кода с картинки
            if (USER_ID == -1) {
                if ($_POST['captcha_code'] != $_SESSION['captcha_code']) {
                    $this->error .= 'Неверно указан код с картинки<br />';
                }
            }
            # Проверка прикрепляемого файла
            if (!empty($_FILES['attach']['tmp_name'])) {
                $file_ext = array_pop(explode('.', $_FILES['attach']['name']));

                if (!strstr(';' . $this->config['forum']['allowed_filetypes'] . ';', ';' . $file_ext . ';'))
                    $this->error .= 'Вы пытаетесь загрузить запрещенный тип файла<br />';

                if (filesize($_FILES['attach']['tmp_name']) > $this->config['forum']['max_filesize'] * 1048576)
                    $this->error .= 'Размер загружаемого файла превышает допустимый размер (' . $this->config['forum']['max_filesize'] . ' Mb)<br />';
            }

            if (!$this->error) {
                $_SESSION['captcha_code'] = main::get_unique_code(4);

                switch ($action) {
                    # Создание темы
                    case 'new_topic':
                        # Добавляем тему
                        $this->db->query("INSERT INTO #__forum_topics SET
							section_id = '" . $forum['section_id'] . "',
							forum_id = '" . $forum['forum_id'] . "',
							user_id = '" . USER_ID . "',
							name = '" . a_safe($_POST['topic_name']) . "',
							time = UNIX_TIMESTAMP(),
							last_message_time = UNIX_TIMESTAMP(),
							last_user_id = '" . USER_ID . "'
						");
                        $topic_id = $this->db->insert_id();

                        # Добавляем сообщение
                        $this->db->query("INSERT INTO #__forum_messages SET
							topic_id = '" . $topic_id . "',
							section_id = '" . $forum['section_id'] . "',
							forum_id = '" . $forum['forum_id'] . "',
							user_id = '" . USER_ID . "',
							message = '" . a_safe($_POST['message']) . "',
							is_first_message = 1,
							time = UNIX_TIMESTAMP()
						");
                        $message_id = $this->db->insert_id();

                        # Увеличиваем количество тем и сообщений в форуме
                        $this->db->query("UPDATE #__forum_forums SET
							topics = topics + 1,
							messages = messages + 1
							WHERE
							forum_id = '" . $forum['forum_id'] . "'
						");

                        # Добавляем рейтинг
                        user::rating_update();

                        $location = a_url('forum/viewtopic', 'topic_id=' . $topic_id, true);
                        break;
                    # Добавление сообщения
                    case 'new_message':
                        # Снимаем метку с последнего сообщения
                        $this->db->query("UPDATE #__forum_messages SET is_last_message = 0 WHERE topic_id = '" . $topic['topic_id'] . "'");

                        # Добавляем сообщение
                        $this->db->query("INSERT INTO #__forum_messages SET
							topic_id = '" . $topic['topic_id'] . "',
							section_id = '" . $topic['section_id'] . "',
							forum_id = '" . $topic['forum_id'] . "',
							user_id = '" . USER_ID . "',
							message = '" . a_safe($_POST['message']) . "',
							is_last_message = 1,
							time = UNIX_TIMESTAMP()
						");
                        $message_id = $this->db->insert_id();

                        # Обновляем счетчик сообщений темы и время последнего сообщения
                        $this->db->query("UPDATE #__forum_topics SET
							messages = messages + 1,
							last_message_time = UNIX_TIMESTAMP(),
							last_user_id = '" . USER_ID . "'
							WHERE topic_id = '" . $topic['topic_id'] . "'
						");

                        # Увеличиваем количество сообщений в форуме
                        $this->db->query("UPDATE #__forum_forums SET
							messages = messages + 1
							WHERE
							forum_id = '" . $topic['forum_id'] . "'
						");

                        # Добавляем рейтинг
                        user::rating_update();

                        # Определяем start для пагинации
                        $messages = $topic['messages'] + 1;
                        $start = floor($messages / $this->config['forum']['messages_per_page']) * $this->config['forum']['messages_per_page'];

                        $location = a_url('forum/viewtopic', 'topic_id=' . $topic['topic_id'] . '&start=' . $start, true);
                        break;
                    # Редактирование сообщения
                    case 'edit_first_message':
                        $this->db->query("UPDATE #__forum_topics SET name = '" . a_safe($_POST['topic_name']) . "' WHERE topic_id = '" . $message['topic_id'] . "'");
                    case 'edit_message':
                        # Изменяем сообщение
                        $this->db->query("UPDATE #__forum_messages SET
							message = '" . a_safe($_POST['message']) . "',
							edit_editor = '" . $this->user['username'] . "',
							edit_time = UNIX_TIMESTAMP(),
							edit_count = edit_count + 1
							WHERE
							message_id = '" . $message['message_id'] . "'
						");
                        $message_id = $message['message_id'];

                        $location = a_url('forum/viewtopic', 'topic_id=' . $message['topic_id'], true);
                        break;
                }

                if (!empty($_FILES['attach']['tmp_name'])) {
                    # Удаляем старый файл, если имеется
                    if ($old_file = $this->db->get_row("SELECT * FROM #__forum_files WHERE message_id = '$message_id'")) {
                        @unlink(ROOT . 'files/forum/' . main::get_dir($old_file['file_id']) . '/' . $old_file['file_name']);
                        $this->db->query("DELETE FROM #__forum_files WHERE file_id = '" . $old_file['file_id'] . "'");
                    }

                    # Получаем ID нового файла
                    $this->db->query("INSERT INTO #__forum_files SET file_id = NULL");
                    $file_id = $this->db->insert_id();

                    # Генерируем имя загружаемого файла
                    $file_name = $file_id . '_' . preg_replace('/[^a-zA-Z0-9_\.]+/', '', $_FILES['attach']['name']);

                    # Создаем папку для файла если необходимо
                    $directory = ROOT . 'files/forum/' . main::get_dir($file_id);
                    if (!file_exists($directory)) {
                        mkdir($directory);
                        chmod($directory, 0777);
                    }

                    # Перемещаем новый файл
                    move_uploaded_file($_FILES['attach']['tmp_name'], $directory . '/' . $file_name);
                    chmod($directory . '/' . $file_name, 0777);

                    # Получаем размер файла
                    $file_size = filesize($directory . '/' . $file_name);

                    # Обновляем данные о файле
                    $this->db->query("UPDATE #__forum_files SET
						message_id = '$message_id',
						file_name = '" . a_safe($file_name) . "',
						file_size = '$file_size'
						WHERE file_id = $file_id
					");
                }

                header('Location: ' . $location);
                exit;
            }
        }
        if (!isset($_POST['submit']) || $this->error) {
            $_SESSION['captcha_code'] = main::get_unique_code(4);

            $this->tpl->assign(array(
                'error' => $this->error,
                'title' => $title,
                'message' => $message,
                'topic' => $topic,
                'forum' => $forum,
                'action' => $action,
                'message_text' => $message_text
            ));

            $this->tpl->display('posting');
        }
    }

    /**
     * Листинг новых сообщений
     */
    public function action_new_messages() {
        $this->per_page = $this->config['forum']['messages_per_page'];

        $sql = "SELECT SQL_CALC_FOUND_ROWS m.*, t.name AS topic_name, u.username, u.last_visit, up.avatar AS avatar_exists,
			(SELECT COUNT(*) FROM #__forum_messages AS fm WHERE fm.topic_id = m.topic_id) AS all_messages
			FROM #__forum_messages AS m LEFT JOIN #__forum_topics AS t USING(topic_id) LEFT JOIN #__users AS u ON u.user_id = m.user_id LEFT JOIN #__users_profiles AS up ON up.user_id = u.user_id
			ORDER BY m.time DESC
			LIMIT $this->start, $this->per_page
		";

        $result = $this->db->query($sql);
        $total = $this->db->get_one("SELECT FOUND_ROWS()");

        $messages = array();
        if (!class_exists('smiles'))
            a_import('modules/smiles/helpers/smiles');
        while ($message = $this->db->fetch_array($result)) {
            $message['message'] = main::bbcode($message['message']);
            $message['message'] = smiles::smiles_replace($message['message']);
            $message['message'] = nl2br($message['message']);
            $messages[] = $message;
        }

        # Пагинация
        $pg_conf['base_url'] = a_url('forum/new_messages', 'start=');
        $pg_conf['total_rows'] = $total;
        $pg_conf['per_page'] = $this->per_page;

        a_import('libraries/pagination');
        $pg = new CI_Pagination($pg_conf);

        $this->tpl->assign(array(
            'messages' => $messages,
            'total' => $total,
            'pagination' => $pg->create_links(),
            'messages_per_page' => $this->per_page
        ));

        $this->tpl->display('new_messages');
    }

    /**
     * Скачивание прикрепленного файла
     */
    public function action_download_attach() {
        if (!$file = $this->db->get_row("SELECT * FROM #__forum_files WHERE file_id = '" . intval($_GET['file_id']) . "'"))
            a_error('Файл не найден!');

        # Обновляем счетчик скачиваний
        $this->db->query("UPDATE #__forum_files SET file_downloads = file_downloads + 1 WHERE file_id = '" . $file['file_id'] . "'");

        # Перенаправляем на файл
        header('Location: ' . URL . 'files/forum/' . main::get_dir($file['file_id']) . '/' . $file['file_name']);
    }

}

?>