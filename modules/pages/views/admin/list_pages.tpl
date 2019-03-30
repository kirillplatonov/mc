<?php $this->display('header', array('title' => 'Страницы')) ?>

<table cellspacing="0" cellpadding="0" class="table">
    <thead align="left" valign="middle">
        <tr>
            <td>ID</td>
            <td>Заголовок</td>
            <td>Ссылка</td>
            <td colspan="2">Действия</td>
        </tr>
    </thead>
    <tbody align="left" valign="middle">
        <?php foreach($pages as $page): ?>
        <tr>
            <td><b><?php echo $page['page_id'] ?></b></td>
            <td><?php echo $page['title'] ?></td>
            <td><a href="<?php echo str_replace('&amp;', '', a_url('pages', 'page_id='. $page['page_id'])) ?>"><?php echo str_replace('&amp;', '', a_url('pages', 'page_id='. $page['page_id'])) ?></a></td>
            <td><a href="<?php echo a_url('pages/admin/page_edit', 'page_id='. $page['page_id']) ?>">Изменить</a></td>
            <td><a href="#" onclick="if (confirm('Действительно хотите удалить страницу &laquo;<?php echo $page['title'] ?>&raquo;?')) {parent.location='<?php echo a_url('pages/admin/page_delete', 'page_id='. $page['page_id']) ?>';}">Удалить</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if($pagination): ?>
<p><?php echo $pagination ?></p>
<?php endif ?>

<?php $this->display('footer') ?>