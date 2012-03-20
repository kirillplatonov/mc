<?php echo $this->display('header', array('title' => 'Смайлы')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Смайлы')) ?>

<?php foreach($smiles AS $smile): ?>
<div class="menu">
<img src="<?php echo URL ?>modules/smiles/smiles/<?php echo $smile['image'] ?>" alt="<?php echo $smile['code'] ?>" /> <?php echo $smile['code'] ?>
</div>
<?php endforeach; ?>

<?php if($pagination): ?>
<div class="block">
<?php echo $pagination ?>
</div>
<?php endif; ?>

<div class="block">
<a href="<?php echo urldecode(str_replace('&amp;amp;', '&amp;', $_GET['return_url'])) ?>"><?php echo urldecode($_GET['return_name']) ?></a><br />
<a href="<?php echo URL ?>">На главную</a>
</div>

<?php echo $this->display('footer') ?>