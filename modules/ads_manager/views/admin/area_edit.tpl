<?php $this->display('header', array('title' => 'Менеджер продажи рекламы')) ?>

<?php if($error): ?>
<div class="error">
    <?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('ads_manager/admin/area_edit', 'area_id='. @$_GET['area_id']) ?>" method="post">
    <div class="box">
        <h3><?php echo ($action == 'add' ? 'Добавление' : 'Редактирование') ?> рекламной площадки</h3>
        <div class="inside">
            <p>
                <label>Название площадки</label>
                <input name="title" type="text" value="<?php echo @$area['title'] ?>">
            </p>
            <p>
                <?php if($action == 'add'): ?>
                <label>Идентификатор</label>
                <input name="ident" type="text" value="<?php echo @$area['ident'] ?>">
                <?php endif; ?>
            </p>
        </div>
    </div>
    <p><input type="submit" name="submit" value="<?php echo ($action == 'add' ? 'Добавить' : 'Изменить') ?>"></p>
</form>

<?php $this->display('footer') ?>