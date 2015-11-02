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
* Простенький класс управления доступом
*/
class Access {
	/**
	 * Роли и права
	 */
	public $levels = array(
		'admin' => 10,
		'moder' => 8,
		'user' => 5,
		'banned' => 2,
		'guest' => 1,
	);

	/**
	 * Русские названия ролей
	 */
	public $ru_roles = array(
		'admin' => 'Администратор',
		'moder' => 'Модератор',
		'user' => 'Пользователь',
		'banned' => 'Забаненый',
		'guest' => 'Гость',
	);

	/**
	 * Назначение прав
	 */
	function set_levels($levels) {
		$this->levels = $levels;
	}

	/**
	 * Получить уровень по роли
	 */
	function get_level($status) {
		return $this->levels[$status];
	}

	/**
	 * Проверка доступа
	 */
	function check_access($level) {
		if ($level >= $this->get_level($GLOBALS['USER']['status'])) return TRUE;
		return FALSE;
	}

	/**
	 * Проверка уровня доступа и сообщение об ошибке, если доступ запрещен
	 */
	function check($level) {
			if ($level > ACCESS_LEVEL) a_error('Доступ запрещен!');
	}
}
?>