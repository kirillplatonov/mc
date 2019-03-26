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

//---------------------------------------------

/**
 * Хелпер библиотеки
 */
class lib {
	/**
	 * Получение реального пути к папке
	 */
	public static function get_path($directory_id, $db, $directory_path = array(), $i = 0) {
		$parent = $db->get_row("SELECT * FROM #__lib_directories WHERE
	 		directory_id = (SELECT parent_id FROM #__lib_directories WHERE directory_id = '". intval($directory_id)."')
	 	");
	 	$i++;
	 	if ($parent['directory_id'] != 0) {
	 		$directory_path[$i]['directory_id'] = $parent['directory_id'];
	  		$directory_path[$i]['name'] = $parent['name'];
	  		$directory_path = self::get_path($parent['directory_id'], $db, $directory_path, $i);
	  	}
	  	return $directory_path;
	}

	/**
	 * Получение полного пути к папке
	 */
	public static function get_realpath($directories_array) {
		if (empty($directories_array)) return;

		foreach ($directories_array AS $directory) {
			$directory_path[] = $directory['directory_id'];
		}
		if (count($directory_path) > 1) {
	   		$realpath = array_reverse($directory_path);
	   		$realpath = implode('/', $realpath);
	  	} else {
	   		$realpath = $directory_path[0];
	  	}

	  	return $realpath;
	}

	/**
	 * @desc получение полного пути к папке
	 * @param $array
	 */
	public static function get_namepath($directories_array, $delim = '/', $admin = FALSE) {
		if(empty($directories_array)) {
			return;
		}

			$segment = $admin ? 'lib/admin' : 'lib';

		foreach($directories_array AS $directory) {
			$directory_path[] = '<a href="'. a_url($segment, 'directory_id='. $directory['directory_id']) .'">'. $directory['name'] .'</a>';
		}
		if(count($directory_path) > 1) {
	   		$realpath = array_reverse($directory_path);
	   		$realpath = implode($delim, $realpath);
	  	} else {
	   		$realpath = $directory_path[0];
	  	}

	  	return $realpath;
	}

	/**
	* Функция рекурсивного копирования
	* @param string $source
	* @param string $dest
	*/
	public static function r_copy($source, $dest) {
		# Simple copy for a file
		if (is_file($source)) {
			return copy($source, $dest);
		}

		# Make destination directory
		if (!is_dir($dest)) {
			mkdir($dest);
		}

		# If the source is a symlink
		if (is_link($source)) {
			$link_dest = readlink($source);
			return symlink($link_dest, $dest);
		}

		# Loop through the folder
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			// Deep copy directories
			if ($dest !== "$source/$entry") {
				self::r_copy("$source/$entry", "$dest/$entry");
			}
		}

		// Clean up
		$dir->close();
		return true;
	}
}
?>