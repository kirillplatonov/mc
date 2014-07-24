<?php $this->display('header.tpl', array('title' => 'Выбор web темы')) ?>

<form action="" method="post">
          <div class="box">
	<h3>Выбор web темы</h3>
	<div class="inside">
	        <p>
		      <label>Тема сайта по умолчанию</label>
		      <select name="web_theme">
		      <?php foreach($web_theme as $theme): ?>
		        <option value="<?php echo $theme['name'] ?>"<?php if($_config['web_theme'] == $theme['name']): ?> selected='selected'<?php ENDIF ?>><?php echo $theme['title'] ?></option>
		      <?php endforeach; ?>
		      </select>
	        </p>
	</div>
</div>

<p><input type="submit" id="submit" name="submit" value="Сохранить"  /></p>

</form>

<?php $this->display('footer.tpl') ?>
