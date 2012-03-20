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
class forum_installer {
	/**
	* Установка модуля
	*/
	public static function install(&$db) {
		$db->query("CREATE TABLE IF NOT EXISTS #__forum_forums (
			  `forum_id` int(11) NOT NULL auto_increment,
			  `section_id` int(11) NOT NULL,
			  `position` int(11) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `topics` int(11) NOT NULL,
			  `messages` int(11) NOT NULL,
			  PRIMARY KEY  (`forum_id`),
			  KEY `section_id` (`section_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
		");

		$db->query("CREATE TABLE IF NOT EXISTS #__forum_messages (
			  `message_id` int(11) NOT NULL auto_increment,
			  `topic_id` int(11) NOT NULL,
			  `section_id` int(11) NOT NULL,
			  `forum_id` int(11) NOT NULL,
			  `user_id` int(11) NOT NULL,
			  `message` varchar(1000) NOT NULL,
			  `is_first_message` tinyint(1) NOT NULL default '0',
			  `time` int(11) NOT NULL,
			  PRIMARY KEY  (`message_id`),
			  KEY `topic_id` (`topic_id`),
			  KEY `forum_id` (`forum_id`),
			  KEY `section_id` (`section_id`),
			  FULLTEXT KEY `message` (`message`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
		");

		$db->query("CREATE TABLE IF NOT EXISTS #__forum_sections (
			  `section_id` int(11) NOT NULL auto_increment,
			  `position` int(11) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  PRIMARY KEY  (`section_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
		");

		$db->query("CREATE TABLE IF NOT EXISTS #__forum_topics (
			  `topic_id` int(11) NOT NULL auto_increment,
			  `section_id` int(11) NOT NULL,
			  `forum_id` int(11) NOT NULL,
			  `user_id` int(11) NOT NULL,
			  `name` varchar(30) NOT NULL,
			  `time` int(11) NOT NULL,
			  `last_message_time` int(11) NOT NULL,
			  `last_user_id` int(11) NOT NULL,
			  `messages` int(11) NOT NULL,
			  `is_top_topic` tinyint(1) default '0',
			  `is_close_topic` tinyint(1) default '0',
			  PRIMARY KEY  (`topic_id`),
			  KEY `forum_id` (`forum_id`),
			  KEY `user_id` (`user_id`),
			  KEY `section_id` (`section_id`),
			  KEY `last_user_id` (`last_user_id`),
			  FULLTEXT KEY `name` (`name`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
		");

		$db->query("INSERT INTO #__config (`id` , `module` , `key` , `value` )
			VALUES
			(NULL , 'forum', 'show_forums_in_list_sections', '1'),
			(NULL , 'forum', 'messages_per_page', '7'),
			(NULL , 'forum', 'topics_per_page', '7'),
			(NULL , 'forum', 'guests_create_topics', '0'),
			(NULL , 'forum', 'guests_write_messages', '0');
		");
	}

	/**
	* Деинсталляция модуля
	*/
	public static function uninstall(&$db) {
		$db->query("DROP TABLE #__forum_forums, #__forum_messages, #__forum_sections, #__forum_topics;");
		$db->query("DELETE FROM #__config WHERE module = 'forum'");
	}
}
?>