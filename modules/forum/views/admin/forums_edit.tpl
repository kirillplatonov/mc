<?php $this->display('header', array('title' => ($action == 'add' ? 'Создать' : 'Изменить') .' форум')) ?>

<?php if($error): ?>
<div class="error">
    <?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('forum/admin/forums', 'a=edit&amp;forum_id='. @$_GET['forum_id']) ?>" method="post">
    <div class="box">
        <h3><?php echo ($action == 'add' ? 'Создать' : 'Изменить') ?> форум</h3>
        <div class="inside">
            <p>
                <label>Название</label>
                <input name="name" type="text" value="<?php echo $forum['name'] ?>">
            </p>
            <p>
                <label>Раздел</label>
                <select size="1" name="section_id">
                    <?php if($sections): ?>
                    <?php foreach($sections as $section): ?>
                    <option value="<?php echo $section['section_id'] ?><?php if($section['section_id'] == $forum['section_id']): ?> selected="selected"<?php endif; ?>"><?php echo $section['name'] ?></option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </p>
        </div>
    </div>
    <p><input type="submit" name="submit" value="<?php echo ($action == 'add' ? 'Создать' : 'Изменить') ?>"></p>
</form>

<?php $this->display('footer') ?>