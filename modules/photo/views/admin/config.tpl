<?php $this->display('header', array('title' => 'Настройки модуля загрузок')) ?>

<form action="" method="post">
          <div class="box">
	<h3>Настройки модуля загрузок</h3>
	<div class="inside">
	       <p>
		      <label>Файлов на страницу</label>
		      <input name="files_per_page" type="text" value="<?php echo $_config['files_per_page'] ?>">
	        </p>
	        <p>
		      <label>Папок на страницу</label>
		      <input name="directories_per_page" type="text" value="<?php echo $_config['directories_per_page'] ?>">
	        </p>
	        <p>
		      <label>Создавать скрины для видео</label>
		      <select size="1" name="make_screens_from_video">
  				<option value="1">Да</option>
  				<option value="0"<?php if(!$_config['make_screens_from_video']) echo ' selected="selected"' ?>>Нет</option>
			  </select>
	        </p>
	        <p>
		      <label>Разрешенные типы файлов для загрузки пользователями</label>
		      <input name="allowed_filetypes" type="text" value="<?php echo $_config['allowed_filetypes'] ?>">
	        </p>
		    <p>
		      <label>Максимальный размер файла для загрузки пользователями в мегабайтах</label>
		      <input name="max_filesize" type="text" value="<?php echo $_config['max_filesize'] ?>">
	        </p>
	        <p>
		      <label>Ширина скриншотов (0 если не хотите изменять скриншоты)</label>
		      <input name="screens_width" type="text" value="<?php echo $_config['screens_width'] ?>">
	        </p>
		<p>
		      <label>Время кеширования, в минутах</label>
		      <input name="cache_time" type="text" value="<?php echo $_config['cache_time'] ?>">
	        </p>
	</div>
</div>

<p><input type="submit" id="submit" name="submit" value="Сохранить"  /></p>

</form>

<?php $this->display('footer.tpl') ?>