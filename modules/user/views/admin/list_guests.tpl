<?php $this->display('header', array('title' => 'Список гостей ('. $total .')')) ?>

<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
			<td>ID</td>
			<td>IP адрес</td>
			<td style="width: 16px;">Устройство</td>
			<td>Посл. посещение</td>
			<td style="width: 16px;"> </td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	<?php if ($total > 0): ?>
		<?php foreach($guests as $guest): ?>
			<tr>
				<td><b><?php echo $guest['id'] ?></b></td>
				<td><?php echo $guest['ip'] ?></td>
				<td><?php echo $guest['user_agent'] ?></td>
				<td><?php echo date('d.m.Y в H:i', $guest['last_time']) ?></td>
				<td><a href="<?php echo a_url('user/admin/ip_ban', 'guest_ip='. $guest['ip'] .'&amp;action=add') ?>"><img src="<?php echo URL ?>views/admin/images/ban.png" alt="" /></a></td>
			</tr>
		<?php endforeach; ?>
	<?php else: ?>
	    <tr>
	        <td>---</td>
				<td>---</td>
				<td>---</td>
				<td>---</td>
				<td><img src="<?php echo URL ?>views/admin/images/ban.png" alt="" /></td>
	    </tr>
	<?php endif; ?>
	</tbody>
</table>

<?php if($pagination): ?>
<p><?php echo $pagination ?></p>
<?php endif ?>

<?php $this->display('footer') ?>