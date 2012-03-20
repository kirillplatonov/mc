<?php $this->display('header', array('sub_title' => 'Форум')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Форум')) ?>

<div class="block">
Новые: <a href="<?php echo a_url('forum/viewforum', 'type=new') ?>">темы</a> | <a href="<?php echo a_url('forum/new_messages') ?>">сообщения</a>
</div>

<?php if($sections): ?>
	<?php foreach($sections as $section): ?>
	<div class="menu">
	<img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo a_url('forum', 'section_id='. $section['section_id']) ?>"><?php echo $section['name'] ?></a>
	</div>
	<?php if($section['forums']): ?>
		<?php foreach($section['forums'] as $forum): ?>
        <div class="block" style="margin: 1px 0px 0px 5px;">
        <a href="<?php echo a_url('forum/viewforum', 'forum_id='. $forum['forum_id']) ?>"><?php echo $forum['name'] ?></a> [<?php echo $forum['topics'] .'/'. $forum['messages'] ?>]
        </div>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php endforeach; ?>
<?php else: ?>
	<div class="block">
	<p>Разделов нет</p>
	</div>
<?php endif; ?>

<div class="block">
<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>