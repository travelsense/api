<?php
	include 'errorHandling.php';
	
	$user="root";
	$password="Yfc6_JFJ";
	$database="vacarious";
	$VARS = requestVariables();
	$USER;
	$USER_ID;

	error_reporting(E_ALL);
	mysql_connect('localhost',$user,$password);
	@mysql_select_db($database) or die( "Unable to select database");

	global $VARS;
	$TOKEN = $VARS["token"];

	if (strlen($TOKEN) < 2) {
		http_error(401, "Unauthorized Action", "Please login");
   		exit();
	}
	$query = "SELECT * FROM users WHERE BINARY token = '{$TOKEN}' ";

	$result = mysql_query($query) or die(mysql_error());
   	if (!mysql_num_rows($result)) {
		http_error(401, "Unauthorized Action", "Please login");
   		exit();
   	}

	$USER = mysql_fetch_array($result, MYSQL_ASSOC);
	$USER_ID = $USER['id'];

	if (!$USER_ID) {
		http_error(401, "Unauthorized Action", "Please login");
	    return;
	}

	function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	function checkEditAuthorizationForTravel($travelId) {
		global $USER_ID;
		$query = "SELECT * FROM travels WHERE id = '{$travelId}' AND user = '{$USER_ID}'";
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result) < 1) {
			http_error(403, "Action unavailable", "You are not authorized to perform this action.");
			exit();
		}
	}

	function addActivity($user, $travel, $text) {
		$query = "INSERT INTO activities (user, travel, action) VALUES ('{$user}', '{$travel}', '{$text}')";
		$result = mysql_query($query) or die(mysql_error());
	}

?>