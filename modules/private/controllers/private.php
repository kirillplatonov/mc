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

/**
 * Пользовательский контроллер модуля личных сообщений
 */
class Private_Controller extends Controller
{

    /**
     * Уровень пользовательского доступа
     */
    public $access_level = 5;

    /**
     * Папки
     */
    public $folders = array(
        'new' => 'Новые',
        'inbox' => 'Входящие',
        'outbox' => 'Исходящие',
        'saved' => 'Сохраненные'
    );

    /**
     * Метод по умолчанию
     */
    public function action_index()
    {
        $this->action_folders();
    }

    /**
     * Список папок
     */
    public function action_folders()
    {
        $result = $this->db->query("SELECT COUNT(*) AS count, folder FROM #__private_messages WHERE user_id = '" . USER_ID . "' GROUP BY folder");
        $folders = array();
        while ($folder = $this->db->fetch_array($result)) {
            $folders[$folder['folder']] = $folder['count'];
        }

        $this->tpl->assign(array(
            'folders' => $folders
        ));

        $this->tpl->display('folders');
    }

    /**
     * Отправить сообщение
     */
    public function action_send()
    {
        if (isset($_POST['submit'])) {
            if (!$user_to_id = $this->db->get_one("SELECT user_id FROM #__users WHERE username = '" . a_safe($_POST['username']) . "' AND user_id != -1")) {
                $this->error .= 'Получаетель не найден!<br />';
            }
            if ($user_to_id == USER_ID) {
                $this->error .= 'Отправлять сообщения самому себе запрещено!<br />';
            }
            if (empty($_POST['message'])) {
                $this->error .= 'Укажите текст сообщения.<br />';
            }

            if (!$this->error) {
                # Отправляем сообщение адресату
                $this->db->query("INSERT INTO #__private_messages SET
					user_id = '$user_to_id',
					user_to_id = '$user_to_id',
					user_from_id = '" . USER_ID . "',
					message = '" . a_safe($_POST['message']) . "',
					folder = 'new',
					time = UNIX_TIMESTAMP()
				");
                # Копируем сообщение в исходящие отправителя
                $this->db->query("INSERT INTO #__private_messages SET
					user_id = '" . USER_ID . "',
					user_to_id = '$user_to_id',
					user_from_id = '" . USER_ID . "',
					message = '" . a_safe($_POST['message']) . "',
					folder = 'outbox',
					time = UNIX_TIMESTAMP()
				");

                a_notice('Сообщение отправлено!', a_url('private'));
            }
        }
        if (!isset($_POST['submit']) OR $this->error) {
            $this->tpl->assign(array(
                'error' => $this->error
            ));

            $this->tpl->display('send');
        }
    }

    /**
     * Просмотр папки
     */
    public function action_list_messages()
    {
        if (!array_key_exists($_GET['folder'], $this->folders))
            a_error("Папка не найдена!");

        $result = $this->db->query("SELECT SQL_CALC_FOUND_ROWS pm.*, u_to.username AS username_to, u_from.username AS username_from
			FROM #__private_messages AS pm
			LEFT JOIN #__users AS u_to ON u_to.user_id = pm.user_to_id
			LEFT JOIN #__users AS u_from ON u_from.user_id = pm.user_from_id
			WHERE pm.folder = '" . a_safe($_GET['folder']) . "' AND pm.user_id = '" . USER_ID . "'
			ORDER BY message_id DESC
			LIMIT $this->start, $this->per_page
		");
        $total = $this->db->get_one("SELECT FOUND_ROWS()");

        if (!class_exists('smiles'))
            a_import('modules/smiles/helpers/smiles');
        $messages = array();
        while ($message = $this->db->fetch_array($result)) {
            $message['message'] = smiles::smiles_replace($message['message']);
            $message['message'] = main::bbcode($message['message']);
            $message['message'] = nl2br($message['message']);

            $messages[] = $message;
        }

        # Если папка "новые", метим сообщения прочитанными
        $this->db->query("UPDATE #__private_messages SET folder = 'inbox' WHERE user_id = '" . USER_ID . "' AND folder = 'new'");

        # Пагинация
        $pg_conf['base_url'] = a_url('private/list_messages', 'folder=' . $_GET['folder'] . '&amp;start=');
        $pg_conf['total_rows'] = $total;
        $pg_conf['per_page'] = $this->per_page;

        a_import('libraries/pagination');
        $pg = new CI_Pagination($pg_conf);

        $this->tpl->assign(array(
            'messages' => $messages,
            'folder_name' => $this->folders[$_GET['folder']],
            'pagination' => $pg->create_links()
        ));

        $this->tpl->display('list_messages');
    }

    /**
     * Просмотр сообщения
     */
    public function view_message()
    {
        $message = $this->db->get_row("SELECT * FROM #__private_messages
			WHERE message_id = '" . intval($_GET['message_id']) . "' AND user_id = '" . USER_ID . "'
		");

        if (!$message)
            a_error("Сообщение не найдено!");

        $this->tpl->assign(array(
            'message' => $message
        ));

        $this->tpl->display('view_message');
    }

    /**
     * Удаление сообщения
     */
    public function action_delete_message()
    {
        $message = $this->db->get_row("SELECT * FROM #__private_messages
			WHERE message_id = '" . intval($_GET['message_id']) . "' AND user_id = '" . USER_ID . "'
		");

        if (!$message)
            a_error("Сообщение не найдено!");

        if (!empty($_GET['confirm'])) {
            $this->db->query("DELETE FROM #__private_messages WHERE message_id = '" . intval($_GET['message_id']) . "' AND user_id = '" . USER_ID . "'");
            a_notice('Сообщение удалено!', a_url('private/list_messages', 'folder=' . $_GET['folder']));
        } else {
            a_confirm('Подтверждаете удаление данного сообщения?', a_url('private/delete_message', 'message_id=' . $_GET['message_id'] . '&amp;folder=' . $_GET['folder'] . '&amp;confirm=ok'), a_url('private/list_messages', 'folder=' . $_GET['folder']));
        }
    }

    /**
     * Сохранение сообщения
     */
    public function action_save_message()
    {
        $message = $this->db->get_row("SELECT * FROM #__private_messages
			WHERE message_id = '" . intval($_GET['message_id']) . "' AND user_id = '" . USER_ID . "'
		");

        if (!$message)
            a_error("Сообщение не найдено!");

        $this->db->query("UPDATE #__private_messages SET folder = 'saved' WHERE message_id = '" . intval($_GET['message_id']) . "' AND user_id = '" . USER_ID . "'");
        a_notice('Сообщение сохранено!', a_url('private/list_messages', 'folder=saved'));
    }

}

?>