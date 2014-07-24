<?php

/**
 * Web version
 *
 * @package
 * @author Platonov Kirill <platonov-kd@ya.ru>
 * @link http://twitter.com/platonov_kd
 */

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

/**
 * Хелпер событий модуля событий
 */
class web_version_events {
	/**
	* Перед выполнением контроллера
	*/
	public static function pre_controller(&$db) {

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $type = 'wap';

    # Определение названия браузера
    if (preg_match('#^([a-z0-9\-\_ ]+)/([0-9]+\.[0-9]+)#i', $user_agent)) {
      $type='pda';
    }

    if (preg_match('#MSIE#ui', $user_agent)) {
      $type='web';
    }

    if (preg_match('#America Online Browser#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#(Avant|Advanced) Browser#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#Camino/([0-9]+\.[0-9]+)#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#ELinks#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#Epiphany#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#Flock#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#IceWeasel#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#IceCat#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#Microsoft Pocket Internet Explorer#i', $user_agent)) {
      $type='pda';
    }

    if (preg_match('#MSPIE#i', $user_agent)) {
      $type='pda';
    }

    if (preg_match('#Windows.+Smartphone#i', $user_agent)) {
      $type='pda';
    }

    if (preg_match('#Konqueror#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#Links#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#Lynx#i', $user_agent)) {
      $type='web';
    } 

    if (preg_match('#Minimo#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#(Firebird|Phoenix|Firefox)/([0-9]+\.[0-9]+)#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#NetPositive#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#Opera/([0-9]+\.[0-9]+)#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#Opera Mini/([0-9]+\.[0-9]+)#i', $user_agent)) {
      $type='wap';
    }

    if (preg_match('#Opera Mobi#i', $user_agent)) {
      $type='pda';
    }

    if (preg_match('#(SymbOS|Symbian).+Opera ([0-9]+\.[0-9]+)#i', $user_agent)) {
      $type='pda';
    }

    if (preg_match('#Windows CE.+Opera ([0-9]+\.[0-9]+)#i', $user_agent)) {
      $type='pda';
    }

    if (preg_match('#PlayStation Portable#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#Safari#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#SeaMonkey#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#Shiira#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#w3m#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#Chrome/([0-9]+\.[0-9]+)#i', $user_agent)) {
      $type='web';
    }

    if (preg_match('#SONY/COM#i', $user_agent)) {
      $type='pda';
    }

    if (preg_match('#Nitro#i', $user_agent)) {
      $type='pda';
    }

    if (preg_match('#^Openwave#i', $user_agent)) {
      $type='pda';
    }

    if (preg_match('#UCWEB#i', $user_agent)) {
      $type='wap';
    }

    if (preg_match('#BOLT/([0-9]+\.[0-9]+)#i', $user_agent)) {
      $type='wap';
    }

    if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) && preg_match('#Opera Mini/([0-9]+\.[0-9]+)#i',$user_agent)) {
      $type='wap';
    }
    
    # Обработчик версии
    if (isset($_GET['version']) || isset($_COOKIE['version'])) {
      # Выбор по GET запросу
      if (isset($_GET['version'])) {
        if ($_GET['version'] == 'web') {
          # Сохраняем выбранную версию
          setcookie('version', 'web', time() + 1000000, '/');
          define('WEB_VERSION', '1');        
        } else if ($_GET['version'] == 'wap') {
          # Сохраняем выбранную версию
          setcookie('version', 'wap', time() + 1000000, '/');
          define('WEB_VERSION', '0');          
        }
      }
      
      # Выбор по ранее сохраненому запросу
      if (isset($_COOKIE['version']) && !isset($_GET['version'])) {
        if ($_COOKIE['version'] == 'web') {
          define('WEB_VERSION', '1');
        } else if ($_COOKIE['version'] == 'wap') {
          define('WEB_VERSION', '0');
        }
      }
    } else {
      # Запускаем веб версию (для текущего браузера)
      if ($type == 'web') define('WEB_VERSION', '1');
    }  
	}
}
?>