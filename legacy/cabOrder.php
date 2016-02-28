<?php
	include 'config.php';
	include 'travel_additions.php';
	include 'airplane.php';

	$latitude = $VARS['latitude']+ 0;
	$longitude = $VARS['longitude'] + 0;
	$toLatitude = $VARS['toLatitude'] + 0;
	$toLongitude = $VARS['toLongitude'] + 0;
			
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		
		$name = "Uber";
		$result = uberPrice($latitude,$longitude, $toLatitude,$toLongitude);
		
			$rtn = array(
				'name' => $name,
				'price' => $result["prices"][0]["estimate"],
				'original' => $result);

			response_code(200);
		    print json_encode($rtn);
		    return;
			
	}

function uberPrice($fromLat, $fromLon, $toLat, $toLon) {
	$url = "https://api.uber.com/v1/estimates/price?server_token=x-eJ6h_AsgtKz2JhKjQP5b0vsHSMN7tjxBiB8wNn&start_latitude=".$fromLat."&start_longitude=".$fromLon."&end_latitude=".$toLat."&end_longitude=".$toLon;
	$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec($ch);
	curl_close($ch);
	$result = json_decode($result, true);
	return $result;
}
?>