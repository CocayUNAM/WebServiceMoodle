<?php
  class Users {
    // DB stuff
    private $conn;
    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }
    // Get Users
    public function read() {
      // Create query
      $query = "SELECT cou.shortname, cou.idnumber FROM mdl_course cou WHERE cou.idnumber LIKE '%\_%'";
      // Prepare statement
      $stmt = $this->conn->prepare($query);
      // Execute query
      $stmt->execute();
      return $stmt;
    }
  }
