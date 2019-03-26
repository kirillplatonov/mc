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

ini_set('session.save_path', '../tmp/');
session_name('sid');
session_start();

$data = array(
	// Ширина изображения
	'width'             =>  '90',
	// Высота изображения
	'height'            =>  '40',
	// Размер шрифта
	'font_size'         =>  '10',
	// Кол-во символов для ввода
	'let_amount'        =>  '4',
	// Кол-во "помех" на фоне
	'fon_let_amount'    =>  '0',
	// Папка со шрифтами
	'path_fonts'        =>  '../utils/fonts/',
	// Символы на изображении
	'letters'           =>  array('0', '2', '3', '4', '5', '6', '7', '9'),
	// Используемые цвета
	'colors'            =>  array('10', '30', '50', '70', '90', '110', '130', '150', '170', '190', '210')
);

// Создание изображения
$src = imagecreatetruecolor($data['width'], $data['height']);
$fon = imagecolorallocate($src, 255, 255, 255);

imagefill($src, 0, 0, $fon);

// Выбор шрифта
$fonts = array();
$dir = opendir($data['path_fonts']);

while($fontName = readdir($dir)) {
	if ($fontName != '.' && $fontName != '..') {
		$fonts[] = $fontName;
	}
	}

closedir($dir);

// Нанесение "помех"
for($i=0; $i<$data['fon_let_amount']; $i++) {
	$color = imagecolorallocatealpha($src, rand(0, 255), rand(0, 255), rand(0, 255), 100);
	$font = $data['path_fonts'] . $fonts[rand(0, sizeof($fonts)-1)];
	$letter = $data['letters'][rand(0, sizeof($data['letters'])-1)];
	$size = rand($data['font_size']-2, $data['font_size']+2);
	imagettftext($src, $size, rand(0, 45), rand($data['width']*0.1, $data['width']-$data['width']*0.1), rand($data['height']*0.2, $data['height']), $color, $font, $letter);
}

// Нанесение символов
for($i=0; $i<$data['let_amount']; $i++) {
	$color = imagecolorallocatealpha($src, $data['colors'][rand(0, sizeof($data['colors'])-1)], $data['colors'][rand(0, sizeof($data['colors'])-1)], $data['colors'][rand(0, sizeof($data['colors'])-1)], rand(20,40));
	$font = $data['path_fonts'] . $fonts[rand(0, sizeof($fonts)-1)];
	$letter = $data['letters'][rand(0, sizeof($data['letters'])-1)];
	$size = rand($data['font_size']*2.1-2, $data['font_size']*2.1+2);
	$x = ($i+1)*$data['font_size'] + rand(4, 7);
	$y = (($data['height']*2)/3) + rand(0, 5);
	$cod[] = $letter;
	imagettftext($src, $size, rand(0, 15), $x, $y, $color, $font, $letter);
}

// Антикеширование
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Создание рисунка в зависимости от доступного формата
if (function_exists("imagepng")) {
	header("Content-type: image/png");
	imagepng($src);
} elseif (function_exists("imagegif")) {
	header("Content-type: image/gif");
	imagegif($src);
} elseif (function_exists("imagejpeg")) {
	header("Content-type: image/jpeg");
	imagejpeg($src);
} else {
	die("No image support in this PHP server!");
}

// Записываем код в сессию
$_SESSION['captcha_code'] = implode('', $cod);

// Удаляем изображение
imagedestroy($src);
		
?>