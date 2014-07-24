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
class blog_installer {
	/**
	* Установка модуля
	*/
	public static function install(&$db) {
		// Добавление таблицы в базу данных
		$db->query("CREATE TABLE IF NOT EXISTS `a_blog` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`user_id` int(11) NOT NULL,
			`title` varchar(256) NOT NULL,
			`message` text NOT NULL,
			`time` int(11) NOT NULL,
			`rating` float NOT NULL,
			`rating_voices` smallint(6) NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");
		
		// Добавление правила роутинга
		$rules  = 'profile/([a-zA-Z0-9]*)/blog#segment1=blog&segment2=view&username=$1'. PHP_EOL;

		main::add_route_rules('blog', $rules);
	}

	/**
	* Деинсталляция модуля
	*/
	public static function uninstall(&$db) {
		// Удаление таблицы из базы данных
		$db->query("DROP TABLE #__blog");
	}
}
?>