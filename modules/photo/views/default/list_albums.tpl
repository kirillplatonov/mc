<?php $this->display('header', array('title' => 'Фотоальбомы '. $profile['username'])) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Фотоальбомы '. $profile['username'])) ?>

<div class="block">
<?php if ($profile['user_id'] == USER_ID): ?>
<a href="<?php echo a_url('photo/edit_albums', 'user_id='. $profile['user_id'] .'&amp;action=add'); ?>">Добавить альбом</a>
<?php else: ?>
<a href="<?php echo a_url('user/profile/view', 'user_id='. $profile['user_id']); ?>">Анкета <?php echo $profile['username']; ?></a>
<?php endif; ?>
</div>

<?php if(!empty($albums)): ?>
  <?php foreach($albums as $album): ?>
    <div class="menu">
      <img src="<?php echo URL ?>modules/photo/images/album.png" alt="" /> <a href="<?php echo a_url('photo/list_photos', 'user_id='. $album['user_id'] .'&amp;album_id='. $album['album_id']) ?>"><?php echo $album['name'] ?></a> (<?php echo $album['count'] ?>)<br />
      <?php if (!empty($album['about'])): echo $album['about'] .'<br />'; endif; ?>
      <?php if (ACCESS_LEVEL >= 8 || a_check_rights($album['user_id'], $album['user_status'])): ?>
      [<a href="<?php echo a_url('photo/edit_albums', 'user_id='. $user_id .'&amp;album_id='. $album['album_id'] .'&amp;action=edit') ?>">изменить</a>] [<a href="<?php echo a_url('photo/edit_albums', 'user_id='. $user_id .'&amp;album_id='. $album['album_id'] .'&amp;action=del'); ?>">удалить</a>]
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <div class="block">
	 <p>Альбомов нет.</p>
  </div>
<?php endif; ?>

<?php if($pagination)
	echo '<div class="block">'. $pagination .'</div>';
?>

<div class="block">
<a href="<?php echo a_url('photo/list_all_albums'); ?>">Фотоальбомы</a><br />
<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>