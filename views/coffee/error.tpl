<?php $this->display('header', array('sub_title' => 'Произошла ошибка')) ?>

<?php echo $this->display('title', array('text' => 'Произошла ошибка')) ?>

<div class="menu">
        <?php echo $error_message ?><br />
        <?php if ($link) echo '<a href="'. $link .'">Вернуться назад</a>' ?>
</div>

<div class="block">
    <a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>