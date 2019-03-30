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
 * Кэширование
 */
class File_Cache {

    /**
     * Constructor
     * @param string $dir
     */
    public function __construct($dir) {
        $this->dir = $dir;

        if (!is_dir($this->dir) OR ! is_writable($this->dir)) {
            exit('Директория для кэша не найдена, либо нет прав на запись');
        }
    }

    /**
     * Получение данных
     */
    public function get($key, $expiration = 3600) {
        $cache_path = $this->_name($key);
        if (!@file_exists($cache_path)) {
            return FALSE;
        }
        if (filemtime($cache_path) < (time() - $expiration)) {
            $this->clear($key);
            return FALSE;
        }
        if (!$fp = @fopen($cache_path, 'rb')) {
            return FALSE;
        }
        flock($fp, LOCK_SH);
        $cache = '';
        if (filesize($cache_path) > 0) {
            $cache = unserialize(fread($fp, filesize($cache_path)));
        } else {
            $cache = NULL;
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        return $cache;
    }

    /**
     * Запись данных
     */
    public function set($key, $data) {
        $cache_path = $this->_name($key, true);
        if (!$fp = fopen($cache_path, 'wb')) {
            return FALSE;
        }
        if (flock($fp, LOCK_EX)) {
            fwrite($fp, serialize($data));
            flock($fp, LOCK_UN);
        } else {
            return FALSE;
        }
        fclose($fp);
        @chmod($cache_path, 0777);
        return true;
    }

    /**
     * Очистка кэша по ключу
     */
    public function clear($key) {
        $cache_path = $this->_name($key);

        if (file_exists($cache_path)) {
            unlink($cache_path);
            return true;
        }

        return false;
    }

    /**
     * Генерация имени файла
     */
    private function _name($key, $is_set = false) {
        $key_name = md5($key);
        $subdir = substr($key_name, 0, 1);
        if ($is_set) {
            if (!file_exists($this->dir . '/' . $subdir)) {
                mkdir($this->dir . '/' . $subdir);
                chmod($this->dir . '/' . $subdir, 0777);
            }
        }
        return sprintf("%s/%s/%s", $this->dir, $subdir, $key_name);
    }

}
