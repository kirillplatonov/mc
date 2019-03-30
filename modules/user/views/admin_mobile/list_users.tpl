<?php $this->display('header', array('title' => 'Пользователи')) ?>

<table cellspacing="0" cellpadding="0" class="table">
    <thead align="left" valign="middle">
        <tr>
            <td>ID</td>
            <td>Логин</td>
            <td>Статус</td>
            <td colspan="3">Действия</td>
        </tr>
    </thead>
    <tbody align="left" valign="middle">
        <?php foreach($users as $user): ?>
        <tr>
            <td><b><?php echo $user['user_id'] ?></b></td>
            <td><a href="<?php echo a_url('user/admin/go_to_user_panel', 'user_id='. $user['user_id']) ?>"><?php echo $user['username'] ?></a></td>
            <td><?php echo $user['status'] ?></td>
            <td><a href="<?php echo a_url('user/admin/edit', 'user_id='. $user['user_id']) ?>">Изменить</a></td>
            <td><a href="<?php echo a_url('user/admin/ban', 'user_id='. $user['user_id']) ?>">Бан</a></td>
            <td><a href="#" onclick="if (confirm('Действительно хотите пользователя <?php echo $user['username'] ?>?')) {parent.location='<?php echo a_url('user/admin/delete', 'user_id='. $user['user_id']) ?>';}">Удалить</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if($pagination): ?>
<p><?php echo $pagination ?></p>
<?php endif ?>

<?php $this->display('footer') ?>