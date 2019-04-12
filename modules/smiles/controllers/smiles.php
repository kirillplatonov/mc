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
 * Контроллер модуля смайлов и ббкода
 */
class Smiles_Controller extends Controller
{

    /**
     * Метод по умолчанию
     */
    public function action_index()
    {
        $this->action_list_smiles();
    }

    /**
     * ББкоды
     */
    public function action_bbcode()
    {
        $this->tpl->display('bbcode');
    }

    /**
     * Смайлы
     */
    public function action_list_smiles()
    {
        # Получение данных
        $group = true;
        $smiles = $this->db->get_array("SELECT SQL_CALC_FOUND_ROWS *
  			FROM #__smiles " . ($group ? 'GROUP BY image ' : '') . " LIMIT $this->start, $this->per_page
  		");

        $total = $this->db->get_one("SELECT FOUND_ROWS()");

        # Пагинация
        $pg_conf['base_url'] = a_url('smiles/list_smiles', 'return_name=' . urlencode($_GET['return_name']) . '&amp;return_url=' . urlencode(str_replace('&amp;', '&', html_entity_decode($_GET['return_url']))) . '&amp;start=');
        $pg_conf['total_rows'] = $total;
        $pg_conf['per_page'] = $this->per_page;

        a_import('libraries/pagination');
        $pg = new CI_Pagination($pg_conf);

        $this->tpl->assign(array(
            'smiles' => $smiles,
            'total' => $total,
            'pagination' => $pg->create_links()
        ));

        $this->tpl->display('list_smiles');
    }

}

?>