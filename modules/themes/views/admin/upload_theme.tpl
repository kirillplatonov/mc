<?php $this->display('header', array('title' => 'Загрузка темы')) ?>

<?php if($error): ?>
<div class="error">
    <?php echo $error ?>
</div>
<?php endif; ?>

<form enctype="multipart/form-data" action="<?php echo a_url('themes/admin/upload_theme') ?>" method="post">
    <div class="box">
        <h3>Загрузить тему</h3>
        <div class="inside">
            <p>
                <label>ZIP файл темы</label>
                <input name="theme" type="file">
            </p>
        </div>
    </div>
    <p><input type="submit" name="submit" value="Загрузить"></p>
</form>

<?php $this->display('footer') ?>