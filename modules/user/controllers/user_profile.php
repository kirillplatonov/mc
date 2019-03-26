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
 * Контроллер профайла пользователей
 */
class User_Profile_Controller extends Controller {
	/**
	 * Уровень пользовательского доступа
	 */
	public $access_level = 0;

	/**
	 * Конструктор
	 */
	public function __construct() {
		parent::__construct();
		
		// Получаем id пользователя
		if ((ROUTE_ACTION == 'view' || ROUTE_ACTION == 'index') && is_numeric($_GET['user_id'])) $user_id = intval($_GET['user_id']);
		else $user_id = USER_ID;
	
		// Получаем профайл пользователя
		if ($user_id == -1) a_notice('Гости не имеют анкеты на сайте.', URL);
	
		if (!$this->profile = $this->db->get_row("SELECT * FROM #__users_profiles JOIN #__users USING(user_id) WHERE user_id = '".$user_id."'")) a_error('Анкета пользователя не найдена.');
	}

	/**
	 * Метод по умолчанию
	 */
	public function action_index() {
		// Запрет доступа гостям
		if (USER_ID == -1) {
			redirect('user/login');
		}
		
		// Перенаправление в кабинет
		$this->action_cabinet();
	}

	/**
	 * Просмотр анкеты
	 */
	public function action_view() {
		// Количество сообщений в чате
		if (modules::is_active_module('chat')) {
			$this->profile['chat_messages'] = $this->db->get_one("SELECT COUNT(*) FROM #__chat_messages WHERE user_id = '". $this->profile['user_id'] ."'");
		}
	
		// Количество сообщений в форуме
		if (modules::is_active_module('forum')) {
			$this->profile['forum_messages'] = $this->db->get_one("SELECT COUNT(*) FROM #__forum_messages WHERE user_id = '". $this->profile['user_id'] ."'");
		}
	
		// Количество сообщений в гостевой
		if (modules::is_active_module('guestbook')) {
			$this->profile['guestbook_messages'] = $this->db->get_one("SELECT COUNT(*) FROM #__guestbook WHERE user_id = '". $this->profile['user_id'] ."'");
		}
			
		// Количество фотоальбомов
		if(modules::is_active_module('photo')) {
			$this->profile['photo'] = $this->db->get_one("SELECT COUNT(*) FROM #__photo WHERE user_id = '". $this->profile['user_id'] ."'");
	  		$this->profile['photo_albums'] = $this->db->get_one("SELECT COUNT(*) FROM #__photo_albums WHERE user_id = '". $this->profile['user_id'] ."'");
		}

		// Счетчик блогов
		if(modules::is_active_module('blog')) {
			$this->profile['blog'] = $this->db->get_one("SELECT COUNT(*) FROM #__blog WHERE user_id = '". $this->profile['user_id'] ."'");
		}
	
		$this->tpl->assign(array(
			'profile' => $this->profile
		));
	
		$this->tpl->display('profile_view');
	}

	/**
	 * Кабинет пользователя
	 */
	public function action_cabinet() {
		// Запрет доступа гостям
		if (USER_ID == -1)
			a_error('Для доступа к этой странице <a href="'.a_url('user/login').'">авторизируйтесь</a> или <a href="'.a_url('user/registration').'">зарегистрируйтесь</a> зарегистрируйтесь на сайте.');
		
		
		$info = array();

		if (modules::is_active_module('tickets')) $info['new_tickets'] = $this->db->get_one("SELECT COUNT(*) FROM #__tickets WHERE user_id = '".USER_ID."' AND status_for_user = 'new'");

		$this->tpl->assign(array(
			'error' => $this->error,
			'info' => $info
		));

		$this->tpl->display('profile_cabinet');
	}

