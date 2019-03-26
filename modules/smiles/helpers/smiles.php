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
 * Хелпер смайлов
 */
class smiles {
	/**
	 * Добавление смайлов в сообщение
	 */
	public static function smiles_replace($string) {
		$smiles_array = unserialize(file_get_contents(ROOT.'data_files/smiles.dat'));
 		$string = strtr($string, $smiles_array);
 		$string = str_replace('{%URL%}', URL, $string);
		return $string;
	}

	/**
	 * Обновление смайлов
	 */
	public static function smiles_update($db) {
		$result = $db->query("SELECT * FROM #__smiles WHERE status = 'enable'");
		while ($smile = $db->fetch_array($result)) {
			$smiles_array[$smile['code']] = '<img src="{%URL%}modules/smiles/smiles/'.$smile['image'].'" alt="'.$smile['code'].'" />';
		}

		$fp = fopen(ROOT.'data_files/smiles.dat', 'w+');
 		fwrite($fp, serialize($smiles_array));
 		fclose($fp);
	}
}
?>