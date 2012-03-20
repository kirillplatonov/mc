<?php $this->display('header.tpl', array('sub_title' => 'Добро пожаловать!')) ?>

<div class="title"><img src="<?php echo URL ?>views/<?php echo THEME ?>/img/titl.gif" class="ico" alt="" /><b>Новости</b></div>
<div class="menu" style="padding-left: 3px; font-size: 11px;">
<i><?php echo $last_news['subject'] ?></i> (<?php echo date('d.m.Y', $last_news['time']) ?>г.)<br />
<?php echo main::limit_words(strip_tags($last_news['text']), 10) ?>...<br />
<a href="<?php echo a_url('news', 'news_id='. $last_news['news_id']) ?>">подробнее</a> | <a href="<?php echo a_url('news') ?>">ранее</a>
</div>

<div class="title"><img src="<?php echo URL ?>views/<?php echo THEME ?>/img/titl.gif" class="ico" alt="" /><b>Бесплатные загрузки</b></div>
<div class="menu">
<img src="<?php echo URL ?>views/<?php echo THEME ?>/img/ic.png" alt="" /> <a href="<?php echo URL ?>downloads/1">Картинки</a><br />
<img src="<?php echo URL ?>views/<?php echo THEME ?>/img/ic.png" alt="" /> <a href="<?php echo URL ?>downloads/2">Мелодии</a><br />
<img src="<?php echo URL ?>views/<?php echo THEME ?>/img/ic.png" alt="" /> <a href="<?php echo URL ?>downloads/3">Игры</a><br />
<img src="<?php echo URL ?>views/<?php echo THEME ?>/img/ic.png" alt="" /> <a href="<?php echo URL ?>downloads/4">Анимация</a><br />
<img src="<?php echo URL ?>views/<?php echo THEME ?>/img/ic.png" alt="" /> <a href="<?php echo URL ?>downloads/5">Видео</a><br />
<img src="<?php echo URL ?>views/<?php echo THEME ?>/img/ic.png" alt="" /> <a href="<?php echo URL ?>downloads/6">Темы</a><br />
</div>

<div class="title"><img src="<?php echo URL ?>views/<?php echo THEME ?>/img/titl.gif" class="ico" alt="" /><b>Общение</b></div>
<div class="menu">
<img src="<?php echo URL ?>views/<?php echo THEME ?>/img/ic.png" alt="" /> <a href="<?php echo a_url('chat') ?>">Чат</a> (<?php echo $info['chat_users_online'] ?>)<br />
<img src="<?php echo URL ?>views/<?php echo THEME ?>/img/ic.png" alt="" /> <a href="<?php echo a_url('forum') ?>">Форум</a><br />
<img src="<?php echo URL ?>views/<?php echo THEME ?>/img/ic.png" alt="" /> <a href="<?php echo a_url('guestbook') ?>">Гостевая книга</a>
</div>

<?php $this->display('footer') ?>