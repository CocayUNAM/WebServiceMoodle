<?php
  // Headers
  
  header('Content-Type: application/json');
  header("Content-type: application/json; charset=utf-8");

  include_once '../../config/Database.php';
  include_once '../../models/Grades.php';


 $clave = $_GET['clave'];
  $array_ini = parse_ini_file("pass.ini");
  if($clave != $array_ini['clave']){
    echo json_encode(array("mensaje" => "Error se necesita identificacion"));
    return;
  }
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
    $posts_arr_final = array();
    // $posts_arr['data'] = array();
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      extract($row);
      $post_group=array('shortname'=>$shortname,'idnumber'=>$idnumber,);
      $post_item = array('grade'=>$grade,'username'=>$username,);
      $posts_arr_final = array_unique($post_group);
      // Push to "data"
      array_push($posts_arr, $post_item);
      //array_push($posts_arr, $post_group);
     
    }
     array_push($posts_arr,$posts_arr_final);
     $reversed = array_reverse($posts_arr);
    // Convert to Json with UTF-8
   // echo json_encode($posts_arr,JSON_UNESCAPED_UNICODE);
    echo json_encode($reversed,JSON_UNESCAPED_UNICODE);
  } else {
    // No Users
    echo json_encode(
      array('message' => 'No Grades Found')
    );
  }



  function unique_multidim_array($array, $key) {
    $temp_array = array();
    $i = 0;
    $key_array = array();
   
    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}
