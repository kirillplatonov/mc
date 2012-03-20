<?php $this->display('header.tpl', array('sub_title' => 'Список гостей')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Список гостей')) ?>

<?php if ( ! empty($guests)): ?>
    <?php foreach($guests as $guest): ?>
        <div class="menu">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td><?php echo '<img src="'. URL .'files/avatars/guest_32.png" alt="" />'; ?>&#160;</td>
                    <td>
                        <b><?php echo $guest['ip'] .'/'. $guest['user_agent'] ?></b> [<?php echo a_is_online($guest['last_time']) ?>]<br />
				<span style="color: grey; font-size: 11px;">[<?php echo date('d.m.Y в H:i', $guest['last_time']) ?>]</span>
                    </td>
                </tr>
            </table>
	</div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="menu">
        Гостей нет
    </div>
<?php endif ?>

<?php if ($pagination) echo '<div class="block">'. $pagination .'</div>' ?>

<div class="block">
    <a href="<?php echo a_url('user/list_users', 'type=online') ?>">Пользователи онлайн</a> (<?php echo USERS_ONLINE ?>)<br />
    <a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>