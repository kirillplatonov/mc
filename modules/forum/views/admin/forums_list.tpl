<?php $this->display('header', array('title' => 'Управление форумами')) ?>

<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
			<td>Название</td>
			<td colspan="4">Действия</td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	<?php if($forums): ?>
	<?php foreach($forums as $forum): ?>
		<tr>
			<td><b><?php echo $forum['name'] ?></b></td>
			<td><?php echo $forum['up'] ?></td>
			<td><?php echo $forum['down'] ?></td>
			<td><a href="<?php echo a_url('forum/admin/forums', 'a=edit&amp;forum_id='. $forum['forum_id']) ?>">Изменить</a></td>
			<td><a href="#" onclick="if(confirm('Вы действительно хотите удалить форум &laquo;<?php echo $forum['name'] ?>&raquo;?')) {parent.location='<?php echo a_url('forum/admin/forums', 'a=delete&amp;forum_id='. $forum['forum_id']) ?>';}">Удалить</a></td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>

<?php if($pagination): ?>
<p><?php echo $pagination ?></p>
<?php endif ?>

<?php $this->display('footer') ?>