<?php $this->display('header', array('sub_title' => 'Поиск файлов')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title.tpl', array('text' => 'Поиск файлов')) ?>

<form action="<?php echo a_url('downloads/search_form') ?>" method="get">
    <div class="menu">
        Что ищем:<br />
        <input name="search_word" type="text" value="" /><br />
        Где ищем:<br />
        <select size="1" name="directory_id">
            <?php if($directory_id > 0): ?>
            <option value="<?php echo $directory_id ?>"><?php echo $directory['name'] ?></option>
            <?php endif; ?>
            <option value="0">Во всех папках</option>
        </select><br />

        <input name="send" type="hidden" value="1" />
        <input type="submit" value="Поиск" />
    </div>
</form>

<div class="block">
    <?php if ( ! empty($navigation)) echo $navigation .'<br />' ?>
    <a href="<?php echo URL ?>downloads">Загруз-центр</a><br />
    <a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>