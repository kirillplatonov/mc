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


/**
 * Виджет гостевой книги
 */
class forum_widget
{

    /**
     * Показ виджета
     */
    public static function display($widget_id)
    {
        $db = Registry::get('db');

        $stat = $db->get_row("SELECT
  			(SELECT COUNT(*) FROM #__forum_topics) AS topics,
  			(SELECT COUNT(*) FROM #__forum_messages) AS messages,
                        (SELECT COUNT(*) FROM #__forum_topics WHERE time > UNIX_TIMESTAMP() - 3600 * 24) AS new_topics,
                        (SELECT COUNT(*) FROM #__forum_messages WHERE time > UNIX_TIMESTAMP() - 3600 * 24) AS new_messages
  		");

        $text .= '<div class="menu"><img src="' . URL . 'modules/forum/images/forum.png" alt="" /> '
                . '<a href="' . a_url('forum') . '">Форум</a> <span class="count">[' . $stat['topics'] . '/' . $stat['messages'] . ']</span>' . ($stat['new_topics'] > 0 || $stat['new_messages'] > 0 ? ' <span class="new">+<a href="' . a_url('forum/viewforum', 'type=new') . '">' . $stat['new_topics'] . '</a>/<a href="' . a_url('forum/new_messages') . '">' . $stat['new_messages'] . '</a></span>' : '') . '</div>';
        $topics = $db->get_array('SELECT SQL_CALC_FOUND_ROWS ft.*, u.username AS last_username
		  			FROM #__forum_topics AS ft
		  			INNER JOIN #__users AS u ON ft.last_user_id = u.user_id
		  			ORDER BY ft.time DESC
		  			LIMIT 0, 4');
         foreach($topics as $topic){
            $text .= '<div class="menu">'. 
                    ($topic['is_top_topic'] ? '!' : '') . 
                    ($topic['is_close_topic'] ? '#' : '') .
                    '<a href="'. a_url('forum/viewtopic', 'topic_id='. $topic['topic_id']) .'">'. 
                    $topic['name'].'</a> ['. $topic['messages'] .'] '. $topic['last_username'] .
                    '<a href="'.a_url('forum/viewtopic', 'topic_id='. $topic['topic_id']) .'">»</a></div>';
         }
        return $text;
    }

    /**
     * Настройка виджета
     */
    public static function setup($widget)
    {
        a_notice('Данный виджет не требует настройки', a_url('index_page/admin'));
    }

}

?>