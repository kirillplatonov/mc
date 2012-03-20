<?php echo $this->display('header', array('title' => 'Информация')) ?>

<?php echo $this->display('title', array('text' => 'Информация')) ?>

<div class="menu">
        <?php echo $message ?><br />
        <a href="<?php echo $link ?>">Продолжить</a>
</div>

<div class="block">
    <a href="<?php echo URL ?>">На главную</a>
</div>

<?php echo $this->display('footer') ?>