<?php $this->display('header.tpl', array('title' => $page['title'])) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => $page['title'])) ?>

<div class="menu">
<?php echo $page['content'] ?>
</div>

<div class="block">
<a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>