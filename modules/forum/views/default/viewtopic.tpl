<?php $this->display('header', array('title' => $topic['name'])) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => '<a href="'. a_url('forum/viewforum', 'forum_id='. $forum['forum_id']) .'">'. $forum['name'] .'</a> | '. $topic['name'])) ?>

<?php if($messages): ?>
<?php foreach($messages as $message): ?>
<div class="menu">
    <?php echo user::get_icon($message['user_id']) ?> <?php echo user::get_username($message['user_id'], TRUE) ?> <?php echo user::online_status($message['last_visit']) ?> (<?php echo main::display_time($message['time']) ?>) [<a href="<?php echo a_url('forum/posting', 'topic_id='. $topic['topic_id'] .'&amp;reply='. $message['username']) ?>">Отв</a>]<br />
    <?php echo $message['message'] ?><br />
    [<a href="<?php echo a_url('forum/posting', 'topic_id='. $topic['topic_id'] .'&amp;q='. $message['message_id']) ?>">Цит</a>] <?php if (a_check_rights($message['user_id'], $message['user_status'])): ?>[<a href="<?php echo a_url('forum/posting', 'message_id='. $message['message_id']) ?>">Изменить</a>]<?php if($message['is_last_message']): ?> [<a href="<?php echo a_url('forum/message_delete', 'message_id='. $message['message_id'] .'&amp;start='. @$_GET['start']) ?>">Удалить</a>]<?php endif; ?>
    <?php endif ?>

    <?php if(!empty($message['file_name'])): ?>
    <hr />
    <img src="<?php echo URL ?>modules/forum/views/default/img/attach.png" alt="" /> <a href="<?php echo a_url('forum/download_attach', 'file_id='. $message['file_id']) ?>"><?php echo $message['file_name'] ?></a> (<?php echo main::byte_format($message['file_size']) ?>)<br />
    <span class="small_text">Скачиваний: <?php echo $message['file_downloads'] ?></span>
    <?php endif; ?>

    <?php if($message['edit_count'] > 0) echo '<br />________<br /><span class="small_text" style="font-size: 10px">посл. ред. '. date('d.m.Y в H:i', $message['edit_time']) .'; всего '. $message['edit_count'] .' раз(а); by '. $message['edit_editor'] .'</span>'; ?>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="menu">
    В данной теме нет сообщений
</div>
<?php endif; ?>

<?php if($pagination)
echo '<div class="block">'. $pagination .'</div>';
?>

<div class="block">
    <a href="<?php echo a_url('forum/posting', 'topic_id='. $topic['topic_id']) ?>">Ответить на тему</a><br />
</div>

<div class="block">
    <a href="<?php echo a_url('forum') ?>">Форум</a><br />
    <a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>