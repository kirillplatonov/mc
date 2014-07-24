<?php

/**
 * Web version
 *
 * @package
 * @author Platonov Kirill <platonov-kd@ya.ru>
 * @link http://twitter.com/platonov_kd
 */

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

/**
 * Хелпер установки модуля
 */
class web_version_installer {
	/**
	* Установка модуля
	*/
	public static function install(&$db) {
		$db->query("INSERT INTO #__config SET
		    `module` = 'system',
		    `key` = 'web_theme',
		    `value` = 'web_default';
		");

		main::add_event('web_version', 'pre_controller');
	}

	/**
	* Деинсталляция модуля
	*/
	public static function uninstall(&$db) {
		$db->query("DELETE FROM #__config WHERE 
        `key` = 'web_theme';");
		main::delete_event('web_version');
	}
}
?>