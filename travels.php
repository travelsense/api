<?php
	include 'config.php';
	
	$user = $VARS['user'];
	if (!$user) {
		$user = $USER_ID;
	}
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {

		$query = "SELECT * FROM travels WHERE user = '{$user}'";

	   	$result = mysql_query($query);
		$travels;

		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
		{
			$travels[] = $row;
		}

	    http_response_code(200);
	    print json_encode($travels);
	    return;
	}
?>
