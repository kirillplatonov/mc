<?php $this->display('header.tpl', array('sub_title' => 'Гостевая книга')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Гостевая книга')) ?>

<div class="block">
    <a href="<?php echo a_url('guestbook') ?>">Обновить</a>
</div>

<?php if ( ! empty($messages)): ?>
<?php foreach($messages as $message): ?>
<div class="menu">
    <?php echo user::get_icon($message['user_id']) ?> <?php echo user::get_username($message['user_id'], TRUE) ?> <?php echo user::online_status($message['last_visit']) ?> (<?php echo main::display_time($message['time']) ?>)<br />
    <?php echo $message['message'] ?>
    <?php if (a_check_rights($message['user_id'], $message['user_status'])): ?>
    <br />
    <span class="action">
        [<a href="<?php echo a_url('guestbook/delete_message', 'message_id='. $message['message_id']) ?>">Удалить</a>]
    </span>
    <?php endif ?>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="menu">
    Сообщений нет
</div>
<?php endif; ?>

<?php if ($pagination) echo '<div class="block">'. $pagination .'</div>' ?>

<?php if ($_config['guestbook_posting'] != 'users' || USER_ID != -1): ?>
<form method="post" action="<?php echo a_url('guestbook/say', 'action=add') ?>">
    <div class="menu">
        Сообщение: (<a href="<?php echo a_url('smiles', 'return_name='. urlencode('Вернуться') .'&amp;return_url='. urlencode(a_url('guestbook'))) ?>">смайлы</a> / <a href="<?php echo a_url('main/bbcode', 'return_name='. urlencode('Вернуться') .'&amp;return_url='. urlencode(a_url('guestbook'))) ?>">теги</a>)<br />
        <textarea name="message" rows="4" cols="20"><?php echo (isset($_GET['reply']) ? '[b]'. a_safe($_GET['reply']) .'[/b], ' : htmlspecialchars($_POST['message'])) ?></textarea><br />

        <?php if (USER_ID == -1): ?>
        Введите код с картинки:<br />
        <?php echo captcha() ?>
        <input name="captcha_code" type="text" /><br />
        <?php endif ?>

        <input type="submit" name="submit" value="Отправить" />
    </div>
</form>
<?php else: ?>
<div class="menu">
    Для написания сообщений <a href="<?php echo a_url('user/login') ?>">авторизируйтесь</a> или <a href="<?php echo a_url('user/registration') ?>">зарегистрируйтесь</a>.
</div>
<?php endif ?>

<div class="block">
    <a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>