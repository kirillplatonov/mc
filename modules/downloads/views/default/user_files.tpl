<?php $this->display('header.tpl', array('sub_title' => $title)) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title.tpl', array('text' => $title)) ?>

<form method="post" action="<?php echo a_url('downloads/user_files', 'action='. $action .'&amp;'. ($action == 'add' ? 'directory_id='. $directory['directory_id'] : 'file_id='. $file['file_id'])) ?>" enctype="multipart/form-data">
	<div class="menu">
	    Название: <br />
	    <input type="text" name="name" value="<?php echo $file['name'] ?>" /><br />
	    
	    <?php if ($action == 'edit'): ?>
	        Текущий файл: <a href="<?php echo URL . $file['path_to_file'] .'/'. $file['real_name'] ?>"><b><?php echo $file['real_name'] ?></b></a><br />
	    <?php endif ?>
	    
	    <?php echo ($action == 'edit' ? 'Выгрузить другой файл' : 'Выгрузить файл') ?>:<br />
		<input type="file" name="file_upload" /><br />
		
		<?php echo ($action == 'edit' ? 'Импортировать другой файл' : 'Импортировать файл') ?>:<br />
		<input type="text" name="file_import" value="http://" /><br />
		
		<?php if ($action == 'edit' && $file['screen1'] != ''): ?>
	        Текущий скриншот:<br /><img src="<?php echo URL . $file['path_to_file'] .'/'. $file['screen1'] ?>" alt="" /><br />
	    <?php endif ?>
		
		<?php echo ($action == 'edit' && $file['screen1'] != '' ? 'Выгрузить другой скриншот' : 'Выгрузить скриншот') ?>:<br />
		<input type="file" name="screen1" /><br />
		
		<?php echo ($action == 'edit' && $file['screen1'] != '' ? 'Импортировать другой скриншот' : 'Импортировать скриншот') ?>:<br />
		<input type="text" name="screen1" value="http://" /><br />
		
		Описание:<br />
	    <textarea name="about" rows="5" cols="20"><?php echo $file['about'] ?></textarea><br />
	    
	    <input type="submit" name="submit" value="<?php echo ($action == 'add' ? 'Добавить' : 'Изменить') ?>" />
	</div>
</form>

<div class="block">
	<?php if ( ! empty($navigation)) echo $navigation .'<br />' ?>
	<a href="<?php echo URL ?>downloads">Загруз-центр</a><br />
	<a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>