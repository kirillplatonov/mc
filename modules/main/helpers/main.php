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
defined('IN_SYSTEM') or die('<b>Access error</b>');

class main
{

    /**
     * Конфигурация для модулей
     * @param string $module
     */
    public static function config($config_data, $module, $db)
    {
        foreach ($config_data as $key => $value) {
            if ($key == 'submit')
                continue;
            $sql = "UPDATE #__config SET \n";
            $sql .= "`value` = '" . mysqli_real_escape_string($db->db_link, stripslashes($value)) . "'\n";
            $sql .= "WHERE `key` = '" . mysqli_real_escape_string($db->db_link, $key) . "' AND module = '" . mysqli_real_escape_string($db->db_link, $module) . "'";
            $db->query($sql);
        }
    }

    /**
     * Функция удаления старых файлов в tmp
     */
    public static function tmp_clear($ttl = 86400)
    {
        $dir = opendir(ROOT . 'tmp/');
        while ($f = readdir($dir)) {
            if ($f == '.' || $f == '..' || $f == '.htaccess' || $f == '.gitignore' || $f == '.htaccess' || $f == '.svn')
                continue;
            $file = ROOT . 'tmp/' . $f;
            if (@filemtime($file) < (time() - $ttl)) {
                if (is_file($file))
                    unlink($file);
                if (is_dir($file))
                    self::delete_dir($file);
            }
        }
    }

    /**
     * Получение папки для файлов
     */
    public static function get_dir($item_id)
    {
        return strval(ceil($item_id / 30000));
    }

    /**
     * Конвертирование строки в windows-1251
     */
    public static function wtext($text)
    {
        return iconv('utf-8', 'windows-1251', $text);
    }

    /**
     * Конвертирование строки в utf-8
     * @param string $text
     */
    public static function utext($text)
    {
        return iconv('windows-1251', 'utf-8', $text);
    }

    /**
     * Генерация случайной строки
     */
    public static function get_unique_code($length = 0)
    {
        $code = md5(uniqid(rand(), true));
        if ($length != 0) {
            return substr($code, 0, $length);
        } else {
            return $code;
        }
    }

    /**
     * Транслитерация латиницы
     * @param string $str
     */
    public static function translite($str)
    {
        $table = array('_' => ' ', 'a' => 'а', 'b' => 'б', 'v' => 'в', 'g' => 'г', 'd' => 'д', 'e' => 'е', 'yo' => 'ё', 'zh' => 'ж', 'z' => 'з', 'i' => 'и', 'j' => 'й', 'k' => 'к', 'l' => 'л', 'm' => 'м', 'n' => 'н', 'o' => 'о', 'p' => 'п', 'r' => 'р', 's' => 'с', 't' => 'т', 'u' => 'у', 'f' => 'ф', 'h' => 'х', 'c' => 'ц', 'ch' => 'ч', 'sh' => 'ш', 'sch' => 'щ', 'q' => 'ь', 'x' => 'ы', 'q' => 'ь', 'ye' => 'э', 'yu' => 'ю', 'ya' => 'я',
            'A' => 'А', 'B' => 'Б', 'V' => 'В', 'G' => 'Г', 'D' => 'Д', 'E' => 'Е', 'YO' => 'Ё', 'ZH' => 'Ж', 'Z' => 'З', 'I' => 'И', 'J' => 'Й', 'K' => 'К', 'L' => 'Л', 'M' => 'М', 'N' => 'Н', 'O' => 'О', 'P' => 'П', 'R' => 'Р', 'S' => 'С', 'T' => 'Т', 'U' => 'У', 'F' => 'Ф', 'H' => 'Х', 'C' => 'Ц', 'CH' => 'Ч', 'SH' => 'Ш', 'SCH' => 'Щ', 'Q' => 'Ь', 'X' => 'Ы', 'YE' => 'Э', 'YU' => 'Ю', 'YA' => 'Я');

        return $str;
    }

    /**
     * Получение размера удаленного файла
     */
    public static function get_filesize($file_path)
    {
        $headers = get_headers($file_path, 1);
        if ((!array_key_exists('Content-Length', $headers))) {
            return false;
        }
        return $headers['Content-Length'];
    }

