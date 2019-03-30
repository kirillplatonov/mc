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
* Контроллер управления пользователями
*/
class User_Admin_Controller extends Controller {
	/**
	 * Уровень пользовательского доступа
	 */
	protected $access_level = 8;
	/**
	 * Тема
	 */
	protected $template_theme = 'admin';

	/**
	 * Метод по умолчанию
	 */
	public function action_index() {
		$this->action_list_users();
	}
	
	/**
	 * Конфигурация модуля
	 */
	public function action_config() {
		$_config = $this->config['user'];

		if (isset($_POST['submit'])) {
			main::is_demo();
			$_config = $_POST;

			main::config($_config, 'user', $this->db);

			a_notice('Настройки модуля успешно изменены!', a_url('user/admin/config'));
		}
		
		$this->tpl->assign(array(
			'_config' => $_config
		));

		$this->tpl->display('config');
	}

	/**
	 * Удаление пользователя
	 */
	public function action_delete() {
		main::is_demo();
		# Получаем инфо о пользователе
		if (!$user_delete = $this->db->get_row("SELECT * FROM #__users WHERE user_id = '".intval($_GET['user_id'])."'"))
			a_error("Пользователь не найден!");

		$user_delete_access = $this->access->get_level($user_delete['status']);

		if (ACCESS_LEVEL <= $user_delete_access) a_error("У вас нет прав на выполнение этой операции!");
		else {
			$this->db->query("DELETE FROM #__users WHERE user_id = ".intval($user_delete['user_id']));
			a_notice('Пользователь удален!', a_url('user/admin'));
		}
	}
	
	/**
	 * Удаление пользователя
	 */
	public function action_user_email() {
		main::is_demo();
		if (ACCESS_LEVEL < 10) a_error('У вас нет прав на выполнение этой операции!');
		
		# Получаем инфо о пользователе
		if (!$user_email = $this->db->get_row("SELECT * FROM #__users WHERE user_id = '".intval($_GET['user_id'])."'"))
			a_error("Пользователь не найден!");
		
		if ($_POST['submit']) {
			if (empty($_POST['title'])) $this->error .= 'Не введен заголовок<br />';
			if (empty($_POST['msg'])) $this->error .= 'Не введено сообщение заголовок<br />';
			
			if (!$this->error) {
				a_import('libraries/email');
				$this->email = new Mail('utf-8');

				$this->email->From($this->config['system']['system_email']);
				$this->email->To($user_email['email']);

				$this->email->Subject(a_safe($_POST['title']));
				$this->email->Body(a_safe($_POST['msg']));

				$this->email->Send();
			    
				a_notice('Сообщение успешно отправлено!', a_url('user/admin/list_users'));
			}
		}
		
		$this->tpl->assign(array(
			'error' => $this->error,
			'user_email'   => $user_email,
		));

		$this->tpl->display('user_email');
	}

