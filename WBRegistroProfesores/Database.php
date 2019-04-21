<?php
class Database{
//Parametros Base de Datos
private $host='localhost';
private $db_name='moodle35';
private $username='root';
private $password='';
private $conn;

//Conexión a la base de Datos
public function connect(){
  $this->conn=null;
  try {
    $this->conn= new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name,
    $this->username, $this->password);
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Succesful";

  } catch (PDOException $e) {
    echo "Connection erro" . $e->getMessage();
  }
  return $this->conn;
  }
}
