<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model
 *
 * @author Олег
 */
class model
{

    /**
     *
     * @var MySQL
     */
    protected $db;

    public function __construct()
    {
        $this->db = Registry::get('db');
    }

}
