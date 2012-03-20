<?php $this->display('header', array('title' => 'Список книг')) ?>

<div class="box">
	<div class="inside">
	<p>
		<a href="<?php echo a_url('lib/admin/directory_edit', 'parent_id='. $directory['directory_id']) ?>">Создать папку</a><br />
		<a href="<?php echo a_url('lib/admin/add_books', 'directory_id='. $directory['directory_id'] .'&amp;type=upload') ?>">Загрузить книгу(и) в текущую директорию (upload)</a><br />
		<a href="<?php echo a_url('lib/admin/add_books', 'directory_id='. $directory['directory_id'] .'&amp;type=import') ?>">Загрузить книгу(и) в текущую директорию (импорт)</a><br />
		<a href="<?php echo a_url('lib/admin/add_books', 'directory_id='. $directory['directory_id'] .'&amp;type=textes') ?>">Загрузить книгу(и) в текущую директорию (тексты)</a><br />
		<?php if($directory['directory_id'] > 0): ?>
		<a href="#" onclick="if(confirm('Действительно хотите очистить папку &laquo;<?php echo $directory['name'] ?>&raquo;?')) {parent.location='<?php echo a_url('lib/admin/directory_clear', 'directory_id='. $directory['directory_id']) ?>';}">Очистить папку от книг</a>
	    <?php endif; ?>
	</p>
	</div>
</div>

<table>
	<thead>
		<tr>
			<th style="width: 20px;">&nbsp;</th>
			<th>Название</th>
			<th colspan="4" width='50%'>Дейстивия</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="4">
				<?php if(!empty($namepath)): echo $namepath .'/'; endif ?><b><?php echo $directory['name'] ?>/</b><b><?php if($directory): ?><a href="<?php echo a_url('lib/admin/list_books', 'directory_id='. $directory['parent_id']) ?>">..</a></b><?php endif; ?>
			</td>
		</tr>
		<?php foreach($books as $book): ?>
		<tr>
			<td><img src="<?php echo URL ?>modules/lib/views/admin/images/<?php echo $book['type'] ?>.png" /></td>
			<td>
				<?php if($book['type'] == 'directory'): ?>
					<a href="<?php echo a_url('lib/admin/list_books', 'directory_id='. $book['book_id']) ?>"><?php echo $book['name'] ?></a>
				<?php else: ?>
					<a href="<?php echo a_url('lib/read_book', 'book_id='. $book['book_id']) ?>"><?php echo $book['name'] ?></a>
				<?php endif; ?>
			</td>
			<td><?php echo $book['up'] ?></td>
			<td><?php echo $book['down'] ?></td>
			<td>
				<?php if($book['type'] == 'directory'): ?>
					<a href="<?php echo a_url('lib/admin/directory_edit', 'directory_id='. $book['book_id']) ?>">Изменить</a>
				<?php else: ?>
					<!-- <a href="<?php echo a_url('lib/admin/book_edit', 'book_id='. $book['book_id']) ?>">Изменить</a> -->
					Изменить
				<?php endif ?>
			</td>
			<td>
				<?php if($book['type'] == 'directory'): ?>
					<a href="#" onclick="if(confirm('Действительно хотите удалить папку &laquo;<?php echo $book['name'] ?>&raquo;?')) {parent.location='<?php echo a_url('lib/admin/directory_delete', 'directory_id='. $book['book_id']) ?>';}">Удалить</a>
				<?php else: ?>
					<a href="#" onclick="if(confirm('Действительно хотите удалить книгу &laquo;<?php echo $book['name'] ?>&raquo;?')) {parent.location='<?php echo a_url('lib/admin/book_delete', 'book_id='. $book['book_id']) ?>';}">Удалить</a>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php if($pagination)
	echo '<p>'. $pagination .'</p>';
?>

<?php $this->display('footer') ?>