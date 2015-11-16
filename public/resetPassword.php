<?php
include 'errorHandling.php';
include 'email.php';

$servername = "localhost";
$username = "remizorrr";
$password = "uthipirak";
$dbname = "remizorrr";
// $conn = new mysqli($servername, $username, $password, $dbname);
mysql_connect($servername, $username, $password) or die(mysql_error());
mysql_select_db($dbname) or die(mysql_error());

$variables = requestVariables();
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

	$email = $variables['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' ||
	$_SERVER['REQUEST_METHOD'] === 'GET')
{
    if (!$email){
    	http_error(500, "Email can't be empty","Please set email");	
    	return;
    }

    $sql = "SELECT * FROM users WHERE email = '{$email}'";

	$result = mysql_query($sql) or die(mysql_error());
	$user;
	if (!mysql_num_rows($result)) {
        http_error(500, "User with such email was not found","Please enter another email");
		return;
	}

	$user = mysql_fetch_array($result, MYSQL_ASSOC);
	$password = randomPassword();
	$userID = $user['id'];
	$sql = "UPDATE users SET password = '{$password}' WHERE id = '{$userID}'";
	mysql_query($sql) or die(mysql_error());
  	$output = sendMessage(forgotYoutPasswordMessage($email,$password);
	$rtn = array(  
    	"succees" => true,
    	"output" => $output
  	);
	

response_code(200);
    print json_encode($rtn);
    return;
}

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
?>