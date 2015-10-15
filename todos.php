<?php
include 'config.php';

$todoID = 	$VARS['id'];
$text = 	$VARS['text'];
$complete = $VARS['complete'];
$checkin =  $VARS['checkin'];
//checkEditAuthorizationForTodo($travelId);
	
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$sql = "SELECT * FROM checkins WHERE id = ".$checkin;
		$result = mysql_query($sql) or die(mysql_error());
		$user;
		if (!mysql_num_rows($result)) {
			http_error(500, "Checkin dosen't exist","Please create a new checkin and repeat the action");
			return;
		}

		$sql = "INSERT INTO todos (checkin, text) VALUES ('{$checkin}','{$text}')";
		$result = mysql_query($sql) or die(mysql_error());
	    
		$sql = "SELECT * FROM todos WHERE id = LAST_INSERT_ID()";
		$result = mysql_query($sql) or die(mysql_error());
		$todo = mysql_fetch_array($result, MYSQL_ASSOC);
		// addActivity($USER_ID,$travelId,"have added a destination in his travel");
	    

response_code(200);
	    print json_encode($todo);
	    return;
}
 else if  ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
		$sql = "DELETE FROM todos WHERE id = '{$todoID}'";
		$result = mysql_query($sql) or die(mysql_error());
} else if  ($_SERVER['REQUEST_METHOD'] === 'PUT') {
		
		$variablesToSet;

		$sql = "UPDATE todos SET";

		if($text){ 
			$variablesToSet['text'] = $text;
		}
		if(strlen($complete)){ 
			$variablesToSet['complete'] = $complete;
		}

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

		$sql = $sql." WHERE id = '{$todoID}'";

		$result = mysql_query($sql) or die(mysql_error());
		
		$sql = "SELECT * FROM todos WHERE id = '{$todoID}'";
		$result = mysql_query($sql) or die(mysql_error());
		$checkin = mysql_fetch_array($result, MYSQL_ASSOC);

	    

response_code(200);
	    print json_encode($checkin);
	    return;
}

?>
