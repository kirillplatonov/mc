<?php
/**
 * Ant0ha's project
 *
 * @package
 * @author Anton Pisarenko <wapwork@bk.ru>
 * @copyright Copyright (c) 2006 - 2010, Anton Pisarenko
 * @license http://ant0ha.ru/license.txt
 * @link http://ant0ha.ru
 */

//---------------------------------------------

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

/**
 * Новости, пользовательская часть
 */
final class News_Controller extends Controller {
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
	* Детализирование новости
	*/
	public function action_detail() {
		if(!$news = $this->db->get_row("SELECT * FROM #__news WHERE news_id = '". intval($_GET['news_id']) ."'")) a_error('Новость не найдена!');
		
		$news['comments'] = $this->db->get_one("SELECT COUNT(*) FROM #__comments_posts WHERE module = 'news' AND item_id = '". intval($_GET['news_id']) ."'");

		// Добавляем bbcod'ы
		$news['text'] = nl2br(main::bbcode($news['text']));

		$this->tpl->assign(array(
			'news' => $news
		));

		$this->tpl->display('detail');
	}

	/**
	* Листинг новостей
	*/
	public function action_list_news() {
		# Получение данных
  		$result = $this->db->query("SELECT SQL_CALC_FOUND_ROWS n.*,
  		 (SELECT COUNT(*) FROM #__comments_posts WHERE module = 'news' AND item_id = n.news_id) AS comments
  		 FROM #__news AS n ORDER BY n.news_id DESC LIMIT $this->start, $this->per_page");

  		$total = $this->db->get_one("SELECT FOUND_ROWS()");

  		$list_news = array();
  		while($news = $this->db->fetch_array($result)) {
  			$news['text'] = nl2br(main::bbcode($news['text']));
  			$list_news[] = $news;
  		}

		# Пагинация
		$pg_conf['base_url'] = a_url('news', 'start=');
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

	/**
	* Скрыть последнюю новость
	*/
	public function action_hide_last_news() {
		if(ACCESS_LEVEL < 5) a_error('У вас нет прав на выполнение этой операции!');
	
		$this->db->query("UPDATE #__users SET hide_last_news = 'yes' WHERE user_id = '". USER_ID ."'");
	
		header("Location: ". URL);
		exit;
	}
}

?>