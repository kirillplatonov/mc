<?php $this->display('header', array('title' => 'Пользователи')) ?>

<?php if($error): ?>
<div class="error">
<?php echo $error ?>
</div>
<?php endif ?>

<form action="<?php echo a_url('user/admin/ban', 'user_id='. $_GET['user_id']) ?>" method="post">
<?php if ($ban): ?>
	<div class="box">
		<h3>Текущий бан пользователя <?php echo $username_ban ?></h3>
		
		<p>
		     <label>Причина</label>
		     <?php echo $ban['description']; ?>
		</p>
		
		<p>
		     <label>Оставшееся время</label>
		     <?php echo date('H:i:s', $ban['to_time'] - TIME()); ?>
		</p>
	</div>
	
	<p><input type="submit" name="delete_ban" value="Разбанить пользователя"></p>
<?php else: ?>
<div class="box">
	<h3>Бан пользователя <?php echo $username_ban ?></h3>
	<div class="inside">
		<p>
			<label>Время бана (в часах)</label>
			<input name="hours" type="text" value="<?php echo @$_POST['hours'] ?>">
		</p>
		<p>
			<label>Причина бана</label>
			<textarea cols=40 rows=10 name="description"><?php echo @$_POST['description'] ?></textarea>
		</p>
	</div>
</div>
	<p><input type="submit" name="submit" value="Применить"></p>
<?php endif; ?>
</form>

<?php $this->display('footer') ?>