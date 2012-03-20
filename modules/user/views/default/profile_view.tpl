<?php $this->display('header', array('title' => 'Анкета '. $profile['username'])) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Анкета '. $profile['username'])) ?>

<div class="menu">
	<?php echo avatar($profile['user_id']) ?>
		
	<?php if ( ! empty($profile['real_name'])): ?>
		Настоящее имя: <?php echo $profile['real_name'] ?><br />
	<?php endif ?>
		
	<?php if ( ! empty($profile['country'])): ?>
		Страна: <?php echo $profile['country'] ?><br />
	<?php endif ?>
		
	<?php if ( ! empty($profile['sity'])): ?>
		Город: <?php echo $profile['sity'] ?><br />
	<?php endif ?>
		
	<?php if ( ! empty($profile['hobbi'])): ?>
		Увлечения: <?php echo $profile['hobbi'] ?><br />
	<?php endif ?>
		
	<?php if ( ! empty($profile['mobile'])): ?>
		Мобильный телефон: <?php echo $profile['mobile'] ?><br />
	<?php endif ?>
		
	<?php if ( ! empty($profile['provider'])): ?>
		Оператор: <?php echo $profile['provider'] ?><br />
	<?php endif ?>
		
	<?php if ( ! empty($profile['skype'])): ?>
		Skype: <?php echo $profile['skype'] ?><br />
	<?php endif ?>
		
	<?php if ( ! empty($profile['jabber'])): ?>
		Jabber: <?php echo $profile['jabber'] ?><br />
	<?php endif ?>
		
	<?php if ( ! empty($profile['sex'])): ?>
		Пол: <?php echo ($profile['sex'] == 'm' ? 'мужской' : 'женский') ?><br />
	<?php endif ?>
		
	<?php if ( ! empty($profile['birthday_time'])): ?>
		Дата рождения: <?php echo date('d.m.Y г.', $profile['birthday_time']) ?><br />
	<?php endif ?>
		
	<?php if ( ! empty($profile['about'])): ?>
		О себе: <?php echo $profile['about'] ?><br />
	<?php endif ?>
		
	Рейтинг: <?php echo $profile['rating'] ?><br />
	
	Репутация: <?php echo '+'. $profile['reputation_plus'] .'/-'. $profile['reputation_minus'] ?> <span style="font-size: 11px;">(<a href="<?php echo a_url('user/profile/change_reputation', 'user_id='. $profile['user_id'] .'&amp;type=plus') ?>"><span style="color: green;">плюс</span></a>/<a href="<?php echo a_url('user/profile/change_reputation', 'user_id='. $profile['user_id'] .'&amp;type=minus') ?>"><span style="color: red;">минус</span></a>)</span><br />
	
	<?php if (modules::is_active_module('forum')): ?>
		Сообщений в форуме: <?php echo $profile['forum_messages'] ?><br />
	<?php endif ?>
		
	<?php if (modules::is_active_module('chat')): ?>
		Сообщений в чате: <?php echo $profile['chat_messages'] ?><br />
	<?php endif ?>
		
	<?php if (modules::is_active_module('guestbook')): ?>
		Сообщений в гостевой: <?php echo $profile['guestbook_messages'] ?><br />
	<?php endif ?>
		
	<?php if ( ! empty($profile['uin'])): ?>
		Номер ICQ: <?php echo $profile['uin'] ?><br />
	<?php endif ?>
		
	<?php if ( ! empty($profile['homepage'])): ?>
		Сайт: <a href="http://<?php echo $profile['homepage'] ?>"><?php echo $profile['homepage'] ?></a><br />
	<?php endif ?>
		
	Дата регистрации: <?php echo date('d.m.Y г.', $profile['reg_time']) ?><br />
	
	Последнее посещение: <?php echo date('d.m.Y г.', $profile['last_visit']) ?><br />
		
	<?php if (modules::is_active_module('blogs')): ?>
		<img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo a_url('blogs', 'user_id='. $profile['user_id']) ?>">Блог</a><br />
	<?php endif ?>
		
	<?php if (modules::is_active_module('photo')): ?>
		<img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo a_url('photo/list_albums', 'user_id='. $profile['user_id']) ?>">Фотоальбомы</a> (<?php echo $profile['photo_albums'] .'/'. $profile['photo'] ?>)<br />
	<?php endif ?>

	<?php if (modules::is_active_module('blog')): ?>
		<img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo URL .'profile/'. $profile['username'] .'/blog' ?>">Блог</a> (<?php echo $profile['blog'] ?>)<br />
	<?php endif ?>

	<?php if ($profile['user_id'] == USER_ID): ?>
		[<a href="<?php echo a_url('user/profile/edit') ?>">Изменить</a>]
	<?php elseif (USER_ID != -1): ?>
		[<a href="<?php echo a_url('private/send', 'username='. $profile['username']) ?>">Написать сообщение</a>]
	<?php endif ?>
</div>

<div class="menu">
	Адрес анкеты <?php echo $profile['username'] ?>:<br />
	<input type="text" value="<?php echo URL .'profile/'. $profile['username'] ?>" />
</div>

<div class="block">
	<?php if (USER_ID != -1): ?>
		<a href="<?php echo a_url(MAIN_MENU) ?>">В кабинет</a><br />
	<?php endif ?>
	
	<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>