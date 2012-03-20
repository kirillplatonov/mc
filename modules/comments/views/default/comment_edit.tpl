<?php $this->display('header.tpl', array('sub_title' => 'Изменение комментария')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Изменение комментария')) ?>

<form action="<?php echo a_url('comments/comment_edit', 'comment_id=' . $comment['comment_id'] . '&amp;return_name=' . urlencode('Вернуться') . '&amp;return_url=' . urlencode(str_replace('&amp;', '&', html_entity_decode($_GET['return_url'])))) ?>" method="post">
<div class="menu">
Сообщение:<br />
<textarea name="message" rows="5" cols="20"><?php echo $comment['text'] ?></textarea><br />
<input type="submit" name="submit" value="Сохранить" />
</div>
</form>

<div class="block">
<a href="<?php echo urldecode($_GET['return_url']) ?>"><?php echo urldecode($_GET['return_name']) ?></a><br />
<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>