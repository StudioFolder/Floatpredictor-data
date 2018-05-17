<?php
include_once 'database.php';
include_once 'flight.php';
$username = $_GET['username'];
$trajectoryId = $_GET['id'];
try{
    $database = new Database();
    $db = $database->getConnection();
    $query = "UPDATE EXPLORERTRAJECTORIES SET username='".$username."' WHERE created > TIMESTAMP(DATE_SUB(NOW(),INTERVAL 15 MINUTE)) AND username IS NULL AND id = ".$trajectoryId; //"SELECT
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = array();
    $result["result"] = "OK";
    echo json_encode($result);
  }catch(Exception $exception){
    echo json_encode(array("Exception error: " => $exception->getMessage()));
  }
?>
