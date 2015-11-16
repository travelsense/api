<?php

function updateMapForTravel($travelId) {
	$url = imageURL($travelId);
	$sql = "UPDATE travels SET image = '{$url}' WHERE id = {$travelId}";
	$result = mysql_query($sql);
}

function imageURL($travelId) {
	$checkins;

	$query = "SELECT * FROM travels WHERE id = {$travelId}";

   	$result = mysql_query($query);
	$travel = mysql_fetch_array($result, MYSQL_ASSOC);
	$path = $travel['path'];	

	$query = "SELECT * FROM checkins WHERE travel = {$travelId} ORDER BY start";
	$result = mysql_query($query) or die(mysql_error());

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$checkins[] = $row;
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
