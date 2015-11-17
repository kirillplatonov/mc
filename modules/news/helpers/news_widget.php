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
 * Виджет загрузок
 */
class news_widget {
	/**
	 * Показ виджета
	 */
	public static function display($widget_id) {
		$db = Registry::get('db');
		$widget = $db->get_row("SELECT * FROM #__index_page_widgets WHERE widget_id = $widget_id");
		$config = parse_ini_string($widget['config']);
		$last_news = $db->get_row("SELECT * FROM #__news ORDER BY news_id DESC LIMIT 1");
		$comments = $db->get_one("SELECT COUNT(*) FROM #__comments_posts WHERE module = 'news' AND item_id = '".$last_news['news_id']."'");

		if ($config['view_last_news'] == 0) {
			return '<img src="'.URL.'modules/news/images/news.png" alt="" /> <a href="'.a_url('news').'">Новости</a> ('.date('d.m.Y', $last_news['time']).')<br />';
		}
		else {
			$last_news['text'] = preg_replace('|[[\/\!]*?[^\[\]]*?]|si', '', $last_news['text']);
			$last_news['text'] = main::limit_words(strip_tags($last_news['text']), 15);

			$code = '<b>'.$last_news['subject'].'</b> ('.date('d.m.Y', $last_news['time']).')<br />'.PHP_EOL;
			$code .= $last_news['text'].'...<br />'.PHP_EOL;
			$code .= '<a href="'.a_url('news/detail', 'news_id='.$last_news['news_id']).'">Читать далее</a> | <a href="'.a_url('comments', 'module=news&amp;item_id='.$last_news['news_id'].'&amp;return='.urlencode(URL)).'">Комментарии</a> ['.$comments.']<br /><img src="'.URL.'modules/news/images/news.png" alt="" /> <a href="'.a_url('news').'">Все новости</a>';
			return $code;
		}
	}

	/**
	 * Настройка виджета
	 */
	public static function setup($widget) {
		$db = Registry::get('db');
		$tpl = Registry::get('tpl');

		if (isset($_POST['submit'])) {
			$error = false;

			if (!$error) {
				if ($_POST['view_last_news'] == 1) $view_last_news = 1;
				else $view_last_news = 0;

				$config = 'view_last_news = "'.$view_last_news.'"';

				$db->query("UPDATE #__index_page_widgets SET
					config = '$config'
					WHERE widget_id = '".$widget['widget_id']."'
				");

				a_notice('Изменения сохранены', a_url('index_page/admin'));
			}
		}
		if (!isset($_POST['submit']) OR $error) {
			$config = parse_ini_string($widget['config']);

			$form_data = '
			<p>
				<label>Выводить на главную последнюю новость:</label>
				<select size="1" name="view_last_news">
  					<option value="1">Да</option>
  					<option value="0"'. ($config['view_last_news'] == 0 ? ' selected="selected"' : '').'>Нет</option>
				</select>
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