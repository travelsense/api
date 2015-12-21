<?php
include 'config.php';
	
$limit = $VARS['limit'];
$offset = $VARS['offset'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
//	$sql = "SELECT * FROM activities ORDER BY id DESC LIMIT {$offset},{$limit}";
	$sql = "SELECT activities.id, activities.action, users.id as user_id, users.first as user_first, users.last as user_last, users.image as user_image, users.hometown as user_hometown, travels.id as travel_id, travels.name as travel_name, travels.image as travel_image FROM activities INNER JOIN users ON activities.user = users.id INNER JOIN travels ON activities.travel = travels.id ORDER BY activities.id DESC LIMIT {$offset},{$limit}";
	$result = mysql_query($sql) or die(mysql_error());
	$activities;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
	{
		$travel = array('id' => $row["travel_id"],
						'name' => $row["travel_name"],
						'image' => $row["travel_image"]);
		$user = array('id' => $row["user_id"],
						'first' => $row["user_first"],
						'last' => $row["user_last"],
						'image' => $row["user_image"],
						'hometown' => $row["user_hometown"]);
		
		$activity = array('id' => $row["id"],
						  'action' => $row["action"],
						  'user' => $user,
						  'travel' => $travel);
		$activities[] = $activity;
	}

    

response_code(200);
    if (count($activities)) {
	    print json_encode($activities);
    } else {
    	print "{}";
    }

    return;
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
} else if  ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
} else if  ($_SERVER['REQUEST_METHOD'] === 'PUT') {
}

?>
