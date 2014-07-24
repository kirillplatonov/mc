<?php  header('Content-Type: application/xhtml+xml; charset=utf-8'); echo '<?xml version="1.0" encoding="UTF-8" ?>'  ?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript" language="javascript"></script>
<script type="text/javascript">
function show_hide (objName) {
	if ( $(objName).css('display') == 'none' ) {
		$(objName).animate({height: 'show'}, 800);
	} else { 
		$(objName).animate({height: 'hide'}, 800);
	} 
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php  echo $GLOBALS['CONFIG']['system']['system_title']  ?> | <?php echo (!empty($title) ? $title : $sub_title) ?></title>
<style type="text/css">
<?php $this->display('style') ?>
</style>
<link rel="shortcut icon" href="<?php  echo URL ?>views/<?php echo THEME ?>/img/favicon.ico" />
</head>
<body>






<div class="header">
<div class="head">
<div class="head_logo">
<div class="razd_top"> </div>
<?php if (USER_ID == -1): ?>
<div class="link">
<a href="javascript:void(0)" onclick="show_hide('#block');">Вход/Регистрация</a>
</div>
<?php else: ?>

<div class="link">
<a href="javascript:void(0)" onclick="show_hide('#block');">Мой кабинет</a> <a href="<?php echo a_url('user/profile'); ?>"><b>&raquo;</b></a> 
</div>


<?php if(defined('PRIVATE_NEW_MESSAGES')): ?>
<div class="link">
<a href="<?php echo a_url('private/list_messages', 'folder=new') ?>"><?php echo PRIVATE_NEW_MESSAGES .' '. main::end_str(PRIVATE_NEW_MESSAGES, 'новое сообщение', 'новых сообщения', 'новых сообщений') ?></a>
</div>
<?php endif; ?>

<?php if(!empty($_SESSION['check_user_id'])): ?>
<div class="link">
<a href="<?php echo a_url('user/exit_from_user_panel') ?>">Покинуть панель пользователя <b><?php echo $user['username'] ?></b> и перейти в панель управления</a>
</div>
<?php endif; ?>


<div class="link">
<a href="<?php echo a_url('user/exit'); ?>">Выход</a>
</div>


<?php endif; ?>
<div class="nav_link">
<div class="razd_top"> </div>
<div class="link">
<a href="/">Главная</a>
</div>
<div class="link">
<a href="<?php echo a_url('forum'); ?>">Форум</a>
</div>
<div class="link">
<a href="<?php echo a_url('guestbook'); ?>">Гостевая</a>
</div>
<div class="link">
<a href="<?php echo a_url('downloads'); ?>">Загрузки</a>
</div>
<div class="link">
<a href="<?php echo a_url('lib'); ?>">Библиотека</a>
</div>
</div>
</div>

<div class="body_top">
</div>

</div>
</div>






<div class="body">
<div id="block" style="display: none; margin-top: 85px; position: fixed; left: 0; top: 0; float: left; width: 187px; overflow: hidden; vertical-align: top;">

<?php

if (USER_ID == -1) {
  echo '<div class="user_nick">Авторизация</div>
  <div class="user_menu">
  <div class="form">
  <form action="'. a_url('user/login') .'" method="get">
  Логин:<br />
  <input name="username" class="input" type="text" /><br />

  Пароль:<br />
  <input name="password" type="password" /><br />

  <input name="remember_me" type="checkbox" value="ON" checked="checked" /> Запомнить меня<br />

  <input type="submit" class="sub" value="Вход" />
  </form>
  </div>
  
  <div class="bb2"> </div>
  <div class="left_link">
  <a href="'. a_url('user/registration') .'">Регистрация</a><br />
  </div>
  <div class="left_link">
  <a href="'. a_url('user/forgot') .'">Напомнить пароль</a><br />
  </div>
  <div class="bb"> </div>
  </div>';
} else {
  echo '<div class="user_nick">'. $user['username'] .'</div>
  <div class="user_menu">';
  
  if($user['avatar']):
  echo '<div class="avatar"><img src="'. URL .'files/avatars/'. $user['user_id'] .'_100.jpg?'. rand(0, 99) .'" alt="" /></div>';
  endif;
  
  ?>
  <div class="bb2"> </div>
  
  <?php if(ACCESS_LEVEL >= 8): ?>
<div class="left_link"><a href="<?php echo a_url('user/admin') ?>">Панель управления</a></div>
<?php endif; ?>
  
  <div class="left_link"><a href="<?php echo a_url('user/profile/view') ?>">Ваша анкета</a></div>
  
  <div class="left_link"><a href="<?php echo a_url('private') ?>">Личные сообщения</a> <?php if(defined(PRIVATE_NEW_MESSAGES)) echo '('. PRIVATE_NEW_MESSAGES .')'; ?></div>
  
  <?php if(modules::is_active_module('blogs')): ?>
  <div class="left_link"><a href="<?php echo a_url('blogs') ?>">Ваш блог</a></div>
  <?php endif; ?>
  
  <?php if(modules::is_active_module('photo')): ?>
  <div class="left_link"><a href="<?php echo a_url('photo') ?>">Ваши фотоальбомы</a></div>
  <?php endif; ?>
  
  <?php if(modules::is_active_module('tickets')): ?>
  <div class="left_link"><a href="<?php echo a_url('tickets') ?>">Связь с администрацией (тикеты)</a><?php if($info['new_tickets']): ?> <span class="new_files">new!</span><?php endif; ?></div>
  <?php endif; ?>
  
  <div class="left_link"><a href="<?php echo a_url('user/profile/autologin') ?>">Автологин</a></div>
  
  <div class="left_link"><a href="<?php echo a_url('user/change_password') ?>">Сменить пароль</a></div>

  <div class="left_link"><a href="<?php echo a_url('user/exit') ?>">Выход</a></div>
  
  <div class="bb"> </div>
  
  </div>
  
  <?php  
}

?>
</div>
<div id="wrap">






<div id="left">

<?php if(modules::is_active_module('ads_manager')) echo '<div class="ads_title_back"><div class="ads_title">Реклама</div></div>'. ads_manager::get_ads_block('all_pages_up', '<div class="left_link">', '</div><div class="bb1"> </div>') ?>

<div class="main_menu_title_back">
<div class="main_menu_title">
Навигация
</div>
</div>

<?php $db = Registry::get('db'); ?>

<div class="left_link"><a href="<?php echo a_url('news') ?>">Новости сайта</a> (<?php echo $db->get_one("SELECT COUNT(*) FROM #__news"); ?>)</div>
              
<div class="left_link"><a href="<?php echo a_url('forum') ?>">Форум</a> (<?php echo $db->get_one("SELECT COUNT(*) FROM #__forum_topics"); ?>/<?php echo $db->get_one("SELECT COUNT(*) FROM #__forum_messages"); ?>)</div>

<div class="left_link"><a href="<?php echo a_url('guestbook') ?>">Гостевая</a> (<?php echo $db->get_one("SELECT COUNT(*) FROM #__guestbook"); ?>)</div>

<div class="left_link"><a href="<?php echo a_url('chat') ?>">Чат</a></div>

<div class="left_link"><a href="<?php echo a_url('downloads') ?>">Загрузки</a> (<?php echo $db->get_one("SELECT COUNT(*) FROM #__downloads_files"); ?>)<br /></div>

<div class="left_link"><a href="<?php echo a_url('lib') ?>">Библиотека</a> (<?php echo $db->get_one("SELECT COUNT(*) FROM #__lib_books"); ?>)<br /></div>


<div class="bb"> </div>

<?php if(modules::is_active_module('ads_manager')) echo '<div class="ads_title_back"><div class="ads_title">Реклама</div></div>'. ads_manager::get_ads_block('all_pages_down', '<div class="left_link">', '</div><div class="bb1"> </div>'); ?>

</div>


<div id="right">

<div class="content_block_title_back">
<div class="content_block_title">
<?php echo $GLOBALS['CONFIG']['system']['system_title'] ?>
</div>
</div>
<div class="ind_cont">


