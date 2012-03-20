<?php $this->display('header', array('title' => 'Редактирование анкеты')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Редактирование анкеты')) ?>

<form action="<?php echo a_url('user/profile/edit') ?>" enctype="multipart/form-data" method="post">
	<div class="menu">
		Аватар:<br />
		<?php if ($profile['avatar']): ?>
			<img src="<?php echo URL .'files/avatars/'. USER_ID .'_100.jpg?'. rand(0, 99) ?>" alt="" /><br />
			Укажите новый если хотите заменить этот<br />
		<?php endif; ?>
		<input name="avatar" type="file" value="" /><br />

		Реальное имя:<br />
		<input name="real_name" type="text" value="<?php echo $profile['real_name'] ?>" /><br />

		Пол:<br />
		<select size="1" name="sex">
			<option value="">Не указан</option>
			<option value="m"<?php if($profile['sex'] == 'm') echo ' selected="selected"' ?>>Мужской</option>
			<option value="w"<?php if($profile['sex'] == 'w') echo ' selected="selected"' ?>>Женский</option>
		</select><br />

		Дата рождения:<br />
		<?php echo main_form::select_date($select_date_birthday) ?><br />

		О себе:<br />
		<textarea name="about" rows="5" cols="20"><?php echo $profile['about'] ?></textarea><br />

		Номер ICQ (только цифры):<br />
		<input name="uin" type="text" value="<?php echo $profile['uin'] ?>" /><br />

		Сайт:<br />
		<input name="homepage" type="text" value="<?php echo $profile['homepage'] ?>" /><br />

		<input type="submit" name="submit" value="Применить" />
	</div>
</form>

<div class="block">
	<a href="<?php echo a_url(MAIN_MENU) ?>">В кабинет</a><br />
	<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>