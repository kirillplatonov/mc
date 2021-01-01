SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
//=====================================//
CREATE TABLE IF NOT EXISTS `a_ads_manager_areas` (
  `area_id` int(11) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL,
  `ident` varchar(50) NOT NULL,
  PRIMARY KEY  (`area_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
//=====================================//
INSERT INTO `a_ads_manager_areas` (`area_id`, `title`, `ident`) VALUES
(1, 'Верх всех страниц', 'all_pages_up'),
(2, 'Низ всех страниц', 'all_pages_down');
//=====================================//
CREATE TABLE IF NOT EXISTS `a_blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `message` text NOT NULL,
  `time` int(11) NOT NULL,
  `rating` float NOT NULL,
  `rating_voices` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_ads_manager_links` (
  `link_id` int(11) NOT NULL auto_increment,
  `area_id` int(11) NOT NULL,
  `area_ident` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `names` text NOT NULL,
  `position` int(11) NOT NULL,
  `count_all` int(11) NOT NULL,
  PRIMARY KEY  (`link_id`),
  KEY `area_id` (`area_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;
//=====================================//
INSERT INTO `a_ads_manager_links` (`link_id`, `area_id`, `area_ident`, `title`, `url`, `names`, `position`, `count_all`) VALUES
(3, 2, 'all_pages_down', 'Ссылка снизу', 'https://github.com/kirillplatonov/mc', '[red]MobileCMS download[/red]\r\n[green]MobileCMS repository[/green]\r\n[blue]new MobileCMS[/blue]', 1, 0);
//=====================================//
CREATE TABLE IF NOT EXISTS `a_comments_posts` (
  `comment_id` int(11) NOT NULL auto_increment,
  `module` varchar(30) NOT NULL,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` varchar(300) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`comment_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_config` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(30) NOT NULL,
  `key` varchar(30) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=63 ;
//=====================================//
INSERT INTO `a_config` (`id`, `module`, `key`, `value`) VALUES
(1, 'system', 'system_title', 'MobileCMS'),
(2, 'system', 'system_email', 'mobilecms@mail.ru'),
(3, 'system', 'default_module', 'index_page'),
(4, 'system', 'ext', '.php'),
(5, 'system', 'main_menu', 'user/profile'),
(6, 'system', 'pin_code_time', '72'),
(7, 'system', 'antiflud_time', '20'),
(8, 'system', 'profiler', 'off'),
(9, 'system', 'display_errors', '1'),
(10, 'system', 'admin_theme', 'admin'),
(11, 'system', 'default_theme', 'default'),
(44, 'downloads', 'files_prefix', ''),
(45, 'downloads', 'files_per_page', '7'),
(46, 'downloads', 'directories_per_page', '50'),
(47, 'downloads', 'make_screens_from_video', '1'),
(52, 'forum', 'show_forums_in_list_sections', '0'),
(53, 'forum', 'messages_per_page', '7'),
(54, 'forum', 'topics_per_page', '7'),
(55, 'forum', 'guests_create_topics', '0'),
(56, 'forum', 'guests_write_messages', '0'),
(57, 'downloads', 'allowed_filetypes', 'jpeg;jpg;gif;png;jar;mp3;mid;midi;wav;nth;sis;3gp;mp4;txt;zip;rar'),
(58, 'downloads', 'max_filesize', '5'),
(59, 'ads_manager', 'enable_notice', '0'),
(60, 'system', 'footer_codes_index', ''),
(61, 'system', 'footer_codes_other_pages', ''),
(62, 'system', 'license_key', '');
//=====================================//
CREATE TABLE IF NOT EXISTS `a_downloads_directories` (
  `directory_id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default '0',
  `name` varchar(30) NOT NULL,
  `images` enum('yes','no') NOT NULL default 'no',
  `user_files` enum('yes','no') NOT NULL default 'no',
  `position` int(11) default '0',
  PRIMARY KEY  (`directory_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//=====================================//
INSERT INTO `a_downloads_directories` (`directory_id`, `parent_id`, `name`, `images`, `user_files`, `position`) VALUES
(1, 0, 'Картинки', 'yes', 'no', 1),
(2, 0, 'Мелодии', 'no', 'no', 2),
(3, 0, 'Игры', 'no', 'no', 3),
(4, 0, 'Анимация', 'yes', 'no', 4),
(5, 0, 'Видео', 'no', 'no', 5);
//=====================================//
CREATE TABLE IF NOT EXISTS `a_downloads_files` (
  `file_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `directory_id` int(11) default '0',
  `time` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `real_name` varchar(255) NOT NULL,
  `path_to_file` varchar(100) NOT NULL,
  `filesize` float NOT NULL,
  `file_ext` varchar(10) NOT NULL,
  `screen1` varchar(255) NOT NULL,
  `screen2` varchar(255) NOT NULL,
  `screen3` varchar(255) NOT NULL,
  `about` text NOT NULL,
  `downloads` int(11) default '0',
  `add_file_real_name_1` varchar(255) NOT NULL,
  `add_file_real_name_2` varchar(255) NOT NULL,
  `add_file_real_name_3` varchar(255) NOT NULL,
  `add_file_real_name_4` varchar(255) NOT NULL,
  `add_file_real_name_5` varchar(255) NOT NULL,
  `status` enum('active','moderate') NOT NULL default 'active',
  `previews` enum('yes','no') NOT NULL default 'no',
  `file_info` varchar(1000) NOT NULL,
  `rating` float NOT NULL,
  `rating_voices` smallint(6) NOT NULL,
  PRIMARY KEY  (`file_id`),
  KEY `dirrectory_id` (`directory_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000 ;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_forum_forums` (
  `forum_id` int(11) NOT NULL auto_increment,
  `section_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `topics` int(11) NOT NULL,
  `messages` int(11) NOT NULL,
  PRIMARY KEY  (`forum_id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_forum_messages` (
  `message_id` int(11) NOT NULL auto_increment,
  `topic_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `forum_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `is_first_message` tinyint(1) NOT NULL default '0',
  `is_last_message` tinyint(1) NOT NULL,
  `time` int(11) NOT NULL,
  `edit_editor` varchar(30) NOT NULL,
  `edit_time` int(11) NOT NULL,
  `edit_count` int(11) NOT NULL,
  PRIMARY KEY  (`message_id`),
  KEY `topic_id` (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `section_id` (`section_id`),
  FULLTEXT KEY `message` (`message`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_forum_sections` (
  `section_id` int(11) NOT NULL auto_increment,
  `position` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_forum_topics` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_guestbook` (
  `message_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `message` varchar(300) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`message_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_index_page_blocks` (
  `block_id` smallint(6) NOT NULL auto_increment,
  `title` varchar(30) NOT NULL,
  `position` smallint(6) NOT NULL,
  PRIMARY KEY  (`block_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;
//=====================================//
INSERT INTO `a_index_page_blocks` (`block_id`, `title`, `position`) VALUES
(1, 'Общение', 3),
(4, 'Бесплатные загрузки', 2),
(3, 'Новости', 1),
(5, 'Другое', 4);
//=====================================//
CREATE TABLE IF NOT EXISTS `a_index_page_widgets` (
  `widget_id` int(11) NOT NULL auto_increment,
  `block_id` int(11) NOT NULL,
  `title` varchar(30) NOT NULL,
  `module` varchar(20) NOT NULL,
  `config` varchar(1000) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY  (`widget_id`),
  KEY `block_id` (`block_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;
//=====================================//
INSERT INTO `a_index_page_widgets` (`widget_id`, `block_id`, `title`, `module`, `config`, `position`) VALUES
(1, 1, 'Гостевая книга', 'guestbook', '', 3),
(17, 5, 'Пользователи', 'user', '', 3),
(5, 3, 'Последняя новость', 'news', 'view_last_news = "1"', 1),
(7, 1, 'Форум', 'forum', '', 5),
(10, 4, 'Картинки', 'downloads', 'directory_id = "1"\r\ndirectory_name = "Картинки"', 1),
(11, 4, 'Мелодии', 'downloads', 'directory_id = "2"\r\ndirectory_name = "Мелодии"', 2),
(12, 4, 'Игры', 'downloads', 'directory_id = "3"\r\ndirectory_name = "Игры"', 3),
(13, 4, 'Анимация', 'downloads', 'directory_id = "4"\r\ndirectory_name = "Анимация"', 4),
(14, 4, 'Видео', 'downloads', 'directory_id = "5"\r\ndirectory_name = "Видео"', 5),
(18, 5, 'Библиотека', 'lib', '', 2),
(20, 1, 'Фотоальбомы', 'photo', '', 6),
(21,1,'Блоги','blog','',7);
//=====================================//
CREATE TABLE IF NOT EXISTS `a_lib_books` (
  `book_id` int(11) NOT NULL auto_increment,
  `directory_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `path_to_file` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `reads` int(11) NOT NULL,
  PRIMARY KEY  (`book_id`),
  KEY `directory_id` (`directory_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_lib_directories` (
  `directory_id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY  (`directory_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_modules` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL,
  `title` varchar(30) NOT NULL,
  `admin_link` varchar(30) NOT NULL,
  `description` varchar(100) NOT NULL,
  `installed` tinyint(1) NOT NULL,
  `status` enum('on','off') NOT NULL default 'off',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
//=====================================//
INSERT INTO `a_modules` (`id`, `name`, `title`, `admin_link`, `description`, `installed`, `status`) VALUES
(5, 'downloads', 'Загрузки', 'downloads/admin', 'Модуль загрузок', 1, 'on'),
(7, 'forum', 'Форум', 'forum/admin', 'Модуль форума', 1, 'on'),
(8, 'smiles', 'Смайлы', 'smiles/admin', 'Модуль смайлов и управления ими', 1, 'on'),
(9, 'pages', 'Страницы', 'pages/admin', 'Модуль управления пользовательскими страницами', 1, 'on'),
(12, 'comments', 'Комментарии', '', 'Модуль комментариев', 1, 'on'),
(13, 'news', 'Новости', 'news/admin', 'Модуль новостей', 1, 'on'),
(14, 'guestbook', 'Гостевая книга', '', 'Модуль гостевый книги', 1, 'on'),
(18, 'ads_manager', 'Продажа рекламы', 'ads_manager/admin', '', 1, 'on'),
(20, 'private', 'Личные сообщения', '', 'Модуль личных сообщений', 1, 'on'),
(21, 'html', 'HTML/текстовые вставки', '', 'HTML/текстовые вставки в главную страницу', 1, 'on'),
(22, 'lib', 'Библиотека', 'lib/admin', 'Модуль библиотеки', 1, 'on'),
(23, 'photo', 'Фотоальбомы', '', 'Модуль фотоальбомов', 1, 'on'),
(24, 'blog', 'Блоги', '', 'Модуль блогов', 1, 'on');
//=====================================//
CREATE TABLE IF NOT EXISTS `a_news` (
  `news_id` int(11) NOT NULL auto_increment,
  `subject` varchar(100) NOT NULL,
  `text` text NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`news_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
//=====================================//
INSERT INTO `a_news` (`subject`, `text`, `time`) VALUES
('Установка MobileCMS {CMS_VERSION}', 'Здравствуйте. Вы установили на сайт MobileCMS - профессиональную CMS для создания мобильных сайтов.
 Изменить содержимое этой новости вы можете в панеле управления сайтом, раздел <b>новости</b>. 
Получить техническую поддержку, а также скачать последнюю версию CMS вы всегда можете на официальном репозитории <a href="https://github.com/kirillplatonov/mc">MobileCMS</a>.
 С уважением, разработчики MobileCMS.', UNIX_TIMESTAMP());
//=====================================//
CREATE TABLE IF NOT EXISTS `a_pages` (
  `page_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY  (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_private_messages` (
  `message_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `user_from_id` int(11) NOT NULL,
  `user_to_id` int(11) NOT NULL,
  `message` varchar(300) NOT NULL,
  `folder` enum('new','inbox','outbox','saved') NOT NULL default 'new',
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`message_id`),
  KEY `user_id` (`user_id`,`user_from_id`,`user_to_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_rating_logs` (
  `id` int(11) NOT NULL auto_increment,
  `ip` varchar(20) NOT NULL,
  `module` varchar(50) NOT NULL,
  `item_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`ip`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_smiles` (
  `smile_id` int(11) NOT NULL auto_increment,
  `code` varchar(50) NOT NULL,
  `image` varchar(50) NOT NULL,
  `status` enum('enable','desable') NOT NULL default 'enable',
  PRIMARY KEY  (`smile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;
//=====================================//
INSERT INTO `a_smiles` (`smile_id`, `code`, `image`, `status`) VALUES
(1, 'O:-)', 'aa.gif', 'enable'),
(2, 'O=)', 'aa.gif', 'enable'),
(3, ':-)', 'ab.gif', 'enable'),
(4, ':)', 'ab.gif', 'enable'),
(5, '=)', 'ab.gif', 'enable'),
(6, ':-(', 'ac.gif', 'enable'),
(7, ':(', 'ac.gif', 'enable'),
(8, ';(', 'ac.gif', 'enable'),
(9, ';-)', 'ad.gif', 'enable'),
(10, ';)', 'ad.gif', 'enable'),
(11, ':-P', 'ae.gif', 'enable'),
(12, '8-)', 'af.gif', 'enable'),
(13, ':-D', 'ag.gif', 'enable'),
(14, ':-[', 'ah.gif', 'enable'),
(15, '=-O', 'ai.gif', 'enable'),
(16, ':-*', 'aj.gif', 'enable'),
(17, ':-X', 'al.gif', 'enable'),
(18, ':-x', 'al.gif', 'enable'),
(19, '>:o', 'am.gif', 'enable'),
(20, ':-|', 'an.gif', 'enable'),
(21, ':-/', 'ao.gif', 'enable'),
(22, '*JOKINGLY*', 'ap.gif', 'enable'),
(23, ']:->', 'aq.gif', 'enable'),
(24, '[:-}', 'ar.gif', 'enable'),
(25, '*KISSED*', 'as.gif', 'enable'),
(26, ':-!', 'at.gif', 'enable'),
(27, '*TIRED*', 'au.gif', 'enable'),
(28, '*STOP*', 'av.gif', 'enable'),
(29, '*KISSING*', 'aw.gif', 'enable'),
(31, '*THUMBS UP*', 'ay.gif', 'enable'),
(32, '*DRINK*', 'az.gif', 'enable'),
(33, '*IN LOVE*', 'ba.gif', 'enable'),
(34, '@=', 'bb.gif', 'enable'),
(35, '*HELP*', 'bc.gif', 'enable'),
(37, '%)', 'be.gif', 'enable'),
(38, '*OK*', 'bf.gif', 'enable'),
(39, '*WASSUP*', 'bg.gif', 'enable'),
(40, '*SUP*', 'bg.gif', 'enable'),
(41, '*SORRY*', 'bh.gif', 'enable'),
(42, '*BRAVO*', 'bi.gif', 'enable'),
(43, '*ROFL*', 'bj.gif', 'enable'),
(44, '*LOL*', 'bj.gif', 'enable'),
(45, '*PARDON*', 'bk.gif', 'enable'),
(46, '*NO*', 'bl.gif', 'enable'),
(47, '*CRAZY*', 'bm.gif', 'enable'),
(48, '*DONT_KNOW*', 'bn.gif', 'enable'),
(49, '*UNKNOWN*', 'bn.gif', 'enable'),
(50, '*DANCE*', 'bo.gif', 'enable'),
(51, '*YAHOO*', 'bp.gif', 'enable'),
(52, '*YAHOO!*', 'bp.gif', 'enable'),
(53, '*NEW_PACK*', 'bq.gif', 'enable'),
(54, '*TEASE*', 'br.gif', 'enable'),
(55, '*SALIVA*', 'bs.gif', 'enable'),
(56, '*DASH*', 'bt.gif', 'enable'),
(57, '*WILD*', 'bu.gif', 'enable'),
(58, '*TRAINING*', 'bv.gif', 'enable'),
(59, '*FOCUS*', 'bw.gif', 'enable'),
(60, '*HANG*', 'bx.gif', 'enable'),
(61, '*DANCE*', 'by.gif', 'enable'),
(62, '*DANCE2*', 'bz.gif', 'enable'),
(63, '*MEGA_SHOK*', 'ca.gif', 'enable'),
(64, '*TO_PICK_ONES_NOSE*', 'cb.gif', 'enable'),
(65, '*YU*', 'cc.gif', 'enable');
//=====================================//
CREATE TABLE IF NOT EXISTS `a_users` (
  `user_id` int(11) NOT NULL auto_increment,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `reg_time` bigint(20) NOT NULL,
  `last_visit` bigint(20) NOT NULL,
  `pin_code` varchar(32) default NULL,
  `pin_code_time` bigint(20) NOT NULL,
  `balance` float NOT NULL,
  `rating` float NOT NULL,
  `reputation_plus` smallint(6) NOT NULL,
  `reputation_minus` smallint(6) NOT NULL,
  `status` enum('guest','banned','user','moder','admin') NOT NULL default 'user',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
//=====================================//
INSERT INTO `a_users` (ìd`, user_id`, `username`, `password`, `email`, `reg_time`, `last_visit`, `pin_code`, `pin_code_time`, `balance`, `rating`, `reputation_plus`, `reputation_minus`, `status`) VALUES
(0, 'System', '', '', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 0, 0, 0, 'user'),
(1, '{ADMIN_LOGIN}', '{ADMIN_PASSWORD}', '{ADMIN_EMAIL}', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 0, 0, 0, 0, 0, 'admin'),
 (-1, 'Guest', '', '', 1243421222, 0, '12462c1ac3bdf5f0673611834b405ec7', 1243430310, 10, 0, 0, 0, 'guest');
//=====================================//
CREATE TABLE IF NOT EXISTS `a_users_ban` (
  `ban_id` bigint(20) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `to_time` int(11) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `status` enum('enable','disable') default 'enable',
  PRIMARY KEY  (`ban_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//=====================================//
CREATE TABLE IF NOT EXISTS `a_users_profiles` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `real_name` varchar(20) NOT NULL,
  `birthday_time` int(11) NOT NULL,
  `about` varchar(500) NOT NULL,
  `avatar` tinyint(1) NOT NULL,
  `uin` varchar(15) NOT NULL,
  `homepage` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
INSERT INTO `a_users_profiles` (`id`, `user_id`, `real_name`, `about`, `homepage`, `sex`) 
VALUES ('0', '0', 'Система', 'Системный бот! \r\nСоздана для уведомлений!', 'https://mobilecms.pro', 'w');
//=====================================//
INSERT INTO `a_users_profiles` (`id`, `user_id`, `real_name`, `birthday_time`, `about`, `avatar`, `uin`, `homepage`) VALUES
(1, 1, '', 0, '', 0, '', '');
//=====================================//
CREATE TABLE IF NOT EXISTS `a_users_reputation_logs` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `user_to_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_to_id` (`user_to_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//=====================================//
ALTER TABLE `a_index_page_widgets` CHANGE `config` `config` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
//=====================================//
ALTER TABLE `a_config` CHANGE `value` `value` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
//=====================================//
ALTER TABLE `a_comments_posts` ADD `username` VARCHAR( 50 ) NOT NULL AFTER `user_id`
//=====================================//
ALTER TABLE `a_users_profiles` ADD `sex` ENUM( '', 'm', 'w' ) NOT NULL
//=====================================//
INSERT INTO `a_config` (`id`, `module`, `key`, `value`) VALUES (NULL, 'downloads', 'screens_width', '0');
//=====================================//
INSERT INTO `a_config` (`id`, `module`, `key`, `value`) VALUES (NULL, 'ftp', 'server', ''), (NULL, 'ftp', 'port', ''), (NULL, 'ftp', 'login', ''), (NULL, 'ftp', 'password', ''), (NULL, 'ftp', 'path', '');
//=====================================//
ALTER TABLE `a_forum_topics` CHANGE `name` `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
//=====================================//
INSERT INTO `a_config` (`id`, `module`, `key`, `value`) VALUES (NULL, 'downloads', 'cache_time', '0');
//=====================================//
INSERT INTO `a_config` (`id`, `module`, `key`, `value`) VALUES
(NULL, 'forum', 'allowed_filetypes', 'jpeg;jpg;gif;png;jar;mp3;mid;midi;wav;nth;sis;3gp;mp4;txt;zip;rar'),
(NULL, 'forum', 'max_filesize', '20');
//=====================================//
CREATE TABLE IF NOT EXISTS `a_forum_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_downloads` int(11) NOT NULL,
  PRIMARY KEY (`file_id`),
  KEY `message_id` (`message_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//=====================================//
INSERT INTO a_config SET `module` = 'system', `key` = 'description', `value` = 'MobileCMS - Мобильный движок';
//=====================================//
INSERT INTO a_config SET `module` = 'system', `key` = 'keywords', `value` = 'MobileCMS, MC2, MC';
//=====================================//
INSERT INTO a_config SET `module` = 'system', `key` = 'guestbook_posting', `value` = 'all';
//=====================================//
CREATE TABLE IF NOT EXISTS a_photo_albums (
  `album_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `about` varchar(3000) NOT NULL,
  PRIMARY KEY  (`album_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
//=====================================//
CREATE TABLE IF NOT EXISTS a_photo (
  `photo_id` int(11) NOT NULL auto_increment,
  `album_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `about` varchar(3000) NOT NULL,
  `time` int(11) NOT NULL,
  `rating` int(11) default '0',
  `file_ext` varchar(30) NOT NULL, 
  PRIMARY KEY  (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
//=====================================//
INSERT INTO a_config (`id`, `module`, `key` , `value`) VALUES
(NULL , 'photo', 'preview_widht', '150'),
(NULL , 'photo', 'max_widht', '300'),
(NULL , 'photo', 'max_size', '5');
//=====================================//
INSERT INTO a_config SET `module` = 'system', `key` = 'comments_posting', `value` = 'all';
//=====================================//
ALTER TABLE `a_users` CHANGE `pin_code` `pin_code` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
//=====================================//
ALTER TABLE `a_users` ADD `theme` VARCHAR( 256 ) NOT NULL DEFAULT 'default'
//=====================================//
ALTER TABLE `a_users` ADD `account` enum('active','moderate','block') NOT NULL DEFAULT 'active'
//=====================================//
ALTER TABLE `a_users_profiles` ADD `country` VARCHAR( 256 ) NOT NULL AFTER `sex` ,
ADD `sity` VARCHAR( 256 ) NOT NULL AFTER `country` ,
ADD `hobbi` VARCHAR( 3000 ) NOT NULL AFTER `sity` ,
ADD `mobile` VARCHAR( 256 ) NOT NULL AFTER `hobbi` ,
ADD `provider` VARCHAR( 256 ) NOT NULL AFTER `mobile` ,
ADD `skype` VARCHAR( 256 ) NOT NULL AFTER `provider` ,
ADD `jabber` VARCHAR( 256 ) NOT NULL AFTER `skype`
//=====================================//
ALTER TABLE `a_private_messages` CHANGE `message` `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
//=====================================//
INSERT INTO a_config (`id`, `module`, `key` , `value`) VALUES
(NULL , 'user', 'registration_captcha', '1'),
(NULL , 'user', 'login_captcha', '1'),
(NULL , 'user', 'email_confirmation', '1'),
(NULL , 'user', 'user_moderate', '1'),
(NULL , 'user', 'registration_stop', '0'),
(NULL , 'downloads', 'user_upload', '1'),
(NULL , 'downloads', 'moderation', '0');
//=====================================//
CREATE TABLE IF NOT EXISTS a_guests (
  `id` int(11) NOT NULL auto_increment,
  `ip` varchar(100) NOT NULL,
  `user_agent` varchar(512) NOT NULL,
  `last_time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
//=====================================//
CREATE TABLE IF NOT EXISTS a_ip_ban (
  `id` int(11) NOT NULL auto_increment,
  `ip` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
