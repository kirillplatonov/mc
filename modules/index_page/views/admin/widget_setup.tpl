<?php $this->display('header', array('title' => 'Настройка виджета')) ?>

<?php if($error): ?>
<div class="error">
    <?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('index_page/admin/widget_setup', 'widget_id='. @$_GET['widget_id']) ?>" method="post">
    <div class="box">
        <h3>Настройка виджета</h3>
        <div class="inside">
            <?php echo $form_data ?>
        </div>
    </div>
    <p><input type="submit" name="submit" value="Сохранить"></p>
</form>

<?php $this->display('footer') ?>