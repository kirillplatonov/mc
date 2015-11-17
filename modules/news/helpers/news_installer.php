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
class news_installer {
	/**
	 * Установка модуля
	 */
	public static function install(&$db) {
		$db->query("CREATE TABLE #__news (
			  `news_id` int(11) NOT NULL auto_increment,
			  `subject` varchar(100) NOT NULL,
			  `text` text NOT NULL,
			  `time` int(11) NOT NULL,
			  PRIMARY KEY  (`news_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
		");
	}

	/**
	 * Деинсталляция модуля
	 */
	public static function uninstall(&$db) {
		$db->query("DROP TABLE #__news;");
	}
}
?>