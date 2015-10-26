<?php
include 'config.php';

$travelId = $VARS['travel'];

	$query = "SELECT id FROM travels";
   	$result = mysql_query($query);
   	$i = 0;

   	$ids;
   	if($travelId) {
   		$ids[] = $travelId;
   	}
	else {
		while ($travel = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$ids[] = $travel["id"];
		}
	}

	for ($i=0; $i < count($ids); ++$i) { 
		$travelId = $ids[$i];
		$url = imageURL($travelId);
		$sql = "UPDATE travels SET image = '{$url}' WHERE id = {$travelId}";
		$result = mysql_query($sql);
		echo "Travel ".$travelId." image = ".$url."\n";
	}

function imageURL($travelId) {
	$checkins;

	$query = "SELECT * FROM travels WHERE id = {$travelId}";

   	$result = mysql_query($query);
	$travel = mysql_fetch_array($result, MYSQL_ASSOC);
	$path = $travel['path'];	

	if ($path && strlen($path)) {
		$xmlpath = "../froggyproggy.com/travel/{$path}/property.xml";   
	   	$xml = simplexml_load_file($xmlpath);
	   	if($xml == false) return "";
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
			
			$checkins[] = $checkin;
		}
	} else {
		$query = "SELECT * FROM checkins WHERE travel = {$travelId} ORDER BY time";
		$result = mysql_query($query) or die(mysql_error());

		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$checkins[] = $row;
		}
	}

	$url = "https://maps.googleapis.com/maps/api/staticmap?size=300x150&maptype=roadmap";

	$markers = "markers=size:mid|color:red";
	$path = "path=color:0x4d4842ff|weight:2";
	$step = 1;
	if(count($checkins) > 10) {
		$step = ceil(count($checkins)/10.0);
	}
	for ($i = 0; $i < count($checkins); $i+=$step)
	{
		$checkin = $checkins[$i];
		$coordinates = "|".$checkin["latitude"].",".$checkin["longitude"];
		$markers = $markers.$coordinates;
		$path = $path.$coordinates;
	}
	$url = $url."&".$markers."&".$path;
	return $url;
}

?>