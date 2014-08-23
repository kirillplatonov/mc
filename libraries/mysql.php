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
* Класс для работы с MySQL
*/
class MySQL {
	/**
	* Ссылка соединения
	*/
	public $db_link;

	/**
	* Режим отладки
	*/
	public $debugging = true;

	/**
	* Запросы
	*/
	public $list_queries = array();

	/**
	* Соединение с базой
	*/
	public function connect(){
            $this->db_link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_BASE, 3306) or 
                    $this->error("Не возможно подключиться к MySQL серверу");
	}

	/**
	* Выполнение запроса к базе
 	*/
	public function query($query){
		$query = $this->replace($query);

		# Засекаем время выполнения запроса
		$start_time = microtime(true);

		# Выполняем запрос
		$result = mysqli_query($this->db_link, $query) or 
                        $this->error($query . PHP_EOL . mysqli_error($this->db_link));

		# Получаем время по окончанию запроса
		$end_time = microtime(true);

		# Высчитываем время на запрос
		$query_time = $end_time - $start_time;

		$this->list_queries[] = array(
			'query' => $query,
			'time' => $query_time
		);

		return $result;
	}

	/**
	* Получение одной ячейки
	*/
	public function get_one($query){
		$result = $this->query($query);
		if($row = mysqli_fetch_row($result)) return stripslashes($row[0]);
		return FALSE;
	}

	/**
	* Получение строки
	*/
	public function get_row($query, $restype = MYSQLI_ASSOC) {
		$result = $this->query($query);
		if($row = mysqli_fetch_array($result, $restype)) return array_map('stripslashes', $row);
		return FALSE;
	}

	/**
	* Получение нескольких строк
	*/
	public function get_array($query){
		$data = array();
		$result = $this->query($query);
		while($row = $this->fetch_array($result)) $data[] = array_map('stripslashes', $row);
		return $data;
	}

	/**
	* Закрытие соединения
	*/
	public function close(){
		if($this->db_link) mysqli_close($this->db_link);
		$this->db_link = NULL;
	}

	/**
	* Кодировка БД
	*/
	public function charset($charset){
            mysqli_set_charset($this->db_link, $charset);
	}

	/**
	* Последний вставленный id
	*/
	public function insert_id() {
		return mysqli_insert_id($this->db_link);
	}

	/**
	* Аналог mysql_fetch_array()
	*/
	public function fetch_array($result){
		return mysqli_fetch_array($result);
	}

	/**
	* Аналог mysql_num_rows()
	*/
	public function num_rows($result) {
		return mysqli_num_rows($result);
	}

	/**
	* Замена префикса
	*/
	protected function replace($query){
		return str_replace('#__', DB_PREFIX, $query);
	}

	/**
	* Вывод ошибки и завершение работы
	*/
	protected function error($error){
		if($this->debugging) print "<pre>". $error ."</pre>";
		exit;
	}
}
