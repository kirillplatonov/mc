<?php $this->display('header', array('title' => 'Отправка E-mail сообщения')) ?>

<?php if($error): ?>
<div class="error">
    <?php echo $error ?>
</div>
<?php endif ?>

<form action="<?php echo a_url('user/admin/user_email', 'user_id='. $_GET['user_id']) ?>" method="post">
    <div class="box">
        <h3>Отправка E-mail сообщения</h3>

        <p>
            <b>Получатель: <?php echo $user_email['username'] ?> (<?php echo $user_email['email'] ?>)</b>
        </p>

        <p>
            <label>Тема сообщения</label>
            <input name="title" type="text">
        </p>

        <p>
            <label>Текст</label>
            <textarea cols="20" rows="7" name="msg"></textarea>
        </p>
    </div>

    <p><input type="submit" name="submit" value="Отправить"></p>
</form>

<?php $this->display('footer') ?>