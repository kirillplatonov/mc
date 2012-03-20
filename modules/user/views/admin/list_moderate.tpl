<?php $this->display('header', array('title' => 'Список пользователей на модерации')) ?>

<div class="box">
    <div class="inside">
        <p>
            В список на модерацию попадают пользователи, уже подтвердившие свой E-mail. Если подтверждение E-mail отключено, то на модерацию пользователи попадают сразу после регистрации.
        </p>
    </div>
</div>

<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
			<td>ID</td>
			<td>Логин</td>
			<td>Статус</td>
			<td>Дата регистрации</td>
			<td>Последнее посещение</td>
			<td style="width: 16px;"> </td>
			<td style="width: 16px;"> </td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	<?php foreach($users as $user): ?>
		<tr>
			<td><b><?php echo $user['user_id'] ?></b></td>
			<td><a href="<?php echo a_url('user/profile/view', 'user_id='. $user['user_id']) ?>"><?php echo $user['username'] ?></a> [<?php echo a_is_online($user['last_visit']) ?>]</td>
			<td><?php
			if ($db->get_one("SELECT * FROM #__users_ban WHERE user_id = '". $user['user_id'] ."' AND status = 'enable'")) echo 'banned'; else echo $user['status'] ?></td>
			<td><?php echo date('d.m.Y', $user['reg_time']) ?></td>
			<td><?php echo date('d.m.Y в H:i', $user['last_visit']) ?></td>
                        <td><a title="Промодерировать" href="<?php echo a_url('user/admin/moderate', 'user_id='. $user['user_id'] .'&amp;action=ok') ?>"><img src="<?php echo URL ?>views/admin/images/accept.png" alt="" /></a></td>
			<td><a title="Заблокировать" href="<?php echo a_url('user/admin/moderate', 'user_id='. $user['user_id'] .'&amp;action=cancel') ?>"><img src="<?php echo URL ?>views/admin/images/cancel.png" alt="" /></a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php if($pagination): ?>
<p><?php echo $pagination ?></p>
<?php endif ?>

<?php $this->display('footer') ?>