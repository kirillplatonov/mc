<?php echo $this->display('header.tpl', array('sub_title' => 'Информация')) ?>

<div class="box">
    <h3>Информация</h3>
    <div class="inside">
        <p>
            <?php echo $message ?><br />
            <a href="<?php echo $link ?>">Продолжить</a>
        </p>
    </div>
</div>

<?php echo $this->display('footer.tpl') ?>