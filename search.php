	<?php
  include 'config.php';
  
  $limit = (isset($VARS['limit']))?$VARS['limit']:10;
  $page = (isset($VARS['page']))?$VARS['page']:0;
  $countries = (isset($VARS['countries']))?$VARS['countries']:array();
  $tags = (isset($VARS['tags']))?$VARS['tags']:array();

  // parse_str($countries, $countries);
  $offset = $page*$limit;

  if (count($countries)) {
    $array = implode("','",$countries);
    $query="SELECT * FROM travels WHERE country IN ('".$array."') GROUP BY path ORDER BY views DESC LIMIT {$offset},{$limit}";
  }  
  else {
    $query="SELECT * FROM travels GROUP BY path ORDER BY views DESC LIMIT {$offset},{$limit}";    
  }
	$travels=mysql_query($query);
  $num = 0;
  $num=mysql_numrows($travels);

  while ($row = mysql_fetch_array($travels, MYSQL_ASSOC)) {
    $items[] = $row;
  }

  

response_code(200);
  print json_encode($items);
?>