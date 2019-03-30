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
 * Хелпер фтп модуля main
 */
class main_ftp {

    /**
     * Подключение к фтп серверу
     */
    public static function connect($args = array()) {
        if (empty($args)) {
            if (!empty($_SESSION['ftp_manager_server'])) {
                $args['server'] = $_SESSION['ftp_manager_server'];
                $args['port'] = $_SESSION['ftp_manager_port'];
                $args['login'] = $_SESSION['ftp_manager_login'];
                $args['password'] = $_SESSION['ftp_manager_password'];
            } else
                a_error('Нет данных для подключения!');
        }

        $args['port'] = empty($args['port']) ? 21 : $args['port'];

        if (!$ftp_handle = ftp_connect($args['server'], $args['port']))
            return false;

        if (!@ftp_login($ftp_handle, $args['login'], $args['password']))
            return false;

        @ftp_pasv($ftp_handle, TRUE);

        return $ftp_handle;
    }

    /**
     * Преобразование chmod
     */
    public static function dechmod($str) {
        $n1 = 0;
        $n2 = 0;
        $n3 = 0;
        $ar = array(4, 2, 1);
        for ($i = 1; $i <= 3; $i++) {
            if ($str[$i] != "-") {
                $n1 += $ar[$i - 1];
            }
        }
        for ($i = 4; $i <= 6; $i++) {
            if ($str[$i] != "-") {
                $n2 += $ar[$i - 4];
            }
        }
        $chi = 7;
        for ($i = 7; $i <= 9; $i++) {
            if ($str[$i] != "-") {
                $n3 += $ar[$i - 7];
            }
        }
        return $n1 . $n2 . $n3;
    }

    /**
     * Получение преобразованного списка файлов
     */
    public static function get_files($ftp_handle, $directory) {
        $files = array();
        $directories = array();
        $lines = ftp_rawlist($ftp_handle, $directory);

        if ($lines) {
            foreach ($lines as $file_line) {
                $name = preg_replace("~([^\\s]*[\\s]*){8}\\s{1}(.*)~m", "\\2", $file_line);
                if ($name == '.' || $name == '..')
                    continue;

                $file['name'] = $name;
                $file['size'] = preg_replace("~([^\\s]*[\\s]*){4}\\s{1}([^\\s]*)(.*)~m", "\\2", $file_line);
                $file['chmod'] = preg_replace("~([^\\s]*).*~m", "\\1", $file_line);
                if ($file['chmod'][0] == '-')
                    $file['type'] = 'file';
                else
                    $file['type'] = 'directory';
                $file['chmod'] = self::dechmod($file['chmod']);

                if ($file['type'] == 'file') {
                    # Определяем расширение файла
                    $file['ext'] = array_pop(explode('.', $file['name']));
                    $files[] = $file;
                } else
                    $directories[] = $file;
            }
        }

        return array_merge($directories, $files);
    }

    /**
     * Рекурсивное удаление папки на фтп
     */
    public static function delete_dir($ftp_handle, $path) {
        $list = self::get_files($ftp_handle, $path);

        if ($list !== FALSE AND count($list) > 0) {
            foreach ($list as $item) {
                if (!@ftp_delete($ftp_handle, $path . '/' . $item['name'])) {
                    self::delete_dir($ftp_handle, $path . '/' . $item['name']);
                }
            }
        }

        $result = @ftp_rmdir($ftp_handle, $path);

        return $result;
    }

    /**
     * Копирование удаленной папки на локальный сервер
     */
    public static function copy_remote_dir($ftp_handle, $remote_dir, $local_dir) {
        # Получаем содержимое папки
        $files = self::get_files($ftp_handle, $remote_dir);

        foreach ($files as $file) {
            if ($file['type'] == 'file') {
                # Копируем файл с сервера
                ftp_get($ftp_handle, $local_dir . '/' . $file['name'], $remote_dir . '/' . $file['name'], FTP_BINARY);
            } else {
                # Копируем папку
                mkdir($local_dir . '/' . $file['name']);
                self::copy_remote_dir($ftp_handle, $remote_dir . '/' . $file['name'], $local_dir . '/' . $file['name']);
            }
        }

        return true;
    }

    /**
     * Копирование локальной папки на удаленный сервер
     * @param boolean $ftp_handle
     * @param string $local_dir
     */
    public function copy_local_dir($ftp_handle, $local_dir, $remote_dir = null) {
        $files_array = self::get_array_files($local_dir, $local_dir);

        foreach ($files_array as $key => $item) {
            if (!empty($item['dir'])) {
                @ftp_mkdir($ftp_handle, $remote_dir . '/' . $item['dir']);
            } elseif (!empty($item['file'])) {
                @ftp_put($ftp_handle, $remote_dir . '/' . $item['file'], $local_dir . '/' . $item['file'], FTP_BINARY);
            }
        }
    }

    /**
     * get list of files from path
     * @param string $local_path  - path of the files
     * @return array - list files
     */
    public function get_array_files($local_path, $delete_path = '') {
        $local = scandir($local_path);
        foreach ($local as $key => $value) {
            if ($value == '.' OR $value == '..') {
                unset($local[$key]);
            } elseif (is_dir($local_path . '/' . $value)) {
                $local_str[]['dir'] = preg_replace('~^' . $delete_path . '~', '', $local_path . '/' . $value);
                $tmp_arr = self::get_array_files($local_path . '/' . $value, $delete_path);
                foreach ($tmp_arr as $item) {
                    $local_str[] = $item;
                }
            } else {
                $local_str[]['file'] = preg_replace('~^' . $delete_path . '~', '', $local_path . '/' . $value);
            }
        }
        return $local_str;
    }

}

?>
