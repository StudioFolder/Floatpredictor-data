<?php
class Database{

    private $host = "hostname";
    private $db_name = "dbname";
    private $username = "mydbusername";
    private $password = "mydbpassword";

    public $table_name = "tablename";

    public $conn;
    public function getConnection(){
      $this->conn = null;
      try{
        $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        $this->conn->exec("set names utf8");
        //echo ("Connected");
      }catch(PDOException $exception){
        echo '{ "Connection error" : "'. $exception->getMessage() .'" }';
      }
      return $this->conn;
    }

    public function listTables(){
      $sql = 'SHOW TABLES';
      if($this->getConnection())
      {
        $query = $this->conn->query($sql);
        return $query->fetchAll(PDO::FETCH_COLUMN);
      }
      return FALSE;
    }
    public function dropTable(){
      $db = $this->getConnection();
      $ret=$db->exec("DROP TABLE EXPLORERTRAJECTORIES");
    }
    public function createTable(){
      try{
        $db = $this->getConnection();
        $sql =
<<<EOF
        CREATE TABLE IF NOT EXISTS EXPLORERTRAJECTORIES(
        id                    INT(11)       AUTO_INCREMENT NOT NULL,
        min_dist              REAL          NOT NULL,
        min_time              REAL          NOT NULL,
        speed                 REAL          NOT NULL,
        distance              REAL          NOT NULL,
        altitude              REAL          NOT NULL,
        departure_date        DATETIME      NOT NULL,
        path                  TEXT          NOT NULL,
        departure_city        VARCHAR(50)   NOT NULL,
        departure_country     VARCHAR(50)   NOT NULL,
        departure_longitude   REAL          NOT NULL,
        departure_latitude    REAL          NOT NULL,
        destination_city      VARCHAR(50),
        destination_country   VARCHAR(50),
        destination_longitude REAL,
        destination_latitude  REAL,
        created               TIMESTAMP,
        email                 VARCHAR(50),
        username              VARCHAR(50),
        PRIMARY KEY (id))
        ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
EOF;

        $ret=$db->exec($sql);
        /*if(!$ret) {
          echo ("error");
        } else {
          echo "Table created successfully\n";
        }*/

    }

    catch(PDOException $e){
      echo $e->getMessage();
    }
  }
}

?>
