<?php echo $this->display('header', array('title' => 'Информация')) ?>

<div class="title"><img src="<?php echo URL ?>views/<?php echo THEME ?>/img/titl.gif" class="ico" alt="" /><b>Информация</b></div>

<div class="menu">
<?php echo $message ?><br />
<a href="<?php echo $link ?>">Продолжить</a>
</div>

<?php echo $this->display('footer') ?>