<?php $this->display('header', array('title' => 'Управление главной страницей')) ?>

<?php foreach($blocks as $block): ?>
<div class="box">
    <h3><?php echo $block['title'] ?></h3>
    <div class="inside">
        <?php if(!empty($block['widgets'])): ?>
        <table cellspacing="0" cellpadding="0" style="padding: 0px; width: 460px;">
            <tbody align="left" valign="middle">
                <?php foreach($block['widgets'] as $widget): ?>
                <tr>
                    <td><b><?php echo $widget['title'] ?></b></td>
                    <td align="right" width="65%">
                        <button onclick="location.href = '<?php echo a_url('index_page / admin / widget_setup', 'widget_id = '. $widget['widget_id']) ?>'">Настроить</button>
                        <button onclick="location.href = '<?php echo a_url('index_page / admin / widget_up', 'widget_id = '. $widget['widget_id']) ?>'">Вверх</button>
                        <button onclick="location.href = '<?php echo a_url('index_page / admin / widget_down', 'widget_id = '. $widget['widget_id']) ?>'">Вниз</button>
                        <button onclick="if (confirm('Действительно хотите удалить виджет &laquo;<?php echo $widget['title'] ?>&raquo;?')) {parent.location='<?php echo a_url('index_page/admin/widget_delete', 'widget_id='. $widget['widget_id']) ?>';}">Удалить</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <p>
            <button onclick="location.href = '<?php echo a_url('index_page / admin / widget_add', 'block_id = '. $block['block_id']) ?>'">Добавить виджет</button>
            <button onclick="location.href = '<?php echo a_url('index_page / admin / block_up', 'block_id = '. $block['block_id']) ?>'">Вверх</button>
            <button onclick="location.href = '<?php echo a_url('index_page / admin / block_down', 'block_id = '. $block['block_id']) ?>'">Вниз</button>
            <button onclick="location.href = '<?php echo a_url('index_page / admin / block_edit', 'block_id = '. $block['block_id']) ?>'">Изменить</button>
            <button onclick="if (confirm('Действительно хотите удалить блок &laquo;<?php echo $block['title'] ?>&raquo;?')) {parent.location='<?php echo a_url('index_page/admin/block_delete', 'block_id='. $block['block_id']) ?>';}">Удалить</button>
        </p>
    </div>
</div>
<?php endforeach; ?>

<?php $this->display('footer') ?>