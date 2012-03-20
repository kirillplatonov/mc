<?php $this->display('header', array('title' => 'Менеджер продажи рекламы')) ?>

<table cellspacing="0" cellpadding="0" class="table" style="width: 900px;">
<?php foreach($areas as $area): ?>
	<thead align="left" valign="middle">
		<tr>
			<td colspan="6"><?php echo $area['title'] ?> (<?php echo $area['ident'] ?>)</td>
			<td><a href="<?php echo a_url('ads_manager/admin/area_edit', 'area_id='. $area['area_id']) ?>"><span style="font-width: 8;">Изменить</span></a></td>
			<td><a href="#" onclick="if(confirm('Вы действительно хотите удалить площадку &laquo;<?php echo $area['title'] ?>&raquo; со всеми ссылками?')) {parent.location='<?php echo a_url('ads_manager/admin/area_delete', 'area_id='. $area['area_id']) ?>';}">Удалить</a></td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	<?php foreach($area['links'] as $link): ?>
		<tr>
			<td><b><?php echo $link['title'] ?></b></td>
			<td><a href="<?php echo $link['url'] ?>"><?php echo $link['url'] ?></a></td>
			<td><?php echo nl2br($link['names']) ?></td>
			<td><?php echo $link['count_all'] ?></td>
			<td><?php echo $link['up'] ?></td>
			<td><?php echo $link['down'] ?></td>
			<td><a href="<?php echo a_url('ads_manager/admin/link_edit', 'link_id='. $link['link_id']) ?>">Изменить</a></td>
			<td><a href="#" onclick="if(confirm('Вы действительно хотите удалить ссылку &laquo;<?php echo $link['title'] ?>&raquo;?')) {parent.location='<?php echo a_url('ads_manager/admin/link_delete', 'link_id='. $link['link_id']) ?>';}">Удалить</a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
<?php endforeach; ?>
</table>

<?php if($pagination): ?>
<p><?php echo $pagination ?></p>
<?php endif ?>

<?php $this->display('footer') ?>