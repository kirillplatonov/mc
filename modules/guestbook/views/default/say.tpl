<?php $this->display('header', array('title' => 'Гостевая книга | Написать')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Гостевая книга | Написать')) ?>

<form action="<?php echo a_url('guestbook/say') ?>" method="post">
    <div class="menu">
        Сообщение:<br />
        <textarea name="message" rows="5" cols="20"><?php echo htmlspecialchars($_POST['message']) ?></textarea><br />
        <?php if(USER_ID == -1): ?>
        Введите код с картинки:<br />
        <img src="<?php echo URL ?>utils/captcha.php" /><br />
        <input name="captcha_code" type="text" value="" /><br />
        <?php endif; ?>
        <input type="submit" name="submit" value="Отправить" />
    </div>
</form>

<div class="block">
    <a href="<?php echo a_url('smiles', 'return_name='. urlencode('Написать') .'&amp;return_url='. urlencode(a_url('guestbook/say'))) ?>">Смайлы</a><br />
    <a href="<?php echo a_url('main/bbcode', 'return_name='. urlencode('Написать') .'&amp;return_url='. urlencode(a_url('guestbook/say'))) ?>">Теги (bbcode)</a><br />
    <a href="<?php echo a_url('guestbook') ?>">В гостевую</a><br />
    <a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>