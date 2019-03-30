<?php $this->display('header', array('title' => 'Личный кабинет')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Личный кабинет')) ?>

<div class="menu">
    <?php if (ACCESS_LEVEL >= 8): ?>
    <img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo a_url('user/admin') ?>">Панель управления</a><br />
    <?php endif; ?>

    <img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo a_url('user/profile/settings') ?>">Настройки</a><br />

    <img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo a_url('user/profile/view') ?>">Ваша анкета</a><br />

    <img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo a_url('private') ?>">Личные сообщения</a> <?php if (defined(PRIVATE_NEW_MESSAGES)) echo '('. PRIVATE_NEW_MESSAGES .')'; ?><br />

    <?php if (modules::is_active_module('blog')): ?>
    <img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo a_url('blog/my') ?>">Ваш блог</a><br />
    <?php endif; ?>

    <?php if (modules::is_active_module('photo')): ?>
    <img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo a_url('photo/list_albums', 'user_id='. USER_ID) ?>">Ваши фотоальбомы</a><br />
    <?php endif; ?>

    <?php if (modules::is_active_module('tickets')): ?>
    <img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo a_url('tickets') ?>">Связь с администрацией (тикеты)</a><?php if ($info['new_tickets']): ?> <span class="new">new!</span><?php endif; ?><br />
    <?php endif; ?>

    <img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo a_url('user/profile/autologin') ?>">Автологин</a><br />

    <img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo a_url('user/change_password') ?>">Сменить пароль</a><br />

    <img src="<?php echo URL ?>views/<?php echo THEME ?>/images/icon.png" alt="" /> <a href="<?php echo a_url('user/exit') ?>">Выход</a>
</div>

<div class="block">
    <a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>