<ul>
	<li><a href="<?php echo a_url('user/admin/config') ?>">Настройка модуля</a></li>
	<li><a href="<?php echo a_url('user/admin/list_users') ?>">Список пользователей</a></li>
	<li><a href="<?php echo a_url('user/admin/list_guests') ?>">Список гостей</a></li>
	<li><a href="<?php echo a_url('user/admin/ip_ban') ?>">Бан по IP адресам</a></li>
        <li><a href="<?php echo a_url('user/admin/moderate') ?>">Модерация пользователей</a></li>
	<!-- <li><a href="<?php echo a_url('user/admin/users_logs') ?>">Логи пользователей</a></li> -->
</ul>

<?php if ($action == 'list'): ?>
<ul>
    <form action="<?php echo a_url('user/admin/list_users') ?>" method="get">
        ID пользователя:<br />
		<input style="margin-left: 15px" size="14" type="text" name="user_id" value="<?php echo intval($_GET['user_id']) ?>" /><br />
		
		Логин:<br />
		<input style="margin-left: 15px" size="14" name="username" type="text" value="<?php echo str_safe($_GET['username']) ?>" /><br />

        Статус пользователей:<br />
		<select style="margin-left: 15px" name="status">
			<option value="">Все статусы</option>
			<option value="banned"<?php if (str_safe($_GET['status']) == 'banned') echo ' selected="selected"' ?>>Забаненые</option>
			<option value="user"<?php if (str_safe($_GET['status']) == 'user') echo ' selected="selected"' ?>>Пользователи</option>
			<option value="moder"<?php if (str_safe($_GET['status']) == 'moder') echo ' selected="selected"' ?>>Модераторы</option>
			<option value="admin"<?php if (str_safe($_GET['status']) == 'admin') echo ' selected="selected"' ?>>Администраторы</option>
		</select><br />
		
		Сортировать по:<br />
		<select style="margin-left: 15px" name="sort">
			<option value="asc"<?php if (str_safe($_GET['sort']) == 'asc') echo ' selected="selected"' ?>>Возрастанию</option>
			<option value="desc"<?php if (str_safe($_GET['sort']) == 'desc') echo ' selected="selected"' ?>>Убыванию</option>
		</select><br />
		
		<input type="checkbox" name="type" value="online"<?php if (str_safe($_GET['type'] == 'online')) echo ' checked' ?>> Только онлайн<br /><br />
		
		<input style="margin-left: 15px" type="submit" value="Поиск">
    </form>
</ul>
<?php endif ?>

<?php if ($action == 'list_ip'): ?>
<ul>
    <form action="<?php echo a_url('user/admin/ip_ban') ?>" method="get">
        Искать IP:<br />
		<input style="margin-left: 15px" size="14" type="text" name="guest_ip" value="<?php echo str_safe($_GET['guest_ip']) ?>" /><br />
		
		Сортировать по:<br />
		<select style="margin-left: 15px" name="sort">
			<option value="asc"<?php if (str_safe($_GET['sort']) == 'asc') echo ' selected="selected"' ?>>Возрастанию</option>
			<option value="desc"<?php if (str_safe($_GET['sort']) == 'desc') echo ' selected="selected"' ?>>Убыванию</option>
		</select><br /><br />

		<input style="margin-left: 15px" type="submit" value="Поиск">
    </form>
</ul>
<?php endif ?>