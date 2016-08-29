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
 * Хелпер установки модуля
 */
class chat_installer {
	/**
	 * Установка модуля
	 */
	public static function install($db) {
		$db->query("CREATE TABLE IF NOT EXISTS #__chat_messages (
			  `message_id` int(11) NOT NULL auto_increment,
			  `user_id` int(11) NOT NULL,
			  `room_id` tinyint(3) NOT NULL,
			  `message` text NOT NULL,
			  `time` int(11) NOT NULL,
			  PRIMARY KEY  (`message_id`),
			  KEY `user_id` (`user_id`,`room_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Модуль чата, таблица сообщений' AUTO_INCREMENT=1 ;
		");

		$db->query("CREATE TABLE IF NOT EXISTS #__chat_rooms (
			  `room_id` tinyint(3) NOT NULL auto_increment,
			  `position` tinyint(2) NOT NULL,
			  `name` varchar(50) NOT NULL,
			  PRIMARY KEY  (`room_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Модуль чата таблица разделов' AUTO_INCREMENT=1 ;
		");

		$db->query("INSERT INTO #__config (`id` , `module` , `key` , `value` )
			VALUES
			(NULL , 'chat', 'online_time', '10'),
			(NULL , 'chat', 'messages_per_page', '7'),
			(NULL , 'chat', 'message_max_len', '300'),
			(NULL , 'chat', 'guests_in_chat', '0');
		");

		$db->query("ALTER TABLE #__users
			ADD `chat_room_id` TINYINT NOT NULL AFTER `balance` ,
			ADD `chat_last_time` INT NOT NULL AFTER `chat_room_id` ,
			ADD `chat_update` TINYINT NOT NULL AFTER `chat_last_time`
		");
	}

	/**
	 * Деинсталляция модуля
	 */
	public static function uninstall($db) {
		$db->query("DROP TABLE #__chat_messages, #__chat_rooms;");

		$db->query("ALTER TABLE #__users
  			DROP `chat_room_id`,
  			DROP `chat_last_time`,
  			DROP `chat_update`;
  		");

  		$db->query("DELETE FROM #__config WHERE module = 'chat'");
	}
}
?>