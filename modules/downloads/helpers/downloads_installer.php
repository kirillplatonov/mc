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

//---------------------------------------------

/**
 * Хелпер установки модуля
 */
class downloads_installer {
	/**
	 * Установка модуля
	 */
	public static function install($db) {
		$db->query("CREATE TABLE IF NOT EXISTS #__downloads_directories (
			  `directory_id` int(11) NOT NULL auto_increment,
			  `parent_id` int(11) default '0',
			  `name` varchar(30) NOT NULL,
			  `images` enum('yes','no') NOT NULL default 'no',
			  `user_files` enum('yes','no') NOT NULL default 'no',
			  `position` int(11) default '0',
			  PRIMARY KEY  (`directory_id`),
			  KEY `parent_id` (`parent_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");

		$db->query("CREATE TABLE IF NOT EXISTS #__downloads_files (
			  `file_id` int(11) NOT NULL auto_increment,
			  `user_id` int(11) NOT NULL,
			  `directory_id` int(11) default '0',
			  `time` int(11) NOT NULL,
			  `name` varchar(50) NOT NULL,
			  `real_name` varchar(50) NOT NULL,
			  `path_to_file` varchar(100) NOT NULL,
			  `filesize` float NOT NULL,
			  `file_ext` varchar(10) NOT NULL,
			  `about` varchar(1000) NOT NULL,
			  `downloads` int(11) default '0',
			  `screen1` varchar(50) NOT NULL,
			  `screen2` varchar(50) NOT NULL,
			  `screen3` varchar(50) NOT NULL,
			  `add_file_real_name_1` varchar(50) NOT NULL,
			  `add_file_view_name_1` varchar(50) NOT NULL,
			  `add_file_real_name_2` varchar(50) NOT NULL,
			  `add_file_view_name_2` varchar(50) NOT NULL,
			  `add_file_real_name_3` varchar(50) NOT NULL,
			  `add_file_view_name_3` varchar(50) NOT NULL,
			  `add_file_real_name_4` varchar(50) NOT NULL,
			  `add_file_view_name_4` varchar(50) NOT NULL,
			  `add_file_real_name_5` varchar(50) NOT NULL,
			  `add_file_view_name_5` varchar(50) NOT NULL,
			  `status` enum('active','moderate') NOT NULL default 'active',
			  `previews` enum('yes','no') NOT NULL default 'no',
			  PRIMARY KEY  (`file_id`),
			  KEY `dirrectory_id` (`directory_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
		");

		$db->query("INSERT INTO #__config (`id`, `module`, `key` , `value`) VALUES
			(NULL , 'downloads', 'files_prefix', 'site'),
			(NULL , 'downloads', 'files_per_page', '7'),
			(NULL , 'downloads', 'directories_per_page', '50');
		");

		if (!is_dir(ROOT.'files/downloads')) {
			mkdir(ROOT.'files/downloads');
			chmod(ROOT.'files/downloads', 0777);
		}
		if (!is_dir(ROOT.'files/downloads/_ftp_upload')) {
			mkdir(ROOT.'files/downloads/_ftp_upload');
			chmod(ROOT.'files/downloads/ftp_upload', 0777);
		}

		# Добавляем правила в реврайт
		if (!strstr(file_get_contents(ROOT.'.htaccess'), '[DOWNLOADS MODULE]')) {
			$rules  = "\t# [DOWNLOADS MODULE]".PHP_EOL;
			$rules .= "\t# Скачать файл".PHP_EOL;
			$rules .= "\tRewriteRule ^download_file/(.*)?$ index.php?segment1=downloads&segment2=download_file&file=$1 [L,QSA]".PHP_EOL;
			$rules .= "\t# Листинг папок".PHP_EOL;
			$rules .= "\tRewriteRule ^downloads/([0-9*])(/)?$ index.php?segment1=downloads&directory_id=$1 [L,QSA]".PHP_EOL;
			$rules .= "\t# Просмотр файла".PHP_EOL;
			$rules .= "\tRewriteRule ^downloads/view/([0-9*])(/)?$ index.php?segment1=downloads&segment2=view_file&file_id=$1 [L,QSA]".PHP_EOL;
	
			main::add_rewrite_rules($rules);
		}
	}

	/**
	 * Деинсталляция модуля
	 */
	public static function uninstall($db) {
		#$db->query("DROP TABLE #__downloads_directories, #__downloads_files");
	}
}
?>