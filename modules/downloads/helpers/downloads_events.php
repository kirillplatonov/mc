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


//---------------------------------------------

/**
 * Хелпер событий модуля событий
 */
class downloads_events
{

    /**
     * Перед выполнением контроллера
     */
    public static function pre_controller($db)
    {
        echo 'Событие модуля загрузок вызванное перед контроллером';
    }

}

?>