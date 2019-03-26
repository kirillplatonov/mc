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
 * Виджет гостевой книги
 */
class chat_widget {
	/**
	 * Показ виджета
	 */
	public static function display($widget_id) {
  		$db = Registry::get('db');
		$config = Registry::get('config');
		
  		$users_online = $db->get_one("SELECT COUNT(*) FROM #__users WHERE chat_last_time >= UNIX_TIMESTAMP() + ". $config['chat']['online_time'] ." * 60 AND user_id != '". USER_ID ."'");
                
				$text = '<img src="'. URL .'modules/chat/images/chat.png" alt="" /> <a href="'. a_url('chat') .'">Чат</a> <span class="count">['. $users_online .']</span><br />';
                
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