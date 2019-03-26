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

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа</b>');

/**
 * Контроллер пользователей
 */
class User_Controller extends Controller {
	/**
	 * Уровень пользовательского доступа
	 */
	protected $access_level = 0;

	/**
	 * Construct
	 */
	public function __construct() {
		parent::__construct();

		$tpl = Registry::get('tpl');

		// Проверка бана пользователя
		if (is_user() && $this->ban = $this->db->get_row("SELECT * FROM #__users_ban WHERE user_id = '".USER_ID."' AND status = 'enable'") AND empty($_SESSION['check_user_id'])) {
			// Если время бана истекло
			if ($this->ban['to_time'] <= TIME()) {
				$this->db->query("UPDATE #__users_ban SET status = 'disable' WHERE ban_id = '".$this->ban['ban_id']."'");
			}
			else {
				$this->ban['to_time'] = $this->ban['to_time'] - TIME();

				$tpl->assign(array(
					'ban' => $this->ban
				));

				$tpl->display('ban');
				exit;
			}

		}

		// Бан по IP
		if ($ip_ban = $this->db->get_row("SELECT * FROM #__ip_ban WHERE ip = '".a_safe($_SERVER['REMOTE_ADDR'])."'") AND empty($_SESSION['check_user_id'])) {
			$this->display('ip_ban');
			exit;
		}

		if (!$_SESSION['login_errors']) $_SESSION['login_errors'] = 0;
	}

	/**
	 * Метод по умолчанию
	 */
	public function action_index() {
		if (is_user()) redirect('user/profile');
		else $this->action_login();
	}

	/**
	 * Просмотр анкеты по логину
	 */
	public function action_view() {
		// Определяем пользователя по логину
		if (ROUTE_ACTION == 'view' && !empty($_GET['username'])) $user_id = $this->db->get_one("SELECT user_id FROM #__users WHERE username = '".a_safe($_GET['username'])."'");
		else redirect('');

		if ($user_id == 0) a_error('Анкета пользователя не найдена.');

		redirect('user/profile/view.php?user_id='.$user_id);
	}

	/**
	 * Регистрация пользователей
	 */
	public function action_registration() {
		// Перенаправление авторизированного пользователя
		if (is_user()) redirect('user/profile');

		// Закрытие регистрации
		if ($this->config['user']['registration_stop'] == 1) a_error('Регистрация на сайте закрыта.', URL);

		if (isset($_POST['submit'])) {
			// Проверка введенных данных
			if ( ! main::check_input($_POST['username'], 'LOGIN')) $this->error .= 'Неверно указан логин, формат правильного: '. main::check_input('', 'LOGIN', 'format') .'<br />';

			if ( ! main::check_input($_POST['email'], 'MAIL')) $this->error .= 'Неверно указан e-mail адрес, формат правильного: '. main::check_input('', 'MAIL', 'format') .'<br />';

			if ( ! main::check_input($_POST['password'], 'PASSWORD')) $this->error .= 'Неверно указан пароль, формат правильного: '. main::check_input('', 'PASSWORD', 'format') .'<br />';

			if ($_POST['password'] != $_POST['password2']) $this->error .= 'Пароли не совпадают<br />';

			// Проверка имени пользователя на занятость
			if ($this->db->get_one("SELECT user_id FROM #__users WHERE username = '". a_safe($_POST['username']) ."'")) $this->error .= 'Указанное имя пользователя уже занято<br />';

			// Проверка e-mail на занятость
			if ($this->db->get_one("SELECT user_id FROM #__users WHERE email = '". a_safe($_POST['email']) ."'")) $this->error .= 'Указанный e-mail адрес уже занят<br />';

			if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['password2'])) $this->error = 'Заполнены не все обязательные поля<br />';

			// Проверка кода с картинки
			if ($this->config['user']['registration_captcha'] == 1) if ($_POST['captcha_code'] != $_SESSION['captcha_code']) $this->error .= 'Неверно указан код с картинки<br />';

