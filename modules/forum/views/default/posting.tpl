<?php $this->display('header', array('sub_title' => $title)) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => $title)) ?>

<form enctype="multipart/form-data" action="<?php echo a_url('forum/posting', 'new_topic='. $_GET['new_topic'] .'&amp;forum_id='. $_GET['forum_id'] .'&amp;topic_id='. $_GET['topic_id'] .'&amp;message_id='. $_GET['message_id']) ?>" method="post">
<div class="menu">

<?php if($action == 'new_topic' || $action == 'edit_first_message'): ?>
Тема:<br />
<input name="topic_name" class="input" type="text" value="<?php echo $topic['name'] ?>" /><br />
<?php endif; ?>

Сообщение:<br />
<textarea name="message" rows="5" cols="20"><?php echo $message_text ?></textarea><br />
<br />

Прикрепить файл:<br />
<input type="file" name="attach" /><br />
<br />

<?php if(USER_ID == -1): ?>
Введите код с картинки:<br />
<img src="<?php echo URL ?>utils/captcha.php" /><br />
<input name="captcha_code" type="text" value="" /><br />
<?php endif; ?>

<input type="submit" name="submit" value="<?php echo (strpos($action, 'edit') === false ? 'Отправить' : 'Изменить') ?>" />
</div>
</form>

<div class="block">
<a href="<?php echo a_url('smiles', 'return_name='. urlencode('К сообщению') .'&amp;return_url='. urlencode(a_url('forum/posting', 'new_topic='. $_GET['new_topic'] .'&forum_id='. $_GET['forum_id'] .'&topic_id='. $_GET['topic_id'] .'&message_id='. $_GET['message_id'], TRUE))) ?>">Смайлы</a><br />
<a href="<?php echo a_url('main/bbcode', 'return_name='. urlencode('К сообщению') .'&amp;return_url='. urlencode(a_url('forum/posting', 'new_topic='. $_GET['new_topic'] .'&forum_id='. $_GET['forum_id'] .'&topic_id='. $_GET['topic_id'] .'&message_id='. $_GET['message_id'], TRUE))) ?>">Теги (bbcode)</a>
</div>

<?php $this->display('footer') ?>