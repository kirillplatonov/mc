<?php $this->display('header', array('title' => $title)) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Настройки')) ?>

<div class="block">
	<?php echo ($action == 'main' ? '<u>Основные</u>' : '<a href="'. a_url('user/profile/settings', 'action=main') .'">Основные</a>') ?> | <?php echo ($action == 'profile' ? '<u>Анкета</u>' : '<a href="'. a_url('user/profile/settings', 'action=profile') .'">Анкета</a>') ?> | <?php echo (ROUTE_ACTION == 'change_password' ? '<u>Безопасность</u>' : '<a href="'. a_url('user/change_password') .'">Безопасность</a>') ?>
</div>

<form action="<?php echo a_url('user/profile/settings', 'action='. $action) ?>" enctype="multipart/form-data" method="post">
	<?php if ($action == 'profile'): ?>
		<div class="menu">
			Страна:<br />
			<input name="country" type="text" value="<?php echo $profile['country'] ?>" /><br />
			
			Город:<br />
			<input name="sity" type="text" value="<?php echo $profile['sity'] ?>" /><br />
			
			Интересы:<br />
			<input name="hobbi" type="text" value="<?php echo $profile['hobbi'] ?>" /><br />
			
			Телефон (модель):<br />
			<input name="mobile" type="text" value="<?php echo $profile['mobile'] ?>" /><br />
			
			Оператор:<br />
			<input name="provider" type="text" value="<?php echo $profile['provider'] ?>" /><br />
			
			Сайт, блог:<br />
			<input name="homepage" type="text" value="<?php echo $profile['homepage'] ?>" /><br />
			
			ICQ:<br />
			<input name="uin" type="text" value="<?php echo $profile['uin'] ?>" /><br />
			
			Skype:<br />
			<input name="skype" type="text" value="<?php echo $profile['skype'] ?>" /><br />
			
			Jabber:<br />
			<input name="jabber" type="text" value="<?php echo $profile['jabber'] ?>" /><br />

			<input type="submit" name="submit" value="Применить" />
		</div>
	<?php else: ?>
		<div class="menu">
			Аватар:<br />
			<?php if ($profile['avatar']): ?>
				<?php echo avatar(USER_ID) ?><br />
				Укажите новый если хотите заменить этот<br />
			<?php endif; ?>
			<input name="avatar" type="file" value="" /><br />

			Реальное имя:<br />
			<input name="real_name" type="text" value="<?php echo $profile['real_name'] ?>" /><br />

			Пол:<br />
			<select size="1" name="sex">
				<option value="">Не указан</option>
				<option value="m"<?php if ($profile['sex'] == 'm') echo ' selected="selected"' ?>>Мужской</option>
				<option value="w"<?php if ($profile['sex'] == 'w') echo ' selected="selected"' ?>>Женский</option>
			</select><br />

			Дата рождения:<br />
			<?php echo main_form::select_date($select_date_birthday) ?><br />

			О себе:<br />
			<textarea name="about" rows="5" cols="20"><?php echo $profile['about'] ?></textarea><br />

			<input type="submit" name="submit" value="Применить" />
		</div>
	<?php endif ?>
</form>

<div class="block">
	<a href="<?php echo a_url(MAIN_MENU) ?>">В кабинет</a><br />
	<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>