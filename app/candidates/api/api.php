<?php
require_once '../../../include/Config.php';
$method = $_SERVER['REQUEST_METHOD'];
$url = $_SERVER['REQUEST_URI'];
$paths = array_filter( explode("/", $url));


array_shift($paths);



// set up the connection variables
// connect to the database
$dbh = new PDO(DB_UNIX, DB_USERNAME, DB_PASSWORD);
if ($method == 'GET') {  
 $queryString = $_SERVER['QUERY_STRING'];
 parse_str($queryString, $outputQuery);
 $party = "";
 $ID = -1;
 $party = $outputQuery['party'];
 $ID = $outputQuery['ID'];
 //echo $party;
 //echo $ID;

 if (strlen($party)!=0 ) {
   if ($party == 'democratic') {
    $sql = 'SELECT * FROM candidates WHERE party="Democratic"';
  }
  else if ($party == 'republican') {
    $sql = 'SELECT * FROM candidates WHERE party="Republican"';
  }
  else if ($party == 'independent') {
    $sql = 'SELECT * FROM candidates WHERE party<>"Democratic" AND party<>"Republican"';
  }
  $stmt = $dbh->prepare($sql);
  $stmt->execute();


  $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

  $json = json_encode( $result );

  echo "{\"candidates\":" . $json . "}";
  }
  else if (strlen($ID)!=0) {
  $sql = 'SELECT * FROM candidates WHERE ID=:ID';
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':ID', $ID);
  $stmt->execute();
  $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

  $json = json_encode( $result );

  echo "{\"candidates\":" . $json . "}";
}  
 
  else {

  $sql = 'SELECT * FROM candidates';
  $stmt = $dbh->prepare($sql);
  $stmt->execute();


  $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

  $json = json_encode( $result );

  echo "{\"candidates\":" . $json . "}";
}


}
else if ($method == 'POST') {
  //var_dump($params);
  $params = json_decode(file_get_contents('php://input'),true);
  $fName = $params['fName'];
  $nickName = $params['nickName'];
  $mName = $params['mName'];
  $lName = $params['lName'];
  $party = $params['party'];
  $occupation = $params['occupation'];
  $birthdate = $params['birthdate'];
  $spouseFName = $params['spouseFName'];
  $spouseMName = $params['spouseMName'];
  $spouseLName = $params['spouseLName'];
  $bio = $params['bio'];
  $twitter = $params['twitter'];
  $url = $params['url'];
  $facebook = $params['facebook'];
  $bioGuide = $params['bioGuide'];
  $image = $params['image'];
  $sql = 'INSERT INTO candidates (fName, nickName, mName, lName, party, occupation, birthdate, spouseFName, spouseMName, spouseLName, bio, twitter, url, facebook, bioGuide, image) VALUES (:fName, :nickName, :mName, :lName, :party, :occupation, :birthdate, :spouseFName, :spouseMName, :spouseLName, :bio, :twitter, :url,:facebook,:bioGuide, :image)';

  $stmt = $dbh->prepare($sql);

  $stmt->bindParam(':fName', $fName);
  $stmt->bindParam(':nickName', $nickName);
  $stmt->bindParam(':mName', $mName);
  $stmt->bindParam(':lName', $lName);
  $stmt->bindParam(':party', $party);
  $stmt->bindParam(':occupation', $occupation);
  $stmt->bindParam(':birthdate', $birthdate);
  $stmt->bindParam(':spouseFName', $spouseFName);
  $stmt->bindParam(':spouseMName', $spouseMName);
  $stmt->bindParam(':spouseLName', $spouseLName);
  $stmt->bindParam(':bio', $bio);
  $stmt->bindParam(':twitter', $twitter);
  $stmt->bindParam(':url', $url);
  $stmt->bindParam(':facebook', $facebook);
  $stmt->bindParam(':bioGuide', $bioGuide);
  $stmt->bindParam(':image', $image);

  $stmt->execute();
  echo "POST executed.";


}
else if ($method == 'DELETE') {
  $queryString = $_SERVER['QUERY_STRING'];
  parse_str($queryString, $outputQuery);
  var_dump ($outputQuery);
  $ID = $outputQuery['ID'];
  $sql = 'DELETE FROM candidates WHERE ID=:ID';
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':ID', $ID);
  $stmt->execute();
  echo "DELETE executed";
}
else if ($method == 'PUT') {
  $queryString = $_SERVER['QUERY_STRING'];
  parse_str($queryString, $outputQuery);

  
    //echo "put event";
  $ID = $outputQuery['ID'];

  $params = json_decode(file_get_contents('php://input'),true);
  $fName = $params['fName'];
  $nickName = $params['nickName'];
  $mName = $params['mName'];
  $lName = $params['lName'];
  $party = $params['party'];
  $occupation = $params['occupation'];
  $birthdate = $params['birthdate'];
  $spouseFName = $params['spouseFName'];
  $spouseMName = $params['spouseMName'];
  $spouseLName = $params['spouseLName'];
  $bio = $params['bio'];
  $twitter = $params['twitter'];
  $url = $params['url'];
  $facebook = $params['facebook'];
  $bioGuide = $params['bioGuide'];
  $image = $params['image'];

  $sql = 'UPDATE candidates SET fName=:fName, nickName=:nickName, mName=:mName, lName=:lName, party=:party, occupation=:occupation, birthdate=:birthdate, spouseFName=:spouseFName, spouseMName=:spouseMName, spouseLName=:spouseLName, bio=:bio, twitter=:twitter, url=:url,facebook=:facebook,bioGuide=:bioGuide,image=:image WHERE ID=:ID';
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(":ID", $ID);
  $stmt->bindParam(':fName', $fName);
  $stmt->bindParam(':nickName', $nickName);
  $stmt->bindParam(':mName', $mName);
  $stmt->bindParam(':lName', $lName);
  $stmt->bindParam(':party', $party);
  $stmt->bindParam(':occupation', $occupation);
  $stmt->bindParam(':birthdate', $birthdate);
  $stmt->bindParam(':spouseFName', $spouseFName);
  $stmt->bindParam(':spouseMName', $spouseMName);
  $stmt->bindParam(':spouseLName', $spouseLName);
  $stmt->bindParam(':bio', $bio);
  $stmt->bindParam(':twitter', $twitter);
  $stmt->bindParam(':url', $url);
  $stmt->bindParam(':facebook', $facebook);
  $stmt->bindParam(':bioGuide', $bioGuide);
  $stmt->bindParam(':image', $image);

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