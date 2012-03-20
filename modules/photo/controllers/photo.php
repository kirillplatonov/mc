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

/**
 * Контроллер пользовательской части фотоальбомов
 */
class Photo_Controller extends Controller {
  /**
	* Construct
	*/
	public function __construct() {
    parent::__construct();
		
		# Получаем ID пользователя в зависимости от действия
		if (isset($_GET['action'])) {
		  $this->user_id = intval($_GET['user_id']);
    } else {
      if (is_numeric($_GET['user_id'])) $this->user_id = intval($_GET['user_id']);
      else $this->user_id = USER_ID;  
    }
    
    # Получаем анкету пользователя
    if (!$this->profile = $this->db->get_row("SELECT * FROM #__users_profiles JOIN #__users USING(user_id) WHERE user_id = '". $this->user_id ."' AND user_id != '-1'")) $this->user_id = FALSE;
	}

	/**
	* Метод по умолчанию
	*/
	public function action_index() {
    $this->action_list_all_albums(); 
	}

	/**
	 * Список фотоальбомов юзера
	 */
	public function action_list_albums() {
    if ($this->user_id == 'FALSE') header('Location: /main/page_not_found.php');
	
		# Получение данных
  	$result = $this->db->query("SELECT SQL_CALC_FOUND_ROWS #__photo_albums.*, #__users.status AS user_status
  	FROM #__photo_albums LEFT JOIN #__users USING(user_id)
  	WHERE user_id = '". $this->user_id ."'
  	ORDER BY album_id DESC LIMIT $this->start, $this->per_page");

  	$total = $this->db->get_one("SELECT FOUND_ROWS()");

    if (!class_exists('smiles')) a_import('modules/smiles/helpers/smiles');

  	while($album = $this->db->fetch_array($result)) {
  		$album['about'] = main::bbcode($album['about']);
  		$album['about'] = smiles::smiles_replace($album['about']);
  		$album['about'] = nl2br($album['about']);
  		$album['count'] = $this->db->get_one("SELECT COUNT(*) FROM #__photo WHERE album_id = '$album[album_id]'");
  		
			$albums[] = $album;
  	}

		# Пагинация
		$pg_conf['base_url'] = a_url('photo/list_albums', 'user_id='. $this->user_id .'&amp;start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'albums' => $albums,
			'total' => $total,
			'profile' => $this->profile,
			'user_id' => $this->user_id,
			'pagination' => $pg->create_links()
		));

		$this->tpl->display('list_albums');
	}
  
