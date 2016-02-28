<?php
	function getTravel($travelId, $numberOfCheckins = -1/*All Checkins*/) {
		global $USER_ID;
		$query = "SELECT * FROM travels WHERE id = {$travelId}";

	   	$result = mysql_query($query);
		$travel = mysql_fetch_array($result, MYSQL_ASSOC);
		$path = $travel['path'];	

		$query = "SELECT * FROM travel_favorite WHERE travel = {$travelId} AND user = {$USER_ID}";
	   	$result = mysql_query($query);
		$num_rows = mysql_num_rows($result);
		$travel['favorite'] = $num_rows;
		$travel['checkins']=getCheckins($travelId, $path, $numberOfCheckins);
		return $travel;
	}

	function getCheckins($travelId, $path, $numberOfCheckins = -1/*All Checkins*/) {
		$checkins;

		if ($path && strlen($path)) {
			$xmlpath = "../../html/travel/{$path}/property.xml";   
		   	$xml = simplexml_load_file($xmlpath) or die("Error loading $xmlpath");
			$tripName = $xml['name'];
			

			$travel['name'] = $row['name'];;
			for ($i=0; $i < count($xml->City); $i++) { 
				$city = $xml->City[$i];
				{
					$checkin['name'] = "".$city['name'];
					$checkin['country'] = "".$city['country'];
					$checkin['nightsInCity'] = "".$city['nightsInCity'];
					$checkin['timeInCity'] = "".$city['timeInCity'];
					$checkin['latitude'] = "".$city['latitude'];
					$checkin['longitude'] = "".$city['longitude'];
					$checkin['notes'] = "".$city->Notes;
					$checkin['identifier'] = "".generateRandomString(5);
					$checkin['leaveTime'] = "".$city['leaveTime'];
					$checkin['roadTime'] = "".$city['roadTime'];
					$checkin['type'] = 0;
					$checkins[] = $checkin;
				}
				{
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
					$checkin['type'] = 1;
					$checkins[] = $checkin;
					
				}
				
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
				$checkins[$i]['hotels'] = getHotels($checkinID);
			}
		}

		if($numberOfCheckins >= 0) {
			$checkins = array_slice($checkins, 0, $numberOfCheckins);
		}
		return $checkins;
	}
	
	function getHotels($checkinId) {
		$query = "SELECT * FROM hotels INNER JOIN checkin_hotels ON hotels.id = checkin_hotels.hotel WHERE checkin_hotels.checkin = {$checkinId}";
	   	$result = mysql_query($query);
		$hotels;
		while ($hotel = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$hotels[] = $hotel;
		}
		return $hotels;
	}

	function getNewCheckin($checkinId) {
		$query = "SELECT * FROM checkins WHERE id = {$checkinId}";
	   	$result = mysql_query($query);
		$checkin = mysql_fetch_array($result, MYSQL_ASSOC);
		return $checkin;
	}
	
	function getCheckin($travelId, $index) {
		$query = "SELECT * FROM travels WHERE id = {$travelId}";
	   	$result = mysql_query($query);
		$travel = mysql_fetch_array($result, MYSQL_ASSOC);
		$path = $travel['path'];	

		$checkins =getCheckins($travelId, $path, $numberOfCheckins);
		return $checkins[$index];
	}

?>
