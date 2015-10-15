<?php
function sendMessage($message) {
	$url = "https://mandrillapp.com/api/1.0/messages/send.json";
    // OK cool - then let's create a new cURL resource handle
    $ch = curl_init();
 
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_REFERER, "http://www.vacarious.org");
    curl_setopt($ch, CURLOPT_USERAGENT, "Mandrill-Curl/1.0");
 
    // Include header in result? (0 = yes, 1 = no)
    // curl_setopt($ch, CURLOPT_HEADER, 0);
 
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $message);                                                                  

    // Download the given URL, and return output
    $output = curl_exec($ch);
 
    // Close the cURL resource, and free system resources
    curl_close($ch);
	return $output; 
}

function forgotYoutPasswordMessage ($password, $recepient)
{
	return messageJSON('Password Reminder',
		'"Your new password is '.$password.'"',
		$recepient);
}

function createAccountMessage ($recepient)
{
	return messageJSON('Vacarious accaount was created',
		'"Account for your email was created',
		$recepient);
}

function messageJSON($subject, $body, $recepient) {
	return 
	'{  
	   "key":"4JbhVtLpBIUqy3QuiFZGSw",
	   "message":{  
	      "text":"'.$body.'",
	      "subject":"'.$subject.'",
	      "from_email":"info@vacarious.org",
	      "from_name":"Vacarious",
	      "to":[  
	         {  
	            "email":"'.$recepient.'"
	         }
	      ],
	      "headers":{  
	         "Reply-To":"info@vacarious.org"
	      },
	      "important":false,
	      "track_opens":null,
	      "track_clicks":null,
	      "auto_text":null,
	      "auto_html":null,
	      "inline_css":null,
	      "url_strip_qs":null,
	      "preserve_recipients":null,
	      "view_content_link":null,
	      "tracking_domain":null,
	      "signing_domain":null,
	      "return_path_domain":null,
	      "merge":true,
	      "merge_language":"mailchimp",
	      "tags":[  
	         "password-resets"
	      ]
	   },
	   "async":false,
	   "ip_pool":"Main Pool"
	}';
}
?>