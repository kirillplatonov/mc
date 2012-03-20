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
 * Основной хелпер менеджера продажи рекламы
 */
class ads_manager {
	/**
	* Генерация блока со ссылками
	*/
	public static function get_ads_block($area_ident, $start_tag, $end_tag, $delim = '<br />') {
		$ads_manager_links = Registry::get('ads_manager_links');
	
		$code = '';
		if(!empty($ads_manager_links[$area_ident])) {
			$code .= $start_tag;
			foreach($ads_manager_links[$area_ident] as $link) {
				$names = explode(PHP_EOL, $link['names']);
				$key = array_rand($names);
				$name = $names[$key];
				$code .= '<a href="'. a_url('ads_manager/out', 'link_id='. $link['link_id'] .'&amp;url='. $link['url']) .'">'. trim(stripslashes(main::bbcode($name))) .'</a>'. $delim . PHP_EOL;
			}
			$code .= $end_tag;
		}
	
		return $code;
	}

	/**
	* Получение списка ссылок
	*/
	public static function get_links($db) {
		$links = $db->get_array("SELECT * FROM #__ads_manager_links ORDER BY position");
	
		# Преобразуем ссылки в нужный вид
		$ads_manager_links = array();
		foreach($links as $link) {
			$ads_manager_links[$link['area_ident']][] = $link;
		}
	
		return $ads_manager_links;
	}
}
?>