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

// Отключаем генерирование ошибок
error_reporting(7);
// Название системы
define('SYSTEM_NAME', 'MobileCMS');
// Адрес сайта
define('SITE_URL', 'http://'.$_SERVER['HTTP_HOST'].str_replace('install/index.php', '', $_SERVER['PHP_SELF']));

// Шаги установки
$steps = array(
	'greeting' => 'Приветствие',
	'license' => 'Лицензионное соглашение',
	'chmod' => 'Настройка прав доступа к файлам',
	'site_data' => 'Заполнение данных сайта',
	'end_of_install' => 'Завершение установки',
);

$step = !empty($_GET['step']) ? $_GET['step'] : 'greeting';

if ( ! array_key_exists($step, $steps))
		die('Шаг не определён!');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../views/admin/css/ui/jquery-ui.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="../views/admin/css/layout.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="../views/admin/css/ui.tabs.css" media="screen" />
    <title>Установка <?php echo SYSTEM_NAME ?></title>
</head>

<body>
    <div id="logoProgress"></div>
    
    <div id="header">
        Установка <?php echo SYSTEM_NAME ?>
    </div>
    
    <div id="main">
        <div id="title">
            <h2><?php echo $steps[$step] ?></h2>
        </div>
        
        <div id="left">
            <div id="sidebar">
                <h3>Шаг</h3>
                
                <p>
                    <ul style="padding: 0 0 0 5px">
                        <li><?php if ($step == 'greeting'): ?><b>1. Приветствие</b><?php else: ?>1. Приветствие<?php endif; ?></li>
                        <li><?php if ($step == 'license'): ?><b>2. Лицензионное соглашение</b><?php else: ?>2. Лицензионное соглашение<?php endif; ?></li>
                        <li><?php if ($step == 'chmod'): ?><b>3. Настройка прав доступа</b><?php else: ?>3. Настройка прав доступа<?php endif; ?></li>
                        <li><?php if ($step == 'site_data'): ?><b>4. Заполнение данных сайта</b><?php else: ?>4. Заполнение данных сайта<?php endif; ?></li>
                        <li><?php if ($step == 'end_of_install'): ?><b>5. Завершение установки</b><?php else: ?>5. Завершение установки<?php endif; ?></li>
                    </ul>
                </p>
            </div>
        </div>
        
        <div id="content">

<?php
// Если установка уже произведена, выводим сообщение об ошибке
if (file_exists('../data_files/config.php') && $step != 'end_of_install')
	exit('<div class="box"><p>Установка была произведена ранее. Рекомендуем удалить папку install.</p></div></div></div></body></html>');

