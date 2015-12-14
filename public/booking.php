<?php
	include 'config.php';
	include 'travel_additions.php';
	include 'airplane.php';

	$travelId = $VARS['id'];
	$name = $VARS['name'];
	$description = $VARS['description'];
	$latitude = $VARS['latitude'];
	$longitude = $VARS['longitude'];
	$when = $VARS['when'];
	
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		
		$travel = getTravel($travelId);
		$count = count($travel["checkins"]);
		
		$bookings = array();		

		$from = airportCode($latitude,$longitude);
		$to = airportCode($travel["checkins"]["latitude"],$travel["checkins"]["longitude"]);

		print "FROM: ".json_encode($from);
		print "TO: ".json_encode($to);
		
		if(strlen($when) <= 0) {
			$when = "2015-12-23";
		}
		
		$finalCarrierName;
		$finalPrice=999999;
		$airpot;
		for($i = 0; $i < count($from); ++$i) {
			for($j = 0; $j < count($to); ++$j) {
				$planeDetails = airplineData($from[$i]["code"], $to[$j]["code"], $when);
				$carrierName = $planeDetails["trips"]["data"]["carrier"][0]["name"];
				$price = $planeDetails["trips"]["tripOption"][0]["saleTotal"];
				print $from[$i]["code"]." -> ".$to[$j]["code"].", Carrier:".$carrierName." , Price:".$price."\n";
				if($price < $finalPrice) {
					$finalCarrierName = $carrierName;
					$finalPrice = $price;
					$airpot = $from[$i];
				}
			}
		}
		
		$price = uberPrice($latitude,$longitude,$airpot["lat"],$airpot["lon"]);
		
		$bookings[] = array(
			'type' => 'taxi',
			'name' => 'Uber',
			'image' => 'http://www.trbimg.com/img-524cbc33/turbine/la-fi-tn-20131002-001/599/599x400',
			'price' => $price
		);
		
		$bookings[] = array(
			'type' => 'plane',
			'name' => $finalCarrierName,
			'image' => 'https://img.s-hawaiianairlines.com/~/media/images/brand/airplanes/airbus-a330/a330-plane2014.jpg?version=c973&sc_lang=en&w=535',
			'price' => $finalPrice
		);
		
		$images = array('https://cdn.kiwicollection.com/media/property/PR000183/xl/000183-01-pool-exterior-night.jpg',
		'https://cdn.kiwicollection.com/media/property/PR011090/xl/011090-01-hotel-view-night.jpg'
		);
		for($i = 0; $i < $count; ++$i) {
			$index = rand(0, 1);
			$bookings[] = array(
						'type' => 'hotel',
						'name' => 'Hotel '.$i,
						'price' => '120.0',
						'image' => 	$images[$index]
					);
		}
		response_code(200);
	    print json_encode($bookings);
	    return;
	} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
	} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
	}
	
	function airportCode($lat, $long) {
		$csv = file_get_contents('airports_large.csv');
		$lines = explode(PHP_EOL, $csv);
		
		$closestAirportCodes = array();
		$closestAirportLines = array();
		$distances = array();
		
		for($i = 0; $i < count($lines); ++$i) {
			$line = $lines[$i];
			if(count($lines) < 10) {
				continue;
			}

			$components = explode(',', $line);
			$lineLat = (float) str_replace('"','',$components[4]);
			$lineLon = (float) str_replace('"','',$components[5]);
			$type = str_replace('"','',$components[2]);
			$newDistance = distance($lat, $long, $lineLat, $lineLon);
			
			$distanceIndex = -1;
			$closestAirportCode;
			$closestComponents;

			$newCode = str_replace('"','',$components[13]);
			if(!strlen($newCode) || $type != "large_airport"){
				continue;
			}

			if (count($closestAirportCodes) < 3){
				$closestAirportCodes[] = $newCode;
				$closestAirportLines[] = array("code" => $newCode,
				"lat" => $lineLat,
				"lon" => $lineLon
			);
				$distances[] = $newDistance;
				continue;
			}
			
			for($j = 0; $j < count($closestAirportCodes); ++$j) {
				if($newDistance < $distances[$j]) {
					if($distanceIndex == -1 || $distances[$j] > $distances[$distanceIndex]) {
						$distanceIndex = $j;
					}
				}
			}
			
			if($distanceIndex >= 0){
				$closestAirportCodes[$distanceIndex] = $newCode;
				$distances[$distanceIndex] = $newDistance;
				$closestAirportLines[$distanceIndex] = array("code" => $newCode,
				"lat" => $lineLat,
				"lon" => $lineLon
			);
			}  			
			
		}
		return $closestAirportLines;
	}
	
	function distance($lat1, $lon1, $lat2, $lon2, $unit = "M") {
	  $theta = $lon1 - $lon2;
	  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	  $dist = acos($dist);
	  $dist = rad2deg($dist);
	  $miles = $dist * 60 * 1.1515;
	  $unit = strtoupper($unit);

	  if ($unit == "K") {
	    return ($miles * 1.609344);
	  } else if ($unit == "N") {
	      return ($miles * 0.8684);
	    } else {
	        return $miles;
	      }
	}
	
	function uberPrice($fromLat, $fromLon, $toLat, $toLon) {
		$url = "https://api.uber.com/v1/estimates/price?server_token=x-eJ6h_AsgtKz2JhKjQP5b0vsHSMN7tjxBiB8wNn&start_latitude=".$fromLat."&start_longitude=".$fromLon."&end_latitude=".$toLat."&end_longitude=".$toLon;
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($result, true);
		return $result["prices"][0]["estimate"];
	}
	
?>