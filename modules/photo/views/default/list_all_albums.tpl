<?php $this->display('header', array('title' => 'Фотоальбомы')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Фотоальбомы')) ?>

<div class="block">
<a href="<?php echo a_url('photo/list_all_photos') ?>">Все фотографии</a>
</div>

<?php if(!empty($albums)): ?>
  <?php foreach($albums as $album): ?>
    <div class="menu">
      <img src="<?php echo URL ?>modules/photo/images/album.png" alt="" /> <a href="<?php echo a_url('photo/list_photos', 'user_id='. $album['user_id'] .'&amp;album_id='. $album['album_id']) ?>"><?php echo $album['name']; ?></a> (<?php echo $album['count'] ?>)<br />
      <?php if (!empty($album['about'])): echo $album['about'] .'<br />'; endif; ?>
      Добавил: <a href="<?php echo a_url('user/profile/view', 'user_id='. $album['user_id']); ?>"><?php echo $album['username'] ?></a>
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
<?php if (USER_ID != -1) echo '<a href="'. a_url('photo/list_albums', 'user_id='. USER_ID) .'">Мои фотоальбомы</a><br />'; ?>
<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>