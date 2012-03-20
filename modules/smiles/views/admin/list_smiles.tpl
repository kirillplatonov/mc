<?php $this->display('header', array('title' => 'Смайлы')) ?>

<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
			<td>Код</td>
			<td>Изображение</td>
			<td>Удалить</td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	<?php foreach($smiles as $smile): ?>
		<tr>
			<td><b><?php echo $smile['code'] ?></b></td>
			<td><span align="center"><img src="<?php echo URL ?>modules/smiles/smiles/<?php echo $smile['image'] ?>" alt="<?php echo $smile['code'] ?>" /></span></td>
			<td><a href="#" onclick="if(confirm('Вы действительно хотите удалить этот смайл?')) {parent.location='<?php echo a_url('format/admin/smile_delete', 'smile_id='. $smile['smile_id']) ?>';}">Удалить</a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php if($pagination): ?>
<p><?php echo $pagination ?></p>
<?php endif ?>

<?php $this->display('footer') ?>