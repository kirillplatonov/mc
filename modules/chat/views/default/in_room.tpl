<?php $this->display('header', array('title' => 'Чат | '. $room['name'])) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Чат | '. $room['name'])) ?>

<div class="block">
<a href="<?php echo a_url('chat/say', 'room_id='. $room['room_id']) ?>">Написать</a><br />
<a href="<?php echo a_url('chat/in_room', 'room_id='. $room['room_id'] .'&amp;rand='. rand(111, 999)) ?>">Обновить</a><br />
</div>

<?php if($messages): ?>
    <?php foreach($messages as $message): ?>
	<div class="menu">
        <?php echo user::get_icon($message['user_id']) ?> <?php echo user::get_username($message['user_id'], TRUE) ?> <?php echo user::online_status($message['last_visit']) ?> (<?php echo main::display_time($message['time']) ?>)<br />
		<?php echo $message['message'] ?>
		<?php if (a_check_rights($message['user_id'], $message['user_status'])): ?>
			<br />
			<span class="action">
				[<a href="<?php echo a_url('chat/delete_message', 'message_id='. $message['message_id'] .'&amp;room_id='. $room['room_id']) ?>">Удалить</a>]
			</span>
		<?php endif ?>
	</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="menu">
	Сообщений нет
	</div>
<?php endif; ?>

<?php if(!empty($pagination))
	echo '<div class="block">'. $pagination .'</div>';
?>

<div class="block">
<a href="<?php echo a_url('chat/say', 'room_id='. $room['room_id']) ?>">Написать</a><br />
<a href="<?php echo a_url('chat/in_room', 'room_id='. $room['room_id'] .'&amp;rand='. rand(111, 999)) ?>">Обновить</a><br />
</div>

<div class="block">
<a href="<?php echo a_url('chat') ?>">В прихожую</a><br />
<a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>