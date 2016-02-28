<?php
	include 'config.php';

	$travelId = $VARS['id'];

	$query = "SELECT * FROM travels WHERE id = {$travelId}";

   	$result = mysql_query($query);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$path = $row['path'];	
  	$xmlpath = "../froggyproggy.com/travel/{$path}/property.xml";   
   	$xml = simplexml_load_file($xmlpath) or die("Error loading $xmlpath");
	$tripName = $xml['name'];
	

	$travel['name'] = $row['name'];;
	for ($i=0; $i < count($xml->City); $i++) { 
		$city = $xml->City[$i];
		$checkin['name'] = "".$city['name'];
		$checkin['country'] = "".$city['country'];
		$checkin['transportType'] = "".$city['transportType'];
		$checkin['roadTime'] = "".$city['roadTime'];
		$checkin['nightsInCity'] = "".$city['nightsInCity'];
		$checkin['timeInCity'] = "".$city['timeInCity'];
		$checkin['latitude'] = "".$city['latitude'];
		$checkin['longitude'] = "".$city['longitude'];
		$checkin['notes'] = "".$city->Notes;
		$checkin['identifier'] = "".generateRandomString(5);
		
		$travel['checkins'][] = $checkin;
	}
	
	

response_code(200);
  	print json_encode($travel);

?>
