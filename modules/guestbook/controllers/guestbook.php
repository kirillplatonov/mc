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

defined('IN_SYSTEM') or die('<b>403<br />Access denied</b>');

/**
 * Controller guestbook module
 */
class Guestbook_Controller extends Controller {
	/**
	 * Уровень доступа к модулю
	 */
	public $access_level = 0;

	/**
	 * Метод по умолчанию
	 */
	public function action_index() {
		// Перенаправляем к листингу сообщений
		$this->action_listing();
	}

	/**
	 * Листинг сообщений
	 */
	public function action_listing() {
		// Проверка доступа к модулю
		if ($this->config['guestbook']['only_for_users'] == 1 AND USER_ID == -1) {
			// Перенаправление на страницу авторизации
			redirect('user/login');
		}

		// Запрос для вывода сообщений
		$sql = "SELECT SQL_CALC_FOUND_ROWS #__guestbook.*, #__users.status AS user_status, up.avatar AS avatar_exists, #__users.last_visit
		FROM #__guestbook LEFT JOIN #__users USING(user_id) LEFT JOIN #__users_profiles AS up USING(user_id)
		ORDER BY message_id DESC LIMIT $this->start, $this->per_page";

		// Выполнение запроса
		$result = $this->db->query($sql);

		// Получение кол-ва сообщений
		$total = $this->db->get_one("SELECT FOUND_ROWS()");

		// Подключение помощника смайлов
		if (!class_exists('smiles')) a_import('modules/smiles/helpers/smiles');

		// Форматирование сообщений
		while ($message = $this->db->fetch_array($result)) {
			$message['message'] = smiles::smiles_replace(main::bbcode(nl2br($message['message'])));

			$messages[] = $message;
		}

		// Конфигурация пагинации
		$pg_conf['base_url'] = a_url('guestbook', 'start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		// Пагинация
		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		// Назначение переменных
		$this->tpl->assign(array(
			'_config' => $this->config['guestbook'],
			'total' => $total,
			'messages' => $messages,
			'pagination' => $pg->create_links(),
		));

		// Вывод шаблона
		$this->tpl->display('listing');
	}

	/**
	 * Управление сообщениями
	 */
	public function action_control() {
		//
	}

	/**
	 * Написать сообщение
	 */
	public function action_say() {
	 $_config = $this->config['system'];

		if(isset($_POST['submit'])) {



			if ($_config['guestbook_posting'] == 'users' && USER_ID == -1) {
		$this->error .= 'Для написания сообщений вам необходимо авторизироваться на сайте<br />';
	  }
			if(empty($_POST['message'])) {
				$this->error .= 'Укажите сообщение<br />';
			}


			# Проверка кода с картинки
			if(USER_ID == -1) {
				if($_POST['captcha_code'] != $_SESSION['captcha_code']) {
					$this->error .= 'Неверно указан код с картинки<br />';
				}
			}

			if(!$this->error) {
				a_antiflud();

				setcookie('username', $_POST['username'], time() + 999999999, '/');

				$this->db->query("INSERT INTO #__guestbook SET
					username = '',
					user_id = '". USER_ID ."',
					message = '". a_safe($_POST['message']) ."',
					time = UNIX_TIMESTAMP()
				");

				$_SESSION['captcha_code'] = main::get_unique_code(4);

				user::rating_update();

				header("Location: ".a_url('guestbook', '', true));
				exit;
			}
		}
		if (!isset($_POST['submit']) OR $this->error) {
			$_SESSION['captcha_code'] = main::get_unique_code(4);

			$this->tpl->assign(array(
				'error' => $this->error
			));

			$this->tpl->display('say');
		}
	}

	/**
	 * Удаление сообщения
	 */
	public function action_delete_message() {
		if (!$message = $this->db->get_row("SELECT #__guestbook.*, #__users.status AS user_status FROM #__guestbook LEFT JOIN #__users USING(user_id) WHERE message_id = '".intval($_GET['message_id'])."'"))
			a_error('Сообщение не найдено!');

		if (!a_check_rights($message['user_id'], $message['user_status'])) a_error('У вас нет прав на выполнение этой операции!');

		if (!empty($_GET['confirm'])) {
			$this->db->query("DELETE FROM #__guestbook WHERE message_id = '".intval($_GET['message_id'])."'");
			user::rating_update(-1, $message['user_id']);
			a_notice('Сообщение удалено!', a_url('guestbook'));
		}
		else {
			a_confirm('Подтверждаете удаление данного сообщения?', a_url('guestbook/delete_message', 'message_id='.$_GET['message_id'].'&amp;confirm=ok'), a_url('guestbook'));
		}
	}
}
?>