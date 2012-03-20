<?php $this->display('header', array('title' => $book['name'])) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => $book['name'])) ?>

<div class="block">
<?php echo $navigation ?>
</div>

<div class="menu">
<?php echo $text_page ?>
</div>

<?php if($pagination)
	echo '<div class="block">'. $pagination .'</div>';
?>
<form action="<?php echo a_url('lib/read_book') ?>" method="get">
<div class="block">
Перейти к стр.:<br />
<input type="text" name="page" size="4" maxlength="4" />
<input name="book_id" type="hidden" value="<?php echo $_GET['book_id'] ?>" />
<input type="submit" value="ok" />
</div>
</form>

<div class="menu">
Скачать: <a href="<?php echo a_url('lib/download_book', 'book_id='. $book['book_id'] .'&amp;type=txt') ?>">txt</a>, <a href="<?php echo a_url('lib/download_book', 'book_id='. $book['book_id'] .'&amp;type=zip') ?>">zip</a>, <a href="<?php echo a_url('lib/download_book', 'book_id='. $book['book_id'] .'&amp;type=jar') ?>">jar</a><br />
<img src="<?php echo URL ?>modules/comments/images/comment.png" alt="" border="0" /> <a href="<?php echo a_url('comments', 'module=lib&amp;item_id='. $book['book_id'] .'&amp;return='. urlencode(a_url('lib/read_book', 'book_id='. $book['book_id'] .'&amp;start='. $_GET['start'], TRUE))) ?>">Обсудить</a> <span class="small_text">[<?php echo a_default($book['comments']) ?>]</span>
</div>

<div class="block">
<?php echo $navigation ?>
</div>

<div class="block">
<a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>