<?php
require_once '../include/Config.php';
$method = $_SERVER['REQUEST_METHOD'];
$url = $_SERVER['REQUEST_URI'];
$paths = array_filter( explode("/", $url));

var_dump($paths);
echo array_shift($paths);
echo $paths[2];
?>