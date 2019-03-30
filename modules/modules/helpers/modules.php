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
 * Основной хелпер модуля модулей =)
 */
class modules {

    /**
     * Получение списка модулей в глобальную видимость
     */
    public static function initialize() {
        if (!Registry::exists('modules')) {
            $db = Registry::get('db');

            $modules = array();
            $result = $db->query("SELECT * FROM #__modules");
            while ($module = $db->fetch_array($result)) {
                $modules[$module['name']] = $module;
            }

            Registry::set('modules', $modules);
        }
    }

    /**
     * Получение списка модулей
     */
    public static function get_modules() {
        # Получаем установленные модули из БД
        self::initialize();
        $modules = Registry::get('modules');

        # Получаем остальные модули из ФС
        $dir = opendir(ROOT . 'modules/');
        while ($f = readdir($dir)) {
            if ($f == '.' || $f == '..' || $f == '.htaccess' || $f == '.gitignore' || $f == 'main' || $f == 'user' || $f == 'modules' || $f == 'themes' || $f == 'index_page')
                continue;
            # проверяем, есть ли модуль в базе
            if (array_key_exists($f, $modules))
                continue;

            if (file_exists(ROOT . 'modules/' . $f . '/module.ini')) {
                $module = parse_ini_file(ROOT . 'modules/' . $f . '/module.ini');
                $modules[$f] = array(
                    'name' => $module['name'],
                    'title' => $module['title'],
                    'description' => $module['description'],
                    'installed' => 0,
                    'status' => 'off'
                );
            }
        }

        return $modules;
    }

    /**
     * Определение активирован ли модуль
     */
    public static function is_active_module($module_name) {
        self::initialize();
        $modules = Registry::get('modules');

        if ($modules[$module_name]['status'] == 'on')
            return true;
        return false;
    }

}

?>