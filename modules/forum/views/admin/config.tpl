<?php $this->display('header', array('title' => 'Настройки форума')) ?>

<form action="" method="post">
    <div class="box">
        <h3>Настроики форума</h3>
        <div class="inside">
            <p>
                <label>Количество тем на страницу</label>
                <input name="topics_per_page" type="text" value="<?php echo $_config['topics_per_page'] ?>">
            </p>
            <p>
                <label>Количество сообщений на страницу</label>
                <input name="messages_per_page" type="text" value="<?php echo $_config['messages_per_page'] ?>">
            </p>
            <p>
                <label>Раскрывать ли форумы в списке разделов</label>
                <select size="1" name="show_forums_in_list_sections">
                    <option value="0">Нет</option>
                    <option value="1"<?php if($_config['show_forums_in_list_sections'] == 1): ?> selected="selected"<?php endif; ?>>Да</option>
                </select>
            </p>
            <p>
                <label>Могут ли гости создавать темы</label>
                <select size="1" name="guests_create_topics">
                    <option value="0">Нет</option>
                    <option value="1"<?php if($_config['guests_create_topics'] == 1): ?> selected="selected"<?php endif; ?>>Да</option>
                </select>
            </p>
            <p>
                <label>Могут ли гости оставлять сообщения</label>
                <select size="1" name="guests_write_messages">
                    <option value="0">Нет</option>
                    <option value="1"<?php if($_config['guests_write_messages'] == 1): ?> selected="selected"<?php endif; ?>>Да</option>
                </select>
            </p>
            <p>
                <label>Разрешенные типы файлов для загрузки пользователями</label>
                <input name="allowed_filetypes" type="text" value="<?php echo $_config['allowed_filetypes'] ?>">
            </p>
            <p>
                <label>Максимальный размер файла для загрузки пользователями в мегабайтах</label>
                <input name="max_filesize" type="text" value="<?php echo $_config['max_filesize'] ?>">
            </p>
        </div>
    </div>

    <p><input type="submit" id="submit" name="submit" value="Сохранить"  /></p>

</form>

<?php $this->display('footer.tpl') ?>