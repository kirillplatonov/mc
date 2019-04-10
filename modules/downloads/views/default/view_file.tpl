<?php $this->display('header', array('title' => $file['name'])) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title.tpl', array('text' => $file['name'])) ?>

<div class="menu">
    <?php echo $file['screens'] ?>

    <?php if(!empty($file['about'])): ?>
    <i>Описание:</i><br />
    <?php echo $file['about'] ?><br />
    <br />
    <?php endif; ?>

    <?php if ($file['file_ext'] == 'mp3'): ?>
    <?php if (empty($file['about'])) echo '<br />'; ?>
    <object type="application/x-shockwave-flash" data="<?php echo URL ?>utils/mp3.swf" width="200" height="20">
        <param name="wmode" value="transparent" />
        <param name="movie" value="<?php echo URL ?>utils/mp3.swf" />
        <param name="FlashVars" value="mp3=<?php echo URL . $file['path_to_file'] .'/'. $file['real_name'] ?>&amp;bgcolor1=ffffff&amp;bgcolor2=cccccc&amp;buttoncolor=999999&amp;buttonovercolor=0&amp;slidercolor1=cccccc&amp;slidercolor2=999999&amp;sliderovercolor=666666&amp;textcolor=0&amp;showvolume=1&amp;showstop=1" />
    </object><br /><br />
    <?php endif; ?>

    <?php if(!empty($file['file_info'])): ?>
    <i>Дополнительная информация:</i><br />
    <?php echo nl2br($file['file_info']) ?><br />
    <?php endif; ?>

    <i>Загружен</i>: <?php echo date('d.m.Y в H:i', $file['time']) ?><br />
    <i>Скачиваний</i>: <?php echo $file['downloads'] ?><br />
    <i>Выложил</i>: <a href="<?php echo a_url('user/profile/view', 'user_id='. $file['user_id'])?>"><?php echo $file['username'] ?></a><br />
    <i>Рейтинг</i>:<br />
    <?php echo $file['rating_stars'] ?><br />
    <i>Голосов</i>: <?php echo $file['rating_voices'] ?><br />
    <?php if(!$file['rated']): ?>
    <form action="<?php echo a_url('downloads/rating_change') ?>" method="get">
        <select size="1" name="est">
            <option value="5">5</option>
            <option value="4">4</option>
            <option value="3">3</option>
            <option value="2">2</option>
            <option value="1">1</option>
        </select>
        <input name="file_id" type="hidden" value="<?php echo $file['file_id'] ?>" />
        <input type="submit" value="Ok" />
    </form>
    <?php endif; ?>
    <br />

    <img src="<?php echo URL ?>modules/comments/images/comment.png" alt="" border="0" /> <a href="<?php echo a_url('comments', 'module=downloads&amp;item_id='. $file['file_id'] .'&amp;return='. urlencode(URL .'downloads/view/'. $file['file_id'])) ?>">Обсудить</a> <span class="small_text">[<?php echo $file['comments'] ?>]</span><br />

    <br />
    <img src="<?php echo URL ?>modules/downloads/images/default/download.png" alt="" /> <a href="<?php echo URL ?>download_file/<?php echo $file['file_id'] ?>"><?php echo $file['real_name'] ?></a> (<?php echo main::byte_format($file['filesize']) ?>)<br />
    <?php for($i = 0; $i <= 10; $i++): ?>
    <?php if(!empty($file['add_file_real_name_'. $i])): ?>
    <img src="<?php echo URL ?>modules/downloads/images/default/download.png" alt="" /> <a href="<?php echo URL ?><?php echo $file['path_to_file'] .'/'. $file['add_file_real_name_'. $i] ?>"><?php echo $file['add_file_real_name_'. $i] ?></a> (<?php echo main::byte_format(@filesize(ROOT . $file['path_to_file'] .'/'. $file['add_file_real_name_'. $i])) ?>)<br />
    <?php endif; ?>
    <?php endfor; ?>
</div>

<?php if ($file['user_id'] == USER_ID): ?>
<div class="block">
    <a href="<?php echo a_url('downloads/user_files', 'action=edit&amp;file_id='. $file['file_id']) ?>">Изменить файл</a><br />
    <a href="<?php echo a_url('downloads/user_files', 'action=delete&amp;file_id='. $file['file_id']) ?>">Удалить файл</a>
</div>
<?php endif ?>

<div class="block">
    <?php if(!empty($navigation)) echo $navigation .'<br />'; ?>
    <a href="<?php echo a_url('downloads') ?>">Загрузки</a><br />
    <a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>