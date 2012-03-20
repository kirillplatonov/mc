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

//---------------------------------------------

/**
 * Хелпер установки модуля
 */
class private_installer {
	/**
	* Установка модуля
	*/
	public static function install(&$db) {
		$db->query("CREATE TABLE IF NOT EXISTS #__private_messages (
			  `message_id` int(11) NOT NULL auto_increment,
			  `user_id` int(11) NOT NULL,
			  `user_from_id` int(11) NOT NULL,
			  `user_to_id` int(11) NOT NULL,
			  `message` varchar(300) NOT NULL,
			  `folder` enum('new','inbox','outbox','saved') NOT NULL default 'new',
			  `time` int(11) NOT NULL,
			  PRIMARY KEY  (`message_id`),
			  KEY `user_id` (`user_id`,`user_from_id`,`user_to_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		");

		main::add_event('private', 'pre_controller');
	}

	/**
	* Деинсталляция модуля
	*/
	public static function uninstall(&$db) {
		$db->query("DROP TABLE #__private_messages ;");
		main::delete_event('private');
	}
}
?>