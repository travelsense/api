<?php
$servername = "localhost";
$username = "remizorrr";
$password = "uthipirak";
$dbname = "remizorrr";
// $conn = new mysqli($servername, $username, $password, $dbname);
mysql_connect($servername, $username, $password) or die(mysql_error());
mysql_select_db($dbname) or die(mysql_error());

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
	$raw_post = json_decode($HTTP_RAW_POST_DATA, true);

	$fbid = $raw_post['fbid'];
	$first = $raw_post['first'];
	$last = $raw_post['last'];
	$token = $raw_post['token'];
	$email = $raw_post['email'];
	$image = $raw_post['image'];

    $sql = "SELECT * FROM users WHERE id = 2";

	$result = mysql_query($sql) or die(mysql_error());
	$user = mysql_fetch_array($result, MYSQL_ASSOC);
	

response_code(200);
    print json_encode($user);
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE')
{
	// $fbid = $_GET['fbid'];

	// $sql = "DELETE FROM users WHERE fbid = {$fbid}";
	// $result = mysql_query($sql) or die(mysql_error());

	// $rtn = array(
	//     "succees" => true,
	// );

	// 

response_code(200);
 //    print json_encode($rtn);
} else {
    $rtn = array(  
    	"succees" => false,
  		"error" => "Unknown Error"
  	);
    

response_code(500);
    print json_encode($rtn);
}

function 

response_code($code)
{
	header(':', true, $code);
	header('X-PHP-Response-Code: '.$code, true, $code);
	header('Content-Type: application/json');
}
?>