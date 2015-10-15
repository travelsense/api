<?php
	include 'config.php';

	$userId = $VARS['user'];

	if ($_SERVER['REQUEST_METHOD'] === 'GET') {

		$query = "SELECT * FROM travels WHERE user = {$userId}";

	   	$travelresult = mysql_query($query);
		$travels;

		while ($travelRow = mysql_fetch_array($travelresult, MYSQL_ASSOC))
		{
			$travel['id']=$travelRow['id'];
			$travel['name']=$travelRow['name'];
			$checkin;
			$path = $travel['path'];	
			$travelId = $travel['id'];
			$checkins = array();
				
			if ($path && strlen($path)) {
				$xmlpath = "../froggyproggy.com/travel/{$path}/property.xml";   
			   	$xml = simplexml_load_file($xmlpath) or die("Error loading $xmlpath");
				$tripName = $xml['name'];
				

				$travel['name'] = $row['name'];;
				for ($i=0; $i < count($xml->City); $i++) { 
					$city = $xml->City[$i];
					$checkin['latitude'] = "".$city['latitude'];
					$checkin['longitude'] = "".$city['longitude'];

					$checkins[] = $checkin;
					break;
				}
			} else {
	  			$query = "SELECT * FROM checkins WHERE travel = {$travelId} ORDER BY start";
				$result = mysql_query($query) or die(mysql_error());
				$checkin;
				while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
					$checkin['latitude'] = $row['latitude'];
					$checkin['longitude'] = $row['longitude'];

					$checkins[] = $checkin;
					break;
				}
			}
			$travel['checkins']=$checkins;
			$travels[] = $travel;
	    }
	    

response_code(200);
	    print json_encode($travels);
	    return;
	} 
?>
