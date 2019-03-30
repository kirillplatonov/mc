<?php $this->display('header', array('title' => 'Новые сообщения')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Новые сообщения')) ?>

<?php if($messages): ?>
<?php foreach($messages as $message): ?>
<div class="menu">
    <?php echo user::get_icon($message['user_id']) ?> <?php echo user::get_username($message['user_id'], TRUE) ?> <?php echo user::online_status($message['last_visit']) ?> (<?php echo main::display_time($message['time']) ?>)<br />
    <?php echo $message['message'] ?>
    <?php if (a_check_rights($message['user_id'], $message['user_status'])): ?>
    <br />
    <span class="action">
        [<a href="<?php echo a_url('forum/posting', 'message_id='. $message['message_id']) ?>">Изменить</a>]<?php if($message['is_last_message']): ?> [<a href="<?php echo a_url('forum/message_delete', 'message_id='. $message['message_id'] .'&amp;start='. @$_GET['start']) ?>">Удалить</a>]<?php endif; ?>
    </span>
    <?php endif ?><br />
    -----<br />
    В теме: <a href="<?php echo a_url('forum/viewtopic', 'topic_id='. $message['topic_id'] .'&amp;start='. (floor($message['all_messages'] / $messages_per_page) * $messages_per_page)) ?>"><?php echo $message['topic_name'] ?></a>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="menu">
    Сообщений нет
</div>
<?php endif; ?>

<?php if($pagination)
echo '<div class="block">'. $pagination .'</div>';
?>

<div class="block">
    <a href="<?php echo a_url('forum') ?>">Форум</a><br />
    <a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>