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
class lib_installer {
	/**
	 * Установка модуля
	 */
	public static function install($db) {
		$db->query("CREATE TABLE IF NOT EXISTS `a_lib_books` (
			  `book_id` int(11) NOT NULL auto_increment,
			  `directory_id` int(11) NOT NULL,
			  `name` varchar(50) NOT NULL,
			  `description` varchar(100) NOT NULL,
			  `path_to_file` varchar(255) NOT NULL,
			  `time` int(11) NOT NULL,
			  `reads` int(11) NOT NULL,
			  PRIMARY KEY  (`book_id`),
			  KEY `directory_id` (`directory_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		");

		$db->query("CREATE TABLE IF NOT EXISTS `a_lib_directories` (
			  `directory_id` int(11) NOT NULL auto_increment,
			  `parent_id` int(11) NOT NULL,
			  `name` varchar(100) NOT NULL,
			  `position` int(11) NOT NULL,
			  PRIMARY KEY  (`directory_id`),
			  KEY `parent_id` (`parent_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		");

		mkdir(ROOT.'files/lib', 0777);
		chmod(ROOT.'files/lib', 0777);
	}

	/**
	 * Деинсталляция модуля
	 */
	public static function uninstall($db) {
	}
}
?>