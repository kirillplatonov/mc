<?php $this->display('header', array('sub_title' => 'Теги')) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title', array('text' => 'Теги (bbcode)')) ?>

<div class="menu">
    [b]жирный[/b]: <b>жирный</b><br />
    [i]курсив[/i]: <i>курсив</i><br />
    [u]подчеркнутый[/u]: <u>подчеркнутый</u><br />
    <br />
    [red]красный[/red]: <font color="red">красный</font><br />
    [green]зеленый[/green]: <font color="green">зеленый</font><br />
    [blue]синий[/blue]: <font color="blue">синий</font><br />
    <br />
    [q]Цитата[/q]<br />
    <div class="q">Цитата</div>
    <br />
    [img]https://mobilecms.pro/modules/smiles/smiles/ab.gif[/img]: <img src="https://mobilecms.pro/modules/smiles/smiles/ab.gif" alt="" /><br />
    <br />
    [url=http://ant0ha.ru]Ссылка[/url]: <a href="http://ant0ha.ru">Ссылка</a><br />
    [email]mne@ant0ha.ru[/email]: <a rel="noindex" href="mailto:mne[dog]ant0ha.ru">mne[dog]ant0ha.ru</a>
</div>

<div class="block">
    <a href="<?php echo urldecode($_GET['return_url']) ?>"><?php echo urldecode($_GET['return_name']) ?></a><br />
    <a href="<?php echo URL ?>">На главную</a>
</div>

<?php echo $this->display('footer') ?>