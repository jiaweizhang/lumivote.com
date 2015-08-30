<?php
$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
echo "<p>Query:</p>";
$urlQuery =  parse_url($url, PHP_URL_QUERY);
echo $urlQuery;
echo "<br/>";
echo "<p>Path:</p>";
$urlPath = parse_url($url, PHP_URL_PATH);
echo $urlPath;
echo "<br/><br/>";


// Connecting, selecting database
$link = mysqli_connect('localhost', 'lumivote_test', 'hunnybunnies91', 'lumivote_testdb')
    or die('Could not connect: ' . mysqli_error());
echo 'Connected successfully';
//mysqli_select_db('lumivote_testdb') or die('Could not select database');

// Performing SQL query
$query = 'SELECT * FROM test_table_1';
// WHERE fname="Jiawei"';
//$result = mysqli_query($query) or die('Query failed: ' . mysqli_error());
$result = $link->query($query); 

// Printing results in HTML
/*echo "<table>\n";
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
        echo "\t\t<td>$col_value</td>\n";
    }
    echo "\t</tr>\n";
}
echo "</table>\n";*/

/*$emparray[] = array();
while($row =mysql_fetch_object($result))
{
    $emparray["users"][] = $row;
}

echo "<br/><br/>";
echo json_encode($emparray, JSON_PRETTY_PRINT);
echo "<br/><br/>";
//echo '{"users":'.json_encode($emparray).'}';

$rows = array();
while($r = mysql_fetch_assoc($result)) {
    $rows['object_name'][] = $r;
}
*/
$result2 = $result;

echo "<h3>Outputted JSON:</h3>";

$rows = array();
while($r = mysqli_fetch_assoc($result)) {
    $rows['users'][] = $r;
}
print json_encode($rows);

/*echo "<h3>Outputted JSON:</h3>";
//echo "<br/><br/>";

$rows2 = array();
while($r = mysqli_fetch_assoc($result)) {
    $rows2[] = $r;
}
$data = array('users' => $rows2);
print json_encode($data);*/

/*$json = array();
if(mysql_num_rows($result)){
	while($row=mysql_fetch_row($result)){
		$json['emp_info'][]=$row;
	}
}

echo json_encode($json);*/

/*$json = array(); 
if(mysql_num_rows($result)){while($row=mysql_fetch_row($result)){ 
$json[]=$row; 
} 
} 

echo json_encode($json);*/

// Free resultset
mysql_free_result($result);

// Closing connection
mysql_close($link);
?>