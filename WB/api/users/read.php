<?php
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header("Content-type: application/json; charset=utf-8");
  include_once '../../config/Database.php';
  include_once '../../models/Users.php';
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();
  // Instantiate blog user object
  $post = new Users($db);
  // Blog post query
  $result = $post->read();
  // Get row count
  $num = $result->rowCount();
  // Check if any users
  if($num > 0) {
    // User array
    $posts_arr = array();
    // $posts_arr['data'] = array();
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      extract($row);
      $post_item = array(
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'institution' => $institution,
        'city' => $city
      );
      // Push to "data"
      array_push($posts_arr, $post_item);
    }
    // Convert to Json with UTF-8
    echo json_encode($posts_arr,JSON_UNESCAPED_UNICODE);
  } else {
    // No Users
    echo json_encode(
      array('message' => 'No Users Found')
    );
  }