	/**
	 * Бан пользователя
	 */
	public function action_ban() {
		# Получаем инфо о пользователе
		if (!$user_ban = $this->db->get_row("SELECT * FROM #__users WHERE user_id = '".intval($_GET['user_id'])."'"))
			a_error("Пользователь не найден!");

		$user_ban_access = $this->access->get_level($user_ban['status']);

		if (ACCESS_LEVEL <= $user_ban_access) a_error("У вас нет прав на выполнение этой операции!");

		if (isset($_POST['submit'])) {
			main::is_demo();
			if (!is_numeric($_POST['hours'])) {
				$this->error .= 'Неверный формат времени - только целые числа.<br />';
			}
			if (empty($_POST['description'])) {
				$this->error .= 'Укажите причину бана.<br />';
			}

			if (!$this->error) {
				$this->db->query("INSERT INTO #__users_ban SET
		   			user_id = '". intval($_GET['user_id'])."',
		   			to_time = '". ($_POST['hours'] * 3600 + time())."',
		   			description = '". a_safe($_POST['description'])."'
		   		");
				a_notice('Пользователь забанен.', a_url('user/admin'));
			}
		}
		
		if (isset($_POST['delete_ban'])) {
			   main::is_demo();
			   $this->db->query("DELETE FROM #__users_ban WHERE user_id = '". intval($_GET['user_id']) ."'");
			   a_notice('Пользователь разбанен.', a_url('user/admin'));
		}
		
		# Получаем информацию о текущем бане
		$this->ban = $this->db->get_row("SELECT * FROM #__users_ban WHERE user_id = '". intval($_GET['user_id']) ."' AND status = 'enable'");

		if(!isset($_POST['submit']) || $this->error) {
			$this->tpl->assign(array(
				'error' => $this->error,
				'ban'   => $this->ban,
				'username_ban' => $user_ban['username']
			));

			$this->tpl->display('ban');
		}
	}


	/**
	 * Редактирование пользователя
	 */
	public function action_edit() {
		# Получаем инфо о пользователе
		if (!$user_edit = $this->db->get_row("SELECT * FROM #__users WHERE user_id = '".intval($_GET['user_id'])."'"))
			a_error("Пользователь не найден!");

		$user_edit_access = $this->access->get_level($user_edit['status']);

		if (ACCESS_LEVEL <= $user_edit_access) a_error("У вас нет прав на выполнение этой операции!");

		if (isset($_POST['submit'])) {
			main::is_demo();
			if (!main::check_input($_POST['email'], 'MAIL')) {
				$this->error .= 'Неверный формат e-mail.<br />';
			}
			if ($_POST['status'] != 'admin' && $_POST['status'] != 'moder' && $_POST['status'] != 'user') {
				$this->error .= 'Укажите валидный статус!<br />';
			}
			if (ACCESS_LEVEL < 10 && $_POST['status'] != $user_edit['status']) {
				$this->error .= 'Только администратор может менять статусы пользователей!<br />';
			}

			if (!$this->error) {
				$this->db->query("UPDATE #__users SET
		 			email = '". a_safe($_POST['email'])."',
		 			status = '". a_safe($_POST['status'])."'
		 			WHERE user_id = '". intval($_GET['user_id'])."'
		 		");

				a_notice('Пользователь изменен!', a_url('user/admin'));
			}
		}
		if (!isset($_POST['submit']) || $this->error) {
			$this->tpl->assign(array(
				'error' => $this->error,
				'user_edit' => $user_edit
			));

			$this->tpl->display('edit');
		}
	}

	/**
	 * Вход в панель к пользователю
	 */
	public function action_go_to_user_panel() {
		// Запрет для модераторов
		if (ACCESS_LEVEL < 10) a_error('У Вас нет прав для выполнения данного действия.');
		
		
		if (!$check_user = $this->db->get_row("SELECT * FROM #__users WHERE user_id = '".intval($_GET['user_id'])."'"))
			a_error('Пользователь не найден!');
	
		if (!a_check_rights($check_user['user_id'], $check_user['status']))
			a_error('У вас нет прав на выполнение этого действия!');
	
		$_SESSION['check_user_id'] = intval($_GET['user_id']);
	
		header('Location: '.a_url(MAIN_MENU));
	}

	/**
	 * Листинг пользователей
	 */
	public function action_list_users() {
		// Кол-во пользователей на страницу
		$this->per_page = 20;

		// Получение данных
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM #__users
			WHERE 1 = 1 AND user_id > 0 ";
			
		if (!empty($_GET['user_id'])) $sql .= " AND user_id = ".intval($_GET['user_id']);
		else {
			if (!empty($_GET['username'])) $sql .= " AND username LIKE '%".a_safe($_GET['username'])."%'";
			if (!empty($_GET['status'])) $sql .= " AND status = '".a_safe($_GET['status'])."'";
		}
		
		if (str_safe($_GET['type']) == 'online') $sql .= " AND last_visit > UNIX_TIMESTAMP() - 600 ";
		
		$sql .= " ORDER BY user_id ".($_GET['sort'] == 'desc' ? 'DESC' : 'ASC')." LIMIT $this->start, $this->per_page";

		$users = $this->db->get_array($sql);
		$total = $this->db->get_one("SELECT FOUND_ROWS()");

		// Пагинация
		$pg_conf['base_url'] = a_url('user/admin/list_users', 'status='.str_safe($_GET['status']).'&amp;login='.str_safe($_GET['username']).'&amp;user_id='.intval($_GET['user_id']).'&amp;sort='.str_safe($_GET['sort']).'&amp;start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'users' => $users,
			'total' => $total,
			'db'    => $this->db,
			'start' => $this->start,
			'pagination' => $pg->create_links(),
			'action' => 'list'
		));

		$this->tpl->display('list_users');
	}
	
	/**
	 * Листинг гостей
	 */
	public function action_list_guests() {
		// Кол-во пользователей на страницу
		$this->per_page = 20;

		// Получение данных
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM #__guests
			WHERE 1 = 1 AND last_time > UNIX_TIMESTAMP() - 180
		ORDER BY last_time DESC LIMIT $this->start, $this->per_page";

		$guests = $this->db->get_array($sql);
		$total = $this->db->get_one("SELECT FOUND_ROWS()");

		// Пагинация
		$pg_conf['base_url'] = a_url('user/admin/list_guests', 'start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'guests' => $guests,
			'total' => $total,
			'db'    => $this->db,
			'start' => $this->start,
			'pagination' => $pg->create_links()
		));

		$this->tpl->display('list_guests');
	}
	
