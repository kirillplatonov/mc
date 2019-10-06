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


class user
{

    /**
     * Довавление рейтинга пользователю
     * 
     * @param int $rating
     * @param int $user_id
     * @return unknown
     */
    public static function rating_update($rating = 1, $user_id = NULL)
    {
        if (!$user_id)
            $user_id = USER_ID;
        $rating = floatval($rating);

        $db = Registry::get('db');
        if ($user_id != -1)
            $db->query("UPDATE #__users SET rating = rating + $rating WHERE user_id = $user_id");
    }

    /**
     * Получение иконки пользователя
     * 
     * @param int $user_id
     * @return string
     */
    public static function get_icon($user_id = NULL)
    {
        if (!$user_id)
            $user_id = USER_ID;

        // Получаем пол пользователя
        $db = Registry::get('db');
        if ($user_id != -1)
            $sex = $db->get_one("SELECT sex FROM #__users_profiles WHERE user_id = $user_id");

        // Возвращаем иконку для гостей
        if ($user_id == -1)
            return '<img src="' . URL . 'modules/user/images/icons/guest_icon.png" alt="" />';

        switch ($sex) {
            case 'm':
                return '<img src="' . URL . 'modules/user/images/icons/male_icon.png" alt="" />';
                break;

            case 'w':
                return '<img src="' . URL . 'modules/user/images/icons/female_icon.png" alt="" />';
                break;

            default:
                return '<img src="' . URL . 'modules/user/images/icons/unknown_icon.png" alt="" />';
                break;
        }
    }

    /**
     * Получение логина пользователя
     * 
     * @param int $user_id
     * @param bool $link
     * @return string
     */
    public static function get_username($user_id = NULL, $link = FALSE)
    {
        if (!$user_id)
            $user_id = USER_ID;

        // Получаем логин пользователя и статус
        $db = Registry::get('db');
        $user = $db->get_row("SELECT
			(SELECT username FROM #__users WHERE user_id = $user_id) AS username,
			(SELECT status FROM #__users WHERE user_id = $user_id) AS status");

        // Возвращаем ник для забаненых пользователей
        if ($db->get_one("SELECT COUNT(*) FROM #__users_ban WHERE user_id = '$user_id' AND status = 'enable'") > 0)
            return ($link ? '<a href="' . URL . 'profile/' . $user['username'] . '">' : NULL) . '<span style="color: black;"><b>' . $user['username'] . '</b></span>' . ($link ? '</a>' : NULL);

        switch ($user['status']) {
            case 'user':
                return ($link ? '<a href="' . URL . 'profile/' . $user['username'] . '">' : NULL) . '<span class="username"><b>' . $user['username'] . '</b></span>' . ($link ? '</a>' : NULL);
                break;

            case 'moder':
                return ($link ? '<a href="' . URL . 'profile/' . $user['username'] . '">' : NULL) . '<span style="color: green;"><b>' . $user['username'] . '</b></span>' . ($link ? '</a>' : NULL);
                break;

            case 'admin':
                return ($link ? '<a href="' . URL . 'profile/' . $user['username'] . '">' : NULL) . '<span style="color: red;"><b>' . $user['username'] . '</b></span>' . ($link ? '</a>' : NULL);
                break;

            case 'guest':
            default:
                return '<b>' . $user['username'] . '</b>';
                break;
        }
    }

    /**
     * Проверка онлайн ли пользователь
     * 
     * @param int $last_visit время последнего посещения
     * @return string
     */
    public static function online_status($last_visit)
    {
        // Кол-во минут, в течении которых пользователь считается в онлайне
        $online_time = 3;

        if ($last_visit > time() - $online_time * 60) {
            return '[<span style="color: green;">On</span>]';
        } else {
            return '[<span style="color: red;">Off</span>]';
        }
    }

    public static function getAvatarUrl($user_id)
    {
        if (file_exists(ROOT . 'files/avatars/' . $user_id . '_100' . '.png'))
            return URL . 'files/avatars/' . $user_id . '_100.png';
        elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_150' . '.png'))
            return URL . 'files/avatars/' . $user_id . '_150.png';
        elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_100' . '.gif'))
            return URL . 'files/avatars/' . $user_id . '_100.gif';
        elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_150' . '.gif'))
            return URL . 'files/avatars/' . $user_id . '_150.gif';
        elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_100' . '.jpg'))
            return URL . 'files/avatars/' . $user_id . '_100.jpg';
        elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_150' . '.jpg'))
            return URL . 'files/avatars/' . $user_id . '_150.jpg';
        elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_100' . '.jpeg'))
            return URL . 'files/avatars/' . $user_id . '_100.jpeg';
        elseif (file_exists(ROOT . 'files/avatars/' . $user_id . '_150' . '.jpeg'))
            return URL . 'files/avatars/' . $user_id . '_150.jpeg';
        else return URL . 'files/avatars/no_ava.jpg';
    }

}

?>