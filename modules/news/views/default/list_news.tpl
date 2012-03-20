<?php $this->display('header.tpl', array('sub_title' => 'Новости')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Новости')) ?>


<?php if(!empty($list_news)): ?>
	<?php foreach($list_news as $news): ?>
	<div class="menu">
	<img src="<?php echo URL ?>modules/news/images/news.png" alt="" /> <a href="<?php echo a_url('news/detail', 'news_id='. $news['news_id']) ?>"><?php echo $news['subject'] ?></a> (<?php echo date('d.m.Y', $news['time']) ?>)
	</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="block">
	<b>Новостей нет</b>
	</div>
<?php endif; ?>

<?php if($pagination)
	echo '<div class="block">'. $pagination .'</div>';
?>

<div class="block">
<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>