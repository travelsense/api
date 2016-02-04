<?php
	
$ts_code = "a6e36";
$key = "119d6e90e2e6e9cf818c";

function airplineData($from, $to, $date, $return, $adults = 1, $children = 0, $infants = 0)  {
	$data = array (
	"request" => array (
		"slice" => array(
			array(
				"origin" => $from,
				"destination" => $to,
				"date" => $date
			),
			array(
				"origin" => $to,
				"destination" => $from,
				"date" => $return
			)
		),
		"passengers" => array(
			"adultCount" => $adults,
			"childCount" => $children,
			"infantInLapCount" => $infants

		),
	    "solutions" => 1,
	    "refundable" => false
	)
	);
		
	$url = "https://www.googleapis.com/qpxExpress/v1/trips/search?key=AIzaSyAGBHRuxFH_greezbid1KbuWFO4yCb9gZk";
	$ch = curl_init( $url );
	$payload = json_encode($data);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	# Return response instead of printing.
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	# Send request.
	$result = curl_exec($ch);
	curl_close($ch);
	return json_decode($result, true);
}

function airplineMultiData($fromItems, $toItems, $date, $return, $adults = 1, $children = 0, $infants = 0)  {
	$mh = curl_multi_init();
	$handles;
	for($i = 0; $i < count($fromItems); ++$i) {
		$from = $fromItems[$i];
		for($j = 0; $j < count($toItems); ++$j) {
			$to = $toItems[$j];

			$data = array (
			"request" => array (
				"slice" => array(
					array(
						"origin" => $from,
						"destination" => $to,
						"date" => $date
					),
					array(
						"origin" => $to,
						"destination" => $from,
						"date" => $return
					)
				),
				"passengers" => array(
					"adultCount" => $adults,
					"childCount" => $children,
					"infantInLapCount" => $infants

				),
			    "solutions" => 1,
			    "refundable" => false
			)
			);
				
			$url = "https://www.googleapis.com/qpxExpress/v1/trips/search?key=AIzaSyAGBHRuxFH_greezbid1KbuWFO4yCb9gZk";
			$ch = curl_init( $url );
			$payload = json_encode($data);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			# Return response instead of printing.
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			# Send request.
			$handles[] = $ch;
			curl_multi_add_handle($mh,$ch);
		}
	}
	print "Launched - ".json_encode($handles);

	$active = null;
	//execute the handles
	do {
	    $mrc = curl_multi_exec($mh, $active);
	} while ($mrc == CURLM_CALL_MULTI_PERFORM);

	$still_running = null;

	// check whether the handles have finished
	do { // "wait for completion"-loop 
	        curl_multi_select($mh); // non-busy (!) wait for state change 
	        Curl_multi_exec($mh, $still_running); // get new state
	} while ($still_running);

	$result;
	for($i = 0; $i < count($handles); ++$i) {
		$result[] = curl_multi_getcontent($handles[$i]);
	}

	for($i = 0; $i < count($handles); ++$i) {
		curl_multi_remove_handle($mh, $handles[$i]);
	}

	curl_multi_close($mh);

	return json_decode($result, true);
}

function startTicketSearch_wego($from, $to, $date, $return, $adults = 1, $children = 0, $infants = 0) {
	global $key, $ts_code;

$data = array (
	"trips" => array (
		array(
				"departure_code" => $from,
				"arrival_code" => $to,
				"outbound_date" => $date,
				"inbound_date" => $return
			)
		),
		"adults_count" => $adults,
		"children_count" => $children,
		"infants_count" => $infants,
		"cabin" => "economy",
		"user_country_code" => "US",
		"country_site_code" => "US"
	);
		
	$url = "http://api.wego.com/flights/api/k/2/searches?api_key=".$key."&ts_code=".$ts_code;
	$ch = curl_init( $url );
	$payload = json_encode($data);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	# Return response instead of printing.
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	# Send request.
	$result = curl_exec($ch);
	curl_close($ch);
	return json_decode($result, true);
}

function getFaresForSearch_wego($searchData) {
	global $key, $ts_code;

	$data = array (
		"id" => getToken(),
		"search_id" => $searchData["id"],
		"trip_id" =>  $searchData["trips"][0]["id"],
		"fares_query_type" => "route"
	);  
		
	$url = "http://api.wego.com/flights/api/k/2/fares?api_key=".$key."&ts_code=".$ts_code;
	$ch = curl_init( $url );
	$payload = json_encode($data);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	# Return response instead of printing.
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	# Send request.
	$result = curl_exec($ch);
	curl_close($ch);
	return json_decode($result, true);
}


	
?>