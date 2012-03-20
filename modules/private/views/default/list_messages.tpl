<?php $this->display('header', array('title' => 'Личные сообщения | '. $folder_name)) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => $folder_name)) ?>

<?php if($messages): ?>
<?php foreach($messages as $message): ?>
<div class="menu">
<span class="small_text">
<?php if($message['user_to_id'] == USER_ID): ?>Отправитель: <a href="<?php echo a_url('user/profile/view', 'user_id='. $message['user_from_id']) ?>"><?php echo $message['username_from'] ?></a><?php else: ?>Получатель: <a href="<?php echo a_url('user/profile/view', 'user_id='. $message['user_to_id']) ?>"><?php echo $message['username_to'] ?></a><?php endif; ?> (<?php echo date('d.m.Y в H:i', $message['time']) ?>)<br />
</span>
<?php echo $message['message'] ?><br />
<span style="font-size: 11px;">
[<a href="<?php echo a_url('private/send', 'username='. ($message['user_to_id'] == USER_ID ? $message['username_from'] : $message['username_to'])) ?>">ответить</a>]
<?php if($_GET['folder'] != 'saved'): ?>
[<a href="<?php echo a_url('private/save_message', 'message_id='. $message['message_id']) ?>">сохранить</a>]
<?php endif; ?>
[<a href="<?php echo a_url('private/delete_message', 'message_id='. $message['message_id'] .'&amp;folder='. $_GET['folder']) ?>">удалить</a>]
</span>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="block">
<p>Сообщений нет</p>
</div>
<?php endif; ?>

<?php if($pagination)
	echo '<div class="block">'. $pagination .'</div>';
?>

<div class="block">
<a href="<?php echo a_url('private') ?>">Личные сообщения</a><br />
<a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>