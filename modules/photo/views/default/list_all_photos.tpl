<?php $this->display('header', array('title' => 'Фотографии')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Фотографии')) ?>

<div class="block">
    <a href="<?php echo a_url('photo/list_all_albums') ?>">Все альбомы</a>
</div>

<?php if(!empty($photos)): ?>
<?php foreach($photos as $photo): ?>
<div class="menu">
    <img src="<?php echo URL ?>modules/photo/images/image.png" alt="" /> <a href="<?php echo a_url('photo/view_photo', 'user_id='. $photo['user_id'] .'&amp;album_id='. $photo['album_id'] .'&amp;photo_id='. $photo['photo_id']) ?>"><?php echo $photo['name']; ?></a><br />
    <?php echo $photo['image'] ?>
    <?php if (!empty($photo['about'])): echo $photo['about'] .'<br />'; endif; ?>
    Добавил: <a href="<?php echo a_url('user/profile/view', 'user_id='. $photo['user_id']); ?>"><?php echo $photo['username'] ?></a><br />
    Альбом: <a href="<?php echo a_url('photo/list_photos', 'user_id='. $photo['user_id'] .'&amp;album_id='. $photo['album_id']); ?>"><?php echo $photo['album_name'] ?></a>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="block">
    <p>Фотографий нет.</p>
</div>
<?php endif; ?>

<?php if($pagination)
echo '<div class="block">'. $pagination .'</div>';
?>

<div class="block">
    <?php if (USER_ID != -1) echo '<a href="'. a_url('photo/list_albums', 'user_id='. USER_ID) .'">Мои фотоальбомы</a><br />'; ?>
    <a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>