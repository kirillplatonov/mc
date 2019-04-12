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
defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа</b>');

/**
 * Подключение файла
 */
function a_import($file)
{
    if (!strstr($file, '.php'))
        $file .= '.php';
    if (file_exists(ROOT . $file)) {
        include_once ROOT . $file;
        return TRUE;
    } else {
        a_error($file . ': Подключаемый файл не найден!');
        return FALSE;
    }
}

/**
 * Перенаправление
 */
function redirect($url)
{
    header('Location: ' . URL . $url);
    exit;
}

/**
 * Проверка авторизации пользователя
 */
function is_user()
{
    if (USER_ID != -1) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Вывод ошибки
 */
function a_error($error_message = '', $link = '')
{
    $tpl = Registry::get('tpl');

    if (empty($error_message)) {
        $error_message = 'Произошла неизвестная ошибка';
    }

    if (!empty($tpl)) {
        $tpl->assign(array(
            'error_message' => $error_message,
            'link' => $link,
        ));
        $tpl->display('error');
    } else {
        echo '<b>' . $error_message . '</b>';
    }

    exit;
}

/**
 * Вывод ошибки
 */
function error($error_message = '', $link = '')
{
    $tpl = Registry::get('tpl');

    if (empty($error_message)) {
        $error_message = 'Произошла неизвестная ошибка';
    }

    if (!empty($tpl)) {
        $tpl->assign(array(
            'error_message' => $error_message,
            'link' => $link,
        ));
        $tpl->display('error');
    } else {
        echo '<b>' . $error_message . '</b>';
    }

    exit;
}

/**
 * Вывод информирующего сообщения
 */
function a_notice($message, $link, $timeout = 5)
{
    $tpl = Registry::get('tpl');

    $message = str_safe($message);
    $link = str_safe($link);
    if (isset($tpl)) {
        $tpl->assign(array(
            'title' => 'Информация',
            'message' => $message,
            'link' => $link,
            'timeout' => $timeout,
            'from' => 'info'
        ));

        $tpl->display('notice');
    } else {
        echo '<b>' . $message . '</b><br />';
        echo '<a href="' . $link . '">Продолжить</a>';
    }
    exit;
}

/**
 * Подтверждение
 */
function a_confirm($message, $link_ok, $link_cancel)
{
    $tpl = Registry::get('tpl');
    $link_ok = str_safe(str_replace('&amp;', '&', $link_ok));
    $link_cancel = str_safe(str_replace('&amp;', '&', $link_cancel));
    if (empty($message))
        $message = 'Подтверждаете выполнение данного действия?';

    if (isset($tpl)) {
        $tpl->assign(array(
            'message' => $message,
            'link_ok' => $link_ok,
            'link_cancel' => $link_cancel
        ));

        $tpl->display('confirm');
    } else {
        echo '<b>' . $message . '</b><br />';
        echo '<a href="' . $link_ok . '">Да</a> | <a href="' . $link_cancel . '">Нет</a>';
    }
    exit;
}

/**
 * Генерация URL адреса
 */
function a_url($path, $query = '', $header = FALSE)
{
    if (!empty($query)) {
        if ($header)
            $query = '&' . $query;
        else
            $query = '&amp;' . $query;
    }
    $url = URL . $path . EXT . '?' . SID . $query;
    $url = str_replace('?&amp;', '?', $url);
    $url = str_replace('?&', '?', $url);
    $url = str_replace(' ', '', $url);

    if (substr($url, -1) == '?')
        $url = substr($url, 0, -1);

    return $url;
}

/**
 * Обработка строки для помещения ее в базу данных
 */
function a_safe($str)
{
    $db = Registry::get('db');
    return htmlspecialchars(mysqli_real_escape_string($db->db_link, trim($str)));
}

/**
 * Вывод дампа переменной
 */
function a_debug($var, $exit = TRUE)
{
    print '<pre>';
    print_r($var);
    print '</pre>';

    if ($exit)
        exit;
}

/**
 * Онлайн ли пользователь
 */
function a_is_online($last_visit)
{
    if ($last_visit > time() - 180)
        return '<span style="color: green;">On</span>';
    else
        return '<span style="color: red;">Off</span>';
}

/**
 * Аватар пользователя
 */
function avatar($user_id)
{
    if (file_exists(ROOT . 'files/avatars/' . $user_id . '_100' . '.png'))
        return '<img src="' . URL . 'files/avatars/' . $user_id . '_100.png' . '" alt="" /><br /><br />';
    elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_150' . '.png'))
        return '<img src="' . URL . 'files/avatars/' . $user_id . '_150.png' . '" alt="" /><br /><br />';
    elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_100' . '.gif'))
        return '<img src="' . URL . 'files/avatars/' . $user_id . '_100.gif' . '" alt="" /><br /><br />';
    elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_150' . '.gif'))
        return '<img src="' . URL . 'files/avatars/' . $user_id . '_150.gif' . '" alt="" /><br /><br />';
    elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_100' . '.jpg'))
        return '<img src="' . URL . 'files/avatars/' . $user_id . '_100.jpg' . '" alt="" /><br /><br />';
    elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_150' . '.jpg'))
        return '<img src="' . URL . 'files/avatars/' . $user_id . '_150.jpg' . '" alt="" /><br /><br />';
    elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_100' . '.jpeg'))
        return '<img src="' . URL . 'files/avatars/' . $user_id . '_100.jpeg' . '" alt="" /><br /><br />';
    elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_150' . '.jpeg'))
        return '<img src="' . URL . 'files/avatars/' . $user_id . '_150.jpeg' . '" alt="" /><br /><br />';
}

/**
 * Небольшой профайлер приложения
 */
function a_profiler($start_time)
{
    GLOBAL $db;

    $end_time = microtime(true);

    echo PHP_EOL . '<!-- ' . PHP_EOL;
    echo 'Время выполнения: ' . round($end_time - $start_time, 5) . ' сек.' . PHP_EOL . PHP_EOL;
    if ($db) {
        echo 'Запросов к БД: ' . count($db->list_queries) . PHP_EOL;
        if (count($db->list_queries) > 0) {
            foreach ($db->list_queries AS $query_data) {
                echo $query_data['query'] . ' (' . round($query_data['time'], 5) . ' сек.)' . PHP_EOL;
            }
        }
    } else {
        echo "Подключения к базе не было.";
    }
    echo PHP_EOL . PHP_EOL;
    echo 'GET данные:' . PHP_EOL;
    print_r($_GET);
    echo PHP_EOL . PHP_EOL;
    echo 'POST данные:' . PHP_EOL;
    print_r($_POST);
    echo ' -->';
}

/**
 * Если переменная пуста, функция возвращает значение по умолчанию
 */
function a_default($var, $default = 0)
{
    if (empty($var))
        return $default;
    return $var;
}

/**
 * Подключение и инициализация класса
 *
 * @param string $path_to_class
 * @param <type> $type
 * @return class_name
 */
function a_load_class($path_to_class, $type = '')
{
    # Подключем файл класса
    a_import($path_to_class);

    # Определяем имя класса
    $file_name = str_replace('.php', '', basename($path_to_class));
    $ex = explode('_', $file_name);
    foreach ($ex as $value)
        $array[] = ucfirst($value);
    $class_name = implode('_', $array);

    switch ($type) {
        case 'controller':
            $class_name = $class_name . '_Controller';
            break;
        case 'ci':
            $class_name = 'CI_' . $class_name;
            break;
        case 'model':
            $class_name = $class_name . '_Model';
    }

    # Инициализируем класс
    return new $class_name();
}

/**
 * Функция антифлуда
 */
function a_antiflud($error = '', $antiflud_time = 0)
{
    $config = Registry::get('config');

    if ($antiflud_time == 0)
        $antiflud_time = $config['system']['antiflud_time'];
    if (empty($error))
        $error = 'Отправляйте сообщения не раньше ' . $antiflud_time . ' секунд с момента последнего поста!';
    else
        $error = str_replace('{ANTIFLUD_TIME}', $antiflud_time, $error);

    if ($_SESSION['last_message_time'] > time() - $antiflud_time)
        a_error($error);
    else
        $_SESSION['last_message_time'] = time();
}

/**
 * Функция проверки прав пользователя на выполнение определенных действий
 */
function a_check_rights($check_user_id, $check_user_status)
{
    if (USER_ID == -1)
        return FALSE;
    if (!class_exists('Access'))
        a_import('libraries/access');
    $access = new Access;
    $access_level = $access->get_level($check_user_status);
    if ((ACCESS_LEVEL > $access_level && ACCESS_LEVEL > 5) OR $check_user_id == USER_ID)
        return TRUE;
    else
        return FALSE;
}

/**
 * htmlspecialchars for arrays
 */
function htmlspecialchars_array($var)
{
    if (!is_array($var))
        return htmlspecialchars($var);
    return false;
}

function highlight($str)
{
    $str = stripslashes(htmlspecialchars_decode($str));
    $str = highlight_string($str, true);
    return '<div style="border: 1px silver solid; margin: 10px; padding-left: 5px; background-color: #f1f2f1;">' . $str . '</div>';
}

/**
 * Вывод проверочного кода (капчи)
 */
function captcha()
{
    echo '<img src="' . URL . 'utils/captcha.php" alt="captcha" /><br />';
}

/**
 * Обработка содержимого строки
 */
function str_safe($str)
{
    return htmlspecialchars(trim($str));
}

/**
 * Генерация URL адреса
 */
function url($path, $query = '', $header = FALSE)
{
    if (!empty($query)) {
        if ($header)
            $query = '&' . $query;
        else
            $query = '&amp;' . $query;
    }

    $url = URL . $path . EXT . '?' . SID . $query;
    $url = str_replace('?&amp;', '?', $url);
    $url = str_replace('?&', '?', $url);

    if (substr($url, -1) == '?')
        $url = substr($url, 0, -1);

    return $url;
}

?>
