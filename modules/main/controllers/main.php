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
 * Главный пользовательский контроллер основного модуля
 */
class Main_Controller extends Controller {
	/**
	 * Метод по умолчанию
	 */
	public function action_index() {
		$this->action_index_page();
	}

	/**
	 * Индексная страница
	 */
	public function action_index_page() {
		$last_news = $this->db->get_row("SELECT * FROM #__news ORDER BY news_id DESC LIMIT 1");

		$this->tpl->assign(array(
			'last_news' => $last_news
		));

		$this->tpl->display('index');
	}

	/**
	 * ББкоды
	 */
	public function action_bbcode() {
		$this->tpl->display('bbcode');
	}
	
	/**
	 * Ошибка не найденой страницы
	 */
	public function action_page_not_found() {
		$error_message  = 'Запрашиваемой страницы не существует!<br />';
		$error_message .= '<a href="'.URL.'">Перейти на главную</a>';
		
		a_error($error_message);
	}

	/**
	 * Рейтинг
	 */
	public function action_rating() {
		header('Content-type: text/plain');
		
		$star_full = '<img src="'.URL.'modules/main/images/rating/star_full.png" alt="" />';
		$star_half = '<img src="'.URL.'modules/main/images/rating/star_half.png" alt="" />';
		$star_empty = '<img src="'.URL.'modules/main/images/rating/star_empty.png" alt="" />';

		$rate = $_GET['rate'];
		if ($rate < 0 or $rate > 5) exit('не верный формат рейтинга');

		# Получаем количество целых звездочек
		$full_stars = floor($rate);

		# Определяем дробную часть числа
		$out = explode('.', $rate);
		$fract = floatval('0.'.$out[1]);

		# Генерируем строку звездочек
		for ($i = 0; $i < $full_stars; $i++) echo $star_full;

		if ($full_stars != 5) {
			if ($fract <= 0.25) echo $star_empty;
			elseif ($fract > 0.25 && $fract < 0.75) echo $star_half;
			else echo $star_full;

			$empty_stars = 5 - ($full_stars + 1);
			for ($i = 0; $i < $empty_stars; $i++) echo $star_empty;
		}
	}

	public function action_go() {
		echo '1';
	}
}
?>