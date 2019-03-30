<?php $this->display('header', array('title' => 'Переименовать файл')) ?>

<?php if($error): ?>
<div class="error">
    <?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('downloads/admin/file_rename', 'file_id='. $_GET['file_id']) .'&amp;field_name='. $_GET['field_name'] ?>" method="post">
    <div class="box">
        <h3>Переименовать файл</h3>
        <div class="inside">
            <p>
                <label>Новое имя</label>
                <input name="new_name" type="text" value="<?php echo $file[$_GET['field_name']] ?>">
            </p>
        </div>
    </div>

    <p><input type="submit" name="submit" value="Переименовать"></p>

</form>

<?php $this->display('footer') ?>