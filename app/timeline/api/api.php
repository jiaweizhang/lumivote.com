<?php
require_once '../../../include/Config.php';
$method = $_SERVER['REQUEST_METHOD'];
$url = $_SERVER['REQUEST_URI'];
$paths = array_filter( explode("/", $url));

//var_dump($paths);
//echo "<br/>";
array_shift($paths);



// set up the connection variables
$db_name  = '';
$hostname = '';
$username = '';
$password = '';

// connect to the database
$dbh = new PDO(DB_UNIX, DB_USERNAME, DB_PASSWORD);
if ($method == 'GET') {  
  $sql = 'SELECT * FROM timeline';

  $stmt = $dbh->prepare($sql);
  $stmt->execute();


  $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

  $json = json_encode( $result );

  echo "{\"timeline\":" . $json . "}";
}
else if ($method == 'POST') {
  //var_dump($params);
  $params = json_decode(file_get_contents('php://input'),true);
  $name = $params['name'];
  $date = $params['date'];
  $time = $params['time'];
  $party = $params['party'];
  $city = $params['city'];
  $state = $params['state'];
  $type = $params['type'];
  $description = $params['description'];
  $sql = 'INSERT INTO timeline (name, date, time, party, city, state, type, description) VALUES (:name, :date, :time, :party, :city, :state, :type, :description)';

  $stmt = $dbh->prepare($sql);

  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':date', $date);
  $stmt->bindParam(':time', $time);
  $stmt->bindParam(':party', $party);
  $stmt->bindParam(':city', $city);
  $stmt->bindParam(':state', $state);
  $stmt->bindParam(':type', $type);
  $stmt->bindParam(':description', $description);

  $stmt->execute();
  echo "POST executed.";


}
else if ($method == 'DELETE') {
  $queryString = $_SERVER['QUERY_STRING'];
  parse_str($queryString, $outputQuery);
  var_dump ($outputQuery);
  $eventID = $outputQuery['eventID'];
  $sql = 'DELETE FROM timeline WHERE eventID=:eventID';
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':eventID', $eventID);
  $stmt->execute();
  echo "DELETE executed";
}
else if ($method == 'PUT') {
  $queryString = $_SERVER['QUERY_STRING'];
  parse_str($queryString, $outputQuery);

  
    //echo "put event";
  $eventID = $outputQuery['eventID'];

  $params = json_decode(file_get_contents('php://input'),true);
  $name = $params['name'];
  $date = $params['date'];
  $time = $params['time'];
  $party = $params['party'];
  $city = $params['city'];
  $state = $params['state'];
  $type = $params['type'];
  $description = $params['description'];

  $sql = 'UPDATE timeline SET name=:name, date=:date, time=:time, party=:party, city=:city, state=:state, type=:type, description=:description WHERE eventID=:eventID';
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(":eventID", $eventID);
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':date', $date);
  $stmt->bindParam(':time', $time);
  $stmt->bindParam(':party', $party);
  $stmt->bindParam(':city', $city);
  $stmt->bindParam(':state', $state);
  $stmt->bindParam(':type', $type);
  $stmt->bindParam(':description', $description);

  $stmt->execute();

  echo "PUT executed";
}



function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
  return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
?>