	/**
	 * Редактирование профиля
	 */
	public function action_edit() {
		// Запрет доступа гостям
		if (USER_ID == -1)
			a_error('Для доступа к этой странице <a href="'.a_url('user/login').'">авторизируйтесь</a> или <a href="'.a_url('user/registration').'">зарегистрируйтесь</a> зарегистрируйтесь на сайте.');
		
		if (!class_exists('main_form')) a_import('modules/main/helpers/main_form');
		
		if (isset($_POST['submit'])) {
			if (!empty($_POST['birthday_day'])) {
				if ($_POST['birthday_day'] < 1 || $_POST['birthday_day'] > 31 || !is_numeric($_POST['birthday_day'])) $this->error .= 'Неверно указан день даты рождения (от 1 до 31)<br />';
					
				if ($_POST['birthday_month'] < 1 || $_POST['birthday_month'] > 12 || !is_numeric($_POST['birthday_month'])) $this->error .= 'Неверно указан месяц даты рождения (от 1 до 12)<br />';
				
				if ($_POST['birthday_year'] < 1900 || $_POST['birthday_year'] > 2010 || !is_numeric($_POST['birthday_year'])) $this->error .= 'Неверно указан год даты рождения (от 1900 до 2010)<br />';
			}

			if (!empty($_POST['uin'])) {
				if (!is_numeric($_POST['uin']) or $_POST['uin'] < 10000 or $_POST['uin'] > 999999999) $this->error .= 'Неверный номер ICQ, только цифры, от 5ти до 9ти цифр<br />';
			}
			
			if (!empty($_POST['homepage'])) {
				# Проверяем домашнюю страницу
				$homepage = str_replace('http://', '', $_POST['homepage']);
				$homepage_contents = @file_get_contents('http://'.$homepage);

				if (empty($homepage_contents)) $this->error .= 'Домашняя страница не найдена, либо в данный момент она не доступна<br />';
			}
			
			if (!empty($_POST['sex'])) {
				if ($_POST['sex'] != 'm' && $_POST['sex'] != 'w') $this->error .= 'Пол не определен!<br />';
				else $sex = $_POST['sex'];
			}

			if (!$this->error) {
				# Работа с авой
				if (!empty($_FILES['avatar']['tmp_name'])) {
					if (!strstr($_FILES['avatar']['type'], 'image/')) a_error("Неверный формат файла аватара! Разрешены только gif, jpg и png");
				
					# Копируем аватар 100х100
					main::image_resize($_FILES['avatar']['tmp_name'], ROOT.'files/avatars/'.USER_ID.'_100.jpg', 100, 100, 90);
				
					# Копируем аватар 32х32
					main::image_resize($_FILES['avatar']['tmp_name'], ROOT.'files/avatars/'.USER_ID.'_32.jpg', 32, 32, 90);
					$this->profile['avatar'] = 1;
				}
				# Генерируем время рождения
				$birthday_time = mktime(0, 0, 0, $_POST['birthday_month'], $_POST['birthday_day'], $_POST['birthday_year']);
            	
				# Сохраняем данные
				$this->db->query("UPDATE #__users_profiles SET
					birthday_time = '$birthday_time',
					real_name = '".a_safe($_POST['real_name'])."',
					about = '". a_safe($_POST['about'])."',
					avatar = '". $this->profile['avatar']."',
					uin = '". a_safe($_POST['uin'])."',
					homepage = '". a_safe($homepage)."',
					sex = '". a_safe($sex)."'
					WHERE user_id = '". USER_ID."'
				");

				a_notice("Данные сохранены!", a_url('user/profile'));
			}
		}
		if (!isset($_POST['submit']) || $this->error) {
			$select_date_birthday = array(
				'prefix' => 'birthday_',
				'field_order' => 'DMY',
				'start_year' => 1900,
				'time' => $this->profile['birthday_time'] > 0 ? $this->profile['birthday_time'] : 0
			);

			$this->tpl->assign(array(
				'error' => $this->error,
				'select_date_birthday' => $select_date_birthday,
				'profile' => $this->profile
			));
	
			$this->tpl->display('profile_edit');
		}
	}

	/**
	 * Изменение репутации
	 */
	public function action_change_reputation() {
		// Запрет доступа гостям
		if (USER_ID == -1)
			a_error('Для доступа к этой странице <a href="'.a_url('user/login').'">авторизируйтесь</a> или <a href="'.a_url('user/registration').'">зарегистрируйтесь</a> зарегистрируйтесь на сайте.');
		
		switch ($_GET['type']) {
			# плюс
			case 'plus':
				$type = 'plus';
				$name = 'плюс';
				$sql = "reputation_plus = reputation_plus + 1";
				break;
			# минус
			case 'minus':
				$type = 'minus';
				$name = 'минус';
				$sql = "reputation_minus = reputation_minus + 1";
				break;
			default:
				a_error('Не известный тип изменения репутации!');
		}

		if (!$user_to_id = $this->db->get_one("SELECT * FROM #__users WHERE user_id = '".intval($_GET['user_id'])."'"))
			a_error("Пользователь не найден!");
	
		if ($user_to_id == USER_ID)
			a_error("Нельзя изменять репутацию самому себе!");
	
		if ($this->user['rating'] < 50)
			a_notice("Вы не имеете права изменять репутацию пока не наберете не менее 50 баллов рейтинга", a_url('user/profile/view', 'user_id='.$_GET['user_id']));
	
		if ($this->db->get_one("SELECT user_to_id FROM #__users_reputation_logs WHERE user_id = '".USER_ID."' AND user_to_id = '".$user_to_id."'"))
			a_error("Вы изменяли репутацию данного пользователя ранее");

		# Изменяем репутацию пользователя
		$this->db->query("UPDATE #__users SET $sql WHERE user_id = '".$user_to_id."'");
		# Добавляем запись в логи
		$this->db->query("INSERT INTO #__users_reputation_logs SET user_id = '".USER_ID."', user_to_id = '".$user_to_id."'");
	
		# Отправляем сообщение пользователю
		$this->db->query("INSERT INTO #__private_messages SET
			user_id = '$user_to_id',
			user_to_id = '$user_to_id',
			user_from_id = '".USER_ID."',
			message = 'Я поставил вам <b>$name</b>',
			folder = 'new',
			time = UNIX_TIMESTAMP()
		");

		a_notice('Репутация пользователя успешно изменена!', a_url('user/profile/view', 'user_id='.$user_to_id));
	}

	/**
	 * Автологин
	 */
	public function action_autologin() {
		if (USER_ID == -1) {
			redirect('user/login');
		}
		
		$this->tpl->display('profile_autologin');
	}
	
	/**
	 * Настройки
	 */
	public function action_settings() {
		if (USER_ID == -1) redirect('user/login');
		
		if (!class_exists('main_form')) a_import('modules/main/helpers/main_form');
		
		switch ($_GET['action']) {
			default:
			case 'main':
				$action = 'main';
				$title = 'Основные настройки';
				
				if ($_POST['submit']) {
					// Проверка темы оформления
					//if ( ! file_exists(ROOT .'views/'. $_POST['theme'] .'/theme.ini') || ! file_exists(ROOT .'views/'. $_POST['theme'] .'/footer.tpl') || ! file_exists(ROOT .'views/'. $_POST['theme'] .'/header.tpl')) $this->error .= 'Выбранная тема оформления не работает. Обратитесь к администратору!<br />';
					
					// Проверка формата возраста
					if (!empty($_POST['birthday_day'])) {
						if ($_POST['birthday_day'] < 1 || $_POST['birthday_day'] > 31 || !is_numeric($_POST['birthday_day'])) $this->error .= 'Неверно указан день даты рождения (от 1 до 31)<br />';
					
						if ($_POST['birthday_month'] < 1 || $_POST['birthday_month'] > 12 || !is_numeric($_POST['birthday_month'])) $this->error .= 'Неверно указан месяц даты рождения (от 1 до 12)<br />';
				
						if ($_POST['birthday_year'] < 1900 || $_POST['birthday_year'] > 2010 || !is_numeric($_POST['birthday_year'])) $this->error .= 'Неверно указан год даты рождения (от 1900 до 2010)<br />';
					}
			
					// Пол
					if (!empty($_POST['sex'])) {
						if ($_POST['sex'] != 'm' && $_POST['sex'] != 'w') $this->error .= 'Пол не определен!<br />';
						else $sex = $_POST['sex'];
					}
					
					// Реальное имя
					if (!empty($_POST['real_name']) && main::strlen($_POST['real_name']) > 20) $this->error .= 'Имя не должно быть длинее 32 символов!<br />';
					
					// О себе
					if (!empty($_POST['about']) && main::strlen($_POST['real_name']) > 20) $this->error .= 'О себе можно писать не более 3000 символов!<br />';
					
					if (!$this->error) {
						// Работа с аватаркой
						if (!empty($_FILES['avatar']['tmp_name'])) {
							if (!strstr($_FILES['avatar']['type'], 'image/')) a_error("Неверный формат файла аватара! Разрешены только gif, jpg и png");
							
							$info = getimagesize($_FILES['avatar']['tmp_name']);
				
							// Копируем аватар 150x150
							if ($info[0] > 150) main::image_resize($_FILES['avatar']['tmp_name'], ROOT.'files/avatars/'.USER_ID.'_150.jpg', 150);
							elseif ($info[1] > 150) main::image_resize($_FILES['avatar']['tmp_name'], ROOT.'files/avatars/'.USER_ID.'_150.jpg', 0, 150);
							else copy($_FILES['avatar']['tmp_name'], ROOT.'files/avatars/'.USER_ID.'_150.jpg');
							
							main::image_resize($_FILES['avatar']['tmp_name'], ROOT.'files/avatars/'.USER_ID.'_30.jpg', 30, 30);
						}
						
						// Генерируем время рождения
						$birthday_time = mktime(0, 0, 0, $_POST['birthday_month'], $_POST['birthday_day'], $_POST['birthday_year']);
            	
						// Сохраняем данные
						$this->db->query("UPDATE #__users_profiles SET
							birthday_time = '$birthday_time',
							real_name = '".a_safe($_POST['real_name'])."',
							about = '". a_safe($_POST['about'])."',
							avatar = '". $this->profile['avatar']."',
							sex = '". a_safe($sex)."'
						WHERE user_id = '". USER_ID."'
						");
						
						$this->db->query("UPDATE #__users SET
							theme = '". a_safe($_POST['theme'])."'
						WHERE user_id = '". USER_ID."'
						");

						a_notice("Настройки успешно сохранены.", a_url('user/profile/settings', 'action='.$action));
					}
				}
				
				// Получаем WAP темы
				/*$themes = array();
				
				$dir = opendir(ROOT .'views');
				
				while ($theme = readdir($dir)) {
					if ($theme == '.' || $theme == '..' || strpos($theme, 'admin') === 0) continue;
					
					if (file_exists(ROOT .'views/'. $theme .'/theme.ini')) {
						$theme_info = parse_ini_file(ROOT .'views/'. $theme .'/theme.ini');
						
						if ( ! empty($theme_info['title'])) {
							if (strpos($theme, 'web') === 0) $theme_info['title'] .= ' (WEB)';
						
							$themes[] = $theme_info;
						}
					}
				}*/
			break;
		
			case 'profile':
				$action = 'profile';
				$title = 'Настройки анкеты';
				
				if ($_POST['submit']) {
					if (!$this->error) {
						// Сохраняем данные
						$this->db->query("UPDATE #__users_profiles SET
							country = '". a_safe($_POST['country'])."',
							sity = '". a_safe($_POST['sity'])."',
							hobbi = '". a_safe($_POST['hobbi'])."',
							mobile = '". a_safe($_POST['mobile'])."',
							provider = '". a_safe($_POST['provider'])."',
							homepage = '". a_safe($_POST['homepage'])."',
							uin = '". a_safe($_POST['uin'])."',
							skype = '". a_safe($_POST['skype'])."',
							jabber = '". a_safe($_POST['jabber'])."'
						WHERE user_id = '". USER_ID."'
						");
						
						a_notice("Данные успешно сохранены.", a_url('user/profile/settings', 'action='.$action));
					}
				}
			break;
		}
		
		$select_date_birthday = array(
			'prefix' => 'birthday_',
			'field_order' => 'DMY',
			'start_year' => 1900,
			'time' => $this->profile['birthday_time'] > 0 ? $this->profile['birthday_time'] : 0
		);
		
		$this->tpl->assign(array(
			'error' => $this->error,
			'select_date_birthday' => $select_date_birthday,
			'profile' => $this->profile,
			'action' => $action,
			'title' => $title,
			'themes' => $themes,
		));
	
		$this->tpl->display('profile_settings');
	}
}
?>
