<?php $this->display('header.tpl', array('sub_title' => $title)) ?>

<?php if ($error) echo '<div class="error">'. $error .'</div>' ?>

<?php $this->display('title.tpl', array('text' => $title)) ?>

<form method="post" action="<?php echo a_url('blog/my', 'action=edit&amp;post_id='. $post['id']) ?>">
    <div class="menu">
        Заголовок:<br />
        <input type="text" name="title" value="<?php echo nl2br($post['title']) ?>" /><br />

        Сообщение: (<a href="<?php echo a_url('smiles', 'return_name='. urlencode('Вернуться') .'&amp;return_url='. urlencode(a_url('blog/my', 'action=edit&amp;post_id='. $post['id']))) ?>">смайлы</a> / <a href="<?php echo a_url('main/bbcode', 'return_name='. urlencode('Вернуться') .'&amp;return_url='. urlencode(a_url('blog/my', 'action=edit&amp;post_id='. $post['id']))) ?>">теги</a>)<br />
        <textarea name="message" rows="5" cols="20"><?php echo nl2br($post['message']) ?></textarea><br /> 

        <input type="submit" name="submit" value="Сохранить" /> 
    </div>
</form>

<div class="block">
    <a href="<?php echo a_url('blog') ?>">Вернуться</a><br />
    <a href="<?php echo URL ?>">На главную</a>
</div>

<?php $this->display('footer') ?>