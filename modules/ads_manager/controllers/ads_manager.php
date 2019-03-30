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
defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

/**
 * Пользовательский контроллер менеджера продажи рекламы
 */
class Ads_Manager_Controller extends Controller {

    /**
     * Уровень пользовательского доступа
     */
    public $access_level = 0;

    /**
     * Метод по умолчанию
     */
    public function action_index() {
        $this->action_out();
    }

    /**
     * Переход по ссылке
     */
    public function action_out() {
        $this->db->query("UPDATE #__ads_manager_links SET count_all = count_all + 1 WHERE link_id = '" . intval($_GET['link_id']) . "'");

        if ($this->config['ads_manager']['enable_notice'])
            a_notice('Внимание!<br />Вы переходите по рекламе на чужой сайт!<br />Мы не несём ответственность за его содержание!', $_GET['url']);
        else
            header("Location: " . $_GET['url']);
    }

}

?>