<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once 'database.php';
include_once 'flight.php';

$database = new Database();
$db = $database->getConnection();
$database->createTable();

$q = $db->prepare("DESCRIBE EXPLORERTRAJECTORIES");
$q->execute();
// echo();
$table_fields = $q->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($table_fields);
//$table_fields = $q->fetchAll();
//print_r($table_fields);

?>
