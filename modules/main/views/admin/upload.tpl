<?php $this->display('header', array('title' => $title)) ?>

<?php if($error): ?>
<div class="error">
<?php echo $error ?>
</div>
<?php endif; ?>

<form enctype="multipart/form-data" action="<?php echo a_url('main/admin/upload', 'action='. $_GET['action']) ?>" method="post">
<div class="box">
	<h3>Загрузить</h3>
	<div class="inside">
		<p>
			<label>ZIP файл</label>
			<input name="file" type="file">
		</p>
   	</div>
</div>
        <p><input type="submit" name="submit" value="Загрузить"></p>
</form>

<?php $this->display('footer') ?>