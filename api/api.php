<?php
$method = $_SERVER['REQUEST_METHOD'];
$url = $_SERVER['REQUEST_URI'];
$paths = array_filter( explode("/", $url));

//var_dump($paths);
//echo array_shift($paths);
//echo $paths[2];

if ($paths[2] == 'events') {
	$db_name  = '';
	$hostname = '';
	$username = '';
	$password = '';

	try {
	$dbh = new PDO("mysql:host=$hostname;dbname=$db_name", $username, $password);
	}
	catch (PDOException $e) {
    		echo 'Connection failed: ' . $e->getMessage();
	}
	$sql = 'SELECT * FROM timeline';

	$stmt = $dbh->prepare($sql);
	$stmt->execute();


	$result = $stmt->fetchAll( PDO::FETCH_ASSOC );

	$json = json_encode( $result );

	echo "{\"timeline\":" . $json . "}";
}
else {
	$db_name  = 'lumivote_candidates';
	$hostname = 'localhost';
	$username = 'lumivote_dev';
	$password = 'votenow91';

	$dbh = new PDO("mysql:host=$hostname;dbname=$db_name", $username, $password);

	$queryString = $_SERVER['QUERY_STRING'];
	parse_str($queryString, $outputQuery);
	$party = "";
	$ID = -1;
	$party = $outputQuery['party'];
	$ID = $outputQuery['ID'];

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
?>