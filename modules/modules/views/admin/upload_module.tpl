<?php $this->display('header', array('title' => 'Загрузка модуля')) ?>

<?php if($error): ?>
<div class="error">
<?php echo $error ?>
</div>
<?php endif; ?>

<form enctype="multipart/form-data" action="<?php echo a_url('modules/admin/upload_module') ?>" method="post">
<div class="box">
	<h3>Загрузить модуль</h3>
	<div class="inside">
		<p>
			<label>ZIP файл модуля</label>
			<input name="module" type="file">
		</p>
   	</div>
</div>
        <p><input type="submit" name="submit" value="Загрузить"></p>
</form>

<?php $this->display('footer') ?>