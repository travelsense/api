<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

	$handle = popen('airplane_thread.php', 'r');
	sleep(2);
	// while(!feof($handle)) {}
    $buffer = fgets($handle);
	print "Response = ".$buffer;
	$response = fread($handle, 2000);
	print "Response = ".$response;
	$response = stream_get_contents($handle);
	print "Response = ".$response;
	pclose($handle);
	return;

?>