<?php $this->display('header', array('sub_title' => 'Ошибка!')) ?>

<div class="title"><img src="<?php echo URL ?>views/<?php echo THEME ?>/img/titl.gif" class="ico" alt="" /><b>Произошла ошибка</b></div>

<div class="menu">
<?php echo $error_message ?>
</div>

<?php $this->display('footer') ?>