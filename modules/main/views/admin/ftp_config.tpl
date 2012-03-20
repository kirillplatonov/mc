<?php $this->display('header', array('title' => 'Настройки FTP')) ?>

<?php if($error): ?>
<div class="error">
<?php echo $error ?>
</div>
<?php endif; ?>

<form action="" method="post">
          <div class="box">
	<h3>Настроики FTP</h3>
	<div class="inside">
	        <p>
		      <label>Сервер</label>
		      <input name="server" type="text" value="<?php echo empty($_config['server']) ? 'localhost' : $_config['server'] ?>">
	        </p>
	        <p>
		      <label>Порт</label>
		      <input name="port" type="text" value="<?php echo empty($_config['port']) ? 21 : $_config['port'] ?>">
	        </p>
	        <p>
		      <label>Пользователь</label>
		      <input name="login" type="text" value="<?php echo $_config['login'] ?>">
	        </p>
	       <p>
		      <label>Пароль</label>
		      <input name="password" type="password" value="<?php echo $_config['password'] ?>">
	        </p>
	       <p>
		      <label>Путь к корню MobileCMS по фтп</label>
		      <input name="path" type="text" value="<?php echo $_config['path'] ?>">
	        </p>
	</div>
</div>

<p><input type="submit" id="submit" name="submit" value="Сохранить"  /></p>

</form>

<?php $this->display('footer.tpl') ?>