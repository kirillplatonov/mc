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

//---------------------------------------------

/**
 * Контроллер пользовательской части модуля главной страницы
 */
class Index_Page_Controller extends Controller
{

    /**
     * Уровень пользовательского доступа
     */
    public $access_level = 0;

    /**
     * Метод по умолчанию
     */
    public function action_index()
    {
        $this->action_view_page();
    }

    /**
     * Показ главной страницы
     */
    public function action_view_page()
    {
        $blocks = $this->cache->get('index_page', 180);
        if (empty($blocks)) {
            $result = $this->model->getPageBlocks();
            $blocks = array();
            while ($block = $this->db->fetch_array($result)) {
                $result1 = $this->model->getPageWidgets($block['block_id']);
                $block['widgets'] = array();
                while ($widget = $this->db->fetch_array($result1)) {
                    # Подключаем класс виджета
                    if (!class_exists($widget['module'] . '_widget'))
                        a_import('modules/' . $widget['module'] . '/helpers/' . $widget['module'] . '_widget.php');
                    # Получаем display виджета
                    $block['widgets'][] = call_user_func(array($widget['module'] . '_widget', 'display'), $widget['widget_id']);
                }

                $blocks[] = $block;
            }

            $this->cache->set('index_page', $blocks);
        }

        $this->tpl->assign(array(
            'blocks' => $blocks
        ));

        $this->tpl->display('view_page');
    }

}

?>