<?php
  class Users {
    // DB stuff
    private $conn;
    private $table = 'mdl_user';
    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }
    // Get Users
    public function read() {
      // Create query
      $query = 'SELECT p.firstname , p.lastname , p.email, p.institution,  p.city FROM ' . $this->table . ' p ';
      // Prepare statement
      $stmt = $this->conn->prepare($query);
      // Execute query
      $stmt->execute();
      return $stmt;
    }
  }
