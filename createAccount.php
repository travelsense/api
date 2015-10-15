<?php
include 'errorHandling.php';
include 'email.php';

$servername = "localhost";
$username = "remizorrr";
$password = "uthipirak";
$dbname = "remizorrr";

mysql_connect($servername, $username, $password) or die(mysql_error());
mysql_select_db($dbname) or die(mysql_error());

$variables = requestVariables();
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST' ||
	$_SERVER['REQUEST_METHOD'] === 'GET')
{

	$name = $variables['name'];
	$email = $variables['email'];
	$password = $variables['password'];
	$names = preg_split('/[.?!]/',$name);
	$first = strlen($names[0] > 0)?$names[0]:"";
	$last  = strlen($names[1] > 0)?$names[1]:"";

	if (!$email) {
	    $rtn = array(  
	    	"succees" => false,
      		"error" => "Can't create user without FBID",
      		"post" =>  $raw_post
      	);
        http_response_code(500);
        print json_encode($rtn);
        return;
    }

    $sql = "SELECT * FROM users WHERE email = '{$email}'";

	$result = mysql_query($sql) or die(mysql_error());
	$user;
	if (mysql_num_rows($result)) {
		$rtn = array(  
	    	"succees" => false,
      		"error" => array(  
	      		"title" => "User with this email already exists",
	      		"body" =>  "If you forgot your password, use \"Remind Passowrd\""
      		)
      	);
      	header($_SERVER['SERVER_PROTOCOL'] . 'User with this email already exists', true, 500);
        print json_encode($rtn);
		return;
	}

	$sql = "INSERT INTO users (first, last, email) VALUES ('{$first}','{$last}','{$email}')";
	$result = mysql_query($sql) or die(mysql_error());
	
	$sql = "SELECT * FROM users WHERE email = '{$email}'";
	$result = mysql_query($sql) or die(mysql_error());
	$user = mysql_fetch_array($result, MYSQL_ASSOC);
	$rtn = array(  
    	"succees" => true,
    	"token" => "randomtoken",
  		"user" => $user
  	);
	$message = createAccountMessage($email);
  	sendMessage($message);

	http_response_code(200);
    print json_encode($rtn);
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE')
{
	$fbid = $_GET['fbid'];

	$sql = "DELETE FROM users WHERE fbid = {$fbid}";
	$result = mysql_query($sql) or die(mysql_error());

	$rtn = array(
	    "succees" => true,
	);

	http_response_code(200);
    print json_encode($rtn);
} else {
    $rtn = array(  
    	"succees" => false,
  		"error" => "Unknown Error"
  	);
    http_response_code(500);
    print json_encode($rtn);
}

?>