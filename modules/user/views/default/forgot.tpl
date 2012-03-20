<?php echo $this->display('header', array('sub_title' => 'Забыли пароль?')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Забыли пароль?')) ?>

<form action="<?php echo a_url('user/forgot') ?>" method="post">
	<div class="menu">
		Логин:<br />
		<input name="username" type="text" value="<?php echo str_safe($_POST['username']) ?>" /><br />

		или<br />

		E-mail:<br />
		<input name="email" type="text" value="<?php echo str_safe($_POST['email']) ?>" /><br />

		<input type="submit" name="submit" value="Отправить" />
	</div>
</form>

<div class="block">
	<a href="<?php echo url('user/login') ?>">Войти на сайт</a><br />
	<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>