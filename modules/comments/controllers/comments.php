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

/**
 *  Контроллер пользовательской части модуля комментариев
 */
class Comments_Controller extends Controller {
	/**
	 * Уровень пользовательского доступа
	 */
	public $access_level = 0;

	/**
	 * Метод по умолчанию
	 */
	public function action_index() {
		$this->action_list_comments();
	}

	/**
	 * Список комментариев
	 */
	public function action_list_comments() {
		if (empty($_GET['module']) || empty($_GET['item_id'])) a_error('Не указан модуль либо деталь');

		if (!modules::is_active_module($_GET['module'])) a_error('Модуль не установлен либо не активен');

		if (file_exists(ROOT . 'modules/' . $_GET['module'] . '/comments.dat')) $comments_data = file(ROOT . 'modules/' . $_GET['module'] . '/comments.dat');
		else a_error('Комментарии для данного модуля не предназначены');

		if (!$this->db->get_one("SELECT " . a_safe($comments_data[1]) . " FROM " . a_safe($comments_data[0]) . " WHERE " . a_safe($comments_data[1]) . " = " . intval($_GET['item_id']))) a_error('Не найден обсуждаемый объект');

		# Листинг комментариев
		$sql = "SELECT SQL_CALC_FOUND_ROWS cp.*, cp.username, u.status AS user_status, up.avatar AS avatar_exists, u.last_visit
        FROM #__comments_posts AS cp
        LEFT JOIN #__users AS u USING(user_id)
        LEFT JOIN #__users_profiles AS up USING(user_id)
        WHERE
        cp.module = '" . a_safe(@$_GET['module'])."' AND
        cp.item_id = " . intval($_GET['item_id']);

		$sql .= " ORDER BY cp.comment_id ASC LIMIT $this->start, $this->per_page";

		$result = $this->db->query($sql);

		if (!class_exists('smiles')) a_import('modules/smiles/helpers/smiles');

		$comments = array();

		while ($comment = $this->db->fetch_array($result)) {
			# Форматируем текст комментария
			$comment['text'] = smiles::smiles_replace($comment['text']);
			$comment['text'] = main::bbcode($comment['text']);
			$comment['text'] = nl2br($comment['text']);

			$comments[] = $comment;
		}

		$total = $this->db->get_one("SELECT FOUND_ROWS()");

		# Пагинация
		$pg_conf['base_url'] = a_url('comments', 'module=' . $_GET['module'] . '&amp;item_id=' . $_GET['item_id'] . '&amp;return=' . urlencode($_GET['return']) . '&amp;start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$_SESSION['captcha_code'] = main::get_unique_code(4);

		$_config = $this->config['system'];

		$this->tpl->assign(array(
		  'error' => $this->error,
		  'comments' => $comments,
		  'total' => $total,
		  '_config' => $_config,
		  'start' => $this->start,
		  'pagination' => $pg->create_links()
		));

		$this->tpl->display('list_comments');
	}

	/**
	 * Добавление комментария
	 */
	public function action_say() {
		if (isset($_POST['submit'])) {

			$_config = $this->config['system'];

			if ($_config['comment_posting'] == 'users' && USER_ID == - 1) {
				$this->error.= 'Для написания сообщений вам необходимо авторизироваться на сайте<br />';
			}

			$_POST['message'] = trim($_POST['message']);

			if (empty($_POST['message'])) $this->error.= 'Укажите сообщение<br />';

			# Проверка кода с картинки
			if (USER_ID == - 1) {
				if ($_POST['captcha_code'] != $_SESSION['captcha_code']) $this->error.= 'Неверно указан код с картинки<br />';
			}

			if (!$this->error) {
				a_antiflud();

				setcookie('username', $_POST['username'], time() + 999999999, '/');

				$this->db->query("INSERT INTO #__comments_posts SET
                    item_id = '" . intval($_GET['item_id']) . "',
                    module = '" . a_safe($_GET['module']) . "',
                    username = '" . a_safe($_POST['username']) . "',
                    user_id = '" . USER_ID . "',
                    `text` = '" . a_safe($_POST['message']) . "',
                    time = UNIX_TIMESTAMP()
                ");

				$_SESSION['captcha_code'] = main::get_unique_code(4);

				user::rating_update();

				# Считаем на какую страницу переходить
				$total = $this->db->get_one("SELECT COUNT(*) FROM #__comments_posts WHERE item_id = '" . intval($_GET['item_id']) . "' AND module = '" . a_safe($_GET['module']) . "'");

				$start = floor($total / $this->per_page) * $this->per_page;

				if ($total == $start) $start = $start - $this->per_page;

				header('Location: ' . a_url('comments', 'item_id=' . $_GET['item_id'] . '&module=' . $_GET['module'] . '&return=' . urlencode($_GET['return']) . '&start=' . $start, true));

				exit;
			}
		}

		if (!isset($_POST['submit']) OR $this->error) {
			# Считаем на какую страницу переходить
			if (isset($_GET['start'])) $_GET['start'] = $this->start;

			$this->action_list_comments();
		}
	}

	/**
	 * Удаление комментария
	 */
	public function action_comment_delete() {
		if (!$comment = $this->db->get_row("SELECT *, (SELECT status FROM #__users WHERE user_id = cp.user_id) AS user_status FROM #__comments_posts AS cp WHERE comment_id = '" . intval($_GET['comment_id']) . "'")) a_error('Комментарий не найден');

		if (!a_check_rights($comment['user_id'], $comment['user_status'])) a_error('У вас нет права удалять данное сообщение');

		if (!empty($_GET['confirm'])) {
			$this->db->query("DELETE FROM #__comments_posts WHERE comment_id = '" . intval($_GET['comment_id']) . "'");

			user::rating_update(-1, $comment['user_id']);

			a_notice('Комментарий успешно удален!', a_url('comments', 'module=' . $_GET['module'] . '&amp;item_id=' . $_GET['item_id'] . '&amp;return=' . urlencode($_GET['return']) . '&amp;start=' . $_GET['start']));
		} else {
			a_confirm('Подтверждаете удаление данного комментария?', a_url('comments/comment_delete', 'comment_id=' . $_GET['comment_id'] . '&amp;confirm=ok&amp;module=' . $_GET['module'] . '&amp;item_id=' . $_GET['item_id'] . '&amp;return=' . urlencode($_GET['return']) . '&amp;start=' . $_GET['start']), a_url('comments', 'module=' . $_GET['module'] . '&amp;item_id=' . $_GET['item_id'] . '&amp;return=' . urlencode($_GET['return']) . '&amp;start=' . $_GET['start']));
		}
	}

	/**
	 * Изменение комментария
	 */
	public function action_comment_edit() {
		if (!$comment = $this->db->get_row("SELECT *, (SELECT status FROM #__users WHERE user_id = cp.user_id) AS user_status FROM #__comments_posts AS cp WHERE comment_id = '" . intval($_GET['comment_id']) . "'")) a_error('Комментарий не найден');

		if (!a_check_rights($comment['user_id'], $comment['user_status'])) a_error('У вас нет права удалять данное сообщение');

		if (isset($_POST['message'])) {
			$_POST['message'] = trim($_POST['message']);
			if (empty($_POST['message'])) $this->error.= 'Укажите сообщение<br />';

			if (!$this->error) {
				a_antiflud();

				$this->db->query("UPDATE #__comments_posts SET
                    text = '" . a_safe($_POST['message']) . "'
                WHERE comment_id = '" . intval($_GET['comment_id']) . "'
                ");

				a_notice('Сообщение успешно изменено!', urldecode($_GET['return_url']));
			}
		}

		$this->tpl->assign(array('error' => $this->error, 'comment' => $comment));

		$this->tpl->display('comment_edit');
	}
}

?>