<?php $this->display('header', array('title' => ($action == 'edit' ? 'Изменить' : 'Создать') .' папку')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<form action="<?php echo a_url('downloads/admin/directory_edit', 'directory_id='. $_GET['directory_id']) .'&amp;parent_id='. $_GET['parent_id'] ?>" method="post">
    <div class="box">
        <h3><?php echo ($action == 'edit' ? 'Изменить' : 'Создать') ?> папку</h3>
        <div class="inside">
            <p>
                <label>Имя папки</label>
                <input name="name" type="text" value="<?php echo $directory['name'] ?>">
            </p>
            <p>
                <input name="images" type="checkbox" value="ON" <?php if($directory['images'] == 'yes'): ?>checked="checked"<?php endif; ?>> Поставить выбор превьюшек для файлов<br />
                <?php if ($_config['user_upload'] == 1): ?>
                <input name="user_files" type="checkbox" value="ON" <?php if($directory['user_files'] == 'yes'): ?>checked="checked"<?php endif; ?>> Пользовательские файлы<br />
                <?php endif ?>

                <?php if($action == 'add'): ?>
                <input name="in_up" type="checkbox" value="ON"> Поставить в самый верх<br />
                <?php endif; ?>
            </p>
        </div>
    </div>

    <p><input type="submit" name="submit" value="<?php echo ($action == 'edit' ? 'Изменить' : 'Создать') ?>"></p>

</form>

<?php $this->display('footer') ?>