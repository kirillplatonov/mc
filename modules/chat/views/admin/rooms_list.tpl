<?php $this->display('header', array('title' => 'Управление комнатами чата')) ?>

<?php if(!empty($rooms)): ?>
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
			<td>Название</td>
			<td colspan="4">Действия</td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	<?php foreach($rooms as $room): ?>
		<tr>
			<td><b><?php echo $room['name'] ?></b></td>
			<td><?php echo $room['up'] ?></td>
			<td><?php echo $room['down'] ?></td>
			<td><a href="<?php echo a_url('chat/admin/rooms', 'a=edit&amp;room_id='. $room['room_id']) ?>">Изменить</a></td>
			<td><a href="#" onclick="if(confirm('Вы действительно хотите удалить комнату &laquo;<?php echo $room['name'] ?>&raquo;?')) {parent.location='<?php echo a_url('chat/admin/rooms', 'a=delete&amp;room_id='. $room['room_id']) ?>';}">Удалить</a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p><b>Комнат нет..</b></p>
<?php endif; ?>

<?php if(!empty($pagination)): ?>
<p><?php echo $pagination ?></p>
<?php endif ?>

<?php $this->display('footer') ?>