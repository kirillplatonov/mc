<?php $this->display('header.tpl', array('sub_title' => 'Регистрация')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Регистрация')) ?>

<form action="<?php echo a_url('user/registration') ?>" method="post">
	<div class="menu">
		Логин:<br />
		<input name="username" type="text" value="<?php echo str_safe($_POST['username']) ?>" /><br />

		E-mail:<br />
		<input name="email" type="text" value="<?php echo str_safe($_POST['email']) ?>" /><br />

		Пароль:<br />
		<input name="password" type="password" /><br />

		Подтвердите пароль:<br />
		<input name="password2" type="password" /><br />

        <?php if ($_config['registration_captcha'] == 1): ?>
            Введите код с картинки:<br />
            <?php captcha(); ?>
            <input name="captcha_code" type="text" /><br />
        <?php endif ?>

		<input type="submit" name="submit" value="Регистрировать" />
	</div>
</form>

<div class="block">
	<a href="<?php echo url('user/login') ?>">Войти на сайт</a><br />
	<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer.tpl') ?>