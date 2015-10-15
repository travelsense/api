<?php
	include 'config.php';

	$travelId = $VARS['id'];
	$name = $VARS['name'];
	$description = $VARS['description'];

	if ($_SERVER['REQUEST_METHOD'] === 'GET') {

		$query = "SELECT * FROM travels WHERE id = {$travelId}";

	   	$result = mysql_query($query);
		$travel = mysql_fetch_array($result, MYSQL_ASSOC);
		$path = $travel['path'];	

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


		$travel['checkins']=$checkins;
	    

response_code(200);
	    print json_encode($travel);
	    return;
	} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$sql = "INSERT INTO travels (name, description, user) VALUES ('{$name}', '{$description}', '{$USER_ID}')";
		$result = mysql_query($sql) or die(mysql_error());
		$travelId = mysql_insert_id ();
		$query = "SELECT * FROM travels WHERE id = {$travelId}";

	   	$result = mysql_query($query);
		$travel = mysql_fetch_array($result, MYSQL_ASSOC);

		addActivity($USER_ID, $travelId, "have created a travel \"".$name."\"");

	    

response_code(200);
	    print json_encode($travel);
	} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
		$sql = "UPDATE travels SET";

		$variablesToSet;
		if($description){ $variablesToSet['description'] = $description;}
		if($name){ $variablesToSet['name'] = $name;}
	
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

		$sql = $sql." WHERE id = {$travelId}";

		$result = mysql_query($sql) or die(mysql_error());

		addActivity($USER_ID, $travelId, "have created updated a travel \"".$name."\"");

		$rtn = array(  
	    	"success" => true,
	  		"result" => $result,
	  	);
	    

response_code(200);
	    print json_encode($rtn);
	    return;

	} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

		addActivity($USER_ID, $travelId, "have deleted his travel \"".$name."\"");

		$sql = "DELETE FROM travels WHERE id = {$travelId}";
		$result = mysql_query($sql) or die(mysql_error());

	}
?>
