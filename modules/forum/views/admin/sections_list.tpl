<?php $this->display('header', array('title' => 'Управление разделами форума')) ?>

<table cellspacing="0" cellpadding="0" class="table">
    <thead align="left" valign="middle">
        <tr>
            <td>Название</td>
            <td colspan="4">Действия</td>
        </tr>
    </thead>
    <tbody align="left" valign="middle">
        <?php if($sections): ?>
        <?php foreach($sections as $section): ?>
        <tr>
            <td><b><a href="<?php echo a_url('forum/admin/forums', 'section_id='. $section['section_id']) ?>"><?php echo $section['name'] ?></a></b></td>
            <td><?php echo $section['up'] ?></td>
            <td><?php echo $section['down'] ?></td>
            <td><a href="<?php echo a_url('forum/admin/sections', 'a=edit&amp;section_id='. $section['section_id']) ?>">Изменить</a></td>
            <td><a href="#" onclick="if (confirm('Вы действительно хотите удалить раздел &laquo;<?php echo $section['name'] ?>&raquo;?')) {parent.location='<?php echo a_url('forum/admin/sections', 'a=delete&amp;section_id='. $section['section_id']) ?>';}">Удалить</a></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if($pagination): ?>
<p><?php echo $pagination ?></p>
<?php endif ?>

<?php $this->display('footer') ?>