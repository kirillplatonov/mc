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
* Хелпер загрузок
*/
class downloads {
	/**
	 * @desc получение реального пути к папке
	 * @param $int
	 */
	public static function get_path($directory_id, &$db, $directory_path = array(), $i = 0) {
		$parent = $db->get_row("SELECT * FROM #__downloads_directories WHERE
	 		directory_id = (SELECT parent_id FROM #__downloads_directories WHERE directory_id = '". intval($directory_id)."')
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
	 * @desc получение полного пути к папке
	 * @param $array
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

			$segment = $admin ? 'downloads/admin' : 'downloads';

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
	* @desc скачка файла
	*/
	public static function force_download($filename = '', $data = '', $prefix = '', $attachment = TRUE)
	{
		if ($filename == '' OR $data == '')
		{
			return FALSE;
		}

		// Try to determine if the filename includes a file extension.
		// We need it in order to set the MIME type
		if (FALSE === strpos($filename, '.'))
		{
			return FALSE;
		}

		// Grab the file extension
		$x = explode('.', $filename);
		$extension = end($x);

		// Load the mime types
		@include(ROOT.'utils/mimes.php');

		// Set a default mime if we can't find it
		if (!isset($mimes[$extension]))
		{
			$mime = 'application/octet-stream';
		} else
		{
			$mime = (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
		}

		if (!$attachment) {
			//exit($mime);
			header('Content-Type: '.$mime);
			header("Content-Length: ".strlen($data));
		} else {
			// Generate the server headers
			if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
			{
				header('Content-Type: '.$mime);
				header('Content-Disposition: attachment; filename='.$prefix.$filename);
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header("Content-Transfer-Encoding: binary");
				header('Pragma: public');
				header("Content-Length: ".strlen($data));
			} else
			{
				header('Content-Type: '.$mime);
				header('Content-Disposition: attachment; filename='.$prefix.$filename);
				header("Content-Transfer-Encoding: binary");
				header('Expires: 0');
				header('Pragma: no-cache');
				header("Content-Length: ".strlen($data));
			}
		}

		exit($data);
	}

	/**
	 * @desc Рекурсивный подсчет файлов в папке
	 * @param $string
	 */
	public static function count_files($directory_id) {
		$db = Registry::get('db');
		
		$files = $db->get_one("SELECT COUNT(*) FROM #__downloads_files WHERE directory_id = '".intval($directory_id)."' AND real_name != ''");
		$result = $db->query("SELECT * FROM #__downloads_directories WHERE parent_id = '".intval($directory_id)."'");
		while ($cild_directory = $db->fetch_array($result)) {
			$files += self::count_files($cild_directory['directory_id']);
		}
		return $files;
	}

	/**
	 * @desc определение имени файла
	 * @param $string
	 */
	public static function get_filename($string) {
		$headers = get_headers($string);
    
		foreach ($headers AS $header) {
			if (stripos($header, 'Location:') === 0) {
				$url = trim(str_ireplace('Location: ', '', $header));
				return basename($url);
			}
		}
    
		return false;
	}

	/**
	 * Действия над файлом
	 */
	public static function filetype_actions($file) {
		$config = Registry::get('config');
		
		$file_path = ROOT.$file['path_to_file'].'/'.$file['real_name'];

		if (in_array('ffmpeg', get_loaded_extensions())) {
			# Работаем с видео файлами
			if ($file['file_ext'] == '3gp' || $file['file_ext'] == 'mp4' || $file['file_ext'] == 'avi') {
				$ff = new ffmpeg_movie($file_path);

				# Продолжительность
				$duration = $ff->getDuration();
				$min = floor($duration / 60);
				$sec = floor($duration % 60);
				$file['file_info'] = 'Продолжительность: '.($min > 0 ? $min.' мин. ' : '').$sec.' сек.'.PHP_EOL;
	    
				# Разрешение экрана
				$w = $ff->getFrameWidth();
				$h = $ff->getFrameHeight();
				$file['file_info'] .= 'Разрешение: '.$w.'x'.$h.PHP_EOL;

				# Создаем скрины для видео
				if ($config['downloads']['make_screens_from_video']) {
					$frame_count = $ff->getFrameCount();

					$frame_1 = floor($frame_count / 3);
					$frame_2 = $frame_1 * 2;
					$frame_3 = $frame_1 * 3;
	
					$frames_arr = array(
						1 => $frame_1,
						2 => $frame_2,
						3 => $frame_3
					);

					foreach ($frames_arr AS $key => $frame_num) {
						if ($frame_num < 1) continue;
						$screen_name = ROOT.$file['path_to_file'].'/screen'.$key.'.jpg';
						if (!$frame = $ff->getFrame($frame_num)) continue;
						$im = $frame->toGDimage();
						$im1 = imagecreatetruecolor(120, 100);
						imagecopyresampled($im1, $im, 0, 0, 0, 0, 120, 100, imagesx($im), imagesy($im));
						imagejpeg($im1, $screen_name);
						imagedestroy($im1);
						imagedestroy($im);

						$file['screen'.$key] = 'screen'.$key.'.jpg';
					}
				}
			}

			# Работаем с мп3
			if ($file['file_ext'] == 'mp3') {
				$ff = new ffmpeg_movie($file_path);

				# Продолжительность
				$duration = $ff->getDuration();
				$min = floor($duration / 60);
				$sec = floor($duration % 60);
				$file['file_info'] = 'Продолжительность: '.($min > 0 ? $min.' мин. ' : '').$sec.' сек.'.PHP_EOL;
	    
				# ID3 информация
				$artist = $ff->getArtist();
				$genre = $ff->getGenre();
				if (!empty($artist)) $file['file_info'] .= 'Исполнитель: '.$artist.PHP_EOL;
				if (!empty($genre)) $file['file_info'] .= 'Жанр: '.$genre.PHP_EOL;
	    
				# Битрейт и тп
				$file['file_info'] .= 'Аудио: '.round($ff->getBitRate() / 1000).' Kbps, '.($ff->getAudioChannels() == 1 ? 'Mono' : 'Stereo').PHP_EOL;
			}
		}

		# Создаем превьюшки для картинок, анимаций и файлов со скринами
		if ($file['screen1'] != '' || $file['file_ext'] == 'png' || $file['file_ext'] == 'gif' || $file['file_ext'] == 'jpg' || $file['file_ext'] == 'jpeg') {
			if ($file['screen1'] != '') $src_file = ROOT.$file['path_to_file'].'/'.$file['screen1'];
			else $src_file = ROOT.$file['path_to_file'].'/'.$file['real_name'];

			# 20x20
			main::image_resize($src_file, ROOT.$file['path_to_file'].'/preview_20.jpg', 20, 20);
			# 60x60
			main::image_resize($src_file, ROOT.$file['path_to_file'].'/preview_60.jpg', 60, 60);
			# 100x100
			main::image_resize($src_file, ROOT.$file['path_to_file'].'/preview_100.jpg', 100, 100);

			$file['previews'] = 'yes';
		}
		return $file;
	}

	/**
	 * Изменение данных файла в базе
	 */
	public static function update_file(&$db, $file_id, $file, $new_file = true) {
		$db->query("UPDATE #__downloads_files SET
			directory_id = '". intval($file['directory_id'])."',
			user_id = '". (!empty($file['user_id']) ? $file['user_id'] : USER_ID)."',
			name = '". ($file['name'] ? a_safe($file['name']) : a_safe(str_replace('.'.$file['file_ext'], '', $file['real_name'])))."',
			". ($new_file ? "time = UNIX_TIMESTAMP()," : "")."
			real_name = '". a_safe($file['real_name'])."',
			path_to_file = '". $file['path_to_file']."',
			about = '". a_safe($file['about'])."',
			filesize = '". $file['filesize']."',
			file_ext = '". strtolower($file['file_ext'])."',
			screen1 = '". a_safe($file['screen1'])."',
			screen2 = '". a_safe($file['screen2'])."',
			screen3 = '". a_safe($file['screen3'])."',
			add_file_real_name_1 = '". (!empty($file['add_file_real_name_1']) ? a_safe($file['add_file_real_name_1']) : '')."',
			add_file_real_name_2 = '". (!empty($file['add_file_real_name_2']) ? a_safe($file['add_file_real_name_2']) : '')."',
			add_file_real_name_3 = '". (!empty($file['add_file_real_name_3']) ? a_safe($file['add_file_real_name_3']) : '')."',
			add_file_real_name_4 = '". (!empty($file['add_file_real_name_4']) ? a_safe($file['add_file_real_name_4']) : '')."',
			add_file_real_name_5 = '". (!empty($file['add_file_real_name_5']) ? a_safe($file['add_file_real_name_5']) : '')."',
			previews = '". $file['previews']."',
			status = '". a_safe($file['status'])."',
			file_info = '". a_safe($file['file_info'])."'
			WHERE
			file_id = '". intval($file_id)."'
   		");
	}

	/**
	 * Получение размера удаленного файла
	 */
	public static function get_filesize($file_path) {
		$headers = get_headers($file_path, 1);
		if ((!array_key_exists('Content-Length', $headers))) {
			return false;
		}
		return $headers['Content-Length'];
	}

	/**
	* Получение настоящего адреса файла
	* @return string
	*/
	public static function get_real_file_path($file_path) {
		$headers = get_headers($file_path, 1);
		if (!array_key_exists('Location', $headers)) {
			return $file_path;
		}
		if (!strstr($headers['Location'], 'http://')) {
			$url_data = parse_url($file_path);
			$location = 'http://'.$url_data['host'].'/'.$headers['Location'];
		}
		else $location = $headers['Location'];
		return self::get_real_file_path($location);
	}

	/**
	* Получение настоящего адреса файла
	* @param integer $directory_id
	*/
	public static function delete_directories($directory_id) {
		$db = Registry::get('db');
		
		$result = $db->query("SELECT * FROM #__downloads_directories WHERE parent_id = $directory_id");
		while ($dir = $db->fetch_array($result)) {
			$db->query("DELETE FROM #__downloads_directories WHERE directory_id = ".$dir['directory_id']);
			self::delete_directories($dir['directory_id']);
		}
	}
}

?>