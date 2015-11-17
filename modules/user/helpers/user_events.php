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
 * Хелпер событий модуля пользователей
 */
class user_events {
	/**
	 * Перед выполнением контроллера
	 */
	public static function pre_controller(&$db) {
		 $tpl = Registry::get('tpl');
			 $config = Registry::get('config');
	
		# Проверяем наличие пользователя в списке забаненых
		if (USER_ID != -1 && $ban = $db->get_row("SELECT * FROM #__users_ban WHERE user_id = '". USER_ID ."' AND status = 'enable'") AND empty($_SESSION['check_user_id'])) {
			# Если время бана истекло
			if ($ban['to_time'] <= TIME()) {
				$db->query("UPDATE #__users_ban SET status = 'disable' WHERE ban_id = '". $ban['ban_id'] ."'");
			} else {
				header('Location: '. URL .'user/');
			}
			
			# Удаляем мусор
			if ($db->get_one("SELECT COUNT(*) FROM #__users_ban WHERE user_id = '". USER_ID ."' AND status = 'enable' AND ban_id != '". $ban['ban_id'] ."'") != 0) {
				$db->query("DELETE FROM #__users_ban WHERE user_id = '". USER_ID ."' AND status = 'enable' AND ban_id != '". $ban['ban_id'] ."'");
			}
		}
		
		// Бан по IP
		if ($ip_ban = $db->get_row("SELECT * FROM #__ip_ban WHERE ip = '". a_safe($_SERVER['REMOTE_ADDR']) ."'") AND empty($_SESSION['check_user_id'])) {
			exit('<h3>Доступ запрещен</h3><p>Ваш IP адрес забанен!</p>');
		}
                
		// Количество пользователей онлайн
		$users_online = $db->get_one("SELECT COUNT(*) FROM #__users WHERE last_visit > UNIX_TIMESTAMP() - 180");
                
		// Количество гостей онлайн
		$guests_online = $db->get_one("SELECT COUNT(*) FROM #__guests WHERE last_time > UNIX_TIMESTAMP() - 180");
		
		// Количество пользователей на модерации
		$moderation_users = $db->get_one("SELECT COUNT(*) FROM #__users WHERE account = 'moderate' AND pin_code = ''"); 
		
		// Массив пользователей онлайн (для веб версии)
		if ($users_online > 0) {
			$users_array = $db->get_array("SELECT SQL_CALC_FOUND_ROWS user_id, username FROM #__users WHERE user_id != -1 AND account = 'active' AND last_visit > UNIX_TIMESTAMP() - 180 ORDER BY user_id ASC LIMIT 15");
			$tpl->assign('users_online', $users_array);
		}
                
		define('USERS_ONLINE', $users_online);
		define('GUESTS_ONLINE', $guests_online);
		define('MODERATION_USERS', $moderation_users);
                
		$user = $db->get_row("SELECT * FROM #__users LEFT JOIN #__users_profiles USING(user_id) WHERE user_id = '".USER_ID."'");
                
		// Проверка подтверждения E-mail
		if ($config['user']['email_confirmation'] == 1) {
			if ($user['account'] == 'moderate' && $user['pin_code'] != '' && ROUTE_ACTION != 'email_confirm')
				header('Location: '.a_url('user/email_confirm'));
		}
                
		// Проверка модерации пользователя
		if ($config['user']['user_moderate'] == 1 && $user['pin_code'] == '' && ROUTE_ACTION != 'exit') {
			if ($user['account'] == 'moderate')
				define('MODERATE', 'Ваш аккаунт находится на модерации у администрации сайта. После прохождения модерации Вы получите уведомление на E-mail.');
			elseif ($user['account'] == 'block')
				define('MODERATE', 'Ваш аккаунт не прошел модерацию и был заблокирован.');
		}
		
		// Приветствие
		if (date('H') > '05' && date('H') <= '12') $hello = 'Доброе утро';
		elseif (date('H') > '12' && date('H') <= '17') $hello = 'Добрый день';
		elseif (date('H') > '17' && date('H') <= '22') $hello = 'Добрый вечер';
		elseif (date('H') > '22' || date('H') <= '05') $hello = 'Доброй ночи';
		else $hello = 'Здравствуйте';
		
		define('HELLO', $hello);
	}
}

?>