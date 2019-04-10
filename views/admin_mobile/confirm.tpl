<?php echo $this->display('header.tpl', array('sub_title' => 'Подтверждение', 'title' => 'Подтверждение')) ?>

<div class="box">
    <h3>Подтверждение</h3>
    <div class="inside">
        <p>
            <?php echo $message ?><br />
            <a href="<?php echo $link_ok ?>">Да</a> | <a href="<?php echo $link_cancel ?>">Нет</a>
        </p>
    </div>
</div>

<?php echo $this->display('footer.tpl') ?>