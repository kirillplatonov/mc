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

/**
 * Новости, админская часть
 */
class News_Admin_Controller extends Controller {
	/**
	 * Уровень доступа
	 */
	public $access_level = 10;
	/**
	 * Тема шаблонов
	 */
	public $template_theme = 'admin';

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Действие по умолчанию
	 */
	public function action_index() {
		$this->action_list_news();
	}

	/**
	 * Добавление / Редактирование новости
	 */
	public function action_edit() {
		if (is_numeric($_GET['news_id'])) {
			$news = $this->db->get_row("SELECT * FROM #__news WHERE news_id = '".intval($_GET['news_id'])."'");
			$action = 'edit';
		} else {
			$news = array('subject' => '', 'text' => '');
			$action = 'add';
		}

  		if (isset($_POST['submit'])) {
  			main::is_demo();

			if (empty($_POST['subject'])) {
				$this->error .= 'Укажите тему новости<br />';
			}
			
  			if (empty($_POST['editor_text'])) {
  				$this->error .= 'Укажите текст новости<br />';
  			}

  			if (!$this->error) {
  				if ($action == 'add') {
  					$this->db->query("INSERT INTO #__news SET
  						subject = '". a_safe($_POST['subject'])."',
  						text = '". mysqli_real_escape_string($this->db->db_link, main::tinymce_p_br($_POST['editor_text']))."',
  						time = UNIX_TIMESTAMP()
  					");
  					
					$message = 'Новость успешно добавлена!';
  				}
  				
  				if ($action == 'edit') {
  					$this->db->query("UPDATE #__news SET
  						subject = '". a_safe($_POST['subject'])."',
  						text = '". mysqli_real_escape_string($this->db->db_link, main::tinymce_p_br($_POST['editor_text']))."'
  						WHERE news_id = '". intval($_GET['news_id'])."'
  					");
  					
  					$message = 'Новость успешно изменена!';
  				}
  				
				a_notice($message, a_url('news/admin'));
  			}
		}
  		if (!isset($_POST['submit']) || $this->error) {
			$this->tpl->assign(array(
				'news' => $news,
				'error' => $this->error,
  			  	'action' => $action
  			));

			$this->tpl->display('edit');
  		}
  	}

	/**
	 * Удаление новости
	 */
	public function action_delete() {
		main::is_demo();
		$this->db->query("DELETE FROM #__news WHERE news_id = '".intval($_GET['news_id'])."'");
		a_notice('Новость успешно удалена!', a_url('news/admin'));
	}

	/**
	 * Листинг новостей
	 */
	public function action_list_news() {
		$this->per_page = 20;

		# Получение данных
  		$list_news = $this->db->get_array("SELECT SQL_CALC_FOUND_ROWS * FROM #__news ORDER BY news_id DESC LIMIT $this->start, $this->per_page");
  		$total = $this->db->get_one("SELECT FOUND_ROWS()");

		# Пагинация
		$pg_conf['base_url'] = a_url('news/admin', 'start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'list_news' => $list_news,
			'total' => $total,
			'pagination' => $pg->create_links()
		));

		$this->tpl->display('list_news');
	}
}
?>