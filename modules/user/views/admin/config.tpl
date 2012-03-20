<?php $this->display('header', array('title' => 'Настройки модуля пользователей')) ?>

<form action="<?php echo url('user/admin/config') ?>" method="post">
	<div class="box">
		<h3>Настройки модуля пользователей</h3>

		<div class="inside">
			<p>
		    	<label>Проверочный код при регистрации</label>
		    	<select name="registration_captcha">
					<option value="1">Показывать</option>
					<option value="0"<?php if ($_config['registration_captcha'] == '0') echo ' selected="selected"' ?>>Скрыть</option>
				</select>
			</p>
			
			<p>
		    	<label>Проверочный код при неудачном входе</label>
		    	<select name="login_captcha">
					<option value="1">Показывать</option>
					<option value="0"<?php if ($_config['login_captcha'] == '0') echo ' selected="selected"' ?>>Скрыть</option>
				</select>
			</p>
			
			<p>
		    	<label>Регистрация на сайте</label>
		    	<select name="registration_stop">
                            <option value="0">Открыта</option>
                            <option value="1"<?php if ($_config['registration_stop'] == '1') echo ' selected="selected"' ?>>Закрыта</option>
				</select>
			</p>
			
			<p>
		    	<label>Подтверждение E-mail при регистрации</label>
		    	<select name="email_confirmation">
				<option value="1"<?php if ($_config['email_confirmation'] == '1') echo ' selected="selected"' ?>>Включено</option>
				<option value="0"<?php if ($_config['email_confirmation'] == '0') echo ' selected="selected"' ?>>Выключено</option>
				</select>
			</p>
			
			<p>
		    	<label>Модерация зарегистрированных пользователей</label>
		    	<select name="user_moderate">
				<option value="1"<?php if ($_config['user_moderate'] == '1') echo ' selected="selected"' ?>>Включена</option>
				<option value="0"<?php if ($_config['user_moderate'] == '0') echo ' selected="selected"' ?>>Выключена</option>
				</select>
			</p>
		</div>
	</div>

<p><input type="submit" id="submit" name="submit" value="Сохранить" /></p>

</form>

<?php $this->display('footer.tpl') ?>