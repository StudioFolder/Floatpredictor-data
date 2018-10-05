<?php
class Flight{

  // database connection and table name
  private $conn;
  private $table_name = "EXPLORERTRAJECTORIES";



  public function create($data, $ip){
    try{
      // print_r($data);
      date_default_timezone_set('UTC');
      $departure_city=htmlspecialchars(strip_tags($data['departure']['city']));
      $departure_country=htmlspecialchars(strip_tags($data['departure']['country']));
      $departure_latitude=htmlspecialchars(strip_tags($data['departure']['coordinates']['latitude']));
      $departure_longitude=htmlspecialchars(strip_tags($data['departure']['coordinates']['longitude']));
      $min_dist=htmlspecialchars(strip_tags($data['min_dist']));
      $min_time=htmlspecialchars(strip_tags($data['min_time']));
      $speed=htmlspecialchars(strip_tags($data['speed']));
      $altitude=htmlspecialchars(strip_tags($data['altitude']));
      $distance=htmlspecialchars(strip_tags($data['distance']));
      $departure_date=htmlspecialchars(strip_tags($data['departure_date']));
      $path=htmlspecialchars(strip_tags(json_encode($data['path'])));
      $email='';
      $created = date('c');

      /*CHECK*********************/
      $valid = is_string($departure_city) && strlen($departure_city)>1 &&
                is_string($departure_country) && strlen($departure_country)>1 &&
                is_numeric($departure_latitude) && floatval($departure_latitude)>= -180 && floatval($departure_latitude)<= 180 &&
                is_numeric($departure_longitude) && floatval($departure_longitude)>= -360 && floatval($departure_longitude)<= 360 &&
                is_numeric($min_dist) && floatval($min_dist) >= 0 &&
                is_numeric($min_time) && floatval($min_time) >= 0 && floatval($min_time) <= 16 &&
                is_numeric($speed) && floatval($speed) >= 0 &&
                is_numeric($altitude) && floatval($altitude) >= 0 &&
                is_numeric($distance) && floatval($distance) >= 0 &&
                ((bool)strtotime($created)) && ((bool)strtotime($departure_date)) &&
                is_array($data['path']) && count($data['path'])==256 &&
                is_array($data['svg']) && count($data['svg'])==384;
      if(!$valid) return false;

      /***************************/
      if($data['destination']){
        $query = "INSERT INTO EXPLORERTRAJECTORIES (departure_city, departure_country, departure_latitude, departure_longitude, destination_city, destination_country, destination_latitude, destination_longitude, min_dist, min_time, speed, altitude, distance, departure_date, path, ip, created)
        VALUES (:departure_city,:departure_country,:departure_latitude,:departure_longitude,:destination_city,:destination_country,:destination_latitude,:destination_longitude,:min_dist,:min_time,:speed,:altitude,:distance,:departure_date,:path,:ip,:created)";
      	$stmt = $this->conn->prepare($query);
      	// bind values
        $destination_city=htmlspecialchars(strip_tags($data['destination']['city']));
        $destination_country=htmlspecialchars(strip_tags($data['destination']['country']));
        $destination_latitude=htmlspecialchars(strip_tags($data['destination']['coordinates']['latitude']));
        $destination_longitude=htmlspecialchars(strip_tags($data['destination']['coordinates']['longitude']));
      	$stmt->bindParam(":departure_city", $departure_city);
      	$stmt->bindParam(":departure_country", $departure_country);
      	$stmt->bindParam(":departure_latitude", $departure_latitude);
      	$stmt->bindParam(":departure_longitude", $departure_longitude);
      	$stmt->bindParam(":destination_city", $destination_city);
      	$stmt->bindParam(":destination_country", $destination_country);
      	$stmt->bindParam(":destination_latitude", $destination_latitude);
      	$stmt->bindParam(":destination_longitude", $destination_longitude);
      	$stmt->bindParam(":min_dist", $min_dist);
      	$stmt->bindParam(":min_time", $min_time);
      	$stmt->bindParam(":speed", $speed);
      	$stmt->bindParam(":altitude", $altitude);
      	$stmt->bindParam(":distance", $distance);
      	$stmt->bindParam(":departure_date", $departure_date);
      	$stmt->bindParam(":path", $path);

        $stmt->bindParam(":ip", $ip);
      	$stmt->bindParam(":created", $created);
        // echo("----");
        // echo($query);
      } else {
      	$query = "INSERT INTO EXPLORERTRAJECTORIES (departure_city, departure_country, departure_latitude, departure_longitude, min_dist, min_time, speed, altitude, distance, departure_date, path, ip, created)
        VALUES (:departure_city,:departure_country,:departure_latitude,:departure_longitude,:min_dist,:min_time,:speed,:altitude,:distance,:departure_date,:path,:ip,:created)";
      	$stmt = $this->conn->prepare($query);
      	// bind values
      	$stmt->bindParam(":departure_city", $departure_city);
      	$stmt->bindParam(":departure_country", $departure_country);
      	$stmt->bindParam(":departure_latitude", $departure_latitude);
      	$stmt->bindParam(":departure_longitude", $departure_longitude);
      	$stmt->bindParam(":min_dist", $min_dist);
      	$stmt->bindParam(":min_time", $min_time);
      	$stmt->bindParam(":speed", $speed);
      	$stmt->bindParam(":altitude", $altitude);
      	$stmt->bindParam(":distance", $distance);
      	$stmt->bindParam(":departure_date", $departure_date);
      	$stmt->bindParam(":path", $path);
        $stmt->bindParam(":ip", $ip);
      	$stmt->bindParam(":created", $created);
        // echo("----");
        // echo($query);
      }
	  if($stmt->execute()){
        return $this->conn->lastInsertId();
      }else{
        print_r($stmt->errorInfo  ());
        return 0;
      }
    }
    catch(Exception $exception){
      echo($exception->getMessage());
      echo '{"Connection error": "' . $exception->getMessage() . '"}';
      return false;
    }
  }

  // constructor with $db as database connection
  public function __construct($db){
    $this->conn = $db;
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  }
}
?>
