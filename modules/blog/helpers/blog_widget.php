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

class blog_widget {
	/**
	* Показ виджета
	*/
	public static function display($widget_id) {
		$db = Registry::get('db');
		$widget = $db->get_row("SELECT * FROM #__index_page_widgets WHERE widget_id = $widget_id");
		$config = parse_ini_string($widget['config']);
		
		// Получение счетчиков
		$counts = $db->get_row("SELECT
			(SELECT COUNT(*) FROM #__blog) AS all_blogs,
			(SELECT COUNT(*) FROM #__blog WHERE time > UNIX_TIMESTAMP() - 86400) AS new_blogs
		");
		
		// Подготовка информации для вывода
		$info = '<img src="'. URL .'modules/blog/images/widget/blog.png" alt="" /> <a href="'. a_url('blog') .'">Блоги</a> ('. $counts['all_blogs'] .')'. ($counts['new_blogs'] > 0 ? ' <span class="new"><a href="'. a_url('blog/list', 'action=new_posts') .'">+'. $counts['new_blogs'] .' new</a></span>' : NULL) .'<br />';

		return $info;
	}

	/**
	* Настройка виджета
	*/
	public static function setup($widget) {
		a_notice('Данный виджет не требует настройки', a_url('index_page/admin'));
	}
}

?>