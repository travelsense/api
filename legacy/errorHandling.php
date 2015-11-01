<?php
	function 

response_code($code)
	{
		header(':', true, $code);
		header('X-PHP-Response-Code: '.$code, true, $code);
		header('Content-Type: application/json');
	}

	function requestVariables()
	{
		global $HTTP_RAW_POST_DATA;
		$raw_post = json_decode($HTTP_RAW_POST_DATA, true);
		$result = array();

		$_PUT = putArray();
		if (count($raw_post)) {
			$result = array_merge_recursive($result,$raw_post);
		}
		if (count($_GET)) {
			$result = array_merge_recursive($result,$_GET);
		}
		if (count($_POST)) {
			$result = array_merge_recursive($result,$_POST);
		}
		if (!count($result) && count($_PUT)) {
			$result = array_merge_recursive($result,$_PUT);
		}
		// todo:
		// for each mysql_real_escape_string.
		return $result;
	}

	function http_error($code, $title, $message) 
	{
			$rtn = array(  
		    	"succees" => false,
	      		"error" => array(  
		      		"title" => $title,
		      		"body" =>  $message
	      		)
	      	);
  			

response_code($code);
	        print json_encode($rtn);

	}

	function putArray () {
		$raw_data = file_get_contents('php://input');
		$json = json_decode($raw_data,true);
		return $json;
	}

	function postArray () {
		$raw_data = file_get_contents('php://input');
		$boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

		// Fetch each part
		if(strlen($boundary))
			$array = explode($boundary, $raw_data);
		else 
			$array = str_split($raw_data);
		$parts = array_slice($array, 1);
		$data = array();

		foreach ($parts as $part) {
		    // If this is the last part, break
		    if ($part == "--\r\n") break; 

		    // Separate content from headers
		    $part = ltrim($part, "\r\n");
		    list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

		    // Parse the headers list
		    $raw_headers = explode("\r\n", $raw_headers);
		    $headers = array();
		    foreach ($raw_headers as $header) {
		        list($name, $value) = explode(':', $header);
		        $headers[strtolower($name)] = ltrim($value, ' '); 
		    } 

		    // Parse the Content-Disposition to get the field name, etc.
		    if (isset($headers['content-disposition'])) {
		        $filename = null;
		        preg_match(
		            '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/', 
		            $headers['content-disposition'], 
		            $matches
		        );
		        list(, $type, $name) = $matches;
		        isset($matches[4]) and $filename = $matches[4]; 

		        // handle your fields here
		        switch ($name) {
		            // this is a file upload
		            case 'userfile':
		                 file_put_contents($filename, $body);
		                 break;

		            // default for all other files is to populate $data
		            default: 
		                 $data[$name] = substr($body, 0, strlen($body) - 2);
		                 break;
		        } 
		    }

		}
		return $data;
	}
?>