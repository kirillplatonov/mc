<?php $this->display('header', array('sub_title' => 'Вход')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Вход')) ?>

<form action="<?php echo a_url('user/login') ?>" method="post">
	<div class="menu">
		Логин:<br />
		<input name="username" class="input" type="text" value="<?php echo str_safe($_POST['username']) ?>" /><br />

		Пароль:<br />
		<input name="password" type="password" /><br />
	
		<?php if ($_SESSION['login_errors'] > 0 && $_config['login_captcha'] == 1): ?>
            Введите код с картинки:<br />
            <?php captcha(); ?>
            <input name="captcha_code" type="text" /><br />
		<?php endif ?>
			
		<input type="checkbox" name="remember_me" value="1" checked="checked" /> Запомнить меня<br />

		<input type="submit" name="submit" value="Войти" />
	</div>
</form>

<div class="block">
	<a href="<?php echo url('user/registration') ?>">Регистрация</a><br />
	<a href="<?php echo url('user/forgot') ?>">Забыли пароль?</a><br />
	<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>