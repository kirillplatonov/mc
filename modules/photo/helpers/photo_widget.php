<?php
/**
 * MobileCMS
 *
 * Open source content management system for mobile sites
 *
 * @author MobileCMS Team <support@mobilecms.pro>
 * @copyright Copyright (c) 2011-2019, MobileCMS Team
 * @link https://mobilecms.pro Official site
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

/**
 * Виджет фотоальбомов
 */
class photo_widget {
	/**
	 * Показ виджета
	 */
	public static function display($widget_id) {
		$db = Registry::get('db');
		$albums = $db->get_one("SELECT COUNT(*) FROM #__photo_albums");
		$photos = $db->get_one("SELECT COUNT(*) FROM #__photo");
		
		return '<img src="'.URL.'modules/photo/images/album.png" alt="" /> <a href="'.a_url('photo').'">Фотоальбомы</a> <span class="count">['.$albums.'/'.$photos.']</span><br />';
	}

	/**
	 * Настройка виджета
	 */
	public static function setup($widget) {
  	a_notice('Данный виджет не требует настройки', a_url('index_page/admin'));
	}
}

?>
