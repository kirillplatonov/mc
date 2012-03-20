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
 * Контроллер пользовательской части модуля главной страницы
 */
class Index_Page_Controller extends Controller {
	/**
	* Уровень пользовательского доступа
	*/
	public $access_level = 0;

	/**
	* Метод по умолчанию
	*/
	public function action_index() {
		$this->action_view_page();
	}

	/**
	* Показ главной страницы
	*/
	public function action_view_page() {
		$blocks = $this->cache->get('index_page', 180);
		$blocks = $blocks;

  		if(empty($blocks)) {
			$result = $this->db->query("SELECT * FROM #__index_page_blocks ORDER BY position ASC");
			$blocks = array();
			while($block = $this->db->fetch_array($result)) {
				$result1 = $this->db->query("SELECT * FROM #__index_page_widgets WHERE block_id = '". $block['block_id'] ."' ORDER BY position ASC");
				$block['widgets'] = array();
				while($widget = $this->db->fetch_array($result1)) {
					# Подключаем класс виджета
					if(!class_exists($widget['module'] .'_widget'))
						a_import('modules/'. $widget['module'] .'/helpers/'. $widget['module'] .'_widget.php');
					# Получаем display виджета
					$block['widgets'][] = call_user_func(array($widget['module'] .'_widget', 'display'), $widget['widget_id']);
				}
	
				$blocks[] = $block;
			}

			$this->cache->set('index_page', $blocks);
	  	}

		$this->tpl->assign(array(
			'blocks' => $blocks
		));
	
		$this->tpl->display('view_page');
	}
}
?>