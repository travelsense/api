<?php
	include 'config.php';
	include 'travel_additions.php';

	$travelId = $VARS['id'];
	$name = $VARS['name'];
	$description = $VARS['description'];

	if ($_SERVER['REQUEST_METHOD'] === 'GET') {

		$travel = getTravel($travelId);
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
