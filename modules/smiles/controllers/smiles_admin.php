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
 * Админская часть модуля формат
 */
class Smiles_Admin_Controller extends Controller {

    /**
     * Уровень пользовательского доступа
     */
    public $access_level = 10;

    /**
     * Тема
     */
    public $template_theme = 'admin';

    /**
     * Конструктор
     */
    public function __construct() {
        parent::__construct();
        if (!class_exists('smiles'))
            a_import('modules/smiles/helpers/smiles');
    }

    /**
     * Метод по умолчанию
     */
    public function action_index() {
        $this->action_list_smiles();
    }

    /**
     * Листинг смайлов
     */
    public function action_list_smiles() {
        $this->per_page = 20;
        # Получение данных
        $group = TRUE;
        $smiles = $this->db->get_array("SELECT SQL_CALC_FOUND_ROWS *
  			FROM #__smiles " . ($group ? 'GROUP BY image ' : '') . " LIMIT $this->start, $this->per_page
  		");

        $total = $this->db->get_one("SELECT FOUND_ROWS()");

        # Пагинация
        $pg_conf['base_url'] = a_url('smiles/admin/list_smiles', 'start=');
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

    /**
     * Обновление смайлов
     */
    public function action_smiles_update() {
        smiles::smiles_update($this->db);
        a_notice("Смайлы успешно обновлены!", a_url('smiles/admin'));
    }

}

?>