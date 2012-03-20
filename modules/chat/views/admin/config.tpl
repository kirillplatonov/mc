<?php $this->display('header', array('title' => 'Настройки чата')) ?>

<form action="" method="post">
          <div class="box">
	<h3>Настроики чата</h3>
	<div class="inside">
	        <p>
		      <label>Сколько времени считать пользователя в онлайне в комнате (минут)</label>
		      <input name="online_time" type="text" value="<?php echo $_config['online_time'] ?>">
	        </p>
	        <p>
		      <label>Количество сообщений на страницу</label>
		      <input name="messages_per_page" type="text" value="<?php echo $_config['messages_per_page'] ?>">
	        </p>
	        <p>
		      <label>Максимальная длина сообщения</label>
		      <input name="message_max_len" type="text" value="<?php echo $_config['message_max_len'] ?>">
	        </p>
	        <p>
		      <label>Могут ли гости находиться в чате и оставлять сообщения</label>
		      <select size="1" name="guests_in_chat">
  				<option value="0">Нет</option>
  				<option value="1"<?php if($_config['guests_in_chat'] == 1): ?> selected="selected"<?php endif; ?>>Да</option>
			  </select>
	        </p>
	</div>
</div>

<p><input type="submit" id="submit" name="submit" value="Сохранить"  /></p>

</form>

<?php $this->display('footer.tpl') ?>