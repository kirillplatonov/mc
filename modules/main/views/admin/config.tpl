<?php $this->display('header.tpl', array('title' => 'Конфигурация')) ?>

<form action="" method="post">
          <div class="box">
	<h3>Конфигурация системы</h3>
	<div class="inside">
	        <p>
		      <label>Название системы</label>
		      <input name="system_title" type="text" value="<?php echo $_config['system_title'] ?>">
	        </p>
	        <p>
		      <label>E-mail системы</label>
		      <input name="system_email" type="text" value="<?php echo $_config['system_email'] ?>">
	        </p>
	        <p>
		      <label>Тема сайта по умолчанию</label>
		      <select name="default_theme">
		      <?php foreach($default_themes as $theme): ?>
		        <option value="<?php echo $theme['name'] ?>"<?php if($_config['default_theme'] == $theme['name']): ?> selected='selected'<?php ENDIF ?>><?php echo $theme['title'] ?></option>
		      <?php endforeach; ?>
		      </select>
	        </p>
                <p>
		      <label>WEB тема сайта по умолчанию</label>
		      <select name="web_theme">
		      <?php foreach($default_themes as $theme): ?>
		        <option value="<?php echo $theme['name'] ?>"<?php if($_config['web_theme'] == $theme['name']): ?> selected='selected'<?php ENDIF ?>><?php echo $theme['title'] ?></option>
		      <?php endforeach; ?>
		      </select>
	        </p>
	        <p>
		      <label>Тема админки</label>
		      <select name="admin_theme">
		      <?php foreach($admin_themes as $theme): ?>
		        <option value="<?php echo $theme['name'] ?>"<?php if($_config['admin_theme'] == $theme['name']): ?> selected='selected'<?php ENDIF ?>><?php echo $theme['title'] ?></option>
		      <?php endforeach; ?>
		      </select>
	        </p>
	        <p>
		      <label>Модуль по умолчанию</label>
		      <select name="default_module">
		      <?php foreach($mainpage_modules as $module): ?>
		        <option value="<?php echo $module['name'] ?>"<?php if($_config['default_module'] == $module['name']): ?> selected='selected'<?php ENDIF ?>><?php echo $module['title'] ?></option>
		      <?php endforeach; ?>
		      </select>
	        </p>
	        <?php if (modules::is_active_module('guestbook')): ?>
	        <p>
		      <label>Написание сообщений в гостевой книге</label>
		      <select name="guestbook_posting">
		        <option value="all"<?php if($_config['guestbook_posting'] == 'all'): ?> selected='selected'<?php endif; ?>>Все</option>
		        <option value="users"<?php if($_config['guestbook_posting'] == 'users'): ?> selected='selected'<?php endif; ?>>Только пользователи</option>
		      </select>
	        </p>
	        <?php endif; ?>
	        <?php if (modules::is_active_module('comments')): ?>
	        <p>
		      <label>Написание комментариев</label>
		      <select name="comments_posting">
		        <option value="all"<?php if($_config['comments_posting'] == 'all'): ?> selected='selected'<?php endif; ?>>Все</option>
		        <option value="users"<?php if($_config['comments_posting'] == 'users'): ?> selected='selected'<?php endif; ?>>Только пользователи</option>
		      </select>
	        </p>
	        <?php endif; ?>
	        <p>
		      <label>Коды счетчиков и баннеров на главной</label>
		      <textarea name="footer_codes_index" style="height: 50px;"><?php echo $_config['footer_codes_index'] ?></textarea>
	        </p>
	        <p>
		      <label>Коды счетчиков и баннеров на остальных страницах</label>
		      <textarea name="footer_codes_other_pages" style="height: 50px;"><?php echo $_config['footer_codes_other_pages'] ?></textarea>
	        </p>
	        <p>
		      <label>Описание сайта для поисковых систем (description)</label>
		      <textarea name="description" style="height: 50px;"><?php echo $_config['description'] ?></textarea>
	        </p>	        
	        <p>
		      <label>Ключевые слова для поисковых систем (keywords)</label>
		      <textarea name="keywords" style="height: 50px;"><?php echo $_config['keywords'] ?></textarea>
	        </p>	        
	        <p>
		      <label>Расширение сраниц</label>
		      <input name="ext" type="text" value="<?php echo $_config['ext'] ?>">
	        </p>
	        <p>
		      <label>Главное меню пользователя</label>
		      <input name="main_menu" type="text" value="<?php echo $_config['main_menu'] ?>">
	        </p>
	        <p>
		      <label>Сколько часов действует pin-code (временный пароль)</label>
		      <input name="pin_code_time" type="text" value="<?php echo $_config['pin_code_time'] ?>">
	        </p>
	        <p>
		      <label>Антифлуд в секундах</label>
		      <input name="antiflud_time" type="text" value="<?php echo $_config['antiflud_time'] ?>">
	        </p>
	        <p>
		      <label>Профайлер системы</label>
		      <select name="profiler">
		        <option value="on">Включен</option>
		        <option value="off"<?php if($_config['profiler'] == 'off'): ?> selected='selected'<?php ENDIF ?>>Выключен</option>
		      </select>
	        </p>
	        <p>
		      <label>Показ ошибок php</label>
		      <select name="display_errors">
		        <option value="1">Включен</option>
		        <option value="0"<?php if($_config['display_errors'] == 0): ?> selected='selected'<?php ENDIF ?>>Выключен</option>
		      </select>
	        </p>
	</div>
</div>

<p><input type="submit" id="submit" name="submit" value="Сохранить"  /></p>

</form>

<?php $this->display('footer.tpl') ?>
