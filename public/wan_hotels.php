<?php
	include '../api/config.php';
	include 'travel_additions.php';

	date_default_timezone_set('America/Dawson_Creek');
	$ts_code = "a6e36";
	$key = "119d6e90e2e6e9cf818c";
	
	$checkinID = $VARS['checkin'];
	$text = $VARS['text'];
	$hotelID = $VARS['hotel'];
	$startDate = $VARS['start'];
	$endDate = $VARS['end'];
	
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		if(strlen($checkinID)){
			$checkin = getNewCheckin($checkinID);
			$text = $checkin["name"];
		}
		$locations = getLocations($text);
		$locationID = $locations[0]["id"];
		$searchID = startSearch($locationID,  $startDate, $endDate);
		sleep(1);
		$hotelsAnswer = getSearchResults($searchID);			
		$hotelResult;
		$hotels = $hotelsAnswer["hotels"];

		if(strlen($hotelID)) {
			for($i = 0; $i < count($hotels); ++$i){
				$hotel = $hotels[$i];
			    if($hotel["id"] == $hotelID){
			    	$hotelResult = $hotel;
					break;
			    }
			}		
		} 

		response_code(200);
		if(strlen($hotelID)) {
			print json_encode($hotelResult);
		} else {
			print json_encode($hotels);
		}
	    return;
	} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$query = "INSERT INTO checkin_hotels (checkin, hotel) VALUES ('{$checkinID}', '{$hotelID}')";
		$result = mysql_query($query) or die(mysql_error());
		response_code(200);
		$rtn = array(  
	    	"success" => true
		);
	    print json_encode($rtn);
	    return;
	} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
	
	} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

	}
	
	function getLocations($text) {
	    global $key, $ts_code;
		$text = urlencode($text);
		$url = 'http://api.wego.com/hotels/api/locations/search?q='.$text.'&key='.$key.'&ts_code='.$ts_code;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$json = json_decode(trim($result), true);
		curl_close($ch);
		return $json["locations"];
	}
	
	function startSearch($locationID, $startDate = 0, $endDate = 0) {
	    global $key, $ts_code;
		if(!$startDate || !strlen($startDate)) {
			$date=strtotime(date('Y-m-d'));
			$startDate = date('Y-m-d',strtotime('+6 months',$date));
		}
		if(!$endDate || !strlen($endDate)) {
			$date=strtotime(date('Y-m-d'));
			$endDate = date('Y-m-d',strtotime('+6 months 2 days',$date));
		}
		
		$url = 'http://api.wego.com/hotels/api/search/new?location_id='.$locationID.'&check_in='.$startDate.'&check_out='.$endDate.'&user_ip=direct&key='.$key.'&ts_code='.$ts_code;
		// print $url;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$json = json_decode(trim($result), true);
		curl_close($ch);
		return $json["search_id"];
	}
	
	function getSearchResults($searchID) {
	    global $key, $ts_code;
		$url = 'http://api.wego.com/hotels/api/search/'.$searchID.'?key='.$key.'&ts_code='.$ts_code;
		// print $url;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$json = json_decode(trim($result), true);
		curl_close($ch);
		return $json;
	}

	function getHotelPrice($searchID, $hotelID) {
	    global $key, $ts_code;
		$url = 'http://api.wego.com/hotels/api/search/show/'.$searchID.'?hotel_id='.$hotelID.'&key='.$key.'&ts_code='.$ts_code;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$json = json_decode(trim($result), true);
		curl_close($ch);
		return $json;
	}
?>
