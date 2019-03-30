<?php $this->display('header', array('title' => ($_GET['type'] == 'new' ? 'Новые темы' : $forum['name']))) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => ($_GET['type'] == 'new' ? 'Новые темы' : '<a href="'. a_url('forum', 'section_id='. $section['section_id']) .'">'. $section['name'] .'</a> | '. $forum['name']))) ?>

<?php if($topics): ?>
<?php foreach($topics as $topic): ?>
<div class="menu">
    <?php if($topic['is_top_topic']) echo '!'; if($topic['is_close_topic']) echo '#'; ?><a href="<?php echo a_url('forum/viewtopic', 'topic_id='. $topic['topic_id']) ?>"><?php echo $topic['name'] ?></a> [<?php echo $topic['messages'] ?>] <?php echo $topic['last_username'] ?> <a href="<?php echo a_url('forum/viewtopic', 'topic_id='. $topic['topic_id'] .'&amp;start='. (floor($topic['messages'] / $messages_per_page) * $messages_per_page)) ?>">»</a>
    <?php if(ACCESS_LEVEL >= 8 && $_GET['type'] != 'new'): ?><br />
    [<?php echo ($topic['is_top_topic'] ? '<a href="'. a_url('forum/topic_top', 'a=untop&amp;topic_id='. $topic['topic_id'] .'&amp;start='. @$_GET['start']) .'">открепить</a>' : '<a href="'. a_url('forum/topic_top', 'a=top&amp;topic_id='. $topic['topic_id'] .'&amp;start='. @$_GET['start']) .'">закрепить</a>') ?>]
    [<?php echo ($topic['is_close_topic'] ? '<a href="'. a_url('forum/topic_close', 'a=open&amp;topic_id='. $topic['topic_id'] .'&amp;start='. @$_GET['start']) .'">открыть</a>' : '<a href="'. a_url('forum/topic_close', 'a=close&amp;topic_id='. $topic['topic_id'] .'&amp;start='. @$_GET['start']) .'">закрыть</a>') ?>]
    [<a href="<?php echo a_url('forum/topic_delete', 'topic_id='. $topic['topic_id'] .'&amp;start='. $_GET['start']) ?>">удалить</a>]
    <?php endif; ?>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="menu">
    В данном форуме нет тем
</div>
<?php endif; ?>

<?php if($pagination)
echo '<div class="block">'. $pagination .'</div>';
?>

<?php if($_GET['type'] != 'new'): ?>
<div class="block">
    <a href="<?php echo a_url('forum/posting', 'new_topic=true&amp;forum_id='. $forum['forum_id']) ?>">Новая тема</a><br />
</div>
<?php endif; ?>

<div class="block">
    <a href="<?php echo a_url('forum') ?>">Форум</a><br />
    <a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>