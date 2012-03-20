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
 * Виджет модуля пользователей
 */
class user_widget {
	/**
	* Показ виджета
	*/
	public static function display($widget_id) {
  		$db = Registry::get('db');

                // Количество зарегистрированных пользователей
  		$users = $db->get_row("SELECT
                    (SELECT COUNT(*) FROM #__users WHERE user_id != -1 AND account = 'active') AS 'all',
                    (SELECT COUNT(*) FROM #__users WHERE user_id != -1 AND reg_time > UNIX_TIMESTAMP() - 3600 * 24 AND account = 'active') AS 'new'");
                
                $text = '<img src="'. URL .'modules/user/images/users.png" alt="" /> <a href="'. a_url('user/list_users') .'">Пользователи</a> <span class="count">['. $users['all'] .']</span>'. ($users['new'] > 0 ? ' <span class="new"><a href="'. a_url('user/list_users', 'type=new') .'">+'. $users['new'] .'</a></span>' : '') .'<br />';
                
                return $text;
	}

	/**
	* Настройка виджета
	*/
	public static function setup($widget) {
		a_notice('Данный виджет не требует настройки', a_url('index_page/admin'));
	}
}
?>