<?php $this->display('header', array('title' => 'Загрузка файла')) ?>

<?php if($error): ?>
<div class="error">
<?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('downloads/admin/file_upload', 'directory_id='. @$_GET['directory_id']) .'&amp;file_id='. @$_GET['file_id'] ?>" enctype="multipart/form-data" method="post">
<div class="box">
	<h3>Загрузка файла</h3>
	<div class="inside">
		<p>
			<label>Имя файла (необязательно)</label>
			<input name="name" type="text" value="<?php echo $file['name'] ?>">
		</p>
        <p>
        	<label>Укажите файл</label>
			<?php if($action == 'edit'): ?>
			Загруженный файл: <?php echo $file['real_name'] ?> [<a href="<?php echo a_url('downloads/admin/file_rename', 'file_id='. $file['file_id'] .'&amp;field_name=real_name') ?>">переименовать</a>]<br />
			<?php else: ?>

			<?php endif; ?>
			<input name="file_upload" type="file" value=""><br />
			<input name="file_import" type="text" value="http://">
		</p>
        <p>
        	<label>Скриншоты</label>
			<?php for($i = 1; $i <= 3; $i++): ?>
			<?php if(!empty($file['screen'. $i])): ?>
			<img src="<?php echo URL . $file['path_to_file'] .'/'. $file['screen'. $i] ?>" width="" height="" alt="" /><br />
			<?php endif; ?>
			<input name="screen<?php echo $i ?>" type="file" value=""><br />
			<input name="screen<?php echo $i ?>" type="text" value="http://"><br /><br />
			<?php endfor; ?>
		</p>
        <p>
			<label>Описание файла (необязательно)</label>
			<textarea cols="20" rows="7" name="about"><?php echo $file['about'] ?></textarea>
		</p>
        <p>
			<label>Статус файла</label>
			<select size="1" name="status">
			  	<option value="active">Активен</option>
			  	<option value="moderate"<?php if($file['status'] == 'moderate'): ?> selected="selected"<?php endif; ?>>На модерации</option>
			</select>
        </p>
   </div>
</div>
<div class="box">
	<h3>Дополнительные файлы</h3>
	<div class="inside">
		<p>
			<?php for($i = 1; $i <= 5; $i++): ?>
        	<?php if($file['add_file_real_name_'. $i] != ''): ?>
			Загруженный файл: <?php echo $file['add_file_real_name_'. $i] ?> [<a href="<?php echo a_url('downloads/admin/file_rename', 'file_id='. $file['file_id'] .'&amp;field_name=add_file_real_name_'. $i) ?>">переименовать</a>]<br />
			<?php endif; ?>
			<input name="add_file_file_<?php echo $i ?>" type="file" value=""><br />
            <input name="add_file_file_<?php echo $i ?>" type="text" value="http://"><br /><br />
			<?php endfor; ?>
		</p>
</div>
</div>
		<p><input type="submit" name="submit" value="Готово"></p>
</form>

<?php $this->display('footer') ?>