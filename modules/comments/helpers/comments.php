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
 * Хелпер модуля комментариев
 */
class comments
{

    /**
     * Получение количества комментов
     */
    public static function get_count_comments($db, $module, $item_id)
    {
        $count = $db->get_one("SELECT COUNT(*) FROM #__comments_posts WHERE
            module = '" . a_safe($module) . "' AND
            item_id = '" . intval($item_id) . "'
        ");

        return $count;
    }

}

?>