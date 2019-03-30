<?php $this->display('header', array('title' => 'Модерация файлов')) ?>

<table>
    <thead>
        <tr>
            <th>Название</th>
            <th>Папка</th>
            <th>Пользователь</th>
            <th colspan="2" width='30%'>Дейстивия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($files as $file): ?>
        <tr>
            <td>
                <a href="<?php echo a_url('downloads/view_file', 'file_id='. $file['file_id']) ?>"><?php echo $file['name'] ?></a>
            </td>
            <td>
                <a href="<?php echo a_url('downloads/admin', 'directory_id='. $file['directory_id']) ?>"><?php echo $file['directory_name'] ?></a>
            </td>
            <td><a href="<?php echo a_url('user/profile/view', 'user_id='. $file['user_id']) ?>"><?php echo $file['username'] ?></a></td>
            <td>
                <a href="<?php echo a_url('downloads/admin/file_upload', 'file_id='. $file['file_id']) ?>">Изменить</a>
            </td>
            <td>
                <a href="#" onclick="if (confirm('Действительно хотите удалить файл &laquo;<?php echo $file['name'] ?>&raquo;?')) {parent.location='<?php echo a_url('downloads/admin/file_delete', 'file_id='. $file['file_id']) ?>';}">Удалить</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if($pagination)
echo '<p>'. $pagination .'</p>';
?>

<?php $this->display('footer') ?>