<?php
include 'errorHandling.php';
include 'email.php';

$servername = "localhost";
$username = "root";
$password = "Yfc6_JFJ";
$dbname = "vacarious";
// $conn = new mysqli($servername, $username, $password, $dbname);
mysql_connect($servername, $username, $password) or die(mysql_error());
mysql_select_db($dbname) or die(mysql_error());

$variables = requestVariables();
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

	$fbid = $variables['fbid'];
	$first = $variables['first'];
	$last = $variables['last'];
	$token = $variables['token'];
	$email = $variables['email'];
	$password = $variables['password'];
	$image = $variables['image'];
	$hometown = $variables['hometown'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' ||
	$_SERVER['REQUEST_METHOD'] === 'GET')
{
	if (!$fbid && !$email) {
	    $rtn = array(  
	    	"succees" => false,
      		"error" => "Can't create user without FBID",
      		"post" =>  $raw_post
      	);
        

response_code(500);
        print json_encode($rtn);
        return;
    }

    if ($fbid) {
	    $sql = "SELECT * FROM users WHERE fbid = {$fbid}";

		$result = mysql_query($sql) or die(mysql_error());
		$user;
		if (!mysql_num_rows($result)) {
			$sql = "INSERT INTO users (fbid, first, last, fbtoken, email, image, hometown) VALUES ('{$fbid}','{$first}','{$last}','{$fbtoken}','{$email}','{$image}','{$hometown}')";
			$result = mysql_query($sql) or die(mysql_error());
			$message = createAccountMessage($email);
		  	sendMessage($message);

			$sql = "SELECT * FROM users WHERE fbid = {$fbid}";
			$result = mysql_query($sql) or die(mysql_error());
			$user = mysql_fetch_array($result, MYSQL_ASSOC);
		} else {
			$user = mysql_fetch_array($result, MYSQL_ASSOC);
		}
	} else if ($email){
	    $sql = "SELECT * FROM users WHERE email = '{$email}' AND password = '{$password}'";

		$result = mysql_query($sql) or die(mysql_error());
		$user;
		if (!mysql_num_rows($result)) {
		    $rtn = array(  
		    	"succees" => false,
	      		"error" => array(  
		      		"title" => "Wrong email or password",
		      		"body" =>  "Please enter email and password or used \"Forgot your password\""
	      		)
	      	);
  			

response_code(500);
	        print json_encode($rtn);
			return;
		}

		$user = mysql_fetch_array($result, MYSQL_ASSOC);
	}

	$token = getToken();
	$userID = $user['id'];
	$sql = "UPDATE users SET token = '{$token}' WHERE id = '{$userID}'";
	mysql_query($sql) or die(mysql_error());

	$rtn = array(  
    	"succees" => true,
    	"token" => $token,
  		"user" => $user
  	);
	

response_code(200);
    print json_encode($rtn);
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE')
{
	$sql = "UPDATE users SET token = '' WHERE id = '{$userID}'";
	mysql_query($sql) or die(mysql_error());

	$rtn = array(  
    	"succees" => true,
  		"user" => $user
  	);
	

response_code(200);
    print json_encode($rtn);
} else {
    $rtn = array(  
    	"succees" => false,
  		"error" => "Unknown Error"
  	);
    

response_code(500);
    print json_encode($rtn);
}

function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
        	//$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = hexdec(bin2hex(rand()));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
}

function getToken($length=32){
    // $token = "";
    // $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    // $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    // $codeAlphabet.= "0123456789";
    // for($i=0;$i<$length;$i++){
    //     $token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
    // }
    // return $token;
    return md5(uniqid(rand(), true));
}
?>