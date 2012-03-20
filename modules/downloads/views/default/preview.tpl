<?php $this->display('header.tpl', array('sub_title' => 'Загрузки')) ?>

<div class="title"><img src="<?php echo URL ?>views/<?php echo THEME ?>/img/titl.gif" class="ico" alt="" />Предпросмотр</div>

<div class="block">
<a href="<?php echo URL ?>downloads">Загрузки</a>
</div>

<div class="menu">
Предпросмотр:<br />
&raquo; <a href="<?php echo URL ?>downloads/<?php echo @$_GET['directory_id'] ?>?preview=0">без картинок</a><br />
&raquo; <a href="<?php echo URL ?>downloads/<?php echo @$_GET['directory_id'] ?>?preview=20">20х20</a><br />
&raquo; <a href="<?php echo URL ?>downloads/<?php echo @$_GET['directory_id'] ?>?preview=60">60х60</a><br />
&raquo; <a href="<?php echo URL ?>downloads/<?php echo @$_GET['directory_id'] ?>?preview=100">100х100</a><br />
</div>

<div class="block">
<a href="<?php echo URL ?>downloads">Загрузки</a><br />
<a href="<?php echo URL ?>">На главную</a><br />
</div>

<?php $this->display('footer') ?>