<?php $this->display('header', array('title' => 'Модули')) ?>

<?php if($modules): ?>
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
			<td>Модуль</td>
			<td colspan="2">Действия</td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">
	<?php foreach($modules as $module): ?>
		<tr>
			<td style="width: 400px"><?php if(!empty($module['admin_link'])): ?><a href="<?php echo a_url($module['admin_link']) ?>"><?php echo $module['title'] ?></a><?php else: ?><?php echo $module['title'] ?><?php endif; ?></td>
			<td>
				<?php if($module['installed'] == 0): ?>
					<a href="<?php echo a_url('modules/admin/install', 'module='. $module['name']) ?>">Установить</a>
				<?php elseif($module['installed'] == 1 && $module['status'] == 'off'): ?>
					<a href="<?php echo a_url('modules/admin/activate', 'module='. $module['name'] .'&amp;status=on') ?>">Активировать</a>
				<?php elseif($module['status'] == 'on'): ?>
					<a href="<?php echo a_url('modules/admin/activate', 'module='. $module['name'] .'&amp;status=off') ?>">Деактивировать</a>
				<?php endif; ?>
			</td>
			<td style="width: 12px">
				<?php if($module['installed'] == 1): ?>
			        <a href="#" onclick="if(confirm('Действительно хотите деинсталлировать модуль &laquo;<?php echo $module['name'] ?>&raquo;? Это может привести к безвозвратной потере данных модуля!')) {parent.location='<?php echo a_url('modules/admin/uninstall', 'module='. $module['name']) ?>';}"><img src="<?php echo URL ?>views/admin/images/delete.png" alt="" /></a>
			    <?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p><b>Нет ниодного модуля</b></p>
<?php endif; ?>

<?php $this->display('footer') ?>