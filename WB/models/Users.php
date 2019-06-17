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
      $query = "SELECT DISTINCT u.username, u.firstname, u.lastname, u.email, u.institution, u.city, rl.shortname, ag.grade
      FROM mdl_user u INNER JOIN mdl_user_enrolments ue ON ue.userid = u.id
      INNER JOIN mdl_assign_grades ag ON ue.userid = ag.userid
      INNER JOIN mdl_role_assignments ass ON u.id = ass.userid
      INNER JOIN mdl_role rl ON rl.id = ass.roleid
      WHERE rl.shortname='student' AND
      ag.grade=(select max(grade) from mdl_assign_grades as f where ue.userid = f.userid) ORDER BY u.lastname, u.firstname";
      // Prepare statement
      $stmt = $this->conn->prepare($query);
      // Execute query
      $stmt->execute();
      return $stmt;
    }
  }
