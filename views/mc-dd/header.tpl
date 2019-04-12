<?php header('Content-Type: application/xhtml+xml; charset=utf-8'); echo '<?xml version="1.0" encoding="UTF-8" ?>' ?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="<?php echo DESCRIPTION ?>" />
        <meta name="keywords" content="<?php echo KEYWORDS ?>" />
        <title><?php echo $GLOBALS['CONFIG']['system']['system_title'] ?> | <?php echo (!empty($title) ? $title : $sub_title) ?></title>
        <link rel="shortcut icon" href="<?php echo URL ?>/views/<?php echo THEME ?>/images/favicon.ico" />
        <link rel="stylesheet" href="<?php echo URL ?>/views/<?php echo THEME ?>/css/default.css" type="text/css" />
    </head>

    <body>
        <div class="head">
            <img src="<?php echo URL ?>/views/<?php echo THEME ?>/images/logo.png" alt="<?php echo $GLOBALS['CONFIG']['system']['system_title'] ?>" />
        </div>

        <div class="auth">
            <?php echo (USER_ID != -1 ? '<a href="'. a_url('user/profile') .'">'. HELLO .', '. $user['username'] .'</a> <a href="'. a_url('user/exit') .'">Выход</a>' : '<a href="'. a_url('user/login') .'">Войти на сайт</a> <a href="'. a_url('user/registration') .'">Регистрация</a>') ?>
        </div>

        <?php echo ads_manager::get_ads_block('all_pages_up', '<div class="adv">', '</div>') ?>

        <?php if (MODERATION_USERS > 0 && ACCESS_LEVEL >= 8): ?>
        <div class="block">
            Модерации <?php echo main::end_str(MODERATION_USERS, 'ждет', 'ждут', 'ждут') ?> <a href="<?php echo a_url('user/admin/moderate') ?>"><?php echo MODERATION_USERS .' '. main::end_str(MODERATION_USERS, 'пользователь', 'пользователя', 'пользователей') ?></a>
        </div>
        <?php endif ?>

        <?php if (defined('PRIVATE_NEW_MESSAGES')): ?>
        <div class="block">
            <a href="<?php echo a_url('private/list_messages', 'folder=new') ?>"><?php echo PRIVATE_NEW_MESSAGES .' '. main::end_str(PRIVATE_NEW_MESSAGES, 'новое сообщение', 'новых сообщения', 'новых сообщений') ?></a>
        </div>
        <?php endif ?>

        <?php if (!empty($_SESSION['check_user_id'])): ?>
        <div class="block">
            <a href="<?php echo a_url('user/exit_from_user_panel') ?>">Покинуть панель пользователя <b><?php echo $user['username'] ?></b> и перейти в панель управления</a>
        </div>
        <?php endif ?>

        <div class="main">