<?php
/**
	 * MobileCMS
	 *
	 * Open source content management system for mobile sites
	 *
	 * @author MobileCMS Team <support@mobilecms.ru>
	 * @copyright Copyright (c) 2011, MobileCMS Team
	 * @link http://mobilecms.ru Official site
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
	 */

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

//---------------------------------------------

/**
 * Управление страницами
 */
class Pages_Admin_Controller extends Controller {
	/**
	 * Уровень пользовательского доступа
	 */
	public $access_level = 10;
	/**
	 * Тема
	 */
	public $template_theme = 'admin';

	/**
	 * Метод по умолчанию
	 */
	public function action_index() {
		$this->action_list_pages();
	}

	/**
	 * Добавление / редактирование страниц
	 */
	public function action_page_edit() {
		if (is_numeric($_GET['page_id'])) {
			$action = 'edit';
			if (!$page = $this->db->get_row("SELECT * FROM #__pages WHERE page_id = '".intval($_GET['page_id'])."'"))
				a_error('Страница не найдена!');
		}
		else {
			$action = 'add';
			$page = array();
		}

		if (isset($_POST['submit'])) {
			main::is_demo();
			if (empty($_POST['title'])) {
				$this->error .= 'Укажите заголовок страницы!<br />';
			}
			if (empty($_POST['editor_content'])) {
				$this->error .= 'Укажите содержимое страницы!<br />';
			}

			if (!$this->error) {
				if ($action == 'add') {
					$this->db->query("INSERT INTO #__pages SET
						`title` = '". a_safe($_POST['title'])."',
						`content` = '". mysqli_real_escape_string($this->db, main::tinymce_p_br($_POST['editor_content']))."'
					");
					$message = 'Страница успешно добавлена!';
				}
				elseif ($action == 'edit') {
					$this->db->query("UPDATE #__pages SET
						`title` = '". a_safe($_POST['title'])."',
						`content` = '". mysqli_real_escape_string($this->db, main::tinymce_p_br($_POST['editor_content']))."'
						WHERE
						page_id = '". intval($_GET['page_id'])."'
					");
					$message = 'Страница успешно изменена!';
				}

				a_notice($message, a_url('pages/admin'));
			}
		}
		if (!isset($_POST['submit']) || $this->error) {
			$this->tpl->assign(array(
				'error' => $this->error,
				'page' => $page,
				'action' => $action
			));
	
			$this->tpl->display('page_edit');
		}
	}

	/**
	 * Листинг страниц
	 */
	public function action_list_pages() {
		$this->per_page = 20;
		# Получение данных
  		$pages = $this->db->get_array("SELECT SQL_CALC_FOUND_ROWS *
  			FROM #__pages ORDER BY page_id LIMIT $this->start, $this->per_page
  		");

  		$total = $this->db->get_one("SELECT FOUND_ROWS()");

		# Пагинация
		$pg_conf['base_url'] = a_url('pages/admin/list_pages', 'start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'pages' => $pages,
			'total' => $total,
			'pagination' => $pg->create_links()
		));

		$this->tpl->display('list_pages');
	}

	/**
	 * Удаление страницы
	 */
	public function action_page_delete() {
		main::is_demo();
		if (!$page = $this->db->get_row("SELECT * FROM #__pages WHERE page_id = '".intval($_GET['page_id'])."'"))
			a_error('Страница не найдена!');

		$this->db->query("DELETE FROM #__pages WHERE page_id = '".intval($_GET['page_id'])."'");
		a_notice('Страница успешно удалена!', a_url('pages/admin'));
	}
}
?>