  /**
   * Фотографии альбома
   */
  public function action_list_photos() {
    if ($this->user_id == 'FALSE') header('Location: /main/page_not_found.php');
  
    $album_id = intval($_GET['album_id']);
        
    if (!$this->album = $this->db->get_row("SELECT * FROM #__photo_albums JOIN #__users USING(user_id) WHERE album_id = '". $album_id ."' AND user_id = '". $this->user_id ."'")) a_error('Выбранный альбом не существует');
  
		# Получение данных
  	$result = $this->db->query("SELECT SQL_CALC_FOUND_ROWS #__photo.*, #__users.status AS user_status
  	FROM #__photo LEFT JOIN #__users USING(user_id)
  	WHERE user_id = '". $this->user_id ."' AND album_id = '$album_id' 
  	ORDER BY photo_id DESC LIMIT $this->start, $this->per_page");

  	$total = $this->db->get_one("SELECT FOUND_ROWS()");

    if (!class_exists('smiles')) a_import('modules/smiles/helpers/smiles');

  	while($photo = $this->db->fetch_array($result)) {
  		$photo['about'] = main::bbcode($photo['about']);
  		$photo['about'] = smiles::smiles_replace($photo['about']);
  		$photo['about'] = nl2br($photo['about']);
  		list($width)= getimagesize(URL .'files/photo/'. $photo['photo_id'] .'.'. $photo['file_ext']);
  		$photo['image'] = '<a href="'. a_url('photo/view_photo', 'user_id='. $photo['user_id'] .'&amp;album_id='. $photo['album_id'] .'&amp;photo_id='. $photo['photo_id']) .'"><img src="'. URL .'files/photo/'. $photo['photo_id'] .'.'. $photo['file_ext'] .'" alt=""'. ($this->config['photo']['preview_widht'] != 0 && $width > $this->config['photo']['preview_widht']?' width="'. $this->config['photo']['preview_widht'] .'"':NULL) .' /></a><br />';
  		
			$photos[] = $photo;
  	}

		# Пагинация
		$pg_conf['base_url'] = a_url('photo/list_photos', 'album_id='. $album_id .'&amp;user_id='. $this->user_id .'&amp;start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);
		
		$_config = $this->config['photo'];

		$this->tpl->assign(array(
			'album' => $this->album,
			'total' => $total,
			'config' => $_config,
			'profile' => $this->profile,
			'photos' => $photos,
			'user_id' => $this->user_id,
			'pagination' => $pg->create_links()
		));

		$this->tpl->display('list_photos');
  }     
  
  /**
   * Список всех фотографий
   */
  public function action_list_all_photos() {
		# Получение данных
  	$result = $this->db->query("SELECT SQL_CALC_FOUND_ROWS #__photo.*, #__users.status AS user_status
  	FROM #__photo LEFT JOIN #__users USING(user_id)
  	ORDER BY album_id DESC LIMIT $this->start, $this->per_page");

  	$total = $this->db->get_one("SELECT FOUND_ROWS()");

    if (!class_exists('smiles')) a_import('modules/smiles/helpers/smiles');

  	while($photo = $this->db->fetch_array($result)) {
  		$photo['about'] = main::bbcode($photo['about']);
  		$photo['about'] = smiles::smiles_replace($photo['about']);
  		$photo['about'] = nl2br($photo['about']);
  		$photo['username'] = $this->db->get_one("SELECT username FROM #__users WHERE user_id = '$photo[user_id]'");
  		$photo['album_name'] = $this->db->get_one("SELECT name FROM #__photo_albums WHERE album_id = '$photo[album_id]'");
  		list($width)= getimagesize(URL .'files/photo/'. $photo['photo_id'] .'.'. $photo['file_ext']);
  		$photo['image'] = '<a href="'. a_url('photo/view_photo', 'user_id='. $photo['user_id'] .'&amp;album_id='. $photo['album_id'] .'&amp;photo_id='. $photo['photo_id']) .'"><img src="'. URL .'files/photo/'. $photo['photo_id'] .'.'. $photo['file_ext'] .'" alt=""'. ($this->config['photo']['preview_widht'] != 0 && $width > $this->config['photo']['preview_widht']?' width="'. $this->config['photo']['preview_widht'] .'"':NULL) .' /></a><br />';
  		
			$photos[] = $photo;
  	}

		# Пагинация
		$pg_conf['base_url'] = a_url('photo/list_all_photos', 'start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);
		
		$_config = $this->config['photo'];

		$this->tpl->assign(array(
			'photos' => $photos,
			'config' => $_config,
			'total' => $total,
			'pagination' => $pg->create_links()
		));

		$this->tpl->display('list_all_photos');  
  }
  
  /**
   * Список всех альбомов
   */
  public function action_list_all_albums() {
		# Получение данных
  	$result = $this->db->query("SELECT SQL_CALC_FOUND_ROWS #__photo_albums.*, #__users.status AS user_status
  	FROM #__photo_albums LEFT JOIN #__users USING(user_id)
  	ORDER BY album_id DESC LIMIT $this->start, $this->per_page");

  	$total = $this->db->get_one("SELECT FOUND_ROWS()");

    if (!class_exists('smiles')) a_import('modules/smiles/helpers/smiles');

  	while($album = $this->db->fetch_array($result)) {
  		$album['about'] = main::bbcode($album['about']);
  		$album['about'] = smiles::smiles_replace($album['about']);
  		$album['about'] = nl2br($album['about']);
  		$album['username'] = $this->db->get_one("SELECT username FROM #__users WHERE user_id = '$album[user_id]'");
  		$album['count'] = $this->db->get_one("SELECT COUNT(*) FROM #__photo WHERE album_id = '$album[album_id]'");
  		
			$albums[] = $album;
  	}

		# Пагинация
		$pg_conf['base_url'] = a_url('photo/list_all_albums', 'start=');
		$pg_conf['total_rows'] = $total;
		$pg_conf['per_page'] = $this->per_page;

		a_import('libraries/pagination');
		$pg = new CI_Pagination($pg_conf);

		$this->tpl->assign(array(
			'albums' => $albums,
			'total' => $total,
			'pagination' => $pg->create_links()
		));

		$this->tpl->display('list_all_albums');  
  }
  
  /**
   * Управление альбомами
   */
  public function action_edit_albums() {
    if ($this->user_id == 'FALSE') header('Location: /main/page_not_found.php');
  
    switch($_GET['action']) {
      case 'add':
        if ($this->user_id != USER_ID) header('Location: /main/page_not_found.php');
      
        if (isset($_POST['submit'])) {
          if (empty($_POST['name'])) $this->error .= 'Не введено название альбома<br />';
          
          if (!empty($_POST['name']) && mb_strlen($_POST['name'], 'UTF-8') > 30) $this->error .= 'Название альбома не должно быть длинее 30 символов<br />';
          
          if (!empty($_POST['name']) && $this->db->get_one("SELECT album_id FROM #__photo_albums WHERE name = '". a_safe($_POST['name']) ."' AND user_id = '". $this->user_id ."'")) $this->error .= 'Альбом с таким названием уже существует<br />';
          
          if (!empty($_POST['about']) && mb_strlen($_POST['about'], 'UTF-8') > 3000) $this->error .= 'Описание альбома не должно быть длинее 3000 символов<br />';
          
          if (!$this->error) {
            $this->db->query("INSERT INTO #__photo_albums SET
              user_id = '". $this->user_id ."',
              name = '". a_safe($_POST['name']) ."',
              about = '". a_safe($_POST['about']) ."'
            ");
            
            user::rating_update();
            
            a_notice('Альбом успешно добавлен', URL .'photo/list_albums/?user_id='. $this->user_id);
          } 
        }
        
        $action = 'add';
        $title = 'Добавление альбома';
          
        $this->tpl->assign(array(
          'error' => $this->error,
          'action' => $action,
          'user_id' => $this->user_id,
          'title' => $title,
        ));

		    $this->tpl->display('edit_albums');
      break;
      
      case 'edit':
        if (ACCESS_LEVEL >= 8 || $this->user_id == USER_ID) {
          $album_id = intval($_GET['album_id']);
        
          if (!$this->album = $this->db->get_row("SELECT * FROM #__photo_albums JOIN #__users USING(user_id) WHERE album_id = '". $album_id ."' AND user_id = '". $this->user_id ."'")) a_error('Выбранный альбом не существует');
        
          if (isset($_POST['submit'])) {
            if (empty($_POST['name'])) $this->error = 'Не введено название альбома<br />';
          
            if (!empty($_POST['name']) && mb_strlen($_POST['name'], 'UTF-8') > 30) $this->error = 'Название альбома не должно быть длинее 30 символов<br />';
          
            if (!empty($_POST['about']) && mb_strlen($_POST['about'], 'UTF-8') > 3000) $this->error = 'Описание альбома не должно быть длинее 3000 символов<br />';
          
            if (!$this->error) {
              $this->db->query("UPDATE #__photo_albums SET
                user_id = '". $this->user_id ."',
                name = '". a_safe($_POST['name']) ."',
                about = '". a_safe($_POST['about']) ."'
              WHERE album_id = '$album_id'
              ");
            
              a_notice('Альбом успешно изменен', URL .'photo/list_albums/?user_id='. $this->user_id);
            } 
          }
      
          $action = 'edit';
          $title = 'Изменение альбома';
          
          $album['username'] = $this->db->get_one("SELECT username FROM #__users WHERE user_id = '$album[user_id]'");
          
          $this->tpl->assign(array(
            'error' => $this->error,
            'album' => $this->album,
            'action' => $action,
            'title' => $title,
            'user_id' => $this->user_id
          ));

		      $this->tpl->display('edit_albums');
        } else {
          a_error('У вас нет прав на выполнение этой операции!');
        } 
      break;
      
      case 'del':        
        if (ACCESS_LEVEL >= 8 || $this->user_id == USER_ID) {
          $album_id = intval($_GET['album_id']);
        
          if (!$this->album = $this->db->get_row("SELECT * FROM #__photo_albums JOIN #__users USING(user_id) WHERE album_id = '". $album_id ."' AND user_id = '". $this->user_id ."'")) a_error("Выбран не существующий альбом!");
          
        	if(!empty($_GET['confirm'])) {
            $this->db->query("DELETE FROM #__photo_albums WHERE album_id = '$album_id'");
            
            user::rating_update(-1, $album['user_id']);
            
            $photo_count = $this->db->query("SELECT * FROM #__photo JOIN #__users USING(user_id) WHERE album_id = '". $album_id ."'");
            
            while ($photo = mysql_fetch_assoc($photo_count)) {
              $this->db->query("DELETE FROM #__photo WHERE photo_id = '$photo[photo_id]'");
              
              @unlink(ROOT . 'files/photo/'. $photo['photo_id'] .'.png');
              @unlink(ROOT . 'files/photo/'. $photo['photo_id'] .'.gif');
              @unlink(ROOT . 'files/photo/'. $photo['photo_id'] .'.jpg');
              
              user::rating_update(-1, $photo['user_id']);
            }
            
            a_notice('Альбом со всеми фотографиями удален', a_url('photo/list_albums', 'user_id='. $this->user_id));
          } else {
            a_confirm('Подтверждаете удаление альбома?', a_url('photo/edit_albums', 'user_id='. $this->user_id .'&amp;album_id='. $album_id .'&amp;action=del&amp;confirm=ok'), a_url('photo/list_albums', 'user_id='. $this->user_id));
          }
        } else {
          a_error('У вас нет прав для выполнения этой операции!');  
        }
      break;
      
      default:
        header('Location: /main/page_not_found.php');
      break;
    }
  }
  
  /**
   * Управление фотографиями
   */
  public function action_edit_photos() {
    if ($this->user_id == 'FALSE') header('Location: /main/page_not_found.php');
  
    switch($_GET['action']) {
      case 'add':
        if ($this->user_id != USER_ID) header('Location: /main/page_not_found.php');
        
        $album_id = intval($_GET['album_id']);
        
        if (!$this->album = $this->db->get_row("SELECT * FROM #__photo_albums JOIN #__users USING(user_id) WHERE album_id = '". $album_id ."' AND user_id = '". $this->user_id ."'")) a_error('Выбранный альбом не существует');
        
        if (isset($_POST['submit'])) {
          if (empty($_POST['name'])) $this->error .= 'Не введено название фотографии<br />';
          
          if (!empty($_POST['name']) && mb_strlen($_POST['name'], 'UTF-8') > 30) $this->error .= 'Название фотографии не должно быть длинее 30 символов<br />';
          
          if (!empty($_POST['name']) && $this->db->get_one("SELECT album_id FROM #__photo_albums WHERE name = '". a_safe($_POST['name']) ."' AND user_id = '". $this->user_id ."' AND album_id = '$album_id'")) $this->error .= 'Фотография с таким названием уже существует<br />';
          
          if (!empty($_POST['about']) && mb_strlen($_POST['about'], 'UTF-8') > 3000) $this->error .= 'Описание фотографии не должно быть длинее 3000 символов<br />';
          
          $file = array();

          if (!empty($_FILES['file_upload']['tmp_name'])) {
            # Транслит названий файла
            $_FILES['file_upload']['tmp_name'] = main::detranslite($_FILES['file_upload']['tmp_name']);
            $_FILES['file_upload']['name'] = main::detranslite($_FILES['file_upload']['name']);
			
            $type = 'upload';
            $file['real_name'] = $_FILES['file_upload']['name'];
            $file['file_ext'] = array_pop(explode('.', $file['real_name']));
            $file['filesize'] = filesize($_FILES['file_upload']['tmp_name']);
            
            if (!strstr($_FILES['file_upload']['type'], 'image/')) $this->error .= 'Неверный формат фотографии! Разрешены только gif, jpg и png<br />';
          } else if (!empty($_POST['file_import']) && $_POST['file_import'] != 'http://') {
            $type = 'import';
            $file['real_name'] = main::detranslite(basename($_POST['file_import']));
            $file['file_ext'] = array_pop(explode('.', $file['real_name']));
            $file['filesize'] = main::get_filesize($_POST['file_import']);
            
            if ($file['file_ext'] != 'jpg' && $file['file_ext'] != 'jpeg' && $file['file_ext'] != 'gif' && $file['file_ext'] != 'png') $this->error .= 'Неверный формат фотографии! Разрешены только gif, jpg и png<br />';
          } else $this->error .= 'Укажите загружаемый файл!<br />';
          
          if (isset($type) && ($file['filesize'] > $this->config['photo']['max_size'] * 1048576) || $file['filesize'] === false) $this->error .= 'Размер загружаемого файла превышает допустимый размер ('. $this->config['photo']['max_size'] .' Mb)<br />';
          
          if (!$this->error) {
            $this->db->query("INSERT INTO #__photo SET
              user_id = '". $this->user_id ."',
              album_id = '". $this->album['album_id'] ."',
              name = '". a_safe($_POST['name']) ."',
              about = '". a_safe($_POST['about']) ."',
              file_ext = '$file[file_ext]',
              time = UNIX_TIMESTAMP()
            ");
            
            $file_id = $this->db->insert_id();
            
            # Удаляем фото с тем же ID
            @unlink(ROOT . 'files/photo/'. $file_id .'.png');
            @unlink(ROOT . 'files/photo/'. $file_id .'.gif');
            @unlink(ROOT . 'files/photo/'. $file_id .'.jpg');
            
            if($type == 'upload') {
   					  $file_path = ROOT .'files/photo/'. $file_id .'.'. $file['file_ext'];
              copy($_FILES['file_upload']['tmp_name'], $file_path);
            } else {
              $file_path = ROOT .'files/photo/'. $file_id .'.'. $file['file_ext'];
              copy($_POST['file_import'], $file_path);
            }
  
            user::rating_update();
            
            a_notice('Фотография успешно добавлена', URL .'photo/list_photos/?user_id='. $this->user_id .'&amp;album_id='. $this->album['album_id']);
          } 
        }
        
        $action = 'add';
        $title = 'Добавление фотографии';
        $_config = $this->config['photo'];
          
        $this->tpl->assign(array(
          'error' => $this->error,
          'config' => $_config,
          'action' => $action,
          'album' => $this->album,
          'user_id' => $this->user_id,
          'title' => $title,
        ));

		    $this->tpl->display('edit_photos');
      break;
      
      case 'edit':
        if (ACCESS_LEVEL >= 8 || $this->user_id == USER_ID) {      
          $album_id = intval($_GET['album_id']);
        
          if (!$this->album = $this->db->get_row("SELECT * FROM #__photo_albums JOIN #__users USING(user_id) WHERE album_id = '". $album_id ."' AND user_id = '". $this->user_id ."'")) a_error('Выбранный альбом не существует');
          
          $photo_id = intval($_GET['photo_id']);
        
          if (!$this->photo = $this->db->get_row("SELECT * FROM #__photo JOIN #__users USING(user_id) WHERE photo_id = '". $photo_id ."' AND user_id = '". $this->user_id ."' AND album_id = '". $this->album['album_id'] ."'")) a_error('Выбранная фотография не существует');
        
          if (isset($_POST['submit'])) {
            if (empty($_POST['name'])) $this->error .= 'Не введено название фотографии<br />';
          
            if (!empty($_POST['name']) && mb_strlen($_POST['name'], 'UTF-8') > 30) $this->error .= 'Название фотографии не должно быть длинее 30 символов<br />';
          
            if (!empty($_POST['name']) && $this->db->get_one("SELECT album_id FROM #__photo_albums WHERE name = '". a_safe($_POST['name']) ."' AND user_id = '". $this->user_id ."' AND album_id = '$album_id'")) $this->error .= 'Фотография с таким названием уже существует<br />';
          
            if (!empty($_POST['about']) && mb_strlen($_POST['about'], 'UTF-8') > 3000) $this->error .= 'Описание фотографии не должно быть длинее 3000 символов<br />';
          
            $file = array();

            if (!empty($_FILES['file_upload']['tmp_name'])) {
              # Транслит названий файла
              $_FILES['file_upload']['tmp_name'] = main::detranslite($_FILES['file_upload']['tmp_name']);
              $_FILES['file_upload']['name'] = main::detranslite($_FILES['file_upload']['name']);
			
              $type = 'upload';
              $file['real_name'] = $_FILES['file_upload']['name'];
              $file['file_ext'] = array_pop(explode('.', $file['real_name']));
              $file['filesize'] = filesize($_FILES['file_upload']['tmp_name']);
              
              if (!strstr($_FILES['file_upload']['type'], 'image/')) $this->error .= 'Неверный формат фотографии! Разрешены только gif, jpg и png<br />';
            } else if (!empty($_POST['file_import']) && $_POST['file_import'] != 'http://') {
              $type = 'import';
              $file['real_name'] = main::detranslite(basename($_POST['file_import']));
              $file['file_ext'] = array_pop(explode('.', $file['real_name']));
              $file['filesize'] = main::get_filesize($_POST['file_import']);
              
              if ($file['file_ext'] != 'jpg' && $file['file_ext'] != 'jpeg' && $file['file_ext'] != 'gif' && $file['file_ext'] != 'png') $this->error .= 'Неверный формат фотографии! Разрешены только gif, jpg и png<br />';
            }
          
            if (isset($type) && ($file['filesize'] > $this->config['photo']['max_size'] * 1048576) || $file['filesize'] === false) $this->error .= 'Размер загружаемого файла превышает допустимый размер ('. $this->config['photo']['max_size'] .' Mb)<br />';
          
            if (!$this->error) {
              if (!isset($type)) $file = $this->photo;
            
              $this->db->query("UPDATE #__photo SET
                user_id = '". $this->user_id ."',
                album_id = '". $this->album['album_id'] ."',
                name = '". a_safe($_POST['name']) ."',
                about = '". a_safe($_POST['about']) ."',
                file_ext = '$file[file_ext]',
                time = UNIX_TIMESTAMP()
              WHERE photo_id = '". $this->photo['photo_id'] ."'
              ");
            
              $file_id = $this->db->insert_id();
            
              if (isset($type)) {
                # Удаляем фото с тем же ID
                @unlink(ROOT . 'files/photo/'. $file_id .'.png');
                @unlink(ROOT . 'files/photo/'. $file_id .'.gif');
                @unlink(ROOT . 'files/photo/'. $file_id .'.jpg');
              }
            
              if(isset($type) && $type == 'upload' ) {
   					    $file_path = ROOT .'files/photo/'. $file_id .'.'. $file['file_ext'];
                copy($_FILES['file_upload']['tmp_name'], $file_path);
              } else if (isset($type) && $type == 'import') {
                $file_path = ROOT .'files/photo/'. $file_id .'.'. $file['file_ext'];
                copy($_POST['file_import'], $file_path);
              }
  
              user::rating_update();
            
              a_notice('Фотография успешно изменена', URL .'photo/list_photos/?user_id='. $this->user_id .'&amp;album_id='. $this->album['album_id']);
            } 
          }
          
          $photo = $this->photo;
          
          list($width)= getimagesize(URL .'files/photo/'. $photo['photo_id'] .'.'. $photo['file_ext']);
  		    $photo['image'] = '<a href="'. a_url('photo/view_photo', 'user_id='. $photo['user_id'] .'&amp;album_id='. $photo['album_id'] .'&amp;photo_id='. $photo['photo_id']) .'"><img src="'. URL .'files/photo/'. $photo['photo_id'] .'.'. $photo['file_ext'] .'" alt=""'. ($this->config['photo']['max_widht'] != 0 && $width > $this->config['photo']['max_widht']?' width="'. $this->config['photo']['max_widht'] .'"':NULL) .' /></a><br />';
        
          $action = 'edit';
          $title = 'Изменение фотографии';
          $_config = $this->config['photo'];
          
          $this->tpl->assign(array(
            'error' => $this->error,
            'config' => $_config,
            'action' => $action,
            'photo' => $photo,
            'album' => $this->album,
            'user_id' => $this->user_id,
            'title' => $title,
          ));

		      $this->tpl->display('edit_photos');
        } else {
          a_error('У вас нет прав для выполнения этой операции!');
        }
      break;
      
      case 'del':
        if (ACCESS_LEVEL >= 8 || $this->user_id == USER_ID) {
          $album_id = intval($_GET['album_id']);
        
          if (!$this->album = $this->db->get_row("SELECT * FROM #__photo_albums JOIN #__users USING(user_id) WHERE album_id = '". $album_id ."' AND user_id = '". $this->user_id ."'")) a_error("Выбран не существующий альбом!");
          
          $photo_id = intval($_GET['photo_id']);
        
          if (!$this->photo = $this->db->get_row("SELECT * FROM #__photo JOIN #__users USING(user_id) WHERE photo_id = '". $photo_id ."' AND user_id = '". $this->user_id ."' AND album_id = '". $this->album['album_id'] ."'")) a_error('Выбранная фотография не существует');
          
        	if(!empty($_GET['confirm'])) {
            $this->db->query("DELETE FROM #__photo WHERE photo_id = '$photo_id'");
            
            @unlink(ROOT . 'files/photo/'. $photo_id .'.png');
            @unlink(ROOT . 'files/photo/'. $photo_id .'.gif');
            @unlink(ROOT . 'files/photo/'. $photo_id .'.jpg');
            
            user::rating_update(-1, $photo['user_id']);
            a_notice('Фотография удалена', a_url('photo/list_photos', 'user_id='. $this->user_id .'&amp;album_id='. $album_id));
          } else {
            a_confirm('Подтверждаете удаление фотографии?', a_url('photo/edit_photos', 'user_id='. $this->user_id .'&amp;album_id='. $album_id .'&amp;photo_id='. $photo_id .'&amp;action=del&amp;confirm=ok'), a_url('photo/list_photos', 'user_id='. $this->user_id .'&amp;album_id='. $album_id));
          }
        } else {
          a_error('У вас нет прав для выполнения этой операции!');  
        }      
      break;
      
      default:
        header('Location: /main/page_not_found.php');
      break; 
    }
  }
  
  /**
   * Просмотр деталей фотографии
   */
  public function action_view_photo() {
    $album_id = intval($_GET['album_id']);
        
    if (!$this->album = $this->db->get_row("SELECT * FROM #__photo_albums JOIN #__users USING(user_id) WHERE album_id = '". $album_id ."' AND user_id = '". $this->user_id ."'")) a_error('Выбранный альбом не существует');
          
    $photo_id = intval($_GET['photo_id']);
    
    if (!$this->photo = $this->db->get_row("SELECT *,
		 	(SELECT username FROM #__users AS u WHERE u.user_id = photo.user_id) AS username,
		 	(SELECT COUNT(*) FROM #__comments_posts WHERE module = 'photo' AND item_id = photo.photo_id) comments
		 	FROM #__photo AS photo WHERE photo.photo_id = '". $photo_id ."' AND photo.user_id = '". $this->user_id ."' AND photo.album_id = '". $album_id ."'")) a_error('Выбранная фотография не существует');
		 	
		$photo = $this->photo;
		
		list($width)= getimagesize(URL .'files/photo/'. $photo['photo_id'] .'.'. $photo['file_ext']);
  	$photo['image'] = '<a href="'. URL .'files/photo/'. $photo['photo_id'] .'.'. $photo['file_ext'] .'"><img src="'. URL .'files/photo/'. $photo['photo_id'] .'.'. $photo['file_ext'] .'" alt=""'. ($this->config['photo']['max_widht'] != 0 && $width > $this->config['photo']['max_widht']?' width="'. $this->config['photo']['max_widht'] .'"':NULL) .' /></a><br />';
		 	
		$_config = $this->config['photo'];

    $this->tpl->assign(array(
			'album' => $this->album,
			'photo' => $photo,
			'profile' => $this->profile,
			'config' => $_config
		));
	
		$this->tpl->display('view_photo');
  }       
}

?>
