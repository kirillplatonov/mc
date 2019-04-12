<?php

/**
 * MobileCMS
 *
 * Open source content management system for mobile sites
 *
 * @author MobileCMS Team <support@mobilecms.pro>
 * @copyright Copyright (c) 2011-2019, MobileCMS Team
 * @link https://mobilecms.pro Official site
 * @license MIT license
 */
defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

//---------------------------------------------

/**
 * Виджет загрузок
 */
class downloads_widget
{

    /**
     * Показ виджета
     */
    public static function display($widget_id)
    {
        $db = Registry::get('db');
        $widget = $db->get_row("SELECT * FROM #__index_page_widgets WHERE widget_id = $widget_id");
        $config = parse_ini_string($widget['config']);
        if (!is_numeric($config['directory_id']))
            return 'Виджет ' . $widget['title'] . ' не настроен, либо настроен не верно!<br />';

        # Получаем количество файлов в папке
        $files = $db->get_row("SELECT COUNT(*) AS all_files, COUNT(CASE WHEN time > UNIX_TIMESTAMP() - 86400 THEN 1 END) AS new_day FROM #__downloads_files WHERE " . ($config['directory_id'] != 0 ? "path_to_file LIKE '%/" . $config['directory_id'] . "/%' AND" : "") . " real_name != '' AND status = 'active'");
        return '<img src="' . URL . 'modules/downloads/images/' . ($config['directory_id'] == 0 ? 'downloads' : 'default/directory') . '.png" alt="" /> <a href="' . URL . 'downloads/' . $config['directory_id'] . '">' . $config['directory_name'] . '</a> <span class="count">[' . $files['all_files'] . ']</span>' . ($files['new_day'] > 0 ? ' <span class="new">+' . $files['new_day'] . '</span>' : '') . '<br />';
    }

    /**
     * Настройка виджета
     */
    public static function setup($widget)
    {
        $db = Registry::get('db');
        $tpl = Registry::get('tpl');

        if (isset($_POST['submit'])) {
            if ($_POST['directory_id'] != 0 && !$directory = $db->get_row("SELECT directory_id, name FROM #__downloads_directories WHERE directory_id = '" . intval($_POST['directory_id']) . "'")) {
                $error .= 'Папка не найдена!<br />';
            }

            if (!$error) {
                if ($_POST['directory_id'] == 0)
                    $directory = array(
                        'directory_id' => 0,
                        'name' => 'Загрузки'
                    );

                $config = 'directory_id = "' . $directory['directory_id'] . '"' . PHP_EOL;
                $config .= 'directory_name = "' . a_safe($directory['name']) . '"';

                $db->query("UPDATE #__index_page_widgets SET
					config = '$config'
					WHERE widget_id = '" . $widget['widget_id'] . "'
				");

                a_notice('Изменения сохранены', a_url('index_page/admin'));
            }
        }
        if (!isset($_POST['submit']) OR $error) {
            $config = parse_ini_string($widget['config']);

            $form_data = '
			<p>
				<label>ID папки (укажите 0 если хотите сослаться на модуль "Загрузки"):</label>
				<input name="directory_id" type="text" value="' . $config['directory_id'] . '">
			</p>';

            $tpl->assign(array(
                'form_data' => $form_data,
                'error' => $error
            ));

            $tpl->display('widget_setup');
        }
    }

}

?>