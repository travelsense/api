<?php
	include '../api/config.php';
	include '../api/travel_additions.php';

	$checkinID = $VARS['checkin'];
	$hotelID = $VARS['hotel'];

	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		$query = "SELECT * FROM hotels INNER JOIN checkin_hotels ON hotels.id = checkin_hotels.hotel WHERE checkin_hotels.checkin = {$checkinID} ORDER BY hotels.id";
		$result = mysql_query($query) or die(mysql_error());
		$hotels;
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
		{
			$hotels[] = $row;
		}
		response_code(200);
	    print json_encode($hotels);
	    return;
	} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$query = "INSERT INTO checkin_hotels (checkin, hotel) VALUES ('{$checkinID}', '{$hotelID}')";
		$result = mysql_query($query) or die(mysql_error());
		response_code(200);
		$rtn = array(  
	    	"success" => true
		);
	    print json_encode($rtn);
	    return;
	} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
	
	} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

	}
?>
