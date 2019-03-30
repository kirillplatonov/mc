<?php $this->display('header', array('title' => 'Загрузить книги')) ?>

<?php if($error): ?>
<div class="error">
    <?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('lib/admin/add_books', 'directory_id='. $_GET['directory_id']) .'&amp;type='. $_GET['type'] ?>" enctype="multipart/form-data" method="post">
    <div class="box">
        <h3>Загрузить книги</h3>
        <div class="inside">
            <?php for($i = 1; $i <= 10; $i++): ?>
            <p>
                <label>Книга <?php echo $i ?></label>
                Название:<br />
                <input name="name_<?php echo $i ?>" type="text" value="" /><br />
                <?php
                switch($type) {
                case 'textes':
                echo 'Содержимое:<br /><textarea name="text_'. $i .'"></textarea>';
                break;
                case 'import':
                echo 'Адрес:<br /><input name="link_'. $i .'" type="text" value="http://" />';
                break;
                case 'upload':
                default:
                echo 'Файл:<br /><input name="file_'. $i .'" type="file" value="" />';
                break;
                }
                ?>
            </p>
            <?php endfor; ?>
        </div>
    </div>

    <p><input type="submit" name="submit" value="Загрузить"></p>

</form>

<?php $this->display('footer') ?>