	/**
	 * Бан по IP
	 */
	public function action_ip_ban() {
		// Выполнение действий
		switch(str_safe($_GET['action'])) {
			case 'add':
				$title = 'Бан нового IP';
				$action = 'add';

				if (isset($_GET['guest_ip'])) {
					main::is_demo();

					if(ACCESS_LEVEL <= 8) a_error('У вас нет прав на выполнение этой операции!');

					if (empty($_GET['guest_ip'])) $this->error .= 'Не указан IP адрес, который нужно забанить<br />';

					// Проверка IP регулярками
					if ( ! empty($_GET['guest_ip']) AND ! main::check_input($_GET['guest_ip'], 'IP')) $this->error .= 'Неверно указан IP адрес, формат правильного: '. main::check_input('', 'IP', 'format') .'<br />';

					if ($ip = $this->db->get_row("SELECT * FROM #__ip_ban WHERE ip = '". str_safe($_GET['guest_ip']) ."'")) a_notice('Указаный IP адрес уже забанен!', a_url('user/admin/ip_ban'));

					// Баним
					if ( ! $this->error) {
						$this->db->query("INSERT INTO #__ip_ban SET
				 			ip = '". a_safe($_GET['guest_ip']) ."'
						");

						a_notice('IP адрес успешно забанен', a_url('user/admin/ip_ban'));
					}
				}
			break;
			
			case 'edit':
				$title = 'Изменение IP адреса';
				$action = 'edit';

				if (isset($_GET['id'])) {
					main::is_demo();

					if(ACCESS_LEVEL <= 8) a_error('У вас нет прав на выполнение этой операции!');
			        
					if ( ! $edit_ip = $this->db->get_row("SELECT * FROM #__ip_ban WHERE id = '". str_safe($_GET['id']) ."'")) a_error('Не найдена запись с указаным ID!<br />');

					if (isset($_POST['guest_ip'])) {
						if (empty($_POST['guest_ip'])) $this->error .= 'Не указан IP адрес, который нужно забанить<br />';

						// Проверка IP регулярками
						if ( ! empty($_POST['guest_ip']) AND ! main::check_input($_POST['guest_ip'], 'IP')) $this->error .= 'Неверно указан IP адрес, формат правильного: '. main::check_input('', 'IP', 'format') .'<br />';

						if ($ip_edit = $this->db->get_row("SELECT * FROM #__ip_ban WHERE ip = '". str_safe($_POST['guest_ip']) ."' AND id != $edit_ip[id]")) a_notice('Указаный IP адрес уже забанен!', a_url('user/admin/ip_ban'));

						// Баним
						if ( ! $this->error) {
							$this->db->query("UPDATE #__ip_ban SET
				 				ip = '". a_safe($_POST['guest_ip']) ."'
							WHERE id = $edit_ip[id]
							");

							a_notice('IP адрес успешно изменен', a_url('user/admin/ip_ban'));
						}
				}
				}
			break;
			
			case 'delete':
				main::is_demo();

				if(ACCESS_LEVEL <= 8) a_error('У вас нет прав на выполнение этой операции!');
			    
				if (empty($_GET['guest_ip'])) $this->error .= 'Не указан IP адрес, который нужно разбанить<br />';
			    
				// Проверка IP регулярками
				if ( ! empty($_GET['guest_ip']) AND ! main::check_input($_GET['guest_ip'], 'IP')) $this->error .= 'Неверно указан IP адрес, формат правильного: '. main::check_input('', 'IP', 'format') .'<br />';
				
				if ( ! $ip_delete = $this->db->get_row("SELECT * FROM #__ip_ban WHERE ip = '". str_safe($_GET['guest_ip']) ."'")) a_error('Не найдена запись с указаным IP адресом!');
				
				if ( ! $this->error) {
					$this->db->query("DELETE FROM #__ip_ban WHERE ip = '". str_safe($_GET['guest_ip']) ."'");
					a_notice('IP адрес '. str_safe($_GET['guest_ip']) .' успешно разбанен!', a_url('user/admin/ip_ban'));
				}
			break;
			
			case 'delete_all':
				main::is_demo();

				if(ACCESS_LEVEL <= 8) a_error('У вас нет прав на выполнение этой операции!');

				$this->db->query("TRUNCATE TABLE #__ip_ban");
				a_notice('Все IP успешно разбанены!', a_url('user/admin/ip_ban'));
			break;
			
			default:
				$title = 'Список забаненых по IP';
				$action = 'list_ip';
			break;
		}
	
		// Кол-во IP на страницу
		$this->per_page = 20;

		// Получение данных
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM #__ip_ban
			WHERE 1 = 1 ";
			
		if (!empty($_GET['guest_ip'])) $sql .= " AND ip LIKE '%". a_safe($_GET['guest_ip']) ."%'";
        
		$sql .= " ORDER BY id ". ($_GET['sort'] == 'desc' ? 'DESC' : 'ASC') ." LIMIT $this->start, $this->per_page";

		$ip_bans = $this->db->get_array($sql);
		$total = $this->db->get_one("SELECT FOUND_ROWS()");

		// Пагинация
		$pg_conf['base_url'] = a_url('user/admin/ip_ban', 'start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'edit_ip' => $edit_ip,
			'error' => $this->error,
			'title' => $title,
			'action' => $action,
			'ip_bans' => $ip_bans,
			'total' => $total,
			'db'    => $this->db,
			'start' => $this->start,
			'pagination' => $pg->create_links()
		));

		$this->tpl->display('ip_ban');
	}
        
	/**
	 * Модерация пользователей
	 */
	public function action_moderate() {
		// Модерация
		if ($_GET['user_id'] && $_GET['action']) {

		# Получаем инфо о пользователе
		if (!$user_moderate = $this->db->get_row("SELECT * FROM #__users WHERE user_id = '".intval($_GET['user_id'])."'")) a_error("Пользователь не найден!");
                
		switch ($_GET['action']) {
			case 'ok':
				$this->db->query("UPDATE #__users SET
					account = 'active'
					WHERE
					user_id = '$user_moderate[user_id]'
				");
						
				// Генерация и отправка письма
				$msg = file_get_contents(ROOT.'data_files/email_templates/reg_moderation.tpl');

				$msg = str_replace('{SYSTEM_TITLE}', $this->config['system']['system_title'], $msg);
				$msg = str_replace('{TEXT}', 'успешно прошел', $msg);
		
				// Отправка пиьсма
				main::send_mail($this->config['system']['system_email'], $user_moderate['email'], 'Модерация аккаунта на '.$this->config['system']['system_title'], $msg);
                        
				a_notice('Пользователь успешно промодерирован', a_url('user/admin/moderate'));
			break;
                
			case 'cancel':
				$this->db->query("UPDATE #__users SET
					account = 'block'
					WHERE
					user_id = '$user_moderate[user_id]'
				");
						
				// Генерация и отправка письма
				$msg = file_get_contents(ROOT.'data_files/email_templates/reg_moderation.tpl');

				$msg = str_replace('{SYSTEM_TITLE}', $this->config['system']['system_title'], $msg);
				$msg = str_replace('{TEXT}', 'не прошел', $msg);
		
				// Отправка пиьсма
				main::send_mail($this->config['system']['system_email'], $user_moderate['email'], 'Модерация аккаунта на '.$this->config['system']['system_title'], $msg);
                        
				a_notice('Пользователь успешно заблокирован', a_url('user/admin/moderate'));
			break;
                
			default:
				a_error('Действие не выбрано');
			break;
			}
		}
            
		// Кол-во пользователей на страницу
		$this->per_page = 20;

		// Получение данных
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM #__users
			WHERE 1 = 1 AND account = 'moderate' AND pin_code = '' 
		ORDER BY reg_time DESC LIMIT $this->start, $this->per_page";

		$users = $this->db->get_array($sql);
		$total = $this->db->get_one("SELECT FOUND_ROWS()");

		// Пагинация
		$pg_conf['base_url'] = a_url('user/admin/list_moderate', 'start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'users' => $users,
			'total' => $total,
			'db'    => $this->db,
			'start' => $this->start,
			'pagination' => $pg->create_links()
		));

		$this->tpl->display('list_moderate');
	}
}
?>