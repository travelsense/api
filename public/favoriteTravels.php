<?php
	include 'config.php';
	include 'travel_additions.php';

	$user = $VARS['user'];
	$travel = $VARS['travel'];
	if (!$user) {
		$user = $USER_ID;
	}
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		// $sql = "SELECT travels.id, activities.action, users.id as user_id, users.first as user_first, users.last as user_last, users.image as user_image, users.hometown as user_hometown, travels.id as travel_id, travels.name as travel_name, travels.image as travel_image FROM activities INNER JOIN users ON activities.user = users.id INNER JOIN travels ON activities.travel = travels.id ORDER BY activities.id DESC LIMIT {$offset},{$limit}";
		// $sql = "SELECT * FROM travels INNER JOIN travel_favorite ON travels.id = travel_favorite.travel ORDER BY travels.id DESC LIMIT {$offset},{$limit}";
		$query = "SELECT travels.id AS id, travels.name as name, travels.image as image, travels.description as description, travels.rating as rating, travels.path as path, travels.country as country, travels.views as views, travels.user as user FROM travels INNER JOIN travel_favorite ON travels.id = travel_favorite.travel ORDER BY travels.id";

	   	$result = mysql_query($query);
		$travels;

		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
		{
			$row['checkins'] = getCheckins($row['id'],$row['path'],1);
			$travels[] = $row;
		}
		response_code(200);
	    print json_encode($travels);
	    return;
	}
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$sql = "INSERT INTO travel_favorite (travel, user) VALUES ('{$travel}', '{$user}')";
		$result = mysql_query($sql) or die(mysql_error());
		
		response_code(200);
		$rtn = array(  
	    	"success" => true,
	  		"result" => $result,
	  	);
	    print json_encode($rtn);
	    return;		
	} else 	if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
		$sql = "DELETE FROM travel_favorite WHERE travel = {$travel} AND user = {$USER_ID}";

		$result = mysql_query($sql) or die(mysql_error());
		
		response_code(200);
		$rtn = array(  
	    	"success" => true,
	  		"result" => $result,
	  	);
	    print json_encode($rtn);
	    return;		
	}
	
?>