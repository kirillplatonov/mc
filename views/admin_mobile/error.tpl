<?php $this->display('header.tpl', array('sub_title' => 'Ошибка!', 'title' => 'Ошибка!')) ?>

<div class="box">
    <h3>Произошла ошибка</h3>
    <div class="inside">
        <p><?php echo $error_message ?></p>
    </div>
</div>

<?php $this->display('footer.tpl') ?>