<?php $this->display('header', array('title' => 'Библиотека')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Библиотека')) ?>

<div class="block">
    <?php if(!empty($navigation)): ?>
    <?php echo $navigation ?>
    <?php else: ?>
    <a href="<?php echo a_url('lib/list_books', 'type=top') ?>">Самые популярные</a>
    <?php endif; ?>
</div>

<?php if($type == 'search'): ?>
<form action="<?php echo a_url('lib/list_books') ?>" method="get">
    <div class="block">
        Укажите запрос:<br />
        <input type="text" name="search_word" value="<?php echo $_GET['search_word'] ?>" />
        <input name="type" type="hidden" value="search" /><br />
        <input type="submit" value="Поиск" />
    </div>
</form>
<?php endif; ?>

<?php if($type != 'search' or !empty($_GET['search_word'])): ?>
<?php if(!empty($books)): ?>
<?php foreach($books as $book): ?>
<div class="menu">
    <img src="<?php echo URL ?>modules/lib/views/default/images/<?php echo $book['type'] ?>.png" alt="" />
    <?php if($book['type'] == 'directory'): ?>
    <a href="<?php echo a_url('lib/list_books', 'directory_id='. $book['book_id']) ?>"><?php echo $book['name'] ?></a> <span class="small_text">[<?php echo $book['count_books'] ?>]</span><?php if($book['new_books'] > 0): ?> <span class="new_files">+<?php echo $book['new_books'] ?></span><?php endif; ?><br />
    <?php else: ?>
    <a href="<?php echo a_url('lib/read_book', 'book_id='. $book['book_id']) ?>"><?php echo $book['name'] ?></a><?php if($book['time'] >= time() - 86400): ?> <span class="new_files">new!</span><?php endif; ?><br />
    <span class="small_text">прочтений: <?php echo $book['reads'] ?></span>
    <?php endif; ?>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="menu">Книг не найдено...</div>
<?php endif; ?>
<?php endif; ?>

<?php if(!empty($pagination))
echo '<div class="block">'. $pagination .'</div>';
?>

<div class="block">
    <?php if(!empty($navigation)): ?>
    <?php echo $navigation ?>
    <?php else: ?>
    <a href="<?php echo a_url('lib/list_books', 'type=search') ?>">Поиск</a>
    <?php endif; ?>
</div>

<div class="block">
    <a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>