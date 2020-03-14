<?php $this->display('header.tpl', array('sub_title' => 'Комментарии')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Комментарии')) ?>

<div class="block">
    <a href="<?php echo a_url('comments', 'module='. $_GET['module'] .'&amp;item_id='. $_GET['item_id'] .'&amp;return='. urlencode($_GET['return']) .'&amp;start='. $start) ?>">Обновить</a>
</div>

<?php if($comments): ?>
<?php foreach($comments AS $comment): ?>
<div class="menu">
    <img src="<?php echo user::getAvatarUrl($comment['user_id']) ?>" width="30" height="30" />
    <?php echo user::get_username($comment['user_id'], TRUE) ?> <?php echo user::online_status($comment['last_visit']) ?> (<?php echo main::display_time($comment['time']) ?>)<br />
    <?php echo $comment['text'] ?>
    <?php if (a_check_rights($comment['user_id'], $comment['user_status'])): ?>
    <br />
    <span class="action">
        [<a href="<?php echo a_url('comments/comment_edit', 'comment_id='. $comment['comment_id'] .'&amp;return_name='. urlencode('Вернуться') .'&amp;return_url='. urlencode(a_url('comments/list_comments', 'module='. $_GET['module'] .'&item_id='. $_GET['item_id'] .'&return='. $_GET['return'] .'&start='. $start))) ?>">Изменить</a>] [<a href="<?php echo a_url('comments/comment_delete', 'comment_id='. $comment['comment_id'] . '&amp;module='. $_GET['module'] .'&amp;item_id='. $_GET['item_id'] .'&amp;return='. urlencode($_GET['return']) .'&amp;start='. $start) ?>">Удалить</a>]
    </span>
    <?php endif ?>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="menu">
    Комментариев нет.
</div>
<?php endif; ?>

<?php if ($pagination): ?>
<div class="block">
    <?php echo $pagination ?>
</div>
<?php endif ?>

<div class="block">
    Всего комментариев: <?php echo $total ?>
</div>

<?php if ($_config['comments_posting'] != 'users' || USER_ID != -1): ?>
<form action="<?php echo a_url('comments/say', 'module='. $_GET['module'] .'&amp;item_id='. $_GET['item_id'] .'&amp;return='. urlencode($_GET['return']) .'&amp;start='. $start) ?>" method="post">
    <div class="menu">
        Сообщение: (<a href="<?php echo a_url('smiles', 'return_name='. urlencode('Вернуться') .'&amp;return_url='. urlencode(a_url('comments/list_comments', 'module='. $_GET['module'] .'&item_id='. $_GET['item_id'] .'&return='. $_GET['return']))) ?>">смайлы</a> / <a href="<?php echo a_url('main/bbcode', 'return_name='. urlencode('Вернуться') .'&amp;return_url='. urlencode(a_url('comments', 'module='. $_GET['module'] .'&item_id='. $_GET['item_id'] .'&return='. urlencode($_GET['return'])))) ?>">теги</a>)<br />
        <textarea name="message" rows="5" cols="20"><?php if (isset($_GET['reply'])) echo '[b]'. htmlspecialchars($_GET['reply']) .'[/b], '; else echo htmlspecialchars($_POST['message']) ?></textarea><br />
        <?php if (USER_ID == -1): ?>
        Введите код с картинки:<br />
        <img src="<?php echo URL ?>utils/captcha.php" /><br />
        <input name="captcha_code" type="text" /><br />
        <?php endif; ?>
        <input type="submit" name="submit" value="Отправить" />
    </div>
</form>
<?php endif; ?>

<div class="block">
    <a href="<?php echo urldecode($_GET['return']) ?>">Вернуться</a><br />
    <a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>