<?php $this->display('header', array('title' => ($action == 'edit'?'Редактирование новости':'Добавление новости'))); ?>

<?php if (modules::is_active_module('tinymce')) include ROOT .'modules/tinymce/views/head.tpl'; ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>'; ?>

<form action="<?php echo a_url('news/admin/edit', 'news_id='. $_GET['news_id']) ?>" method="post">
	<div class="box">
		<h3><?php echo ($action == 'edit'?'Редактирование новости':'Добавление новости'); ?></h3>
		
		<div class="inside">
		<p>
			<label>Заголовок</label>
			<input name="subject" type="text" value="<?php echo $news['subject']; ?>">
		</p>
		
		<p>
			<label>Текст новости</label>
			<textarea name="editor_text"><?php echo $news['text']; ?></textarea>
		</p>
		</div>
	</div>
	
	<p>
	     <input type="submit" name="submit" value="Отправить">
	</p>
</form>

<?php $this->display('footer'); ?>