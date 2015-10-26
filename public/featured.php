<?php
include 'config.php';
include 'travel_additions.php';

$limit = 20;
$offset = 0;
$countries = array();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query="SELECT * FROM travels WHERE country IN ('".$array."') GROUP BY path ORDER BY views DESC LIMIT {$offset},{$limit}";
	$travels = mysql_query($query) or die(mysql_error());
	$items = array();
	while ($row = mysql_fetch_array($travels, MYSQL_ASSOC)) {
		$row['checkins'] = getCheckins($row['id'],$row['path'],1);
		$items[] = $row;
	}

	$featured = array();

	$featured[] = array(
		'layout' => 'single', 
		'title' => '',
		'travels' => array_slice($items,0,3));

	$featured[] = array(
		'layout' => 'double', 
		'title' => 'Popular',
		'travels' => array_slice($items,3,3));

	$featured[] = array(
		'layout' => 'double', 
		'title' => 'In-Progress',
		'travels' => array_slice($items,6,3));

for ($i=0; $i < 3; $i++) { 
	$featured[] = array(
		'layout' => 'double', 
		'title' => 'Other '.$i,
		'travels' => array_slice($items,9 + $i*3,3));
}
	response_code(200);
  	print json_encode($featured);	
// [
//     {
//         "layout": "single",
//         "title": "No Title",
//         "travels": []
//     },
//     {
//         "layout": "double",
//         "title": "Popular",
//         "travels": []
//     },
//     {
//         "layout": "double",
//         "title": "In-Progress",
//         "travels": []
//     }
// ]

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
} else if  ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
} else if  ($_SERVER['REQUEST_METHOD'] === 'PUT') {
}
?>