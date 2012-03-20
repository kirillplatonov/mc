<?php $this->display('header', array('title' => 'Чат | Прихожая')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Чат | Прихожая')) ?>

<?php if(!empty($rooms)): ?>
    <?php foreach($rooms as $room): ?>
	<div class="menu">
		<img src="<?php echo URL ?>views/<?php echo THEME ?>/img/icon.png" alt="" /> <a href="<?php echo a_url('chat/in_room', 'room_id='. $room['room_id']) ?>"><?php echo $room['name'] ?></a> <span class="small_text">[<?php echo $room['users_in_room'] ?>]</span><br />
	</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="menu">Комнат нет...</div>
<?php endif; ?>

<div class="block">
<a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>