<?php $this->display('header', array('sub_title' => 'Добро пожаловать!')) ?><!-- Заголовок страницы (см. файл header.tpl в теме) -->

<?php if ( ! empty($blocks)): ?>
<?php foreach($blocks as $block): ?>

<!-- Вывод блоков -->

<?php $this->display('title', array('text' => $block['title'])) ?><!-- Заголовок (см. файл title.tpl в теме) -->

<?php if ( ! empty($block['widgets'])): ?>
<!-- Вывод содержимого виджета -->

<div class="menu">
    <?php foreach($block['widgets'] as $widget): ?>
    <?php echo $widget ?>
    <?php endforeach; ?>
</div>

<?php endif; ?>
<?php endforeach; ?>
<?php else: ?>
<!-- Выводится если нет активных блоков с виждетами -->
<div class="menu">
    Блоков нет
</div>
<?php endif ?>

<?php $this->display('footer') ?><!-- Ноги страницы (см. файл footer.tpl в теме) -->