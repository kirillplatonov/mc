<?php $this->display('header', array('title' => 'Новости')) ?>

<?php if(!empty($list_news)): ?>
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
			<td>Дата</td>
			<td>Заголовок</td>
			<td colspan="2">Действия</td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	<?php foreach($list_news as $news): ?>
		<tr>
			<td><b><?php echo date('d.m.Y', $news['time']) ?></b></td>
			<td><?php echo $news['subject'] ?></td>
			<td><a href="<?php echo a_url('news/admin/edit', 'news_id='. $news['news_id']) ?>">Изменить</a></td>
			<td><a href="#" onclick="if(confirm('Вы действительно хотите удалить новость &laquo;<?php echo $news['subject'] ?>&raquo;?')) {parent.location='<?php echo a_url('news/admin/delete', 'news_id='. $news['news_id']) ?>';}">Удалить</a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p><b>Новостей нет</b></p>
<?php endif; ?>

<?php if($pagination): ?>
<p><?php echo $pagination ?></p>
<?php endif ?>

<?php $this->display('footer') ?>