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
 * Виджет страниц
 */
class pages_widget {
	/**
	 * Показ виджета
	 */
	public static function display($widget_id) {
		$db = Registry::get('db');
		$widget = $db->get_row("SELECT * FROM #__index_page_widgets WHERE widget_id = $widget_id");
		$config = parse_ini_string($widget['config']);
		if (!is_numeric($config['page_id'])) return 'Виджет &laquo;'.$widget['title'].'&raquo; не настроен, либо настроен не верно!<br />';
		return '<img src="'.URL.'modules/pages/images/page.png" alt="" /> <a href="'.a_url('pages', 'page_id='.$config['page_id']).'">'.$config['title'].'</a><br />';
	}

	/**
	 * Настройка виджета
	 */
	public static function setup($widget) {
		$db = Registry::get('db');
		$tpl = Registry::get('tpl');

		if (isset($_POST['submit'])) {
			if (!$page = $db->get_row("SELECT page_id, title FROM #__pages WHERE page_id = '".intval($_POST['page_id'])."'")) {
				$error .= 'Страница не найдена!<br />';
			}

			if (!$error) {
				$config  = 'page_id = "'.$page['page_id'].'"'.PHP_EOL;
				$config .= 'title = "'.a_safe($page['title']).'"';

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
				<label>ID страницы:</label>
				<input name="page_id" type="text" value="'. $config['page_id'].'">
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