			if ( ! $this->error) {
				// Добавление пользователя в базу данных
				$this->db->query("INSERT INTO #__users SET
                    username = '". a_safe($_POST['username']) ."',
                    email = '". a_safe($_POST['email']) ."',
                    password = '". md5(md5($_POST['password'])) ."',
                    reg_time = UNIX_TIMESTAMP(),
                    last_visit = UNIX_TIMESTAMP()
                ");

				$user_id = $this->db->insert_id();

				// Создание профиля пользователя
				$this->db->query("INSERT INTO #__users_profiles SET user_id = '$user_id'");

				// Генерация и отправка письма о регистрации
				$msg = file_get_contents(ROOT .'data_files/email_templates/reg_message.tpl');

				$msg = str_replace('{SYSTEM_TITLE}', $this->config['system']['system_title'], $msg);
				$msg = str_replace('{USERNAME}', $_POST['username'], $msg);
				$msg = str_replace('{PASSWORD}', $_POST['password'], $msg);

				// Подключение email библиотеки
				a_import('libraries/email');
				$this->email = new Mail('utf-8');

				$this->email->From($this->config['system']['system_email']);
				$this->email->To($_POST['email']);

				$this->email->Subject('Регистрация на сайте '.$this->config['system']['system_title']);
				$this->email->Body($msg);

				$this->email->Send();

				unset($this->email);
				unset($msg);

				// Отправка пользователя на модерацию
				if ($this->config['user']['email_confirmation'] == 1 || $this->config['user']['user_moderate'] == 1) {
					$this->db->query("UPDATE #__users SET
                        account = 'moderate'
                        WHERE user_id = '$user_id'
                    ");
				}

				// Подтверждение email
				if ($this->config['user']['email_confirmation'] == 1) {
					$pin_code = main::get_unique_code(7);

					$this->db->query("UPDATE #__users SET
                        pin_code = '". md5(md5($pin_code)) ."',
                        pin_code_time = UNIX_TIMESTAMP()
                        WHERE user_id = '$user_id'
                     ");

					// Генерация письма с подтверждением
					$msg = file_get_contents(ROOT .'data_files/email_templates/reg_confirmation.tpl');

					$msg = str_replace('{SYSTEM_TITLE}', $this->config['system']['system_title'], $msg);
					$msg = str_replace('{LINK}', a_url('user/email_confirm', 'pin_code='. $pin_code), $msg);
					$msg = str_replace('{PIN_CODE_TIME}', $this->config['system']['pin_code_time'], $msg);

					$this->email1 = new Mail('utf-8');

					$this->email1->From($this->config['system']['system_email']);
					$this->email1->To($_POST['email']);

					$this->email1->Subject('Подтверждение E-mail на '.$this->config['system']['system_title']);
					$this->email1->Body($msg);

					$this->email1->Send();

					// Авторизуем пользователя
					$_SESSION['user_id'] = $user_id;

					a_notice('Вы успешно зарегистрированы. Теперь вы должны подтвердить Ваш E-mail. Для этого перейдите по ссылке, которая была выслана Вам на E-mail.', a_url($this->config['system']['main_menu']));
					exit;
				}

				// Авторизуем пользователя
				$_SESSION['user_id'] = $user_id;

				a_notice('Вы успешно зарегистрированы, на ваш электронный ящик высланы все необходимые данные!', a_url($this->config['system']['main_menu']));
			}
		}

		$this->tpl->assign(array(
			'error' => $this->error,
			'_config' => $this->config['user'],
		));

		$this->tpl->display('registration');
	}

	/**
	 * Восстановление пароля
	 */
	public function action_forgot() {
		// Если пользователь уже авторизирован
		if (is_user()) {
			redirect('user/profile');
		}

		if (isset($_POST['submit'])) {
			if ($user_info = $this->db->get_row("SELECT * FROM #__users WHERE username = '".a_safe($_POST['username'])."' OR email = '".a_safe($_POST['email'])."'")) {
				$pin_code = main::get_unique_code(7);

 				$this->db->query("UPDATE #__users SET
 					pin_code = '". md5(md5($pin_code))."',
 					pin_code_time = UNIX_TIMESTAMP()
					WHERE
 					user_id = '". $user_info['user_id']."'
 				");

				// Генерация и отправка письма
				$msg = file_get_contents(ROOT.'data_files/email_templates/forgot_message.tpl');

				$msg = str_replace('{USERNAME}', $user_info['username'], $msg);
				$msg = str_replace('{PIN_CODE}', $pin_code, $msg);
				$msg = str_replace('{PIN_CODE_TIME}', $this->config['system']['pin_code_time'], $msg);

				a_import('libraries/email');

				$this->email = new Mail('utf-8');

				$this->email->From($this->config['system']['system_email']);
				$this->email->To($user_info['email']);

				$this->email->Subject('Восстановление пароля на '.$this->config['system']['system_title']);
				$this->email->Body($msg);

				$this->email->Send();

				a_notice('На ваш e-mail адрес выслан временный пароль, временный пароль необходимо сменить в течение '.$this->config['system']['pin_code_time'].' часов.', a_url('user'));
			}
			else $this->error .= 'Имя пользователя или e-mail адрес не найдены!<br />';
		}

		$this->tpl->assign(array(
			'action' => 'form',
			'error' => $this->error
		));

		$this->tpl->display('forgot');
	}

	/**
	 * Подтверждение E-mail
	 */
	public function action_email_confirm() {
			// Если пользователь уже авторизирован
			if ( ! is_user()) {
		a_error('Авторизируйтесь и повторите!');
			}

			if ( ! $_GET['pin_code'] && ! $_GET['action'] && $this->config['user']['email_confirmation'] == 1 && $this->user['pin_code'] != '' && $this->user['account'] == 'moderate') {
				a_error('Для того чтобы получить доступ к сайту Вам необходимо подтвердить Ваш E-mail. Для этого перейдите по ссылке, которая была выслана Вам на E-mail ранее.');
			}

			if ($_GET['action'] == 'send_email') {
				$pin_code = main::get_unique_code(7);

				$this->db->query("UPDATE #__users SET
                    pin_code = '". md5(md5($pin_code)) ."',
                    pin_code_time = UNIX_TIMESTAMP()
                    WHERE
                    user_id = '". USER_ID ."'
 		");

				 // Генерация и отправка письма
				$msg = file_get_contents(ROOT .'data_files/email_templates/reg_confirmation.tpl');

				$msg = str_replace('{SYSTEM_TITLE}', $this->config['system']['system_title'], $msg);
				$msg = str_replace('{LINK}', a_url('user/email_confirm', 'pin_code='. $pin_code), $msg);
				$msg = str_replace('{PIN_CODE_TIME}', $this->config['system']['pin_code_time'], $msg);

				a_import('libraries/email');

				$this->email = new Mail('utf-8');

				$this->email->From($this->config['system']['system_email']);
				$this->email->To($user_info['email']);

				$this->email->Subject('Подтверждение E-mail на '.$this->config['system']['system_title']);
				$this->email->Body($msg);

				$this->email->Send();

				a_notice('Вы должны подтвердить Ваш E-mail. Для этого перейдите по ссылке, которая была выслана Вам на E-mail.', a_url($this->config['system']['main_menu']));
			}

			// Проверка времени жизни пин кода
			if ($this->user['pin_code_time'] < time() - intval($this->config['system']['pin_code_time']) * 3600) {
				a_notice('Срок подтверждения E-mail истек. Нажмите "Продолжить", чтобы выслать письмо для подтверждения повторно.', a_url('user/email_confirm', 'action=send_email'));
			}

			// Проверка PIN кода
			if ($this->user['pin_code'] != md5(md5($_GET['pin_code']))) a_error('Неверный PIN код. Проверьте правильность ссылки.');

			// Удаляем PIN код
			$this->db->query("UPDATE #__users SET
                pin_code = '',
                pin_code_time = ''
                WHERE
                user_id = '". USER_ID."'
            ");

			// Меняем статус аккаунта
			if ($this->config['user']['user_moderate'] == 0) {
				$this->db->query("UPDATE #__users SET
                account = 'active'
                WHERE
                user_id = '". USER_ID."'
            ");

				a_notice('Ваш E-mail успешно подтвержден.', a_url($this->config['system']['main_menu']));
				exit;
			}

			a_notice('Ваш E-mail успешно подтвержден. Ваш аккаунт отправлен на модерацию. После прохождения модерации он будет активирован. О прохождении модерации мы сообщим Вам на E-mail.', a_url($this->config['system']['main_menu']));
		}


	/**
	 * Смена пароля
	 */
	public function action_change_password() {
		if (ACCESS_LEVEL < 5) a_error('Запрет доступа!');

		if (isset($_POST['submit'])) {
			main::is_demo();
			# Проверка старого пароля
			if (md5(md5($_POST['password'])) != $this->db->get_one("SELECT password FROM #__users WHERE user_id = '".USER_ID."'")) {
				$this->error .= 'Неверно указан старый пароль!<br />';
			}
			# Проверка нового пароля
			if (!empty($_POST['new_password'])) {
				if (!main::check_input($_POST['new_password'], 'PASSWORD')) {
					$this->error .= 'Неверно указан пароль, формат правильного: '.main::check_input('', 'PASSWORD', 'format').'<br />';
				}
				if ($_POST['new_password'] != $_POST['new_password2']) {
					$this->error .= 'Пароли не совпадают!<br />';
				}
			}

			# Изменяем данные
			if (!$this->error) {
				$this->db->query("UPDATE #__users SET
		 			password = '". md5(md5($_POST['new_password']))."'
		 			WHERE
		 			user_id = '". USER_ID."'
		 		");

				a_notice('Данные успешно изменены!', a_url(MAIN_MENU));
			}
		}

		if (!isset($_POST['submit']) || $this->error) {
			$this->tpl->assign(array(
				'error' => $this->error,
				'success' => $success
			));

			$this->tpl->display('change_password');
		}
	}

	/**
	 * Авторизация пользователей
	 */
	public function action_login() {
		// Если пользователь уже авторизирован
		if (is_user()) {
			redirect('user/profile');
		}

		// Авторизация
		if (isset($_POST['submit']) OR ! empty($_GET['username'])) {
			if (isset($_POST['submit'])) {
				// Фильтрация
				$username = str_safe($_POST['username']);
				$password = $_POST['password'];
			}
			else {
				$username = !empty($_GET['username']) ? $_GET['username'] : '';
				$password = !empty($_GET['password']) ? $_GET['password'] : '';
			}

			// Проверка заполнения
			if (empty($username)) $this->error .= 'Не введено имя пользователя<br />';
			if (empty($password)) $this->error .= 'Не введен пароль<br />';

			if ($_SESSION['login_errors'] > 0 && $this->config['user']['login_captcha'] == 1) {
				// Проверка кода с картинки
				if ($_POST['captcha_code'] != $_SESSION['captcha_code']) $this->error .= 'Неверно указан код с картинки<br />';
			}

			// Проверка наличия пользователя
			if (!$user_id = $this->db->get_one("SELECT user_id FROM #__users WHERE username = '".a_safe($username)."' AND (password = '".md5(md5($password))."' OR (pin_code = '".md5(md5($password))."' AND pin_code_time > UNIX_TIMESTAMP() - ".intval($this->config['system']['pin_code_time'])." * 3600))") AND !$this->error) $this->error .= 'Неверное имя пользователя или пароль<br />';

			if ($user_id AND !$this->error) {
				// Обновление времени последнего визита
				$this->db->query("UPDATE #__users SET last_visit = UNIX_TIMESTAMP() WHERE user_id = $user_id");

				// Авторизация
				$_SESSION['user_id'] = $user_id;

				// Удаляем ошибки авторизации
				$_SESSION['login_errors'] = 0;

				// Если передан параметр то записываем данные в куки
				if (isset($_POST['remember_me'])) {
					setcookie('username', $username, time() + 86400 * 14, '/', '.'. $_SERVER['HTTP_HOST']);
					setcookie('password', md5(md5($password)), time() + 86400 * 14, '/', '.'. $_SERVER['HTTP_HOST']);
				}

				// Перенаправление в кабинет
				redirect('user/profile');
			}
		}

		// Если есть ошибка создаем запись в сессиях
		if ($this->error) {
			$_SESSION['login_errors']++;
		}

		// Форма авторизации
		$this->tpl->assign(array(
			'error' => $this->error,
						'_config' => $this->config['user'],
		));

		$this->tpl->display('login');
	}

	/**
	 * Список польльзователей
	 */
	public function action_list_users() {
	 	# Получение данных
  		$sql = "SELECT SQL_CALC_FOUND_ROWS u.*, up.avatar AS avatar_exists FROM #__users AS u LEFT JOIN #__users_profiles AS up USING(user_id) WHERE user_id != -1 AND account = 'active' ";

  		switch($_GET['type']) {
						// Список пользователей онлайн
  			case 'online':
								$title = 'Пользователи онлайн';
  				$type = 'online';
  				$sql .= " AND last_visit > UNIX_TIMESTAMP() - 180 ";
  			break;

						// Список новых пользователей
  			case 'new':
								$title = 'Новые пользователи';
  				$type = 'new';
  				$sql .= " AND reg_time > UNIX_TIMESTAMP() - 3600 * 24 ";
  			break;

  			default:
								$title = 'Все пользователи';
								$type = 'all';
  			break;
  		}

		$sql .= " ORDER BY rating DESC LIMIT $this->start, $this->per_page";

		$users = $this->db->get_array($sql);
		$total = $this->db->get_one("SELECT FOUND_ROWS()");

		# Пагинация
		$pg_conf['base_url'] = a_url('user/list_users', 'type='.$type.'&amp;start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'title' => $title,
			'users' => $users,
			'type' => $type,
			'total' => $total,
			'pagination' => $pg->create_links()
		));

		$this->tpl->display('list_users');
	}

	/**
	 * Список гостей
	 */
	public function action_list_guests() {
				// Получение данных
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM #__guests
			WHERE 1 = 1 AND last_time > UNIX_TIMESTAMP() - 180
		ORDER BY last_time DESC LIMIT $this->start, $this->per_page";

		$guests = $this->db->get_array($sql);
		$total = $this->db->get_one("SELECT FOUND_ROWS()");

		# Пагинация
		$pg_conf['base_url'] = a_url('user/list_guests', 'start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'guests' => $guests,
			'total' => $total,
			'pagination' => $pg->create_links()
		));

		$this->tpl->display('list_guests');
	}

	/**
	 * Выход
	 */
	public function action_exit() {
		$_SESSION = array();

		setcookie('username', '', time() + 86400 * 14, '/', '.'. $_SERVER['HTTP_HOST']);
		setcookie('password', '', time() + 86400 * 14, '/', '.'. $_SERVER['HTTP_HOST']);

		# Уничтожаем сессию
		session_destroy();

		unset($user_id);

		a_notice('Вы вышли. До свидания!', URL);
	}

	/**
	 * Покинуть панель пользователя и перейти в панель управления
	 */
	public function action_exit_from_user_panel() {
		$_SESSION['check_user_id'] = '';
		a_notice('Переходим в панель управления', a_url('user/admin'), 3);
	}
}
?>