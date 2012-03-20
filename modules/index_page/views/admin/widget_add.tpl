<?php $this->display('header', array('title' => 'Добавить виджет')) ?>

<?php if($error): ?>
<div class="error">
<?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('index_page/admin/widget_add', 'block_id='. $_GET['block_id']) ?>" method="post">
<div class="box">
	<h3>Добавить виджет</h3>
	<div class="inside">
		<p>
			<label>Заголовок (виден только вам)</label>
			<input name="title" type="text" value="">
		</p>
		<p>
			<label>Модуль</label>
			<select size="1" name="module">
			<?php foreach($widgets as $module_name => $module_title): ?>
  				<option value="<?php echo $module_name ?>"><?php echo $module_title ?></option>
  			<?php endforeach; ?>
			</select>
		</p>
   	</div>
</div>
        <p><input type="submit" name="submit" value="Добавить"></p>
</form>

<?php $this->display('footer') ?>