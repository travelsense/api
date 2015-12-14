<?php
	
function airplineData($from, $to, $date)  {
	$data = array (
	"request" => array (
		"slice" => array(array(
			"origin" => $from,
			"destination" => $to,
			"date" => $date
		)),
		"passengers" => array(
			"adultCount" => 1
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
	
?>