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
      $query = "SELECT DISTINCT us.username, us.firstname, us.lastname, us.email, us.institution, us.city FROM mdl_role_assignments ass INNER JOIN mdl_context con on ass.contextid = con.id INNER JOIN mdl_course cou on con.instanceid = cou.id INNER JOIN mdl_user us on ass.userid = us.id INNER JOIN mdl_user_enrolments ue ON ue.userid = us.id INNER JOIN mdl_assign_grades ag ON ue.userid = ag.userid INNER JOIN mdl_role rl ON rl.id = ass.roleid WHERE rl.shortname='student' AND ag.grade=(select max(grade) from mdl_assign_grades as f where ue.userid = f.userid) ORDER BY us.lastname, us.firstname";
      // Prepare statement
      $stmt = $this->conn->prepare($query);
      // Execute query
      $stmt->execute();
      return $stmt;
    }
  }
