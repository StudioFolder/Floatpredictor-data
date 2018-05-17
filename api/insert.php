<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// get database connection
include_once 'database.php';

// instantiate product object
include_once 'flight.php';

$database = new Database();
$db = $database->getConnection();

$flight = new Flight($db);

// get posted data
$data = json_decode(file_get_contents("php://input"), true);

function toSVG($svg_data, $color, $id){
  $s ='<svg xmlns="http://www.w3.org/2000/svg" height="210" width="210">';
  $s .= '<polyline points="';
  $isFirst = true;
  $initX = '0';
  $initY = '0';
  foreach ($svg_data as $value) {
    $s .=strval($value[0]). ',' . strval($value[1]).' ';
    if ($isFirst) {
      $initX = floatval($value[0]);
      $initY = floatval($value[1]);
      $isFirst = false;
    }

  }
  $s .= '" style="fill:none;stroke:'.$color.';stroke-width:2" />';
  $s .= '<circle cx="'.strval($initX).'" cy="'.strval($initY).'" r="8" style="fill:#FFFFFF"></circle>';
  $triangleAx = $initX;
  $triangleAy = $initY - 1.5;
  $triangleBx = $initX - 2;
  $triangleBy = $initY + 2;
  $triangleCx = $initX + 2;
  $triangleCy = $initY + 2;
  $s .= '<polygon stroke="#1E1E1E" stroke-width="2" fill="#1E1E1E"
        points="'.strval($triangleAx).' '.strval($triangleAy).' '.strval($triangleBx).' '.strval($triangleBy).' '.strval($triangleCx).' '.strval($triangleCy).'"></polygon>';
  $s .= '</svg>';
  // echo("*********");
  // echo($id);
  $file ="svg/".strval($id).".svg";
  file_put_contents($file, $s);//
}





try{
  $result=$flight ->create($data);
  if($result){
    $webColors = ['#003769', '#2e6a9c', '#0095d7', '#587a98', '#7eafd4', '#b9e5fb', '#656868', '#ffffff'];
    toSVG($data['svg'],$webColors[intval($data['explorerIndex'])], $result);
    echo '{';
    echo '"response": "OK",';
    echo '"id" : ';
    echo strval($result);
    echo '}';
  }
  else{
    echo '{"message" : "Unable to create product."}';
  }
}
catch(PDOException $exception){
  echo '{';
  echo $exception->getMessage();
  echo '}';
}
?>
