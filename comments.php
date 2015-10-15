<?php
include 'config.php';

$id =			$VARS['id'];
$travel = 		$VARS['travel'];
$user = 		$VARS['user'];
$text = 		$VARS['text'];
$offset = 		$VARS['offset'];
$limit = 		$VARS['limit'];

if(!$offset) {
	$offset = 0;
}
if(!$limit) {
	$limit = 20;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$sql = "SELECT * FROM comments WHERE travel = {$travel} ORDER BY id ASC LIMIT {$offset},{$limit}";
	$result = mysql_query($sql) or die(mysql_error());
	$comments = array();

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
	{
		$comments[] = $row;
	}

    

response_code(200);
    print json_encode($comments);
    return;
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$sql = "SELECT * FROM travels WHERE id = ".$travel;
	$result = mysql_query($sql) or die(mysql_error());
	$user;
	if (!mysql_num_rows($result)) {
		http_error(500, "Travel dosen't exist","Please create a new travel plan and repeat the action");
		return;
	}

	$sql = "INSERT INTO comments (text, travel, user) VALUES ('{$text}','{$travel}','{$user}')";
	$result = mysql_query($sql) or die(mysql_error());
    
	$sql = "SELECT * FROM comments WHERE id = LAST_INSERT_ID()";
	$result = mysql_query($sql) or die(mysql_error());
	$comment = mysql_fetch_array($result, MYSQL_ASSOC);
    

response_code(200);
    print json_encode($comment);
    return;
} else if  ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

	$sql = "DELETE FROM comments WHERE id = '{$id}'";
	$result = mysql_query($sql) or die(mysql_error());
} else if  ($_SERVER['REQUEST_METHOD'] === 'PUT') {
	$variablesToSet;
	$sql = "UPDATE comments SET";

	if($text){ $variablesToSet['text'] = $text;}
	
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

	$sql = $sql." WHERE id = '{$id}'";
	$result = mysql_query($sql) or die(mysql_error());		
	$sql = "SELECT * FROM comments WHERE id = '{$id}'";
	$result = mysql_query($sql) or die(mysql_error());
	$comment = mysql_fetch_array($result, MYSQL_ASSOC);

    

response_code(200);
    print json_encode($comment);
    return;
}

?>
