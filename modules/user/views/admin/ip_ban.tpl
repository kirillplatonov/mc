<?php $this->display('header', array('title' => $title)) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<div class="box">
    <div class="inside">
        <?php if ($action == 'add' OR $action == 'edit'): ?>
        <form method="<?php if ($action == 'add') echo 'get'; else echo 'post' ?>" action="<?php echo a_url('user/admin/ip_ban', 'action='. $action .'&amp;id='. $edit_ip['id']) ?>">
            <p>
                <label>IP адрес</label>
                <input type="text" name="guest_ip" value="<?php if ($action == 'add') echo str_safe($_GET['guest_ip']); else echo $edit_ip['ip'] ?>"><br /><br />

                <input type="hidden" name="action" value="<?php echo $action ?>">
                <input type="submit" id="submit" value="<?php if ($action == 'add') echo 'Забанить'; else echo 'Сохранить' ?>" /></p>
        </form>
        <?php else: ?>
        <p>
            <a href="<?php echo a_url('user/admin/ip_ban', 'action=add') ?>">Забанить новый IP</a><br />
            <a href="#" onclick="if (confirm('Действительно хотите разбанить все IP?'))
               {parent.location='<?php echo a_url('user/admin/ip_ban', 'action=delete_all') ?>';}">Разбанить все</a>
        </p>
        <?php endif ?>
    </div>
</div>

<table cellspacing="0" cellpadding="0" class="table">
    <thead align="left" valign="middle">
        <tr>
            <td>ID</td>
            <td>IP адрес</td>
            <td style="width: 16px;"> </td>
            <td style="width: 16px;"> </td>
        </tr>
    </thead>
    <tbody align="left" valign="middle">
        <?php if ($total > 0): ?>
        <?php foreach($ip_bans as $ip_ban): ?>
        <tr>
            <td><b><?php echo $ip_ban['id'] ?></b></td>
            <td><?php echo $ip_ban['ip'] ?></td>
            <td><a href="<?php echo a_url('user/admin/ip_ban', 'id='. $ip_ban['id'] .'&amp;action=edit') ?>"><img src="<?php echo URL ?>views/admin/images/edit.png" alt="" /></a></td>
            <td><a href="#" onclick="if (confirm('Действительно хотите разбанить IP <?php echo $ip_ban['ip'] ?>?')) {parent.location='<?php echo a_url('user/admin/ip_ban', 'guest_ip='. $ip_ban['ip'] .'&amp;action=delete') ?>';}"><img src="<?php echo URL ?>views/admin/images/delete.png" alt="" /></a></td>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
        <tr>
            <td>---</td>
            <td>---</td>
            <td><img src="<?php echo URL ?>views/admin/images/edit.png" alt="" /></td>
            <td><img src="<?php echo URL ?>views/admin/images/delete.png" alt="" /></td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if($pagination): ?>
<p><?php echo $pagination ?></p>
<?php endif ?>

<?php $this->display('footer') ?>