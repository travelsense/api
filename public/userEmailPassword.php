<?php
	include 'config.php';

	$email = $VARS['email'];
	$password = $VARS['password'];
	$newPassword = $VARS['newPassword'];

	if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
		if($USER["password"] == $password) {
			$sql = "UPDATE users SET";
			$variablesToSet;
			if($email){ $variablesToSet['email'] = $email;}
			if($password){ $variablesToSet['password'] = $newPassword;}
	
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

			$sql = $sql." WHERE id = {$USER_ID}";
			
			$result = mysql_query($sql) or die(mysql_error());

			$rtn = array(  
		    	"success" => true,
		  		"result" => $result,
		  	);
	    
			response_code(200);
		    print json_encode($rtn);
		} else {
			http_error(500, "Unauthorized Action", "Wrong password.");
		}
	    return;

	} 
?>