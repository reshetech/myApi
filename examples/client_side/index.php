<?php

$clientKey  = ''; // Unhashed user key from authentications table
$clientPass = ''; // Unhashed user password from authentications table
$url        = "http://server-provider.com/path/to/myApi"; // Change to your web service site and path to myApi folder


// Do not edit beneath this remark.
$format      = (isset($_GET['format']))? urlencode($_GET['format']): 'json';

$num         = (isset($_GET['num']))?    urlencode($_GET['num'])   : '0';

$where       = (isset($_GET['where']))?  urlencode($_GET['where']) : ''; 

$credentials = "key={$clientKey}&pass={$clientPass}&where={$where}&format={$format}&num={$num}";

$handle = curl_init($url);

curl_setopt_array($handle,
				  array(
					CURLOPT_URL=>$url,
					CURLOPT_POST=>true,
					CURLOPT_RETURNTRANSFER=>true,
					CURLOPT_POSTFIELDS=>$credentials,
					CURLOPT_HEADER=>true)
);

$data = curl_exec($handle); 

// Extract the head and body parts of the response.
$headerSize   = curl_getinfo($handle,CURLINFO_HEADER_SIZE);

$responseCode = curl_getinfo($handle,CURLINFO_HTTP_CODE);

if(curl_errno($handle))
	print curl_error($handle);
else
	curl_close($handle);
	
$header = substr($data, 0, $headerSize);
$body   = substr($data, $headerSize);

// Output the response.
if(strpos($header,'xml'))
	header('Content-type: text/xml');
elseif(strpos($header,'json'))
	header('Content-type: application/json');
else
	header('Content-Type: text/html; charset=utf-8');

echo($responseCode != '200')? $header : $body;
