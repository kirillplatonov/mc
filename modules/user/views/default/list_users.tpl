<?php $this->display('header.tpl', array('sub_title' => $title)) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Пользователи')) ?>

<div class="block">
    <?php echo ($type == 'all' ? '<u>Все</u>' : '<a href="'. a_url('user/list_users') .'">Все</a>') ?> | <?php echo ($type == 'online' ? '<u>Онлайн</u>' : '<a href="'. a_url('user/list_users', 'type=online') .'">Онлайн</a>') ?> | <?php echo ($type == 'new' ? '<u>Новые</u>' : '<a href="'. a_url('user/list_users', 'type=new') .'">Новые</a>') ?>
</div>

<?php if ( ! empty($users)): ?>
<?php foreach($users as $user): ?>
<div class="menu">
    <table cellpadding="0" cellspacing="0">
        <tr>
            <td><img src="<?php echo user::getAvatarUrl($user['user_id']) ?>" width="30" height="30" /></td>
            <td>
                <b><a href="<?php echo a_url('user/profile/view', 'user_id='. $user['user_id']) ?>"><?php echo $user['username'] ?></a></b> [<?php echo a_is_online($user['last_visit']) ?>]<br />
                <span style="color: grey; font-size: 11px;">[<?php echo $GLOBALS['controller']->access->ru_roles[$user['status']] ?>]</span>
            </td>
        </tr>
    </table>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="menu">
    Пользователей нет
</div>
<?php endif ?>

<?php if ($pagination) echo '<div class="block">'. $pagination .'</div>' ?>

<div class="block">
    <a href="<?php echo a_url('user/list_guests') ?>">Гости онлайн</a> (<?php echo GUESTS_ONLINE ?>)<br />
    <a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>