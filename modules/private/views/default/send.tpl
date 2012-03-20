<?php $this->display('header', array('title' => 'Новое сообщение')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Новое сообщение')) ?>

<form action="<?php echo a_url('private/send') ?>" method="post">
<div class="menu">
Получатель:<br />
<input name="username" type="text" value="<?php echo (empty($_GET['username']) ? htmlspecialchars($_POST['username']) : $_GET['username']) ?>" /><br />
Сообщение:<br />
<textarea name="message" rows="5" cols="20"><?php echo htmlspecialchars($_POST['message']) ?></textarea><br />
<input type="submit" name="submit" value="Отправить" />
</div>
</form>

<div class="block">
<a href="<?php echo a_url('smiles', 'return_name='. urlencode('К сообщению') .'&amp;return_url='. urlencode(a_url('private/send'))) ?>">Смайлы</a><br />
<a href="<?php echo a_url('main/bbcode', 'return_name='. urlencode('К сообщению') .'&amp;return_url='. urlencode(a_url('private/send'))) ?>">Теги (bbcode)</a><br />
<a href="<?php echo a_url('private') ?>">Личные сообщения</a><br />
<a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>