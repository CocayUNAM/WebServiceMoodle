<?php
  class Users {
    // DB stuff
    private $conn;
    private $table = 'mdl_user';
    // Post Properties
    public $id;
    public $category_id;
    public $category_name;
    public $title;
    public $body;
    public $author;
    public $created_at;
    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }
    // Get Posts
    public function read() {
      // Create query

      $query = 'SELECT p.firstname , p.lastname FROM ' . $this->table . ' p ';

      // Prepare statement
      $stmt = $this->conn->prepare($query);
      // Execute query
      $stmt->execute();
      return $stmt;
    }
  }
