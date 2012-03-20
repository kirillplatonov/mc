<?php $this->display('header', array('title' => 'Менеджер продажи рекламы')) ?>

<?php if($error): ?>
<div class="error">
<?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('ads_manager/admin/link_edit', 'link_id='. @$_GET['link_id']) ?>" method="post">
<div class="box">
	<h3><?php echo ($action == 'add' ? 'Добавление' : 'Редактирование') ?> рекламной ссылки</h3>
	<div class="inside">
		<p>
			<label>Название ссылки</label>
			<input name="title" type="text" value="<?php echo @$link['title'] ?>">
		</p>
        <p>
			<label>URL</label>
			<input name="url" type="text" value="<?php echo @$link['url'] ?>">
		</p>
		<p>
			<label>Тексты ссылки (новый текст с новой строки)</label>
			<textarea name="names" wrap="off"><?php echo stripslashes($link['names']) ?></textarea>
		</p>
		<p>
			<label>Площадка</label>
			<select size="1" name="area_id">
			<?php foreach($areas as $area): ?>
  				<option value="<?php echo $area['area_id'] ?>"<?php if($link['area_id'] == $area['area_id']): ?> selected="selected"<?php endif; ?>><?php echo $area['title'] ?></option>
  			<?php endforeach; ?>
			</select>
		</p>
   	</div>
</div>
        <p><input type="submit" name="submit" value="<?php echo ($action == 'add' ? 'Добавить' : 'Изменить') ?>"></p>
</form>

<?php $this->display('footer') ?>