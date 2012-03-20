<?php $this->display('header', array('title' => 'Пользователи')) ?>

<?php if($error): ?>
<div class="error">
<?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('user/admin/edit', 'user_id='. $_GET['user_id']) ?>" method="post">
<div class="box">
	<h3>Изменить пользователя <?php echo $user_edit['username'] ?></h3>
	<div class="inside">
		<p>
			<label>E-mail (<a href="<?php echo a_url('user/admin/user_email', 'user_id='. $user_edit['user_id']) ?>">Написать сообщение</a>)</label>
			<input name="email" type="text" value="<?php echo $user_edit['email'] ?>">
		</p>
		<p>
			<label>Статус</label>
			<select name="status">
				<option value="user"<?php if($user_edit['status'] == 'user') echo ' selected="selected"'; ?>>Пользователь</option>
				<option value="moder"<?php if($user_edit['status'] == 'moder') echo ' selected="selected"'; ?>>Модератор</option>
				<option value="admin"<?php if($user_edit['status'] == 'admin') echo ' selected="selected"'; ?>>Администратор</option>
			</select>
        </p>
   	</div>
</div>
        <p><input type="submit" name="submit" value="Применить"></p>
</form>

<?php $this->display('footer') ?>