    /**
     * Транслитерация кирилицы
     */
    public static function detranslite($str)
    {
        $str = strtr($str, array(' ' => '_', 'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ь' => 'q', 'ы' => 'x', 'э' => 'ye', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ь' => 'Q', 'Ы' => 'X', 'Э' => 'YE', 'Ю' => 'YU', 'Я' => 'YA')
        );
        return $str;
    }

    /**
     * Добавление новых правил в роутинг
     * @param string $block_name
     * @param string $new_rules
     */
    public static function add_route_rules($block_name, $new_rules)
    {
        file_put_contents(ROOT . 'data_files/route_rules/' . $block_name . '.rules', $new_rules);
    }

    /**
     * Удаление правил роутинга
     * @param string $block_name
     */
    public static function delete_route_rules($block_name)
    {
        unlink(ROOT . 'data_files/route_rules/' . $block_name . '.rules');
    }

    /**
     * Запрет действий в демо версии
     */
    public static function is_demo()
    {
        if (file_exists(ROOT . 'is_demo')) {
            a_error('В демо версии данное действие запрещено!');
        }
    }

    /**
     * Функция рекурсивного копирования
     */
    public static function r_copy($source, $dest)
    {
        # Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        # Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest);
        }

        # If the source is a symlink
        if (is_link($source)) {
            $link_dest = readlink($source);
            return symlink($link_dest, $dest);
        }

        # Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            if ($dest !== "$source/$entry") {
                self::r_copy("$source/$entry", "$dest/$entry");
            }
        }

        // Clean up
        $dir->close();
        return true;
    }

    /**
     * Выполнение событий
     * @param string $type
     */
    public static function events_exec($db, $type)
    {
        $dir = opendir(ROOT . 'data_files/events');
        while ($f = readdir($dir)) {
            if (strstr($f, $type . '.event')) {
                $module = trim(str_replace('.' . $type . '.event', '', $f));
                if (!empty($module)) {
                    a_import('modules/' . $module . '/helpers/' . $module . '_events');
                    call_user_func(array($module . '_events', $type), $db);
                }
            }
        }
    }

    /**
     * Добавление события
     * @param string $module
     * @param string $place
     */
    public static function add_event($module, $place)
    {
        $filename = ROOT . 'data_files/events/' . $module . '.' . $place . '.event';
        file_put_contents($filename, '');
        chmod($filename, 0777);
    }

    /**
     * Удаление события
     * @param string $module
     */
    public static function delete_event($module)
    {
        $dir = opendir(ROOT . 'data_files/events');
        while ($f = readdir($dir)) {
            if (preg_match('~^' . $module . '\.~', $f))
                @unlink(ROOT . 'data_files/events/' . $f);
        }
    }

    /**
     * Подстройка окончания
     * @param int, string, string, string
     * @using echo end_str(2,'сообщение','сообщения','сообщений');
     */
    public static function end_str($count, $k_1, $k_234, $k_many)
    {
        $count = (string) $count;
        $num_xx = (int) $count[self::strlen($count) - 2] . $count[self::strlen($count) - 1];
        $num_x = (int) $count[self::strlen($count) - 1];
        if ($num_xx <> 11 && $num_xx <> 12 && $num_xx <> 13 && $num_xx <> 14) {
            if ($num_x == 1) {
                return $k_1;
            } else
            if ($num_x == 2 || $num_x == 3 || $num_x == 4) {
                return $k_234;
            } else {
                return $k_many;
            }
        } else {
            return $k_many;
        }
    }

    /**
     * Получение длины строки для utf-8
     */
    public static function strlen($str)
    {
        return strlen(self::wtext($str));
    }

    /**
     * substr для utf-8
     * @param string $string, int $start [int $lenght]
     */
    public static function substr($string, $start, $lenght = NULL)
    {
        return self::utext(substr(self::wtext($string), $start, $lenght));
    }

    /**
     * Обновление файлового счетчика
     */
    public static function update_file_counter($file, $number)
    {
        # Получаем текущее число
        $old_number = floatval(implode('', file($file)));
        # Прибавляем (отбавляем) число
        $new_number = $old_number + $number;
        # Записываем данные в файл
        $fp = fopen($file, 'w+');
        fwrite($fp, $new_number);
        fclose($fp);

        return $new_number;
    }

    /**
     * Обрезание части строки по словам
     * @param integer $limit
     */
    public static function limit_words($string, $limit)
    {
        $words = explode(' ', $string);
        $i = 0;
        $string = '';
        while ($i < $limit && $i < count($words)) {
            $string .= $words[$i] . ' ';
            $i++;
        }

        return trim($string);
    }

    /**
     * Рекурсивное удаление папки
     * @param string $arg
     */
    public static function delete_dir($arg, $clear = FALSE)
    {
        $d = opendir($arg);
        while ($f = readdir($d)) {
            if ($f != "." && $f != "..") {
                if (is_dir($arg . "/" . $f))
                    self::delete_dir($arg . "/" . $f);
                else
                    @unlink($arg . "/" . $f);
            }
        }
        if (!$clear)
            @rmdir($arg);
    }

    /**
     * Formats a numbers as bytes, based on size, and adds the appropriate suffix
     *
     * @access	public
     * @param	mixed	// will be cast as int
     * @return	string
     */
    public static function byte_format($num)
    {
        if (!$num)
            $num = 0;

        if ($num >= 1000000000000) {
            $num = round($num / 1099511627776, 1);
            $unit = 'Tb';
        } elseif ($num >= 1000000000) {
            $num = round($num / 1073741824, 1);
            $unit = 'Gb';
        } elseif ($num >= 1000000) {
            $num = round($num / 1048576, 1);
            $unit = 'Mb';
        } elseif ($num >= 1000) {
            $num = round($num / 1024, 1);
            $unit = 'Kb';
        } else {
            $unit = 'b';
            return number_format($num) . ' ' . $unit;
        }

        return number_format($num, 1) . ' ' . $unit;
    }

    /**
     * Изменение размера изображений
     *
     * Пропорциональное изменение размера производится в случае,
     * если отсутствует один из параметров высоты или ширины изображения
     *
     * @param string $path_to_file путь к существующему изображению
     * @param string $path_to_save путь для сохранения
     * @param int $width ширина изображения
     * @param int $height высота изображения
     * @param int $quality качество изображения в процентах
     * @return boolean
     */
    public static function image_resize($path_to_file, $path_to_save, $width, $height = 0, $quality = 100)
    {
        // Проверка наличия изображения на сервере
        if (!file_exists($path_to_file))
            return FALSE;

        // Получение информации о изображении
        $info = getimagesize($path_to_file);

        // Формат изображения
        $format = strtolower(substr($info['mime'], strpos($info['mime'], '/') + 1));

        // Выбор функции для изображения
        $picfunc = 'imagecreatefrom' . $format;

        // Старая ширина изображения
        $old_width = $info[0];

        // Старая высота изображения
        $old_height = $info[1];

        // Вычисление горизонтального соотношения
        $horizontal = $width / $old_width;

        // Вычисление вертикального соотношения
        $vertical = $height / $old_height;

        // Пропорциональное вычисление параметров
        if ($height == 0) {
            $vertical = $horizontal;
            $height = $vertical * $old_height;
        } elseif ($width == 0) {
            $horizontal = $vertical;
            $width = $horizontal * $old_width;
        }

        // Формирование размера изображения
        $ratio = min($horizontal, $vertical);

        // Необходимость пропорционального изменения
        if ($horizontal == $ratio) {
            $use_horizontal = TRUE;
        } else {
            $use_horizontal = FALSE;
        }

        $new_width = $use_horizontal ? $width : floor($old_width * $ratio);
        $new_height = !$use_horizontal ? $height : floor($old_height * $ratio);
        $new_left = $use_horizontal ? 0 : floor(($width - $new_width) / 2);
        $new_top = !$use_horizontal ? 0 : floor(($height - $new_height) / 2);

        $pic_to_src = $picfunc($path_to_file);

        // Создание изображения в памяти
        $pic_to_save = imagecreatetruecolor($width, $height);

        // Заполнение цветом
        $white = imagecolorallocate($pic_to_save, 0xFF, 0xFF, 0xFF);
        imagefill($pic_to_save, 0, 0, $white);

        // Нанесение старого изображения на новое
        imagecopyresampled($pic_to_save, $pic_to_src, $new_left, $new_top, 0, 0, $new_width, $new_height, $old_width, $old_height);

        // Определение формата изображения на выходе
        $ext = array_pop(explode('.', $path_to_save));

        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($pic_to_save, $path_to_save, $quality);
                break;

            case 'gif':
                imagegif($pic_to_save, $path_to_save);
                break;

            case 'png':
                imagepng($pic_to_save, $path_to_save);
                break;

            default:
                return FALSE;
                break;
        }

        // Очистка памяти
        imagedestroy($pic_to_src);
        imagedestroy($pic_to_save);

        return TRUE;
    }

    /**
     * Получение названия месяца на русском
     */
    public static function get_month_name($month_num, $ucfirst = false, $type = 1)
    {
        $months = array(
            1 => array('январь', 'января'),
            2 => array('февраль', 'февраля'),
            3 => array('март', 'марта'),
            4 => array('апрель', 'апреля'),
            5 => array('май', 'мая'),
            6 => array('июнь', 'июня'),
            7 => array('июль', 'июля'),
            8 => array('август', 'августа'),
            9 => array('сенрябрь', 'сентября'),
            10 => array('октябрь', 'октября'),
            11 => array('ноябрь', 'ноября'),
            12 => array('декабрь', 'декабря')
        );

        $month_name = $months[intval($month_num)][$type];
        return ($ucfirst ? self::utext(ucfirst(self::utext($month_name))) : $month_name);
    }

    /**
     * Замена <p> на <br />
     * @return string
     */
    public function tinymce_p_br($content)
    {
        $content = preg_replace('/<p[^>]*>/', '', $content);
        $content = str_replace('</p>', '<br />', $content);
        if (substr(self::wtext($content), self::strlen($content) - 6) == '<br />') {
            $content = self::utext(substr(self::wtext($content), 0, -6));
        }
        return $content;
    }

    /**
     * Проверка данных регуляркой
     * @param string $type
     */
    public static function check_input($value, $type, $mode = 'check')
    {
        $filterinput = array(
            "MAIL" => array("^[a-zA-Z0-9_.-]+\@[a-zA-Z0-9_.-]+\.[a-zA-Z]{2,5}$", "xxx@yyy.zz"),
            "LOGIN" => array("^[a-zA-Z0-9]{3,50}$", "латинские буквы и цифры, не менее трех символов"),
            "PASSWORD" => array("^[a-zA-Z0-9.,!#%*()$]{3,20}$", "от 3х латинских букв, цифр и знаков: .,!#%*()$"),
            "INT" => array("^\-?[0-9]*$", "только цифры"),
            "FLOAT" => array("^\-?[0-9]*\.?[0-9]*$", "целаячасть.дробная"),
            "IP" => array("^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$", "XXX.XXX.XXX.XXX"),
            "WORD" => array("^[0-9a-z_/]*$", "только английские буквы и цифры"),
            "ROUTE" => array("^[0-9a-z_-]*$", "только английские буквы и цифры"),
            "URL" => array("^(https?|HTTPS?|ftp|gopher):\/\/[a-zA-Z0-9_-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z]{1,7}" . // Основное имя сервера
                "(\/[a-zA-Z0-9\_\.-]*\/?)*" . // Имена возможных подкаталогов и файл
                "(\?[a-zA-Z0-9\_]+=[a-zA-Z0-9]+([&][a-zA-Z0-9_]+=[a-zA-Z0-9]+)*)?$"//get параметр
                , "http://site.com/path/"),
            "TIME" => array("^[0-9]{2}(:[0-9]{2})?(:[0-9]{2})?$", "ЧЧ:ММ:СС"),
            "DATE" => array("^[0-9]{4}-[0-9]{2}-[0-9]{2}$", "ГГГГ-ММ-ДД"),
            "DATETIME" => array("^[0-9]{4}-[0-9]{2}-[0-9]{2}([ ]+[0-9]{2}:[0-9]{2}:[0-9]{2}){0,1}$", "ГГГГ-ММ-ДД ЧЧ:ММ:СС"),
            "NUMPHONE" => array("^\+?[0-9 ()-]{5,20}$", "только цифры"),
            "SESSION" => array("^[0-9a-zA-Z]*$", "правильная сессия"),
            "FILE" => array("^[0-9a-zA-Z_.-]*$", "только английские буквы и цифры"),
            "DOMAIN" => array("^[a-z0-9\.\-]{2,50}$", "только английские буквы и цифры")
        );

        if ($mode == 'check') {
            return preg_match('~' . $filterinput[$type][0] . '~', $value);
        } elseif ($mode == 'format') {
            return $filterinput[$type][1];
        }
    }
    /**
     * Уведомление пользователя о упоминании через @username
     * @example https://github.com/mobilecms-pro/cms/blob/master/modules/guestbook/controllers/guestbook.php#L126
     * @param type $markup 
     * @param type $url
     * @param type $urlName
     * @return type
     */
    public static function bbNickName($markup, $url, $urlName = null)
    {
        if (preg_match('#@([A-z0-9]+)(.*)$#ui', $markup, $login)) {
            $db = Registry::get('db');
            $query = $db->query("SELECT user_id FROM #__users WHERE username = '" . a_safe($login[1]) . "'");
            if ($query->num_rows !== 0) {
                $id = $query->fetch_row()[0];
                $db->query("INSERT INTO #__private_messages SET
					user_id = '$id',
					user_to_id = '$id',
					user_from_id = '0',
					message = 'Вас упомянули на странице: [url=" . $url . "]". ($urlName === null ? $url : $urlName) . "[/url]',
					folder = 'new',
					time = UNIX_TIMESTAMP()
				");
                
                return preg_replace('/@([A-z0-9]+),/si', '[user]$1[/user]', $markup);
            }
        }
    }

    /**
     * BBcode
     */
    public static function bbcode($markup)
    {
        $preg = array(
            # Text arrtibutes
            '/\[b\](.*?)\[\/b\]/si' => '<b>$1</b>',
            '/\[i\](.*?)\[\/i\]/si' => '<i>$1</i>',
            '/\[u\](.*?)\[\/u\]/si' => '<u>$1</u>',
            # цвет текста
            '/\[red\](.*?)\[\/red\]/si' => '<font color="red">$1</font>',
            '/\[green\](.*?)\[\/green\]/si' => '<font color="green">$1</font>',
            '/\[blue\](.*?)\[\/blue\]/si' => '<font color="blue">$1</font>',
            # выравнивания
            '/\[center\](.*?)\[\/center\]/si' => '<div style="text-align: center">$1</div>',
            '/\[left\](.*?)\[\/left\]/si' => '<div style="text-align: left">$1</div>',
            '/\[right\](.*?)\[\/right\]/si' => '<div style="text-align: right">$1</div>',
            # код
            '/\[code\](.*?)\[\/code\]/si' => "highlight('$1')",
            # цитаты
            '/\[q\](.*?)\[\/q\]/si' => '<div class="q">$1</div>',
            # e-mail
            '/\[email\](.*?)\[\/email\]/si' => "'<a rel=\"noindex\" href=\"mailto:'.str_replace('@', '[dog]','$1').'\">'.str_replace('@', '[dog]','$1').'</a>'",
            # ссылки
            '/\[url\=(.*?)\](.*?)\[\/url\]/si' => '<a href="$1">$2</a>',
            # images
            '/\[img\](.*?)\[\/img\]/si' => "<img src=\"$1\" alt=\"\" style=\"max-width: 150px;\" />",
            '/\[user](.*?)\[\/user\]/si' => '<b><a href="/profile/$1">$1</a></b>,'
        );
        
        return preg_replace(array_keys($preg), array_values($preg), $markup);
    }

    /**
     * Простая функция для отправки E-mail
     *
     * @param string $from адрес отправителя
     * @param string $to адрес получателя
     * @param string $title тема письма
     * @param string $message сообщение
     * @return mixed
     */
    public function send_mail($from, $to, $title, $message)
    {
        mail($to, '=?utf-8?B?' . base64_encode($title) . '?=', $message, "From: $from <$from>\nContent-Type: text; charset=utf-8") or die('Не удалось отправить письмо на E-mail.');
    }

    /**
     * Форматирование времени для вывода
     *
     * @param int $time
     * @return string
     */
    public function display_time($time = NULL)
    {
        if (!$time)
            $time = time();

        // Получение даты
        $data = date('d.m.Y', $time);

        // Получение времени
        $time = date('H:i', $time);

        // Сегодня
        if ($data == date('d.m.Y'))
            return 'Сегодня в ' . $time;

        // Вчера
        if ($data == date('d.m.Y', time() - 3600 * 24))
            return 'Вчера в ' . $time;

        return $data . ' в ' . $time;
    }

}

?>
