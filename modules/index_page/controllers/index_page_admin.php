<?php
/**
	 * MobileCMS
	 *
	 * Open source content management system for mobile sites
	 *
	 * @author MobileCMS Team <support@mobilecms.pro>
	 * @copyright Copyright (c) 2011-2019, MobileCMS Team
	 * @link https://mobilecms.pro Official site
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
	 */

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

//---------------------------------------------

/**
 * Контроллер управления главной страницей
 */
class Index_Page_Admin_Controller extends Controller {
	/**
	 * Уровень пользовательского доступа
	 */
	public $access_level = 10;
	/**
	 * Тема
	 */
	public $template_theme = 'admin';

	/**
	 * Конструктор
	 */
	public function __construct() {
		parent::__construct();
		a_import('modules/index_page/helpers/index_page');

		# Чистка кэша главной страницы
		if (ROUTE_ACTION != '') {
			main::is_demo();
			if (!class_exists('File_Cache')) a_import('libraries/file_cache'); ;
			$file_cache = new File_Cache(ROOT.'cache/file_cache');
			$file_cache->clear('index_page');
		}
	}

	/**
	 * Метод по умолчанию
	 */
	public function action_index() {
		$this->action_view_page();
	}

	/**
	 * Просмотр главной страницы
	 */
	public function action_view_page() {
		$result = $this->db->query("SELECT * FROM #__index_page_blocks ORDER BY position ASC");

		$blocks = array();
		while ($block = $this->db->fetch_array($result)) {
			# Получаем виджеты блока
			$block['widgets'] = $this->db->get_array("SELECT * FROM #__index_page_widgets WHERE block_id = '".$block['block_id']."' ORDER BY position ASC");

			$blocks[] = $block;
		}

		$this->tpl->assign(array(
			'blocks' => $blocks
		));
	
		$this->tpl->display('view_page');
	}

	/**
	 * Очистить кэш главной
	 */
	public function action_cache_clear() {
		a_notice('Кэш главной очищен!', a_url('index_page/admin'));
	}

