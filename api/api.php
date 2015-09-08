<?php
require_once '../include/Config.php';
$method = $_SERVER ['REQUEST_METHOD'];
$url = $_SERVER ['REQUEST_URI'];
$paths = array_filter(explode("/", $url));

// var_dump($paths);
// echo array_shift($paths);
// echo $paths[2];
// echo DB_UNIX;

if ($paths [2] == 'events') {
    try {
        $dbh = new PDO (DB_UNIX, DB_USERNAME, DB_PASSWORD);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
    $sql = 'SELECT * FROM timeline';

    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $json = json_encode($result);

    echo "{\"timeline\":" . $json . "}";
} else {
    $dbh = new PDO (DB_UNIX, DB_USERNAME, DB_PASSWORD);

    $queryString = $_SERVER ['QUERY_STRING'];
    parse_str($queryString, $outputQuery);
    $party = "";
    $ID = -1;
    $party = $outputQuery ['party'];
    $ID = $outputQuery ['ID'];

    if (strlen($party) != 0) {
        if ($party == 'democratic') {
            $sql = 'SELECT * FROM candidates WHERE party="Democratic"';
        } else if ($party == 'republican') {
            $sql = 'SELECT * FROM candidates WHERE party="Republican"';
        } else if ($party == 'independent') {
            $sql = 'SELECT * FROM candidates WHERE party<>"Democratic" AND party<>"Republican"';
        }
        $stmt = $dbh->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $json = json_encode($result);

        echo "{\"candidates\":" . $json . "}";
    } else if (strlen($ID) != 0) {
        $sql = 'SELECT * FROM candidates WHERE ID=:ID';
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':ID', $ID);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $json = json_encode($result);

        echo "{\"candidates\":" . $json . "}";
    } else {

        $sql = 'SELECT * FROM candidates';
        $stmt = $dbh->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $json = json_encode($result);

        echo "{\"candidates\":" . $json . "}";
    }
}
?>