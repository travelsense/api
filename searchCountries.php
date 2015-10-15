	<?php
$user="remizorrr";
$password="uthipirak";
$database="remizorrr";
  mysql_connect(localhost,$user,$password);
  @mysql_select_db($database) or die( "Unable to select database");
  
  $text = $_GET['text'];

	$query="SELECT * FROM countries WHERE name LIKE '%{$text}%'";
  $travels=mysql_query($query);
  $num = 0;
  $num=mysql_numrows($travels);

  while ($row = mysql_fetch_array($travels, MYSQL_ASSOC)) {
    $items[] = $row;
  }

  // $query="SELECT * FROM countries WHERE id = ".$cId;
  // $result=mysql_query($query);
  // $name=mysql_result($result,0,"name");
  // $continent=mysql_result($result,0,"continent");

  $rtn = array(
      "succees" => true,
      "data" => $items
  );

  http_response_code(200);
  print json_encode($items);

function http_response_code($code)
{
  header(':', true, $code);
  header('X-PHP-Response-Code: '.$code, true, $code);
  header('Content-Type: application/json');
}  
?>