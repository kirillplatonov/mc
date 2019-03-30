<?php $this->display('header.tpl', array('sub_title' => $title)) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => '<b><a href="'. a_url('photo/list_photos', 'album_id='. $album['album_id'] .'&amp;user_id='. $user_id) .'">'. $album[name] .'</a></b> | <b>'. $title .'</b>')) ?>

<form action="<?php echo a_url('photo/edit_photos', 'album_id='. $album['album_id'] .'&amp;user_id='. $user_id .'&amp;action='. $action .'&amp;photo_id='. $photo['photo_id']) ?>" method="post" enctype="multipart/form-data">
    <div class="menu">
        Альбом:<br />
        <b><?php echo $album['name']; ?></b><br />
        Название:<br />
        <input name="name" type="text" value="<?php echo ($action == 'add'?htmlspecialchars($_POST['name']):$photo['name']) ?>" /><br />
        Описание:<br />
        <textarea name="about" rows="5" cols="20"><?php echo ($action == 'add'?htmlspecialchars($_POST['about']):$photo['about']) ?></textarea><br />
        <?php echo ($action == 'edit'?'Текущая фотография:<br />'. $photo['image']:NULL) ?>
        <?php echo ($action == 'edit'?'Заменить фотографию:':'Загрузить фотографию:') ?><br />
        <input name="file_upload" type="file" value="" /><br />
        Или импортировать с другого сайта:<br />
        <input name="file_import" type="text" value="http://" /><br />
        <input type="submit" name="submit" value="<?php echo ($action == 'add'?'Добавить':'Сохранить') ?>" />
    </div>
</form>

<div class="block">
    <a href="<?php echo a_url('photo/list_albums', 'user_id='. $user_id) ?>"><?php echo ($action == 'add'?'Мои фотоальбомы':'Фотоальбомы '. $album['username']) ?></a><br />
    <a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>