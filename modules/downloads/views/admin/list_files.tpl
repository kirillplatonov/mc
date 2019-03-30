<?php $this->display('header', array('title' => 'Список файлов')) ?>

<div class="box">
    <div class="inside">
        <p>
            <a href="<?php echo a_url('downloads/admin/directory_edit', 'parent_id='. $directory['directory_id']) ?>">Создать папку</a><br />
            <a href="<?php echo a_url('downloads/admin/file_upload', 'directory_id='. $directory['directory_id']) ?>">Загрузить файл</a><br />
            <?php if ($directory['directory_id'] > 0): ?>
            <a href="<?php echo a_url('downloads/admin/ftp_upload', 'directory_id='. $directory['directory_id']) ?>">Загрузить файлы по FTP</a><br />
            <?php endif; ?>
        </p>
    </div>
</div>

<table cellspacing="0" cellpadding="0" class="table">
    <thead>
        <tr>
            <td style="width: 16px;"> </td>
            <td>ID</td>
            <td>Название</td>
            <td style="width: 16px;"> </td>
            <td style="width: 16px;"> </td>
            <td style="width: 16px;"> </td>
            <td style="width: 16px;"> </td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="5">
                <?php if(!empty($namepath)): echo $namepath .'/'; endif ?><b><?php echo $directory['name'] ?>/</b><b><?php if($directory): ?><a href="<?php echo a_url('downloads/admin/list_files', 'directory_id='. $directory['parent_id']) ?>">..</a></b><?php endif; ?>
            </td>
        </tr>
        <?php foreach($files as $file): ?>
        <tr>
            <td><img src="<?php echo URL ?>modules/downloads/images/admin/<?php echo $file['type'] ?>.png" /></td>
            <td><b><?php echo $file['file_id'] ?></b></td>
            <td>
                <?php if($file['type'] == 'directory'): ?>
                <a href="<?php echo a_url('downloads/admin/list_files', 'directory_id='. $file['file_id']) ?>"><?php echo $file['name'] ?></a>
                <?php else: ?>
                <a href="<?php echo a_url('downloads/view_file', 'file_id='. $file['file_id']) ?>"><?php echo $file['name'] ?></a>
                <?php endif; ?>
            </td>
            <td><?php echo $file['up'] ?></td>
            <td><?php echo $file['down'] ?></td>
            <td>
                <?php if($file['type'] == 'directory'): ?>
                <a href="<?php echo a_url('downloads/admin/directory_edit', 'directory_id='. $file['file_id']) ?>"><img src="<?php echo URL ?>views/admin/images/edit.png" alt="" /></a>
                <?php else: ?>
                <a href="<?php echo a_url('downloads/admin/file_upload', 'file_id='. $file['file_id']) ?>"><img src="<?php echo URL ?>views/admin/images/edit.png" alt="" /></a>
                <?php endif ?>
            </td>
            <td>
                <?php if($file['type'] == 'directory'): ?>
                <a href="#" onclick="if (confirm('Действительно хотите удалить папку &laquo;<?php echo $file['name'] ?>&raquo;?')) {parent.location='<?php echo a_url('downloads/admin/directory_delete', 'directory_id='. $file['file_id']) ?>';}"><img src="<?php echo URL ?>views/admin/images/delete.png" alt="" /></a>
                <?php else: ?>
                <a href="#" onclick="if (confirm('Действительно хотите удалить файл &laquo;<?php echo $file['name'] ?>&raquo;?')) {parent.location='<?php echo a_url('downloads/admin/file_delete', 'file_id='. $file['file_id']) ?>';}"><img src="<?php echo URL ?>views/admin/images/delete.png" alt="" /></a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if($pagination)
echo '<p>'. $pagination .'</p>';
?>

<?php $this->display('footer') ?>