<?php $this->display('header', array('title' => $title)) ?>

<?php if($error): ?>
<div class="error">
    <?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('main/admin/mysql') ?>" method="post">
    <div class="box">
        <h3>MySQL запросы</h3>
        <div class="inside">
            <p>
                <label>Запросы</label>
                <textarea name="queries" style="height: 50px;"><?php echo $_POST['queries'] ?></textarea>
            </p>	        
            <p>
        </div>
    </div>
    <p><input type="submit" name="submit" value="Выполнить"></p>
</form>

<?php $this->display('footer') ?>