<?php $this->display('header', array('title' => ($action == 'add' ? 'Добавление' : 'Редактирование') .' блока')) ?>

<?php if($error): ?>
<div class="error">
    <?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('index_page/admin/block_edit', 'block_id='. $_GET['block_id']) ?>" method="post">
    <div class="box">
        <h3><?php echo ($action == 'add' ? 'Добавление' : 'Редактирование') ?> блока</h3>
        <div class="inside">
            <p>
                <label>Заголовок блока</label>
                <input name="title" type="text" value="<?php echo $block['title'] ?>">
            </p>
        </div>
    </div>
    <p><input type="submit" name="submit" value="<?php echo ($action == 'add' ? 'Добавить' : 'Изменить') ?>"></p>
</form>

<?php $this->display('footer') ?>