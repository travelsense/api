<?php
	function getTravel($travelId, $numberOfCheckins = -1/*All Checkins*/) {
		$query = "SELECT * FROM travels WHERE id = {$travelId}";

	   	$result = mysql_query($query);
		$travel = mysql_fetch_array($result, MYSQL_ASSOC);
		$path = $travel['path'];	

		$travel['checkins']=getCheckins($travelId, $path, $numberOfCheckins);
		return $travel;
	}

	function getCheckins($travelId, $path, $numberOfCheckins = -1/*All Checkins*/) {
		$checkins;

		if ($path && strlen($path)) {
			$xmlpath = "../travel/{$path}/property.xml";   
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
				$checkin['leaveTime'] = "".$city['leaveTime'];
				$checkin['roadTime'] = "".$city['roadTime'];

				$checkins[] = $checkin;
			}
		} else {
  			$query = "SELECT * FROM checkins WHERE travel = {$travelId} ORDER BY start";
			$result = mysql_query($query) or die(mysql_error());

			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$checkins[] = $row;
			}

			for ($i=0; $i < count($checkins); $i++) { 
				$checkinItem = $checkins[$i];
				$checkinID = $checkinItem['id'];
	  			$query = "SELECT * FROM todos WHERE checkin = {$checkinID}";
				$result = mysql_query($query) or die(mysql_error());
				$todos = array();
				while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
					$todos[] = $row;
				}
				$checkins[$i]['todos'] = $todos;
			}

		}

		if($numberOfCheckins >= 0) {
			$checkins = array_slice($checkins, 0, $numberOfCheckins);
		}
		return $checkins;
	}

?>
