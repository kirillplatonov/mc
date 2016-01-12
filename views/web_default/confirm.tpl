<?php echo $this->display('header.tpl', array('sub_title' => 'Подтверждение', 'title' => 'Подтверждение')) ?>

<div class="title"><img src="<?php echo URL ?>views/<?php echo THEME ?>/img/titl.gif" class="ico" alt="" /><b>Подтверждение</b></div>

<div class="menu">
<?php echo $message ?><br />
<a href="<?php echo $link_ok ?>">Да</a> | <a href="<?php echo $link_cancel ?>">Нет</a>
</div>

<?php echo $this->display('footer.tpl') ?>