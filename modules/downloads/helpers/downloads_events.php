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
 * Хелпер событий модуля событий
 */
class downloads_events {
	/**
	* Перед выполнением контроллера
	*/
	public static function pre_controller(&$db) {
		echo 'Событие модуля загрузок вызванное перед контроллером';
	}
}
?>