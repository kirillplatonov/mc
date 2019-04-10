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
 * Админская часть управления рекламой
 */
class Ads_Manager_Admin_Controller extends Controller
{

    /**
     * Уровень пользовательского доступа
     */
    public $access_level = 10;

    /**
     * Тема
     */
    public $template_theme = 'admin';

    /**
     * Метод по умолчанию
     */
    public function action_index()
    {
        $this->action_list_links();
    }

    /**
     * Конфигурация модуля
     */
    public function action_config()
    {
        $_config = $this->config['ads_manager'];

        if (isset($_POST['submit'])) {
            main::is_demo();
            $_config = $_POST;

            main::config($_config, 'ads_manager', $this->db);

            a_notice('Данные успешно изменены!', a_url('ads_manager/admin/config'));
        }

        if (!isset($_POST['submit']) || $this->error) {
            $this->tpl->assign(array(
                '_config' => $_config
            ));

            $this->tpl->display('config');
        }
    }

    /**
     * Добавление / редактирование площадки
     */
    public function action_area_edit()
    {
        if (is_numeric($_GET['area_id'])) {
            if (!$area = $this->db->get_row("SELECT * FROM #__ads_manager_areas WHERE area_id = '" . intval($_GET['area_id']) . "'"))
                a_error('Редактируемая площадка не найдена!');
            $action = 'edit';
        } else {
            $area = array();
            $action = 'add';
        }

        if (isset($_POST['submit'])) {
            if (empty($_POST['title'])) {
                $this->error .= 'Укажите название площадки<br />';
            }
            if (!main::check_input($_POST['ident'], 'WORD')) {
                $this->error .= 'Неверный формат идентификатора площадки (только латинские буквы и цифры)<br />';
            }
            if ($action == 'add') {
                if (empty($_POST['ident'])) {
                    $this->error .= 'Укажите идентификатор площадки<br />';
                }
                if ($this->db->get_one("SELECT area_id FROM #__ads_manager_areas WHERE title = '" . a_safe($_POST['title']) . "'")) {
                    $this->error .= 'Площадка с таким названием уже имеется, введите другое!<br />';
                }
                if ($this->db->get_one("SELECT area_id FROM #__ads_manager_areas WHERE ident = '" . a_safe($_POST['ident']) . "'")) {
                    $this->error .= 'Площадка с таким идентификатором уже имеется, введите другой!<br />';
                }
            }

            if (!$this->error) {
                if ($action == 'add') {
                    $this->db->query("INSERT INTO #__ads_manager_areas SET
						title = '" . a_safe($_POST['title']) . "',
						ident = '" . a_safe($_POST['ident']) . "'
					");
                    $message = 'Площадка успешно добавлена!';
                }
                if ($action == 'edit') {
                    $this->db->query("UPDATE #__ads_manager_areas SET
						title = '" . a_safe($_POST['title']) . "'
						WHERE area_id = '" . $area['area_id'] . "'"
                    );
                    $message = 'Площадка успешно изменена!';
                }

                a_notice($message, a_url('ads_manager/admin'));
            }
        }
        if (!isset($_POST['submit']) || $this->error) {
            $this->tpl->assign(array(
                'error' => $this->error,
                'area' => $area,
                'action' => $action
            ));

            $this->tpl->display('area_edit');
        }
    }

    /**
     * Добавление / редактирование ссылки
     */
    public function action_link_edit()
    {
        if (is_numeric($_GET['link_id'])) {
            if (!$link = $this->db->get_row("SELECT * FROM #__ads_manager_links WHERE link_id = '" . intval($_GET['link_id']) . "'"))
                a_error('Редактируемая ссылка не найдена!');
            $action = 'edit';
        } else {
            $link = array();
            $action = 'add';
        }

        if (isset($_POST['submit'])) {
            if (empty($_POST['title'])) {
                $this->error .= 'Укажите заголовок ссылки<br />';
            }
            if (empty($_POST['url'])) {
                $this->error .= 'Укажите URL ссылки<br />';
            }
            if (empty($_POST['names'])) {
                $this->error .= 'Укажите текст ссылки<br />';
            }
            if (!$area = $this->db->get_row("SELECT * FROM #__ads_manager_areas WHERE area_id = '" . a_safe($_POST['area_id']) . "'")) {
                $this->error .= 'Площадка с данным идентификатором не найдена!<br />';
            }

            if (!$this->error) {
                if ($action == 'add') {
                    $position = $this->db->get_one("SELECT MAX(position) FROM #__ads_manager_links WHERE area_id = '" . $area['area_id'] . "'") + 1;

                    $this->db->query("INSERT INTO #__ads_manager_links SET
						title = '" . a_safe($_POST['title']) . "',
						url = '" . a_safe($_POST['url']) . "',
						names = '" . mysqli_real_escape_string($this->db_link, $_POST['names']) . "',
						area_id = '" . $area['area_id'] . "',
						area_ident = '" . $area['ident'] . "',
						position = '" . $position . "'
					");
                    $message = 'Ссылка успешно добавлена!';
                }
                if ($action == 'edit') {
                    $this->db->query("UPDATE #__ads_manager_links SET
						title = '" . a_safe($_POST['title']) . "',
						url = '" . a_safe($_POST['url']) . "',
						names = '" . mysqli_real_escape_string($this->db_link, $_POST['names']) . "',
						area_id = '" . $area['area_id'] . "',
						area_ident = '" . $area['ident'] . "'
						WHERE link_id = '" . $link['link_id'] . "'"
                    );
                    $message = 'Ссылка успешно изменена!';
                }

                a_notice($message, a_url('ads_manager/admin'));
            }
        }
        if (!isset($_POST['submit']) || $this->error) {
            $areas = $this->db->get_array("SELECT * FROM #__ads_manager_areas");

            $this->tpl->assign(array(
                'error' => $this->error,
                'link' => $link,
                'action' => $action,
                'areas' => $areas
            ));

            $this->tpl->display('link_edit');
        }
    }

    /**
     * Удаление площадки
     */
    public function action_area_delete()
    {
        if (!$area = $this->db->query("DELETE FROM #__ads_manager_areas WHERE area_id = '" . intval($_GET['area_id']) . "'"))
            a_error('Площадка не найдена!');

        $this->db->query("DELETE FROM #__ads_manager_areas WHERE area_id = '" . intval($_GET['area_id']) . "'");
        $this->db->query("DELETE FROM #__ads_manager_links WHERE area_id = '" . intval($_GET['area_id']) . "'");

        a_notice('Площадка успешно удалена!', a_url('ads_manager/admin'));
    }

    /**
     * Удаление ссылки
     */
    public function action_link_delete()
    {
        if (!$link = $this->db->query("DELETE FROM #__ads_manager_links WHERE link_id = '" . intval($_GET['link_id']) . "'"))
            a_error('Рекламная ссылка не найдена!');

        $this->db->query("DELETE FROM #__ads_manager_links WHERE link_id = '" . intval($_GET['link_id']) . "'");

        # Меняем позиции
        $this->db->query("UPDATE #__ads_manager_links SET position = position - 1 WHERE area_id = '" . $link['area_id'] . "' AND position > '" . $link['position'] . "'");

        a_notice('Ссылка успешно удалена!', a_url('ads_manager/admin'));
    }

    /**
     * Листинг площадок и ссылок
     */
    public function action_list_links()
    {
        $areas = array();
        $result = $this->db->query("SELECT * FROM #__ads_manager_areas");
        while ($area = $this->db->fetch_array($result)) {
            $area['links'] = array();

            $min_p = $this->db->get_one("SELECT MIN(position) FROM #__ads_manager_links WHERE area_id = '" . $area['area_id'] . "'");
            $max_p = $this->db->get_one("SELECT MAX(position) FROM #__ads_manager_links WHERE area_id = '" . $area['area_id'] . "'");

            $result1 = $this->db->query("SELECT * FROM #__ads_manager_links WHERE area_id = '" . $area['area_id'] . "' ORDER BY position ASC");
            while ($link = $this->db->fetch_array($result1)) {
                if ($link['position'] != $min_p)
                    $link['up'] = '<a href="' . a_url('ads_manager/admin/link_up', 'link_id=' . $link['link_id']) . '">up</a>';
                else
                    $link['up'] = 'up';

                if ($link['position'] != $max_p)
                    $link['down'] = '<a href="' . a_url('ads_manager/admin/link_down', 'link_id=' . $link['link_id']) . '">down</a>';
                else
                    $link['down'] = 'down';

                $ex = explode(PHP_EOL, $link['names']);
                $names = '';
                foreach ($ex as $name)
                    $names .= stripslashes(main::bbcode($name)) . PHP_EOL;
                $link['names'] = $names;

                $area['links'][] = $link;
            }

            $areas[] = $area;
        }

        $this->tpl->assign(array(
            'areas' => $areas
        ));

        $this->tpl->display('list_links');
    }

    /**
     * Поднятие ссылки
     */
    public function action_link_up()
    {
        if (!$link = $this->db->get_row("SELECT * FROM #__ads_manager_links WHERE link_id = " . intval($_GET['link_id'])))
            a_error('Ссылка не найдена!');

        // Меняем позиции
        $this->db->query("UPDATE #__ads_manager_links SET position = " . $link['position'] . " WHERE area_id = '" . $link['area_id'] . "' AND position = " . ($link['position'] - 1));
        $this->db->query("UPDATE #__ads_manager_links SET position = " . ($link['position'] - 1) . " WHERE area_id = '" . $link['area_id'] . "' AND link_id = " . intval($_GET['link_id']));

        header("Location: " . a_url('ads_manager/admin'));
        exit;
    }

    /**
     * Опускание ссылки
     */
    public function action_link_down()
    {
        if (!$link = $this->db->get_row("SELECT * FROM #__ads_manager_links WHERE link_id = " . intval($_GET['link_id'])))
            a_error('Ссылка не найдена!');

        // Меняем позиции
        $this->db->query("UPDATE #__ads_manager_links SET position = " . $link['position'] . " WHERE area_id = '" . $link['area_id'] . "' AND position = " . ($link['position'] + 1));
        $this->db->query("UPDATE #__ads_manager_links SET position = " . ($link['position'] + 1) . " WHERE area_id = '" . $link['area_id'] . "' AND link_id = " . intval($_GET['link_id']));

        header("Location: " . a_url('ads_manager/admin'));
        exit;
    }

}

?>
