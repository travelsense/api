<?php
	include 'config.php';

	$userId = $VARS['id'];
	$first = $VARS['first'];
	$last = $VARS['last'];
	$about = $VARS['about'];
	$activities = $VARS['activities'];
	$comments = $VARS['comments'];
	
	if ($_SERVER['REQUEST_METHOD'] === 'GET')
	{
	    $sql = "SELECT * FROM users WHERE id = '{$userId}'";

		$result = mysql_query($sql) or die(mysql_error());
		$user = mysql_fetch_array($result, MYSQL_ASSOC);
		response_code(200);
	    print json_encode($user);
	}  else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
		$sql = "UPDATE users SET";

		$variablesToSet = array();
		if(strlen($first)){ $variablesToSet['first'] = $first;}
		if(strlen($last)){ $variablesToSet['last'] = $last;}
		if(strlen($about)){ $variablesToSet['about'] = $about;}
		if(strlen($activities)){ $variablesToSet['activities'] = $activities;}
		if(strlen($comments)){ $variablesToSet['comments'] = $comments;}
	
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
		$sql = $sql." WHERE id = {$userId}";

		$result = mysql_query($sql) or die(mysql_error());

		$rtn = array(  
	    	"success" => true,
	  		"result" => $result,
	  	);
	    
		response_code(200);
	    print json_encode($rtn);
	    return;

	} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE')
	{
		// $fbid = $_GET['fbid'];

		// $sql = "DELETE FROM users WHERE fbid = {$fbid}";
		// $result = mysql_query($sql) or die(mysql_error());

		// $rtn = array(
		//     "succees" => true,
		// );

		// 

		response_code(500);
	 	//    print json_encode($rtn);
	} 
?>