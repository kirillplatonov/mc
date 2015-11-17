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
class photo_installer {
	/**
	 * Установка модуля
	 */
	public static function install(&$db) {
	$db->query("CREATE TABLE IF NOT EXISTS #__photo_albums (
			`album_id` int(11) NOT NULL auto_increment,
			`user_id` int(11) NOT NULL,
			`name` varchar(30) NOT NULL,
			`about` varchar(3000) NOT NULL,
			PRIMARY KEY  (`album_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		
		$db->query("CREATE TABLE IF NOT EXISTS #__photo (
			`photo_id` int(11) NOT NULL auto_increment,
			`album_id` int(11) NOT NULL,
			`user_id` int(11) NOT NULL,
			`name` varchar(30) NOT NULL,
			`about` varchar(3000) NOT NULL,
			`time` int(11) NOT NULL,
			`rating` int(11) default '0',
      `file_ext` varchar(30) NOT NULL, 
			PRIMARY KEY  (`photo_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		
		$db->query("INSERT INTO #__config (`id`, `module`, `key` , `value`) VALUES
			(NULL , 'photo', 'preview_widht', '150'),
			(NULL , 'photo', 'max_widht', '300'),
			(NULL , 'photo', 'max_size', '5');
		");
		
		if (!is_dir(ROOT.'files/photo')) {
			mkdir(ROOT.'files/photo');
			chmod(ROOT.'files/photo', 0777);
		}		
		
		$rules = 'photo/view/([0-9]*)#segment1=user_id&segment2=album_id&photo_id=$1';

		main::add_route_rules('photo', $rules);
	}

	/**
	 * Деинсталляция модуля
	 */
	public static function uninstall(&$db) {
		$db->query("DROP TABLE #__photo_albums, #__photo");
		
		main::delete_dir(ROOT.'files/photo');
		
		main::delete_route_rules('photo');
	}
}

?>
