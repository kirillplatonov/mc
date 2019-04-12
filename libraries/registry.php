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
 * Статический класс registry
 */
class Registry
{

    /**
     * Статическое хранилище для данных
     */
    protected static $store = array();

    /**
     * Защита от создания экземпляров статического класса
     */
    protected function __construct()
    {
        
    }

    protected function __clone()
    {
        
    }

    /**
     * Проверяет существуют ли данные по ключу
     *
     * @param string $name
     * @return bool
     */
    public static function exists($name)
    {
        return isset(self::$store[$name]);
    }

    /**
     * Возвращает данные по ключу или null, если не данных нет
     *
     * @param string $name
     * @return unknown
     */
    public static function get($name)
    {
        return (isset(self::$store[$name])) ? self::$store[$name] : null;
    }

    /**
     * Сохраняет данные по ключу в статическом хранилище
     *
     * @param string $name
     * @param mixed $data
     * @return unknown
     */
    public static function set($name, $data)
    {
        return self::$store[$name] = $data;
    }

}
