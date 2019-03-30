<?php $this->display('header.tpl', array('sub_title' => $title)) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => $title)) ?>

<form action="<?php echo ($action == 'add'?a_url('photo/edit_albums', 'user_id='. $user_id .'&amp;action='. $action):a_url('photo/edit_albums', 'user_id='. $user_id .'&amp;album_id='. $album['album_id'] .'&amp;action='. $action)) ?>" method="post">
    <div class="menu">
        Название:<br />
        <input name="name" type="text" value="<?php echo ($action == 'add'?htmlspecialchars($_POST['name']):$album['name']) ?>" /><br />
        Описание:<br />
        <textarea name="about" rows="5" cols="20"><?php echo ($action == 'add'?htmlspecialchars($_POST['about']):$album['about']) ?></textarea><br />
        <input type="submit" name="submit" value="<?php echo ($action == 'add'?'Добавить':'Сохранить') ?>" />
    </div>
</form>

<div class="block">
    <a href="<?php echo a_url('photo/list_albums', 'user_id='. $user_id) ?>"><?php echo ($action == 'add'?'Мои фотоальбомы':'Фотоальбомы '. $album['username']) ?></a><br />
    <a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>