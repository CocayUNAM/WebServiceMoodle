<?php
  class Database {
    // DB Params
    private $conn;
    // DB Connect
    public function connect() {
      $this->conn = null;
      try {
        $this->conn = new PDO('mysql:host=127.0.0.1;dbname=moodle35;port=8889','root','root');
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
        echo 'Connection Error: ' . $e->getMessage();
      }
      return $this->conn;
    }
  }
