<?php $this->display('header', array('title' => ($action == 'edit' ? 'Редактировать' : 'Создать') .' страницу')) ?>
<?php if(modules::is_active_module('tinymce')) include ROOT .'modules/tinymce/views/head.tpl'; ?>

<?php if($error): ?>
<div class="error">
<?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('pages/admin/page_edit', 'page_id='. @$_GET['page_id']) ?>" method="post">
<div class="box">
	<h3><?php echo ($action == 'edit' ? 'Редактировать' : 'Создать') ?> страницу</h3>
	<div class="inside">
	<p>
		<label>Заголовок</label>
		<input name="title" type="text" value="<?php echo $page['title'] ?>">
	</p>
    <p>
    	<label>Содержимое страницы</label>
		<textarea name="editor_content" wrap="off"><?php echo $page['content'] ?></textarea>
	</p>
	</div>
</div>

<p><input type="submit" name="submit" value="<?php echo ($action == 'edit' ? 'Изменить' : 'Создать') ?>"></p>

</form>

<?php $this->display('footer') ?>