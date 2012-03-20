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

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

/**
 * Контроллер управления чатом
 */
class Chat_Admin_Controller extends Controller {
	/**
	* Уровень пользовательского доступа
	*/
	public $access_level = 10;
	/**
	* Тема
	*/
	public $template_theme = 'admin';

	/**
	* Метод по умолчанию
	*/
	public function action_index() {
		$this->action_config();
	}

	/**
	* Конфигурация модуля
	*/
	public function action_config() {
		$_config = $this->config['chat'];

		if(isset($_POST['submit'])) {
			main::is_demo();
			$_config = $_POST;

			main::config($_config, 'chat', $this->db);

			a_notice('Данные успешно изменены!', a_url('chat/admin/config'));
		}

		if(!isset($_POST['submit']) || $this->error) {
			$this->tpl->assign(array(
				'_config' => $_config
			));

			$this->tpl->display('config');
		}
	}

	/**
	* Управление комнатами
	*/
	public function action_rooms() {
		switch($_GET['a']) {
			# Удаление комнатаа
  			case 'delete':
  				main::is_demo();
				$room = $this->db->get_row("SELECT * FROM #__chat_rooms WHERE room_id = ". intval($_GET['room_id']));
				$this->db->query("DELETE FROM #__chat_rooms WHERE room_id = ". intval($_GET['room_id']));

				# Меняем позиции
				$this->db->query("UPDATE #__chat_rooms SET position = position - 1 WHERE position > '". $room['position'] ."'");
	
				# Удаляем все сообщения из данной комнаты
				$this->db->query("DELETE FROM #__chat_messages WHERE room_id = '". $room['room_id'] ."'");
	
				a_notice('Комната успешно удалена!', a_url('chat/admin'));
				break;

  			# Создание / Редактирование комнаты
  			case 'edit':
  				if(is_numeric(@$_GET['room_id'])) {
					if(!$room = $this->db->get_row("SELECT * FROM #__chat_rooms WHERE room_id = '". intval($_GET['room_id']) ."'"))
  						a_error('комната не найден!');
  					$action = 'edit';
  				}
  				else {
  					$room = array();
  					$action = 'add';
  				}

				if(isset($_POST['submit'])) {
					main::is_demo();
    				if(empty($_POST['name'])) {
    					$this->error .= 'Укажите название комнаты<br />';
    				}

    				if(!$this->error) {
    					if($action == 'add') {
         					$position = $this->db->get_one("SELECT MAX(position) FROM #__chat_rooms") + 1;
						$this->db->query("INSERT INTO #__chat_rooms SET
							name = '". a_safe($_POST['name']) ."',
							position = '". $position ."'
						");
						$message = "Комната успешно создана!";
    					}
    					if($action == 'edit') {
    						$this->db->query("UPDATE #__chat_rooms SET
    							name = '". a_safe($_POST['name']) ."'
    							WHERE room_id='". intval($_GET['room_id']) ."'
    						");
    						$message = "Комната успешно изменена!";
    					}
    					a_notice($message, a_url('chat/admin'));
    				}
				}
				if(!isset($_POST['submit']) || $this->error) {
					$this->tpl->assign(array(
						'action' => $action,
						'error' => $this->error,
						'room' => $room
					));
		
					$this->tpl->display('rooms_edit');
				}
				break;

  			# Увеличение позиции
			case 'up':
				if(!$room = $this->db->get_row("SELECT * FROM #__chat_rooms WHERE room_id = ". intval($_GET['room_id'])))
		    		a_error('Комната не найдена!');

				// Меняем позиции
				$this->db->query("UPDATE #__chat_rooms SET position = ". $room['position'] ." WHERE position = ". ($room['position'] - 1));
				$this->db->query("UPDATE #__chat_rooms SET position = ". ($room['position'] - 1) ." WHERE room_id = ". intval($_GET['room_id']));
	
				header("Location: ". a_url('chat/admin'));
				exit;
				break;

			# Уменьшение позиции
			case 'down':
				if(!$room = $this->db->get_row("SELECT * FROM #__chat_rooms WHERE room_id = ". intval($_GET['room_id'])))
		    		a_error('комната не найден!');

				// Меняем позиции
				$this->db->query("UPDATE #__chat_rooms SET position = ". $room['position'] ." WHERE position = ". ($room['position'] + 1));
				$this->db->query("UPDATE #__chat_rooms SET position = ". ($room['position'] + 1) ." WHERE room_id = ". intval($_GET['room_id']));
	
				header("Location: ". a_url('chat/admin'));
				exit;
				break;

  			# Список комнат
  			default:
				$sql = "SELECT SQL_CALC_FOUND_ROWS *
					FROM #__chat_rooms";

				$sql .= " ORDER BY position ASC";

				$result = $this->db->query($sql);
	
				$min_p = $this->db->get_one("SELECT MIN(position) FROM #__chat_rooms");
 				$max_p = $this->db->get_one("SELECT MAX(position) FROM #__chat_rooms");

				$rooms = array();
				while($room = $this->db->fetch_array($result)) {
					if($room['position'] != $min_p) $room['up'] = '<a href="'. a_url('chat/admin/rooms', 'a=up&amp;room_id='. $room['room_id']) .'">up</a>';
					else $room['up'] = 'up';

					if($room['position'] != $max_p) $room['down'] = '<a href="'. a_url('chat/admin/rooms', 'a=down&amp;room_id='. $room['room_id']) .'">down</a>';
					else $room['down'] = 'down';

					$rooms[] = $room;
				}

				$this->tpl->assign(array(
					'rooms' => $rooms
				));

				$this->tpl->display('rooms_list');
				break;
		}
	}
}
?>