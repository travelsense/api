<?php
include 'config.php';
include 'travelMap.php';
$token = 		$VARS['token'];
//check rights

$checkinID = 	$VARS['id'];
$travelId = 	$VARS['travel'];
$latitude = 	$VARS['latitude'];
$longitude = 	$VARS['longitude'];
$end_latitude = 	$VARS['end_latitude'];
$end_longitude = 	$VARS['end_longitude'];
$type = 		$VARS['type'];
$name = 		$VARS['name'];
$country = 		$VARS['country'];
$city = 		$VARS['city'];
$address = 		$VARS['address'];
$address = 		$VARS['zip'];
$place = 		$VARS['place'];
$action = 		$VARS['action'];
$notes = 		$VARS['notes'];
$start = 		$VARS['start'];
$end = 			$VARS['end'];

// $place = $variables['category'];
// $place = $variables['subcategory'];
checkEditAuthorizationForTravel($travelId);
	
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$sql = "SELECT * FROM travels WHERE id = ".$travelId;
		$result = mysql_query($sql) or die(mysql_error());
		$user;
		if (!mysql_num_rows($result)) {
			http_error(500, "Travel dosen't exist","Please create a new travel plan and repeat the action");
			return;
		}

		$sql = "INSERT INTO checkins (travel, latitude, longitude, end_latitude, end_longitude, type, name, country, city, address, zip, place, action, notes, start, end) VALUES ('{$travelId}','{$latitude}','{$longitude}','{$end_latitude}','{$end_longitude}','{$type}','{$name}','{$country}', '{$city}', '{$address}', '{$zip}', '{$place}', '{$action}', '{$notes}', '{$start}', '{$end}')";
		$result = mysql_query($sql) or die(mysql_error());
	    
		$sql = "SELECT * FROM checkins WHERE id = LAST_INSERT_ID()";
		$result = mysql_query($sql) or die(mysql_error());
		$checkin = mysql_fetch_array($result, MYSQL_ASSOC);
		addActivity($USER_ID,$travelId,"have added a destination in his travel");
		updateMapForTravel($travelId);
	    

response_code(200);
	    print json_encode($checkin);
	    return;
} else if  ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
		$sql = "DELETE FROM checkins WHERE id = '{$checkinID}'";
		$result = mysql_query($sql) or die(mysql_error());
		updateMapForTravel($travelId);
		addActivity($USER_ID,$travelId,"have removed a destination in his travel");
} else if  ($_SERVER['REQUEST_METHOD'] === 'PUT') {
		
		$variablesToSet;

		$sql = "UPDATE checkins SET";

		if($latitude){ $variablesToSet['latitude'] = $latitude;}
		if($longitude){ $variablesToSet['longitude'] = $longitude;}
		if($end_latitude){ $variablesToSet['end_latitude'] = $end_latitude;}
		if($end_longitude){ $variablesToSet['end_longitude'] = $end_longitude;}
		if($type){ $variablesToSet['type'] = $type;}
		if($name){ $variablesToSet['name'] = $name;}
		if($country){ $variablesToSet['country'] = $country;}
		if($city){ $variablesToSet['city'] = $city;}
		if($address){ $variablesToSet['address'] = $address;}
		if($zip){ $variablesToSet['zip'] = $zip;}
		if($place){ $variablesToSet['place'] = $place;}
		if($action){ $variablesToSet['action'] = $action;}
		if($notes){ $variablesToSet['notes'] = $notes;}
		if($start){ $variablesToSet['start'] = $start;}
		if($end){ $variablesToSet['end'] = $end;}

		$i = 0;
		foreach($variablesToSet as $key=>$value) {
			if($i == count($variablesToSet) - 1)
			{
				$sql = $sql." ".$key." = '".$value."'";
			} else {
				$sql = $sql." ".$key." = '".$value."',";
			}
			++$i;
    	}

		$sql = $sql." WHERE id = '{$checkinID}'";

		$result = mysql_query($sql) or die(mysql_error());
		
		$sql = "SELECT * FROM checkins WHERE id = '{$checkinID}'";
		$result = mysql_query($sql) or die(mysql_error());
		$checkin = mysql_fetch_array($result, MYSQL_ASSOC);

		updateMapForTravel($travelId);
		addActivity($USER_ID,$travelId,"have changed a destination in his travel");
	    

response_code(200);
	    print json_encode($checkin);
	    return;
}

?>
