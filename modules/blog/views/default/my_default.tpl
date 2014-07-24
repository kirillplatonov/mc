<?php $this->display('header.tpl', array('sub_title' => $title)) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title.tpl', array('text' => 'Новая запись')) ?>

<form method="post" action="<?php echo a_url('blog/my', 'action=say') ?>">
	<div class="menu">
		Заголовок:<br />
		<input type="text" name="title" value="<?php echo htmlspecialchars(trim($_POST['title'])) ?>" /><br />
	
		Сообщение: (<a href="<?php echo a_url('smiles', 'return_name='. urlencode('Вернуться') .'&amp;return_url='. urlencode(a_url('blog/my'))) ?>">смайлы</a> / <a href="<?php echo a_url('main/bbcode', 'return_name='. urlencode('Вернуться') .'&amp;return_url='. urlencode(a_url('blog/my'))) ?>">теги</a>)<br />
		<textarea name="message" rows="5" cols="20"><?php echo htmlspecialchars(trim($_POST['message'])) ?></textarea><br /> 
	
		<input type="submit" name="submit" value="Добавить" /> 
	</div>
</form>

<div class="menu">
	Записи: <?php echo ($action == 'new_posts' ? '<u>новые</u>' : '<a href="'. a_url('blog/list', 'action=new_posts') .'">новые</a>') ?>, <?php echo ($action == 'best_posts' ? '<u>лучшие</u>' : '<a href="'. a_url('blog/list', 'action=best_posts') .'">лучшие</a>') ?>, <?php echo ($action == 'all_posts' ? '<u>все</u>' : '<a href="'. a_url('blog/list', 'action=all_posts') .'">все</a>') ?>
</div>

<?php $this->display('title.tpl', array('text' => 'Ваши записи')) ?>

<?php if ($posts): ?>
	<?php foreach($posts AS $post): ?>
		<div class="menu">
			<img src="<?php echo URL ?>modules/blog/images/default/post.png" alt="" /> <b><?php echo $post['title'] ?></b> (<?php echo date('d.m.Y в H:i', $post['time']) ?>)<br />
			<?php echo $post['message'] ?><?php if ($post['long']) echo ' <a href="'. a_url('blog/read_more', 'post_id='. $post['id']) .'">Читать далее &raquo;</a>' ?><br />
			<?php echo $post['rating_stars'] ?> (<?php echo $post['rating_voices'] ?> гол.)<?php if ( ! $post['rated']): echo '<form method="post" action="'. a_url('blog/rating_change', 'post_id='. $post['id']) .'"><select size="1" name="rate"><option value="5">Отлично</option><option value="4">Хорошо</option><option value="3">Нормально</option><option value="2">Плохо</option><option value="1">Ужасно</option></select><input type="submit" value="Ok" /></form>' ?><?php else: ?><br /><?php endif ?>
			<img src="<?php echo URL ?>modules/comments/images/comment.png" alt="" /> <a href="<?php echo a_url('comments', 'module=blog&amp;item_id='. $post['id'] .'&amp;return='. urlencode(a_url('blog/my'))) ?>">Комментарии</a> (<?php echo $post['comments'] ?>)<br />
			[<a href="<?php echo a_url('blog/my', 'action=edit&amp;post_id='. $post['id']) ?>">Изменить</a>] [<a href="<?php echo a_url('blog/my', 'action=delete&amp;post_id='. $post['id']) ?>">Удалить</a>]
		</div>
	<?php endforeach ?>
<?php else: ?>
	<div class="menu">
		Записей еще нет. Добавляйте ;-)
	</div>
<?php endif ?>

<?php if ($pagination) echo '<div class="block">'. $pagination .'</div>' ?>

<div class="block">
	Ссылка на блог:<br />
	<input type="text" value="<?php echo URL .'profile/'. $user['username'] ?>/blog" />
</div>

<div class="block">
	<?php if (USER_ID != -1): ?><a href="<?php echo a_url('user/profile/cabinet') ?>">В кабинет</a><br /><?php endif ?>
	<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>