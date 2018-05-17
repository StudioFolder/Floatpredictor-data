<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$pagesize=30;
// include database and object files
include_once 'database.php';
include_once 'flight.php';

try{
    $database = new Database();
    $db = $database->getConnection();
    $start=0;
    $page=1;
    $total_distance = 0;
    $count = 0;
    $id = -1;
    if (isset($_GET['page'])) {
      //echo $_GET['link'];
      if(is_numeric($_GET['page']) &&$_GET['page']>0){
        $page=(int)$_GET['page'];
        $start=(($page-1)*$pagesize);
      }
    }
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
      $id=(int)$_GET['id'];
    }
    $query = "SELECT SUM(distance) as total_distance, COUNT(id) as count FROM EXPLORERTRAJECTORIES";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $num = $stmt->rowCount();
    if($num == 1){
      if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $total_distance = $row['total_distance'];
        $count = $row['count'];
      }
    }
    $products_arr=array();
    $query = "";
    if($id > 0){
      $query = "SELECT EXPLORERTRAJECTORIES.* FROM EXPLORERTRAJECTORIES WHERE id = ".$id;
    } else {
      $query = "SELECT EXPLORERTRAJECTORIES.* FROM EXPLORERTRAJECTORIES ORDER BY id DESC LIMIT ".$pagesize." OFFSET ".$start;
      $products_arr["total_distance"] = floatval($total_distance);
      $products_arr["count"] = intval($count);
      // $products_arr["page"] = intval($page);
    }
    $products_arr["flights"]=array();
    $stmt = $db->prepare($query);
    $stmt->execute();
    $num = $stmt->rowCount();

    if($num>0){
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $product_item=array(
          "id" => $id,
          "departure_date"  => $departure_date,
          "created"  => $created,
          "min_dist"  => floatval ($min_dist),
          "min_time"  => floatval ($min_time),
          "avg_speed"  => floatval ($speed),
          "altitude"  => floatval ($altitude),
          "distance"  => floatval ($distance),
          "path" => json_decode($path),
          "departure" => array(
            "city" => $departure_city,
            "country" => $departure_country,
            "coordinates" =>array(
              "latitude" => floatval ($departure_latitude),
              "longitude" => floatval ($departure_longitude)
            )
          ),
          // "email"  => $email,
          // "username"  => $username,

        );
        if($destination_city != NULL) {
        	$product_item["destination"] = array(
            "city" => $destination_city,
            "country" => $destination_country,
            "coordinates" =>array(
              "latitude" => floatval ($destination_latitude),
              "longitude" => floatval ($destination_longitude)
            )
          );
        }
        if($username != NULL) {
        	$product_item["username"] = $username;
        }
        array_push($products_arr["flights"], $product_item);
      }
    }
    echo json_encode($products_arr);

  }catch(Exception $exception){
    echo json_encode(array("Exception error: " =>  $exception->getMessage()));
  }

?>
