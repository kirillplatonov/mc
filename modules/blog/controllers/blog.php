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
class Blog_Controller extends Controller {

    /**
     * Метод по умолчанию
     */
    public function action_index() {
        // Для пользователей их блог
        if (USER_ID != -1) {
            $this->action_my();
        }
        // Для гостей список всех блогов
        else {
            $this->action_list();
        }
    }

    /**
     * Пользовательская страница
     */
    public function action_my() {
        // Запрет доступа гостям
        if (!is_user()) {
            redirect('user/login');
        }

        switch ($_GET['action']) {
            case 'say':
                if ($_POST) {
                    if (!$this->error) {
                        a_antiflud();

                        $this->db->query("INSERT INTO #__blog SET
							user_id = '" . USER_ID . "',
							title = '" . a_safe($_POST['title']) . "',
							message = '" . a_safe($_POST['message']) . "',
							time = UNIX_TIMESTAMP(),
							rating = 0
						");

                        user::rating_update('5');

                        a_notice('Ваша запись опубликована.', a_url('blog'));
                    }
                } else {
                    a_error('Вы не ввели запись.', a_url('blog'));
                }
                break;

            case 'edit':
                $action = 'edit';
                $title = 'Изменение записи';

                // Проверка существования записи
                if (!$post = $this->db->get_row("SELECT *, (SELECT status FROM #__users WHERE user_id = cp.user_id) AS user_status FROM #__blog AS cp WHERE id = '" . intval($_GET['post_id']) . "'"))
                    a_error('Запись не найдена.', a_url('blog/my'));

                // Проверка прав на удаление
                if (!a_check_rights($post['user_id'], $post['user_status']))
                    a_error('У Вас нет прав для изменения этой записи.', a_url('blog'));

                if ($_POST) {
                    if (!$this->error) {
                        a_antiflud();

                        $this->db->query("UPDATE #__blog SET
							title = '" . a_safe($_POST['title']) . "',
							message = '" . a_safe($_POST['message']) . "'
							WHERE id = '$post[id]'
						");

                        a_notice('Ваша запись изменена.', a_url('blog'));
                    }
                }

                $this->tpl->assign(array(
                    'error' => $this->error,
                    'config' => $this->config['blog'],
                    'action' => $action,
                    'title' => $title,
                    'post' => $post,
                ));

                $this->tpl->display('my_edit');
                break;

            case 'delete':
                $action = 'delete';
                $title = 'Удаление записи';

                // Проверка существования записи
                if (!$post = $this->db->get_row("SELECT *, (SELECT status FROM #__users WHERE user_id = cp.user_id) AS user_status FROM #__blog AS cp WHERE id = '" . intval($_GET['post_id']) . "'"))
                    a_error('Запись не найдена.', a_url('blog'));

                // Проверка прав на удаление
                if (!a_check_rights($post['user_id'], $post['user_status']))
                    a_error('У Вас нет прав для удаления этой записи.', a_url('blog'));

                if ($_GET['confirm']) {
                    // Удаление записи
                    $this->db->query("DELETE FROM #__blog WHERE id = '" . intval($_GET['post_id']) . "'");

                    // Удаление рейтинга за запись
                    user::rating_update(-5, $post['user_id']);

                    a_notice('Запись успешно удалена.', a_url('blog'));
                } else {
                    a_confirm('Вы подтверждаете удаление этой записи?', a_url('blog/my', 'action=delete&amp;post_id=' . $post['id'] . '&amp;confirm=ok'), a_url('blog'));
                }
                break;

            default:
                $action = 'default';
                $title = 'Ваш блог';

                // Листинг записей
                $sql = "SELECT SQL_CALC_FOUND_ROWS b.*,
					(SELECT COUNT(*) FROM #__comments_posts WHERE module = 'blog' AND item_id = b.id) comments
					FROM #__blog AS b
					WHERE user_id = '" . USER_ID . "'
				ORDER BY time DESC LIMIT $this->start, $this->per_page";

                $result = $this->db->query($sql);

                if (!class_exists('smiles'))
                    a_import('modules/smiles/helpers/smiles');

                $posts = array();

                while ($post = $this->db->fetch_array($result)) {
                    // Проверка длины main::limit_words(strip_tags($last_news['text']), 15)
                    if (main::strlen($post['message']) > 500) {
                        $post['message'] = main::limit_words($post['message'], 100);
                        $post['message'] .= '..';
                        $post['long'] = TRUE;
                    }

                    // Проверка доступности голосования пользователем
                    if ($this->db->get_one("SELECT id FROM a_rating_logs WHERE ip = '" . a_safe($_SERVER['REMOTE_ADDR']) . "' AND module = 'blog' AND item_id = '" . $post['id'] . "'"))
                        $post['rated'] = TRUE;
                    else
                        $post['rated'] = FALSE;

                    // Получение звезд
                    $post['rating_stars'] = file_get_contents(URL . 'main/rating?rate=' . $post['rating']);

                    // Форматирование
                    $post['message'] = smiles::smiles_replace($post['message']);
                    $post['message'] = main::bbcode($post['message']);
                    $post['message'] = nl2br($post['message']);

                    $posts[] = $post;
                }

                $total = $this->db->get_one("SELECT FOUND_ROWS()");

                // Пагинация
                $pg_conf['base_url'] = a_url('blog/my', 'start=');
                $pg_conf['total_rows'] = $total;
                $pg_conf['per_page'] = $this->per_page;

                a_import('libraries/pagination');
                $pg = new CI_Pagination($pg_conf);

                $this->tpl->assign(array(
                    'error' => $this->error,
                    'config' => $this->config['blog'],
                    'action' => $action,
                    'title' => $title,
                    'posts' => $posts,
                    'total' => $total,
                    'pagination' => $pg->create_links(),
                ));

                $this->tpl->display('my_default');
                break;
        }
    }

    /**
     * Читать далее
     */
    public function action_read_more() {
        // Проверка существования записи
        if (!$post = $this->db->get_row("SELECT *,
			(SELECT status FROM #__users WHERE user_id = b.user_id) AS user_status, 
			(SELECT username FROM #__users WHERE user_id = b.user_id) AS username,
			(SELECT COUNT(*) FROM #__comments_posts WHERE module = 'blog' AND item_id = b.id) comments
			FROM #__blog AS b WHERE id = '" . intval($_GET['post_id']) . "'"))
            a_error('Запись не найдена.', a_url('blog'));

        if (!class_exists('smiles'))
            a_import('modules/smiles/helpers/smiles');

        // Проверка доступности голосования пользователем
        if ($this->db->get_one("SELECT id FROM a_rating_logs WHERE ip = '" . a_safe($_SERVER['REMOTE_ADDR']) . "' AND module = 'blog' AND item_id = '" . $post['id'] . "'"))
            $post['rated'] = TRUE;
        else
            $post['rated'] = FALSE;

        // Получение звезд
        $post['rating_stars'] = file_get_contents(URL . 'main/rating?rate=' . $post['rating']);

        // Форматирование
        $post['message'] = smiles::smiles_replace($post['message']);
        $post['message'] = main::bbcode($post['message']);
        $post['message'] = nl2br($post['message']);

        $this->tpl->assign(array(
            'error' => $this->error,
            'config' => $this->config['blog'],
            'post' => $post,
        ));

        $this->tpl->display('read_more');
    }

    /**
     * Просмотр блога
     */
    public function action_view() {
        $user = $this->db->get_row("SELECT * FROM #__users WHERE username = '" . a_safe($_GET['username']) . "'");

        if ($user['user_id'] == 0 || $user['user_id'] == -1) {
            a_error('Пользователь не найден. Проверьте правильность адреса.');
        }

        // Листинг записей
        $sql = "SELECT SQL_CALC_FOUND_ROWS b.*,
			(SELECT COUNT(*) FROM #__comments_posts WHERE module = 'blog' AND item_id = b.id) comments,
			(SELECT status FROM #__users WHERE user_id = b.user_id) AS user_status,
			(SELECT username FROM #__users WHERE user_id = b.user_id) AS username
			FROM #__blog AS b
			WHERE user_id = '" . $user['user_id'] . "'
		ORDER BY time DESC LIMIT $this->start, $this->per_page";

        $result = $this->db->query($sql);

        if (!class_exists('smiles'))
            a_import('modules/smiles/helpers/smiles');

        $posts = array();

        while ($post = $this->db->fetch_array($result)) {
            // Проверка длины main::limit_words(strip_tags($last_news['text']), 15)
            if (main::strlen($post['message']) > 500) {
                $post['message'] = main::limit_words($post['message'], 100);
                $post['message'] .= '..';
                $post['long'] = TRUE;
            }

            // Проверка доступности голосования пользователем
            if ($this->db->get_one("SELECT id FROM a_rating_logs WHERE ip = '" . a_safe($_SERVER['REMOTE_ADDR']) . "' AND module = 'blog' AND item_id = '" . $post['id'] . "'"))
                $post['rated'] = TRUE;
            else
                $post['rated'] = FALSE;

            // Получение звезд
            $post['rating_stars'] = file_get_contents(URL . 'main/rating?rate=' . $post['rating']);

            // Форматирование
            $post['message'] = smiles::smiles_replace($post['message']);
            $post['message'] = main::bbcode($post['message']);
            $post['message'] = nl2br($post['message']);

            $posts[] = $post;
        }

        $total = $this->db->get_one("SELECT FOUND_ROWS()");

        // Пагинация
        $pg_conf['base_url'] = a_url('blog/view', 'username=' . $user['username'] . '&amp;start=');
        $pg_conf['total_rows'] = $total;
        $pg_conf['per_page'] = $this->per_page;

        a_import('libraries/pagination');
        $pg = new CI_Pagination($pg_conf);

        $this->tpl->assign(array(
            'error' => $this->error,
            'config' => $this->config['blog'],
            'posts' => $posts,
            'total' => $total,
            'pagination' => $pg->create_links(),
            'profile' => $user,
        ));

        $this->tpl->display('view');
    }

    /**
     * Листинг блогов
     */
    public function action_list() {
        switch ($_GET['action']) {
            /**
             * Лучшие записи
             */
            case 'best_posts':
                $action = 'best_posts';
                $title = 'Лучшие записи';

                // Листинг записей
                $sql = "SELECT SQL_CALC_FOUND_ROWS b.*,
					(SELECT COUNT(*) FROM #__comments_posts WHERE module = 'blog' AND item_id = b.id) comments,
					(SELECT username FROM #__users WHERE user_id = b.user_id) username,
					(SELECT status FROM #__users WHERE user_id = b.user_id) AS user_status
					FROM #__blog AS b
				ORDER BY rating DESC LIMIT $this->start, $this->per_page";
                break;

            /**
             * Новые записи
             */
            case 'new_posts':
                $action = 'new_posts';
                $title = 'Новые записи';

                // Листинг записей
                $sql = "SELECT SQL_CALC_FOUND_ROWS b.*,
					(SELECT COUNT(*) FROM #__comments_posts WHERE module = 'blog' AND item_id = b.id) comments,
					(SELECT username FROM #__users WHERE user_id = b.user_id) username,
					(SELECT status FROM #__users WHERE user_id = b.user_id) AS user_status
					FROM #__blog AS b
					WHERE time > UNIX_TIMESTAMP() - 86400
				ORDER BY time DESC LIMIT $this->start, $this->per_page";
                break;

            /**
             * Все записи
             */
            case 'all_posts':
            default:
                $action = 'all_posts';
                $title = 'Все записи';

                // Листинг записей
                $sql = "SELECT SQL_CALC_FOUND_ROWS b.*,
					(SELECT COUNT(*) FROM #__comments_posts WHERE module = 'blog' AND item_id = b.id) comments,
					(SELECT username FROM #__users WHERE user_id = b.user_id) username,
					(SELECT status FROM #__users WHERE user_id = b.user_id) AS user_status
					FROM #__blog AS b
				ORDER BY time DESC LIMIT $this->start, $this->per_page";
                break;
        }

        $result = $this->db->query($sql);

        $total = $this->db->get_one("SELECT FOUND_ROWS()");

        if (!class_exists('smiles'))
            a_import('modules/smiles/helpers/smiles');

        $posts = array();

        while ($post = $this->db->fetch_array($result)) {
            // Проверка длины
            if (main::strlen($post['message']) > 500) {
                $post['message'] = main::limit_words($post['message'], 100);
                $post['message'] .= '..';
                $post['long'] = TRUE;
            }

            // Проверка доступности голосования пользователем
            if ($this->db->get_one("SELECT id FROM a_rating_logs WHERE ip = '" . a_safe($_SERVER['REMOTE_ADDR']) . "' AND module = 'blog' AND item_id = '" . $post['id'] . "'"))
                $post['rated'] = TRUE;
            else
                $post['rated'] = FALSE;

            // Получение звезд
            $post['rating_stars'] = file_get_contents(URL . 'main/rating?rate=' . $post['rating']);

            // Форматирование
            $post['message'] = smiles::smiles_replace($post['message']);
            $post['message'] = main::bbcode($post['message']);
            $post['message'] = nl2br($post['message']);

            $posts[] = $post;
        }

        // Пагинация
        $pg_conf['base_url'] = a_url('blog/list', 'action=' . $action . '&amp;start=');
        $pg_conf['total_rows'] = $total;
        $pg_conf['per_page'] = $this->per_page;

        a_import('libraries/pagination');
        $pg = new CI_Pagination($pg_conf);

        $this->tpl->assign(array(
            'error' => $this->error,
            'config' => $this->config['blog'],
            'action' => $action,
            'title' => $title,
            'posts' => $posts,
            'total' => $total,
            'pagination' => $pg->create_links(),
        ));

        $this->tpl->display('list');
    }

    /**
     * Изменение рейтинга
     */
    public function action_rating_change() {
        // Проверка существования записи
        if (!$post = $this->db->get_row("SELECT * FROM #__blog WHERE id = '" . intval($_GET['post_id']) . "'"))
            a_error('Запись не найдена.', a_url('blog'));

        if ($this->db->get_one("SELECT id FROM a_rating_logs WHERE module = 'blog' AND ip = '" . a_safe($_SERVER['REMOTE_ADDR']) . "' AND item_id = '" . $post['id'] . "'"))
            a_error('Вы уже голосовали за эту запись!', a_url('blog'));

        if ($post['user_id'] == USER_ID)
            a_error('Нельяз голосовать за свою запись!', a_url('blog'));

        $rate = intval($_POST['rate']);
        if (!in_array($rate, array('1', '2', '3', '4', '5')))
            a_eror('Ваша оценка не определена!', a_url('blog'));

        // Увеличиваем количество голосов
        $this->db->query("UPDATE a_blog SET
			rating = (rating * rating_voices + $rate) / (rating_voices + 1),
			rating_voices = rating_voices + 1
			WHERE id = '$post[id]'
		");

        // Добавляем голос в логи
        $this->db->query("INSERT INTO a_rating_logs SET
			module = 'blog',
			ip = '" . a_safe($_SERVER['REMOTE_ADDR']) . "',
			item_id = '" . $post['id'] . "',
			time = UNIX_TIMESTAMP()
		");

        a_notice('Ваша оценка принята!', a_url('blog'));
    }

}

?>