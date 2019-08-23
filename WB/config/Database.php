<?php
  class Database {
    
    // DB Params
    private $port='8889';
    private $conn;

    // DB Connect
    public function connect() {
      require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
      $this->conn = null;
      try {
        $this->conn = new PDO('mysql:host=' . $CFG->dbhost . ';dbname=' . $CFG->dbname . ';charset=utf8', $CFG->dbuser, $CFG->dbpass);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
        try {
			$this->conn = new PDO('mysql:host=' . $CFG->dbhost . ';dbname=' . $CFG->dbname . ';port='. $this->port . ';charset=utf8', $CFG->dbuser, $CFG->dbpass);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			echo 'Connection Error: ' . $e->getMessage();
		}
      }
      return $this->conn;
    }
  }
