<?php $this->display('header', array('title' => 'Фотографии '. $profile['username'])) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Фотографии '. $profile['username'])) ?>

<div class="block">
<?php if ($profile['user_id'] == USER_ID): ?>
<a href="<?php echo a_url('photo/edit_photos', 'user_id='. $profile['user_id'] .'&amp;album_id='. $album['album_id'] .'&amp;action=add'); ?>">Добавить фотографию</a>
<?php else: ?>
<a href="<?php echo a_url('user/profile/view', 'user_id='. $profile['user_id']); ?>">Анкета <?php echo $profile['username']; ?></a>
<?php endif; ?>
</div>

<?php if(!empty($photos)): ?>
  <?php foreach($photos as $photo): ?>
    <div class="menu">
      <img src="<?php echo URL ?>modules/photo/images/image.png" alt="" /> <a href="<?php echo a_url('photo/view_photo', 'user_id='. $photo['user_id'] .'&amp;album_id='. $photo['album_id'] .'&amp;photo_id='. $photo['photo_id']) ?>"><?php echo $photo['name']; ?></a><br />
      <?php echo $photo['image'] ?>
      <?php if (!empty($photo['about'])): echo $photo['about'] .'<br />'; endif; ?>
      <?php if (ACCESS_LEVEL >= 8 || a_check_rights($photo['user_id'], $photo['user_status'])): ?>
      [<a href="<?php echo a_url('photo/edit_photos', 'user_id='. $user_id .'&amp;album_id='. $album['album_id'] .'&amp;photo_id='. $photo['photo_id'] .'&amp;action=edit'); ?>">изменить</a>] [<a href="<?php echo a_url('photo/edit_photos', 'user_id='. $user_id .'&amp;album_id='. $album['album_id'] .'&amp;photo_id='. $photo['photo_id'] .'&amp;action=del'); ?>">удалить</a>]
      <?php endif; ?>
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
<a href="<?php echo a_url('photo/list_albums', 'user_id='. $user_id) ?>"><?php echo ($profile['user_id'] == USER_ID?'Мои фотоальбомы':'Фотоальбомы '. $profile['username']) ?></a><br />
<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>