	/**
	 * Добавление виджета
	 */
	public function action_widget_add() {
		if (!$block = $this->db->get_row("SELECT * FROM #__index_page_blocks WHERE block_id = '".intval($_GET['block_id'])."'"))
			a_error("Блок не найден!");

		if (isset($_POST['submit'])) {
			if (!file_exists(ROOT.'modules/'.$_POST['module'].'/helpers/'.$_POST['module'].'_widget.php')) {
				$this->error .= 'Не найден вспомогательный файл виджета<br />';
			}
			if (empty($_POST['title'])) {
				$this->error .= 'Укажите заголовок виджета<br />';
			}

			if (!$this->error) {
				$position = $this->db->get_one("SELECT MAX(position) FROM #__index_page_widgets WHERE block_id = '".intval($_GET['block_id'])."'") + 1;

				$this->db->query("INSERT INTO #__index_page_widgets SET
					block_id = '". intval($_GET['block_id'])."',
					title = '". a_safe($_POST['title'])."',
					module = '". a_safe($_POST['module'])."',
					position = '$position'
				");
		
				a_notice('Виджет успешно добавлен!', a_url('index_page/admin'));
			}
		}
		if (!isset($_POST['submit']) OR $this->error) {
			$widgets = index_page::get_widgets();

			$this->tpl->assign(array(
				'error' => $this->error,
				'widgets' => $widgets
			));
	
			$this->tpl->display('widget_add');
		}
	}

	/**
	 * Настройка виджета
	 */
	public function action_widget_setup() {
		if(!$widget = $this->db->get_row("SELECT * FROM #__index_page_widgets WHERE widget_id = '". intval($_GET['widget_id']) ."'"))
			a_error('Виджет не найден!');

		# Подключаем класс виджета
  		if(!class_exists($widget['module'] .'_widget'))
			a_import('modules/'. $widget['module'] .'/helpers/'. $widget['module'] .'_widget.php');
		# Получаем setup виджета
		call_user_func(array($widget['module'] .'_widget', 'setup'), $widget);
	}

	/**
	 * Перемещение виджета вверх
	 */
	public function action_widget_up() {
		if(!$widget = $this->db->get_row("SELECT * FROM #__index_page_widgets WHERE widget_id = '". intval($_GET['widget_id']) ."'")) {
					a_error('Виджет не найден!');
		}

		$min_position = $this->db->get_one("SELECT MIN(position) FROM #__index_page_widgets WHERE block_id = '". $widget['block_id'] ."'");

		if($widget['position'] > $min_position) {
			# Меняем позиции
			$this->db->query("UPDATE #__index_page_widgets SET position = ". $widget['position'] ." WHERE block_id = '". $widget['block_id'] ."' AND position = ". ($widget['position'] - 1));
			$this->db->query("UPDATE #__index_page_widgets SET position = ". ($widget['position'] - 1) ." WHERE block_id = '". $widget['block_id'] ."' AND widget_id = ". intval($_GET['widget_id']));
		}

		header("Location: ". a_url('index_page/admin'));
		exit;
	}

	/**
	 * Перемещение виджета вниз
	 */
	public function action_widget_down() {
		if (!$widget = $this->db->get_row("SELECT * FROM #__index_page_widgets WHERE widget_id = '".intval($_GET['widget_id'])."'"))
			a_error('Виджет не найден!');

			$max_position = $this->db->get_one("SELECT MAX(position) FROM #__index_page_widgets WHERE block_id = '".$widget['block_id']."'");

		if ($widget['position'] < $max_position) {
			# Меняем позиции
			$this->db->query("UPDATE #__index_page_widgets SET position = ".$widget['position']." WHERE block_id = '".$widget['block_id']."' AND position = ".($widget['position'] + 1));
			$this->db->query("UPDATE #__index_page_widgets SET position = ".($widget['position'] + 1)." WHERE block_id = '".$widget['block_id']."' AND widget_id = ".intval($_GET['widget_id']));
		}
	
		header("Location: ".a_url('index_page/admin'));
		exit;
	}

	/**
	 * Удаление виджета
	 */
	public function action_widget_delete() {
		if (!$widget = $this->db->get_row("SELECT * FROM #__index_page_widgets WHERE widget_id = '".intval($_GET['widget_id'])."'"))
			a_error('Виджет не найден!');
	
		$this->db->query("DELETE FROM #__index_page_widgets WHERE widget_id = '".intval($_GET['widget_id'])."'");
	
		a_notice('Виджет успешно удален', a_url('index_page/admin'));
	}

	/**
	 * Перемещение блока вверх
	 */
	public function action_block_up() {
		if (!$block = $this->db->get_row("SELECT * FROM #__index_page_blocks WHERE block_id = ".intval($_GET['block_id'])))
			a_error('Блок не найден!');
	
		$min_position = $this->db->get_one("SELECT MIN(position) FROM #__index_page_blocks");
	
		if ($block['position'] > $min_position) {
			# Меняем позиции
			$this->db->query("UPDATE #__index_page_blocks SET position = ".$block['position']." WHERE position = ".($block['position'] - 1));
			$this->db->query("UPDATE #__index_page_blocks SET position = ".($block['position'] - 1)." WHERE block_id = ".intval($_GET['block_id']));
		}
	
		header("Location: ".a_url('index_page/admin'));
		exit;
	}

	/**
	 * Перемещение блока вниз
	 */
	public function action_block_down() {
		if (!$block = $this->db->get_row("SELECT * FROM #__index_page_blocks WHERE block_id = ".intval($_GET['block_id'])))
			a_error('Блок не найден!');

		$max_position = $this->db->get_one("SELECT MAX(position) FROM #__index_page_blocks");

		if ($block['position'] < $max_position) {
			# Меняем позиции
			$this->db->query("UPDATE #__index_page_blocks SET position = ".$block['position']." WHERE position = ".($block['position'] + 1));
			$this->db->query("UPDATE #__index_page_blocks SET position = ".($block['position'] + 1)." WHERE block_id = ".intval($_GET['block_id']));
		}

		header("Location: ".a_url('index_page/admin'));
		exit;
	}

	/**
	 * Удаление блока
	 */
	public function action_block_delete() {
		if(!$block = $this->db->get_row("SELECT * FROM #__index_page_blocks WHERE block_id = ". intval($_GET['block_id'])))
			a_error('Блок не найден!');

		# Удаляем блок
		$this->db->query("DELETE FROM #__index_page_blocks WHERE block_id = ". intval($_GET['block_id']));
		# Удаляем все виджеты данного блока
		$this->db->query("DELETE FROM #__index_page_widgets WHERE block_id = ". intval($_GET['block_id']));
	
		header("Location: ". a_url('index_page/admin'));
		exit;
	}

	/**
	 * Добавление / Редактирование блока
	 */
	public function action_block_edit() {
		if (is_numeric($_GET['block_id'])) {
			if (!$block = $this->db->get_row("SELECT * FROM #__index_page_blocks WHERE block_id = '".intval($_GET['block_id'])."'"))
				a_error("Блок не найден!");
			$action = 'edit';
		}
		else {
			$block = array();
			$action = 'add';
		}

		if (isset($_POST['submit'])) {
			if (empty($_POST['title'])) {
				$this->error .= 'Укажите заголовок блока<br />';
			}

			if (!$this->error) {
				if ($action == 'add') {
					$position = $this->db->get_one("SELECT MAX(position) FROM #__index_page_blocks") + 1;
		
					$this->db->query("INSERT INTO #__index_page_blocks SET
						title = '". a_safe($_POST['title'])."',
						position = '$position'
					");
					$message = "Блок успешно добавлен!";
				}
				if ($action == 'edit') {
					$this->db->query("UPDATE #__index_page_blocks SET
						title = '". a_safe($_POST['title'])."'
						WHERE block_id = '". intval($_GET['block_id'])."'
					");
					$message = "Блок успешно изменен!";
				}

				a_notice($message, a_url('index_page/admin'));
			}
		}
		if (!isset($_POST['submit']) OR $this->error) {
			$this->tpl->assign(array(
				'error' => $this->error,
				'block' => $block,
				'action' => $action
			));

			$this->tpl->display('block_edit');
		}
	}
}
?>