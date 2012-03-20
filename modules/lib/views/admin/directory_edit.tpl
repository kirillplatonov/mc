<?php $this->display('header', array('title' => ($action == 'edit' ? 'Изменить' : 'Создать') .' папку')) ?>

<?php if($error): ?>
<div class="error">
<?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('lib/admin/directory_edit', 'directory_id='. $_GET['directory_id']) .'&amp;parent_id='. $_GET['parent_id'] ?>" method="post">
<div class="box">
	<h3><?php echo ($action == 'edit' ? 'Изменить' : 'Создать') ?> папку</h3>
	<div class="inside">
	<p>
		<label>Имя папки</label>
		<input name="name" type="text" value="<?php echo @$directory['name'] ?>">
	</p>
	</div>
</div>

<p><input type="submit" name="submit" value="<?php echo ($action == 'edit' ? 'Изменить' : 'Создать') ?>"></p>

</form>

<?php $this->display('footer') ?>