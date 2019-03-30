<?php $this->display('header', array('title' => 'Личные сообщения')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Личные сообщения')) ?>

<div class="block">
    <a href="<?php echo a_url('private/send') ?>">Новое сообщение</a>
</div>

<div class="menu">
    <a href="<?php echo a_url('private/list_messages', 'folder=new') ?>">Новые</a> (<?php echo a_default($folders['new']) ?>)<br />
    <a href="<?php echo a_url('private/list_messages', 'folder=inbox') ?>">Входящие</a> (<?php echo a_default($folders['inbox']) ?>)<br />
    <a href="<?php echo a_url('private/list_messages', 'folder=outbox') ?>">Исходящие</a> (<?php echo a_default($folders['outbox']) ?>)<br />
    <a href="<?php echo a_url('private/list_messages', 'folder=saved') ?>">Сохраненные</a> (<?php echo a_default($folders['saved']) ?>)<br />
</div>

<div class="block">
    <a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>