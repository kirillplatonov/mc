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
 * Хелпер установки модуля
 */
class guestbook_installer
{

    /**
     * Установка модуля
     */
    public static function install($db)
    {
        $db->query("CREATE TABLE IF NOT EXISTS #__guestbook (
			`message_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`user_id` INT NOT NULL ,
			`message` VARCHAR( 300 ) NOT NULL ,
			`time` INT NOT NULL
			) ENGINE = InnoDB ;
		");
    }

    /**
     * Деинсталляция модуля
     */
    public static function uninstall($db)
    {
        $db->query("DROP TABLE #__guestbook ;");
    }

}

?>
