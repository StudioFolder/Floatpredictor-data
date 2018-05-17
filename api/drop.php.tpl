<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once 'database.php';
include_once 'flight.php';
try{
  $database = new Database();
  $database->dropTable();
  echo '{"result":"ok"}';
}catch(PDOException $exception){
    echo "Connection error: " . $exception->getMessage();
}
?>
