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
 * Виджет гостевой книги
 */
class forum_widget {
	/**
	 * Показ виджета
	 */
	public static function display($widget_id) {
  		$db = Registry::get('db');
                
  		$stat = $db->get_row("SELECT
  			(SELECT COUNT(*) FROM #__forum_topics) AS topics,
  			(SELECT COUNT(*) FROM #__forum_messages) AS messages,
                        (SELECT COUNT(*) FROM #__forum_topics WHERE time > UNIX_TIMESTAMP() - 3600 * 24) AS new_topics,
                        (SELECT COUNT(*) FROM #__forum_messages WHERE time > UNIX_TIMESTAMP() - 3600 * 24) AS new_messages
  		");
                
				$text = '<img src="'. URL .'modules/forum/images/forum.png" alt="" /> <a href="'. a_url('forum') .'">Форум</a> <span class="count">['. $stat['topics'] .'/'. $stat['messages'] .']</span>'. ($stat['new_topics'] > 0 || $stat['new_messages'] > 0 ? ' <span class="new">+<a href="'. a_url('forum/viewforum', 'type=new') .'">'. $stat['new_topics'] .'</a>/<a href="'. a_url('forum/new_messages') .'">'. $stat['new_messages'] .'</a></span>' : '') .'<br />';
                
		return $text;
	}

	/**
	 * Настройка виджета
	 */
	public static function setup($widget) {
		a_notice('Данный виджет не требует настройки', a_url('index_page/admin'));
	}
}
?>