<?php $this->display('header', array('title' => 'Смена пароля')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Настройки')) ?>

<div class="block">
    <?php echo ($action == 'main' ? '<u>Основные</u>' : '<a href="'. a_url('user/profile/settings', 'action=main') .'">Основные</a>') ?> | <?php echo ($action == 'profile' ? '<u>Анкета</u>' : '<a href="'. a_url('user/profile/settings', 'action=profile') .'">Анкета</a>') ?> | <?php echo (ROUTE_ACTION == 'change_password' ? '<u>Безопасность</u>' : '<a href="'. a_url('user/change_password') .'">Безопасность</a>') ?>
</div>

<form action="<?php echo a_url('user/change_password') ?>" method="post">
    <div class="menu">
        Старый пароль (или pin code):<br />
        <input name="password" type="password" /><br /><br />

        Новый пароль:<br />
        <input name="new_password" type="password" /><br />

        Повторите новый пароль:<br />
        <input name="new_password2" type="password" /><br />

        <input type="submit" name="submit" value="Применить" />
    </div>
</form>

<div class="block">
    <a href="<?php echo a_url(MAIN_MENU) ?>">В кабинет</a><br />
    <a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>