<?php
include 'config.php';
include 'travel_additions.php';

$limit = 20;
$offset = 0;
$countries = array();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$query="SELECT * FROM travels WHERE id = 8499";
	$travels = mysql_query($query) or die(mysql_error());
	$travel = mysql_fetch_array($travels, MYSQL_ASSOC);
	$travel['checkins'] = getCheckins($travel['id'],$travel['path'],1);

    $query="SELECT * FROM travels WHERE country IN ('".$array."') GROUP BY path ORDER BY views DESC LIMIT {$offset},{$limit}";
	$travels = mysql_query($query) or die(mysql_error());
	$items = array();
	while ($row = mysql_fetch_array($travels, MYSQL_ASSOC)) {
		$row['checkins'] = getCheckins($row['id'],$row['path'],1);
		$items[] = $row;
	}

	$banners = array();
	$banners[] = array(
		'title' => 'Hawaii',
		'subtitle' => 'Popular Destinations',
		'image' => 'http://www.astonhotels.com/assets/slides/690x380-Hawaii-Sunset.jpg',
		'url' => 'search.php');
	$banners[] = array(
		'title' => 'Mexico',
		'subtitle' => 'Authentic experience',
		'image' => 'http://image1.masterfile.com/em_w/02/93/35/625-02933564em.jpg',
		'url' => 'search.php');
	$banners[] = array(
		'title' => 'California',
		'subtitle' => 'Explore local experiences',
		'image' => 'http://cdn.sheknows.com/articles/2012/02/southern-california-beach-horiz.jpg',
		'url' => 'search.php');

	$featured = array();


	$travels = array();
	$travels[] = $travel;

	$featured[] = array(
		'title' => '',
		'travels' => array_merge($travels, array_slice($items,0,3)),
		'url' => 'search.php');

	$featured[] = array(
		'title' => 'Popular',
		'travels' => array_slice($items,3,3),
		'url' => 'search.php');

	$featured[] = array(
		'title' => 'In-Progress',
		'travels' => array_slice($items,6,3),
		'url' => 'search.php');

	for ($i=0; $i < 3; $i++) { 
		$featured[] = array(
			'title' => 'Other '.$i,
			'travels' => array_slice($items,9 + $i*3,3),
			'url' => 'search.php');
	}
	response_code(200);
	$rtn = array(
			'banners' => $banners,
			'featured' => $featured);
  	print json_encode($rtn);	

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
} else if  ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
} else if  ($_SERVER['REQUEST_METHOD'] === 'PUT') {
}
?>