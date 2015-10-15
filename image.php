<?php
  include 'config.php';

	$travelId = $VARS['travel'];
	$cId = $VARS['country'];
	$cName = $VARS['countryName'];

if (strlen($travelId)) {
	$checkins;

	$query = "SELECT * FROM travels WHERE id = {$travelId}";

   	$result = mysql_query($query);
	$travel = mysql_fetch_array($result, MYSQL_ASSOC);
	$path = $travel['path'];	

	if ($path && strlen($path)) {
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

	$markers = "markers=color:red";
	$path = "path=color:0xff0000ff|weight:5";
	for ($i = 0; $i < count($checkins); ++$i)
	{
		$checkin = $checkins[$i];
		$coordinates = "|".$checkin["latitude"].",".$checkin["longitude"];
		$markers = $markers.$coordinates;
		$path = $path.$coordinates;
	}
	$url = $url."&".$markers."&".$path;
	$name = $url;
	echo $url;
	exit;
} else {
	if (strlen($cId)>0) {
		$query="SELECT * FROM countries WHERE id = {$cId}";
	} else if (strlen($cName)>0) {
		$query="SELECT * FROM countries WHERE name LIKE '%".$cName."%'";
	}
	$countries=mysql_query($query);
 	$row = mysql_fetch_array($countries, MYSQL_ASSOC);

 	$name = "continents/".strtolower($row['continent']).".jpg";
}
	$fp = fopen($name, 'rb');

	header("Content-Type: image/jpeg");
	header("Content-Length: " . filesize($name));
	fpassthru($fp);
	exit;
?>