switch ($step) {
	// Приветствие
	case 'greeting':
?>
	<div class="box">
            <p><?php echo file_get_contents('./greeting.txt') ?></p>
	</div>
            
            <p><button onclick="location.href='?step=license'">Далее</button></p>
<?php
	break;

	// Лицензионное соглашение
	case 'license':
?>
	<div class="box">
            <p><?php echo nl2br(file_get_contents('../LICENSE.txt')) ?></p>
	</div>
            
            <p><button onclick="location.href='?step=chmod'">Согласен</button>&nbsp;<button onclick="location.href='http://ant0ha.ru'">Не согласен</button></p>
<?php
	break;
    
	// Проверка прав доступа
	case 'chmod':
		$chmod_files = file('./chmod_files.txt');
		$flag = TRUE;
?>
	<table cellspacing="0" cellpadding="0" class="table">
            <thead align="left" valign="middle">
                <tr>
                    <td>Файл / Папка</td>
                    <td>Статус</td>
                </tr>
            </thead>
            
            <tbody align="left" valign="middle">
		<?php foreach ($chmod_files as $file): ?>
                <tr>
                    <td><b><?php echo str_replace('../', '', $file) ?></b></td>
                    <td>
                                                <?php
												if (is_writable(trim($file))) {
													echo '<span style="color: green">Записываемый</span>';
												}
												else {
													echo '<span style="color: red">Необходимо выставить chmod 777</span>';
													$flag = FALSE;
												}
												?>
                    </td>
                </tr>
		<?php endforeach; ?>
            </tbody>
        </table>
                <?php
		if ($flag == TRUE) {
					echo "<div class=\"box\"><p>Все необходимые файлы и папки доступны для записи, можно продолжать установку</p></div>";
					echo "<p><button onclick=\"location.href='?step=site_data'\">Далее</button></p>";
		}
		else {
			echo "<div class=\"box\"><p>Для продолжения установки необходимо чтобы все файлы и папки из списка выше были записываемыми</p></div>";
			echo "<p><button onclick=\"location.href='?step=chmod&amp;".rand(111, 999)."'\">Обновить</button></p>";
		}
	break;

	// Настройка базы данных и генерация файла конфигурации
	case 'site_data':
			if (isset($_POST['submit'])) {
				$error = FALSE;
                
			if (empty($_POST['db_server'])) $error .= 'Укажите MySQL сервер<br />';

			if (empty($_POST['db_user'])) $error .= 'Укажите имя пользователя базы MySQL<br />';

			if (empty($_POST['db_base'])) $error .= 'Укажите имя базы данных<br />';

			if (empty($_POST['site_url'])) $error .= 'Укажите адрес сайта<br />';

			if (empty($_POST['admin_login'])) $error .= 'Укажите логин администратора<br />';

			if (empty($_POST['admin_email'])) $error .= 'Укажите e-mail администратора<br />';

			if (empty($_POST['admin_password'])) $error .= 'Укажите пароль администратора<br />';

			if ($_POST['admin_password'] != $_POST['admin_password2']) $error .= 'Пароли не совпадают<br />';

			if(!$error) {
				# Коннектимся к базе данных
				@mysql_connect($_POST['db_server'], $_POST['db_user'], $_POST['db_password'])
			   		or die ("Не возможно подключиться к MySQL серверу, проверьте правильность введенных данных</div></div></body></html>");

			 	@mysql_select_db($_POST['db_base'])
			   		or die ("Не удалось выбрать базу данных ". $_POST['db_base']);

			   	mysql_query("SET NAMES utf8");

			   	# Заливаем дамп системы
			   	$dump = file_get_contents('./system_dump.sql');
			   	$dump = str_replace('{ADMIN_LOGIN}', $_POST['admin_login'], $dump);
				$dump = str_replace('{ADMIN_EMAIL}', $_POST['admin_email'], $dump);
				$dump = str_replace('{ADMIN_PASSWORD}', md5(md5($_POST['admin_password'])), $dump);

			   	$queryes = explode('//=====================================//', $dump);
			   	foreach($queryes as $query) {
					mysql_query(trim($query));
			   	}

			   	# Создаем файл конфигурации системы
				$config_data = file_get_contents('./config_data.txt');
				$config_data = str_replace('{DB_HOST}', $_POST['db_server'], $config_data);
				$config_data = str_replace('{DB_USER}', $_POST['db_user'], $config_data);
				$config_data = str_replace('{DB_PASS}', $_POST['db_password'], $config_data);
				$config_data = str_replace('{DB_BASE}', $_POST['db_base'], $config_data);
				$config_data = str_replace('{SITE_URL}', $_POST['site_url'], $config_data);

				file_put_contents('../data_files/config.php', $config_data);
				?>
                <div class="box">
                <p>
                Файл конфигурации системы успешно создан, дамп базы данных залит.
                </p>
                </div>
                <p><button onclick="location.href='?step=end_of_install'">Завершить установку</button>
                <?php
			}
		}
		if (!isset($_POST['submit']) OR $error) {
			if ($error) {
				echo "<div class=\"error\">";
				echo $error;
				echo "</div>";
			}
			?>
            <form action="?step=site_data" method="post">
            <div class="box">
            <p>
            	<label>Сервер MySQL</label>
            	<input name="db_server" type="text" value="localhost">
            </p>
            <p>
            	<label>Пользователь базы MySQL</label>
            	<input name="db_user" type="text" value="<?php echo @$_POST['db_user'] ?>">
            </p>
            <p>
            	<label>Пароль пользователя MySQL</label>
            	<input name="db_password" type="password" value="">
            </p>
            <p>
            	<label>Имя базы данных MySQL</label>
            	<input name="db_base" type="text" value="<?php echo @$_POST['db_base'] ?>">
            </p>
            <p>
            	<label>Адрес сайта</label>
            	<input name="site_url" type="text" value="<?php echo SITE_URL ?>">
            </p>
            <p>
            	<label>Логин администратора</label>
            	<input name="admin_login" type="text" value="<?php echo @$_POST['admin_login'] ?>">
            </p>
            <p>
            	<label>E-mail администратора</label>
            	<input name="admin_email" type="text" value="<?php echo @$_POST['admin_email'] ?>">
            </p>
            <p>
            	<label>Пароль администратора</label>
            	<input name="admin_password" type="password" value="<?php echo @$_POST['admin_password'] ?>">
            </p>
            <p>
            	<label>Повторите пароль администратора</label>
            	<input name="admin_password2" type="password" value="<?php echo @$_POST['admin_password2'] ?>">
            </p>
            </div>
            <p><input type="submit" name="submit" value="Далее"></p>
			</form>
        	<?php
		}
	break;

	# Завершение установки
	case 'end_of_install':
	?>
    <div class="box">
    <p>
    Установка <?php echo SYSTEM_NAME ?> успешно завершена! Благодарим за выбор нашей CMS. Не забудьте удалить папку install.
    </p>
    </div>
    <p><button onclick="location.href='<?php echo SITE_URL ?>'">Перейти на сайт</button></p>
	<?php
	break;
}
?>
		</div>
	</div>
</body>
</html>