<?php echo $this->display('header', array('sub_title' => 'Подтверждение')) ?>

<?php echo $this->display('title', array('text' => 'Подтверждение')) ?>

<div class="menu">
    <?php echo $message ?><br />
    <a href="<?php echo $link_ok ?>">Да</a> | <a href="<?php echo $link_cancel ?>">Нет</a>
</div>

<div class="block">
    <a href="<?php echo URL ?>">На главную</a>
</div>

<?php echo $this->display('footer') ?>