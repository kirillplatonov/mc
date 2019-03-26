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
 * Контроллер пользовательской части чата
 */
class Chat_Controller extends Controller {
	/**
	 * Уровень пользовательского доступа
	 */
	public $access_level = 0;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		if (ACCESS_LEVEL < 5)
			a_notice('Для того чтобы находиться в чате необходимо <a href="'.a_url('user/registration').'">зарегистрироваться</a> либо <a href="'.a_url('user/login').'">войти</a> под своим именем!', a_url('user/registration'), 10);
	}

	/**
	 * Метод по умолчанию
	 */
	public function action_index() {
		$this->action_list_rooms();
	}

	/**
	 * Список комнат
	 */
	public function action_list_rooms() {
		# Обновляем время последнего посещения
		$this->db->query("UPDATE #__users SET
			chat_room_id = '0',
			chat_last_time = UNIX_TIMESTAMP()
			WHERE
			user_id = '". USER_ID."'
		");

		# Список комнат
		$rooms = $this->db->get_array("SELECT *,
			(SELECT COUNT(*) FROM #__users AS u WHERE u.chat_room_id = cr.room_id AND u.chat_last_time >= UNIX_TIMESTAMP() + ". intval($this->config['chat']['online_time'])." * 60) AS users_in_room
			FROM #__chat_rooms AS cr ORDER BY position");

		$this->tpl->assign(array(
			'rooms' => $rooms
		));

		$this->tpl->display('list_rooms');
	}

	/**
	 * Удаление сообщения
	 */
	public function action_delete_message() {
		if (!$message = $this->db->get_row("SELECT * FROM #__chat_messages WHERE message_id = '".intval($_GET['message_id'])."'"))
			a_error('Сообщение не найдено!');

		if (ACCESS_LEVEL < 8 && USER_ID != $message['user_id']) a_error('У вас нет прав на выполнение этой операции!');

		if (!empty($_GET['confirm'])) {
			$this->db->query("DELETE FROM #__chat_messages WHERE message_id = '".intval($_GET['message_id'])."'");
			# Уменьшаем рейтинг
			user::rating_update(-1, $message['user_id']);
			a_notice('Сообщение удалено!', a_url('chat/in_room', 'room_id='.$_GET['room_id']));
		}
		else {
			a_confirm('Подтверждаете удаление данного сообщения?', a_url('chat/delete_message', 'message_id='.$_GET['message_id'].'&amp;room_id='.$_GET['room_id'].'&amp;confirm=ok'), a_url('chat/in_room', 'room_id='.$_GET['room_id']));
		}
	}

	/**
	 * Просмотр комнаты
	 */
	public function action_in_room() {
		$this->per_page = $this->config['chat']['messages_per_page'];

		if (isset($_POST['update'])) {
			if (is_numeric($_POST['update']) && ($_POST['update'] >= 0 OR $_POST['update'] <= 60)) {
				$this->db->query("UPDATE #__users SET chat_update = '".intval($_POST['update'])."' WHERE user_id = '".USER_ID."'");
				# Обновим информацию о пользователе
				$this->user['chat_update'] = intval($_POST['update']);
			}
		}

  		if (!$room = $this->db->get_row("SELECT * FROM #__chat_rooms WHERE room_id = '".intval($_GET['room_id'])."'"))
			a_error('Раздел не найден!');

		# Обновляем время последнего посещения
		$this->db->query("UPDATE #__users SET
			chat_room_id = '". $room['room_id']."',
			chat_last_time = UNIX_TIMESTAMP()
			WHERE
			user_id = '". USER_ID."'
		");

		 $sql = "SELECT SQL_CALC_FOUND_ROWS cm.*, u.username, u.status AS user_status, up.avatar AS avatar_exists, u.last_visit
			FROM #__chat_messages AS cm LEFT JOIN #__users AS u USING(user_id) LEFT JOIN #__users_profiles AS up USING(user_id)
			WHERE cm.room_id = ". intval($_GET['room_id']);

		$sql .= " ORDER BY cm.message_id DESC LIMIT $this->start, $this->per_page";

		$result = $this->db->query($sql);

		$messages = array();
		if (!class_exists('smiles')) a_import('modules/smiles/helpers/smiles');
		while ($message = $this->db->fetch_array($result)) {
			# Форматируем текст сообщения
			$message['message'] = smiles::smiles_replace($message['message']);
			$message['message'] = main::bbcode($message['message']);
			$message['message'] = nl2br($message['message']);

			$messages[] = $message;
		}

		$total = $this->db->get_one("SELECT FOUND_ROWS()");

		# Пагинация
		$pg_conf['base_url'] = a_url('chat/in_room', 'room_id='.intval($_GET['room_id']).'&amp;start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'messages' => $messages,
			'room' => $room,
			'pagination' => $pg->create_links()
		));
	
		$this->tpl->display('in_room');
	}

	/**
	 * Написать сообщение
	 */
	public function action_say() {
		if (!$room = $this->db->get_row("SELECT * FROM #__chat_rooms WHERE room_id = '".intval($_GET['room_id'])."'"))
			a_error('Комната не найдена!');

		if (isset($_POST['submit'])) {
			if (empty($_POST['message'])) {
				$this->error .= 'Укажите сообщение!<br />';
			}
			if (main::strlen($_POST['message']) > $this->config['chat']['message_max_len']) {
				$this->error .= 'Сообщение превышает '.$this->config['chat']['message_max_len'].' символов!<br />';
			}

			if (!$this->error) {
				# Добавляем сообщение
				$this->db->query("INSERT INTO #__chat_messages SET
					user_id = '". USER_ID."',
					room_id = '". $room['room_id']."',
					message = '". a_safe($_POST['message'])."',
					time = UNIX_TIMESTAMP()
				");

				# Добавляем рейтинг
				user::rating_update();
		
				header('location: '.a_url('chat/in_room', 'room_id='.intval($_GET['room_id']), TRUE));
				exit;
			}
		}
		if (!isset($_POST['submit']) || $this->error) {
			$this->tpl->assign(array(
			'error' => $this->error,
			'room' => $room
			));
	
			$this->tpl->display('say');
		}
	}
}
?>