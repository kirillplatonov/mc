<?php $this->display('header.tpl', array('sub_title' => 'Новости')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => '<b>'. $news['subject'] .'</b> ('. date('d.m.Y', $news['time']) .')')) ?>


<div class="menu">
<?php echo $news['text'] ?><br />
<img src="<?php echo URL ?>modules/comments/images/comment.png" alt="" border="0" /> <a href="<?php echo a_url('comments', 'module=news&amp;item_id='. $news['news_id'] .'&amp;return='. urlencode(a_url('news', 'start='. @$_GET['start'], TRUE))) ?>">Комментарии</a> [<?php echo $news['comments'] ?>]
</div>

<?php if($pagination)
	echo '<div class="block">'. $pagination .'</div>';
?>

<div class="block">
<a href="<?php echo a_url('news') ?>">Все новости</a><br />
<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>