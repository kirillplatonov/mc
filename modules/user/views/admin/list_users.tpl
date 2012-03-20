<?php $this->display('header', array('title' => 'Список пользователей')) ?>

<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
			<td>ID</td>
			<td>Логин</td>
			<td>Статус</td>
			<td>Дата регистрации</td>
			<td>Посл. посещение</td>
			<td style="width: 16px;"> </td>
			<td style="width: 16px;"> </td>
			<td style="width: 16px;"> </td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	<?php foreach($users as $user): ?>
		<tr>
			<td><b><?php echo $user['user_id'] ?></b></td>
			<td><a href="<?php echo a_url('user/admin/go_to_user_panel', 'user_id='. $user['user_id']) ?>"><?php echo $user['username'] ?></a> [<?php echo a_is_online($user['last_visit']) ?>]</td>
			<td><?php
			if ($db->get_one("SELECT * FROM #__users_ban WHERE user_id = '". $user['user_id'] ."' AND status = 'enable'")) echo 'banned'; else echo $user['status'] ?></td>
			<td><?php echo date('d.m.Y', $user['reg_time']) ?></td>
			<td><?php echo date('d.m.Y в H:i', $user['last_visit']) ?></td>
			<td><a href="<?php echo a_url('user/admin/edit', 'user_id='. $user['user_id']) ?>"><img src="<?php echo URL ?>views/admin/images/edit.png" alt="" /></a></td>
			<td><a href="<?php echo a_url('user/admin/ban', 'user_id='. $user['user_id']) ?>"><img src="<?php echo URL ?>views/admin/images/ban.png" alt="" /></a></td>
			<td><a href="#" onclick="if(confirm('Действительно хотите пользователя <?php echo $user['username'] ?>?')) {parent.location='<?php echo a_url('user/admin/delete', 'user_id='. $user['user_id']) ?>';}"><img src="<?php echo URL ?>views/admin/images/delete.png" alt="" /></a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php if($pagination): ?>
<p><?php echo $pagination ?></p>
<?php endif ?>

<?php $this->display('footer') ?>