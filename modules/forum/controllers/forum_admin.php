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

/**
 * Контроллер админки форума
 */
class Forum_Admin_Controller extends Controller {
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
		$this->action_sections();
	}

	/**
	 * Конфигурация модуля
	 */
	public function action_config() {
		$_config = $this->config['forum'];

		if (isset($_POST['submit'])) {
			main::is_demo();
			$_config = $_POST;

			main::config($_config, 'forum', $this->db);

			a_notice('Данные успешно изменены!', a_url('forum/admin/config'));
		}

		if (!isset($_POST['submit']) || $error) {
			$this->tpl->assign(array(
				'_config' => $_config
			));

			$this->tpl->display('config');
		}
	}

	/**
	 * Управление разделами форума
	 */
	public function action_sections() {
		switch ($_GET['a']) {
			# Создание раздела
  			case 'create':
				main::is_demo();
				if (!empty($_POST['new_section'])) {
					$position = $this->db->get_one("SELECT MAX(position) FROM #__forum_sections") + 1;
					$this->db->query("INSERT INTO #__forum_sections SET
						name = '". a_safe($_POST['new_section'])."',
						position = '". $position."'
					");

					a_notice('Раздел успешно создан!', a_url('forum/admin'));
				} else {
					a_error('Укажите название раздела!');
				}
				break;

  			# Удаление раздела
  			case 'delete':
				main::is_demo();
				$section = $this->db->get_row("SELECT * FROM #__forum_sections WHERE section_id = ".intval($_GET['section_id']));
				$this->db->query("DELETE FROM #__forum_sections WHERE section_id = ".intval($_GET['section_id']));

				# Меняем позиции
				$this->db->query("UPDATE #__forum_sections SET position = position - 1 WHERE position > ".$section['position']);

				a_notice('Раздел успешно удален!', a_url('forum/admin'));
				break;

  			# Редактирование раздела
  			case 'edit':
				if (is_numeric($_GET['section_id'])) {
					if (!$section = $this->db->get_row("SELECT * FROM #__forum_sections WHERE section_id = '".intval($_GET['section_id'])."'"))
  						a_error('Раздел не найден!');
  					$action = 'edit';
  				}
  				else {
  					$section = array();
  					$action = 'add';
  				}

				if (isset($_POST['submit'])) {
					main::is_demo();
					if (empty($_POST['name'])) {
						$this->error .= 'Укажите название категории<br />';
					}

					if (!$this->error) {
						if ($action == 'add') {
							$position = $this->db->get_one("SELECT MAX(position) FROM #__forum_sections") + 1;
							$this->db->query("INSERT INTO #__forum_sections SET
								name = '". a_safe($_POST['name'])."',
								position = '". $position."'
							");
							$message = 'Раздел успешно создан!';
						}
						if ($action == 'edit') {
							$this->db->query("UPDATE #__forum_sections SET name = '".a_safe($_POST['name'])."' WHERE section_id='".intval($_GET['section_id'])."'");
							$message = 'Раздел успешно переименован!';
						}

						a_notice($message, a_url('forum/admin'));
					}
				}
				if (!isset($_POST['submit']) || $this->error) {
					$this->tpl->assign(array(
						'error' => $this->error,
						'section' => $section,
						'action' => $action
					));
					$this->tpl->display('sections_edit');
				}
				break;

  			# Увеличение позиции
			case 'up':
				if(!$section = $this->db->get_row("SELECT * FROM #__forum_sections WHERE section_id = ". intval($_GET['section_id']))) {
									a_error('Раздел не найден!');
				}
	
				# Меняем позиции
				$this->db->query("UPDATE #__forum_sections SET position = ". $section['position'] ." WHERE position = ". ($section['position'] - 1));
				$this->db->query("UPDATE #__forum_sections SET position = ". ($section['position'] - 1) ." WHERE section_id = ". intval($_GET['section_id']));
	
				header("Location: ". a_url('forum/admin'));
				exit;
			break;

			# Уменьшение позиции
			case 'down':
				if(!$section = $this->db->get_row("SELECT * FROM #__forum_sections WHERE section_id = ". intval($_GET['section_id'])))
					a_error('Раздел не найден!');
	
				# Меняем позиции
				$this->db->query("UPDATE #__forum_sections SET position = ". $section['position'] ." WHERE position = ". ($section['position'] + 1));
				$this->db->query("UPDATE #__forum_sections SET position = ". ($section['position'] + 1) ." WHERE section_id = ". intval($_GET['section_id']));
	
				header("Location: ". a_url('forum/admin'));
				exit;
			break;

  			# Список разделов
  			default:
				$sql = "SELECT SQL_CALC_FOUND_ROWS f_s.*
					FROM #__forum_sections AS f_s";

				$sql .= " ORDER BY f_s.position ASC";

				$result = $this->db->query($sql);

				$min_p = $this->db->get_one("SELECT MIN(position) FROM #__forum_sections");
 				$max_p = $this->db->get_one("SELECT MAX(position) FROM #__forum_sections");

				while ($section = $this->db->fetch_array($result)) {
					if ($section['position'] != $min_p) $section['up'] = '<a href="'.a_url('forum/admin/sections', 'a=up&amp;section_id='.$section['section_id']).'">up</a>';
					else $section['up'] = 'up';

					if ($section['position'] != $max_p) $section['down'] = '<a href="'.a_url('forum/admin/sections', 'a=down&amp;section_id='.$section['section_id']).'">down</a>';
					else $section['down'] = 'down';

					$sections[] = $section;
				}

				$this->tpl->assign(array(
					'sections' => $sections
				));

				$this->tpl->display('sections_list');
				break;
		}
	}

	/**
	 * Управление форумами
	 */
	public function action_forums() {
		switch ($_GET['a']) {
			# Редактирование форума
  			case 'edit':
				if (is_numeric($_GET['forum_id'])) {
  					if (!$forum = $this->db->get_row("SELECT * FROM #__forum_forums WHERE forum_id = '".intval($_GET['forum_id'])."'"))
  						a_error('Форум не найден!');
  					$action = 'edit';
  				}
  				else {
  					$forum = array();
  					$action = 'add';
  				}

				if (isset($_POST['submit'])) {
					main::is_demo();
					if (empty($_POST['name'])) {
						$this->error .= 'Укажите название Форума<br />';
					}
					if (!$this->db->get_one("SELECT section_id FROM #__forum_sections WHERE section_id = '".intval($_POST['section_id'])."'")) {
						$this->error .= 'Раздел не найден!<br />';
					}

					if (!$this->error) {
						if ($action == 'add') {
							$position = $this->db->get_one("SELECT MAX(position) FROM #__forum_forums WHERE section_id = '".intval($_POST['section_id'])."'") + 1;
							$this->db->query("INSERT INTO #__forum_forums SET
								section_id = '". intval($_POST['section_id'])."',
								name = '". a_safe($_POST['name'])."',
								position = '$position'
							");
							$message = 'Форум успешно создан!';
						}
						if ($action == 'edit') {
							$this->db->query("UPDATE #__forum_forums SET
								section_id = '". intval($_POST['section_id'])."',
								name = '". a_safe($_POST['name'])."'
								WHERE forum_id='". intval($_GET['forum_id'])."'
							");
							$message = 'Форум успешно изменён!';
						}
						a_notice($message, a_url('forum/admin/forums', 'a=list_forums&amp;section_id='.$_POST['section_id']));
					}
				}
				if (!isset($_POST['submit']) || $this->error) {
					$sections = $this->db->get_array("SELECT * FROM #__forum_sections ORDER BY position");
					$this->tpl->assign(array(
						'error' => $this->error,
						'sections' => $sections,
						'forum' => $forum,
						'action' => $action
					));
					$this->tpl->display('forums_edit');
				}
			break;

			# Удаление форума
  			case 'delete':
  				main::is_demo();
				if(!$forum = $this->db->get_row("SELECT * FROM #__forum_forums WHERE forum_id = '". intval($_GET['forum_id']) ."'")) {
				  					a_error('Форум не найден!');
				}

				$this->db->query("DELETE FROM #__forum_forums WHERE forum_id = ". intval($_GET['forum_id']));

				# Меняем позиции
				$this->db->query("UPDATE #__forum_forums SET position = position - 1 WHERE section_id = '". $forum['section_id'] ."' AND position > ". $forum['position']);

				a_notice('Форум успешно удален!', a_url('forum/admin/forums', 'a=list_forums&amp;section_id='. $forum['section_id']));
				break;

  			# Увеличение позиции
			case 'up':
				if(!$forum = $this->db->get_row("SELECT * FROM #__forum_forums WHERE forum_id = ". intval($_GET['forum_id'])))
					a_error('Форум не найден!');

				# Меняем позиции
				$this->db->query("UPDATE #__forum_forums SET position = ". $forum['position'] ." WHERE section_id = '". $forum['section_id'] ."' AND position = ". ($forum['position'] - 1));
				$this->db->query("UPDATE #__forum_forums SET position = ". ($forum['position'] - 1) ." WHERE section_id = '". $forum['section_id'] ."' AND forum_id = ". intval($_GET['forum_id']));
	
				header("Location: ". a_url('forum/admin/forums', 'section_id='. $forum['section_id'], TRUE));
				exit;
				break;

			# Уменьшение позиции
			case 'down':
				if(!$forum = $this->db->get_row("SELECT * FROM #__forum_forums WHERE forum_id = ". intval($_GET['forum_id'])))
					a_error('Форум не найден!');

				# Меняем позиции
				$this->db->query("UPDATE #__forum_forums SET position = ". $forum['position'] ." WHERE section_id = '". $forum['section_id'] ."' AND position = ". ($forum['position'] + 1));
				$this->db->query("UPDATE #__forum_forums SET position = ". ($forum['position'] + 1) ." WHERE section_id = '". $forum['section_id'] ."' AND forum_id = ". intval($_GET['forum_id']));
	
				header("Location: ". a_url('forum/admin/forums', 'section_id='. $forum['section_id'], TRUE));
				exit;
				break;

  			# Список форумов
  			case 'forums_list':
  			case 'list_forums':
  			default:
				if(!$section = $this->db->get_row("SELECT * FROM #__forum_sections WHERE section_id = ". intval($_GET['section_id'])))
					a_error('Раздел не найден!');

  				$sql = "SELECT SQL_CALC_FOUND_ROWS ff.*
					FROM #__forum_forums AS ff";
				$sql .= " WHERE ff.section_id = '". intval($_GET['section_id']) ."'";
				$sql .= " ORDER BY ff.position ASC LIMIT $this->start, $this->per_page";

				$result = $this->db->query($sql);

				$min_p = $this->db->get_one("SELECT MIN(position) FROM #__forum_forums WHERE section_id = '". intval($_GET['section_id']) ."'");
 				$max_p = $this->db->get_one("SELECT MAX(position) FROM #__forum_forums WHERE section_id = '". intval($_GET['section_id']) ."'");

				while($forum = $this->db->fetch_array($result)) {
					if($forum['position'] != $min_p) {
						$forum['up'] = '<a href="'. a_url('forum/admin/forums', 'a=up&amp;forum_id='. $forum['forum_id']) .'">up</a>';
					} else {
						$forum['up'] = 'up';
					}

					if($forum['position'] != $max_p) {
						$forum['down'] = '<a href="'. a_url('forum/admin/forums', 'a=down&amp;forum_id='. $forum['forum_id']) .'">down</a>';
					} else {
						$forum['down'] = 'down';
					}

					$forums[] = $forum;
				}

				$this->tpl->assign(array(
					'section' => $section,
					'forums' => $forums
				));

				$this->tpl->display('forums_list');
			break;
		}
	}
}
?>