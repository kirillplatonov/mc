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
class guestbook_widget
{

    /**
     * Показ виджета
     */
    public static function display($widget_id)
    {
        $db = Registry::get('db');
        $messages = $db->get_one("SELECT COUNT(*) FROM #__guestbook");
        return '<img src="' . URL . 'modules/guestbook/images/widget/guestbook.png" alt="" /> <a href="' . a_url('guestbook') . '">Гостевая книга</a> <span class="count">[' . $messages . ']</span><br />';
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
