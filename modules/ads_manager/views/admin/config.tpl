<?php $this->display('header', array('title' => 'Настройки модуля продажи рекламы')) ?>

<form action="" method="post">
    <div class="box">
        <h3>Настройки модуля продажи рекламы</h3>
        <div class="inside">
            <p>
                <label>Показывать предупреждающее сообщение при переходе по рекламной ссылке</label>
                <select size="1" name="enable_notice">
                    <option value="1">Да</option>
                    <option value="0"<?php if(!$_config['enable_notice']): ?> selected="selected"<?php endif; ?>>Нет</option>
                </select>
            </p>
        </div>
    </div>

    <p><input type="submit" id="submit" name="submit" value="Сохранить"  /></p>

</form>

<?php $this->display('footer.tpl') ?>