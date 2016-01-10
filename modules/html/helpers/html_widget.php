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
 * Виджет html вставок
 */
class html_widget {
	/**
	 * Показ виджета
	 */
	public static function display($widget_id) {
		$db = Registry::get('db');
		$widget = $db->get_row("SELECT * FROM #__index_page_widgets WHERE widget_id = $widget_id");
		$config = parse_ini_string($widget['config']);
		if (empty($config['code'])) return 'Виджет &laquo;'.$widget['title'].'&raquo; не настроен, либо настроен не верно!<br />';
		$code = $config['code'];
		$code = str_replace('{ICON}', '<img src="'.URL.'views/'.THEME.'/images/icon.png" alt="" />', $code);
		$code = str_replace('{URL}', URL, $code);
		$code = str_replace('{EXT}', EXT, $code);
		return $code.'<br />';
	}

	/**
	 * Настройка виджета
	 */
	public static function setup($widget) {
		$db = Registry::get('db');
		$tpl = Registry::get('tpl');

		if (isset($_POST['submit'])) {
			if (!$error) {
				$code = str_replace("\n", "", $_POST['code']);
				$code = str_replace("\r", "", $code);

				$config = 'code = "'.mysqli_real_escape_string($this->db->db_link, $code).'"';

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
				<label>HTML/текстовая вставка:</label>
				<textarea name="code">'. htmlspecialchars($config['code']).'</textarea>
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
