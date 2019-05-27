<?php
  class Users {
    // DB stuff
    private $conn;
    private $table = 'mdl_user';
    private $table2 = 'mdl_user_enrolments';
    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }
    // Get Users
    public function read() {
      // Create query
      $query = 'SELECT r.enrolid,p.firstname , p.lastname , p.email, p.institution,  p.city , p.username FROM ' . $this->table . ' p  JOIN
       mdl_user_enrolments r WHERE r.userid=7';
      // Prepare statement
      $stmt = $this->conn->prepare($query);
      // Execute query
      $stmt->execute();
      return $stmt;
    }
  }
