<?php

// $db = new Database(array(host, user, password));
class Database extends Error {

	private $db;
	private $connection;
	private	$query;

	public function Database($args = array()) {
		$this->db['host']	= $args['host'];
		$this->db['user']	= $args['user'];
		$this->db['pass']	= $args['pass'];
	}

	public function connect() {
		$this->connection = mysqli_connect($this->db['host'], $this->db['user'], $this->db['pass']);
		if (!$this->connection) {
			$this->_add("Connection Error: " . mysqli_connect_error(), 2);
			return false;
		};
	return true; }

	public function disconnect() {
		if ($this->connection) {
			mysqli_close($this->connection);
			$this->connection = null;
			return true;
		} else {
			$this->_add("Connection Error: Unable to disconnect, not connected to '" . $this->db['host'] ."'", 2);
		};
	return false; }

	public function select_db($e) {
		if (mysqli_select_db($this->connection, $e)) {
			return true;
		} else {
			$this->_add("Connection Error: Unable to select the database", 2);
		};
	return false; }

	public function query($sql) {
		$this->query = $sql;
		if ($result = mysqli_query($this->connection, $sql)) {
			return $result;
		} else {
			$this->_add("SQL Error: The following query was not successful '" . $this->query . "'", 2);
		};
	return false; }

	public function clean($dirty) {
		if (!is_array($dirty)) {
			$dirty = mysqli_real_escape_string($this->connection, trim($dirty));
			$clean = stripslashes($dirty);
	return $clean; };
		$clean = array();
		foreach ($dirty as $p => $data) {
			$data = mysqli_real_escape_string($this->connection, trim($data));
			$data = stripslashes($data);
			$clean[$p] = $data; };
	return $clean; }

}

class Error {

	private $error;
	private $name;

	public function Error($name = null) {
		if (!empty($name)) {
			$this->name = $name;
		} else {
			$this->name = "log";
		};
	}

	public function _add($error, $id = null) {
		$time = "[" . date("Y-m-d H:i:s") . "]";
		$this->error = $time . " (" . $id . ") " . $error;
		$this->_store();
	}

	private function _store() {
			$file = fopen("log.txt", "a+");
			fwrite($file, $this->error."\n");
			fclose($file);
			$this->error = null;
	}

}

?>