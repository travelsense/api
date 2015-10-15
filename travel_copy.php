<?php
	include 'config.php';

	$travelId = $VARS['id'];
	$name = $VARS['name'];
	$description = $VARS['description'];

	if ($_SERVER['REQUEST_METHOD'] === 'GET') {

	} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$query = "SELECT * FROM travels WHERE id = {$travelId}";
		$result = mysql_query($query) or die(mysql_error());
		$travel = mysql_fetch_array($result, MYSQL_ASSOC);
		$name = $travel['name'];
		$description = $travel['description'];

		$query = "INSERT INTO travels (name, description, user) VALUES ('{$name}', '{$description}', '{$USER_ID}')";
		$result = mysql_query($query) or die(mysql_error());
		$newTravelId = mysql_insert_id ();

		$query = "SELECT * FROM checkins WHERE travel = {$travelId} ORDER BY start";
		$result = mysql_query($query) or die(mysql_error());
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$latitude = 	$row['latitude'];
			$longitude = 	$row['longitude'];
			$end_latitude = 	$row['end_latitude'];
			$end_longitude = 	$row['end_longitude'];
			$type = 		$row['type'];
			$name = 		$row['name'];
			$country = 		$row['country'];
			$city = 		$row['city'];
			$address = 		$row['address'];
			$address = 		$row['zip'];
			$place = 		$row['place'];
			$action = 		$row['action'];
			$notes = 		$row['notes'];
			$start = 		$row['start'];
			$end = 			$row['end'];
			$sql = "INSERT INTO checkins (travel, latitude, longitude, end_latitude, end_longitude, type, name, country, city, address, zip, place, action, notes, start, end) VALUES ('{$newTravelId}','{$latitude}','{$longitude}','{$end_latitude}','{$end_longitude}','{$type}','{$name}','{$country}', '{$city}', '{$address}', '{$zip}', '{$place}', '{$action}', '{$notes}', '{$start}', '{$end}')";
			$sqlResult = mysql_query($sql) or die(mysql_error());
		}

		addActivity($USER_ID, $newTravelId, "have copied a travel \"".$name."\"");

	    http_response_code(200);
	    print json_encode($travel);
	} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

	} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

	}
?>
