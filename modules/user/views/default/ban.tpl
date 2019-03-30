<?php $this->display('header', array('sub_title' => 'Ваш аккаунт забанен')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Ваш аккаунт забанен')) ?>

<div class="menu">
    Причина бана: <?php echo (!empty($ban['description']) ? $ban['description'] : 'не указана') ?><br />
    До окончания бана осталось: <?php echo date('H:i:s', $ban['to_time']) ?>
</div>

<?php $this->display('footer') ?>