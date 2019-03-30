<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Index_Page_Model
 *
 * @author Олег
 */
class Index_Page_Model extends model {

    public function getPageBlocks() {
        return $this->db->query("SELECT * FROM #__index_page_blocks ORDER BY position ASC");
    }

    public function getPageWidgets($blockId) {
        return $this->db->query("SELECT * FROM #__index_page_widgets WHERE block_id = '" . $blockId . "' ORDER BY position ASC");
        ;
    }

}
