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
 * Виджет гостевой книги
 */
class lib_widget {
	/**
	 * Показ виджета
	 */
	public static function display($widget_id) {
  		$db = Registry::get('db');
  		$stat = $db->get_row("SELECT COUNT(*) AS all_books, COUNT(CASE WHEN time > UNIX_TIMESTAMP() - 86400 THEN 1 END) AS new_books FROM #__lib_books");
		return '<img src="'.URL.'modules/lib/images/lib.png" alt="" /> <a href="'.a_url('lib').'">Библиотека</a> <span class="count">['.$stat['all_books'].']</span>'.($stat['new_books'] > 0 ? ' <span class="new">+'.$stat['new_books'].'</span>' : '').'<br />';
	}

	/**
	 * Настройка виджета
	 */
	public static function setup($widget) {
		a_notice('Данный виджет не требует настройки', a_url('index_page/admin'));
	}
}
?>