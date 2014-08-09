<?php

if(isset($_GET['where']))
{
	// Edit this part.
	$clientKey  = ''; // Unhashed user key from authentications table
	$clientPass = ''; // Unhashed user password from authentications table
	$url        = "http://client-site/path/to/myApi/index.php"; // Change to your web service site and path to myApi folder
	
	
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
						CURLOPT_HEADER=>false)
	);

	$data = curl_exec($handle); 

	if(curl_errno($handle))
		print curl_error($handle);
	else
		curl_close($handle);

	if($format==="xml") 
	{
	    header('Content-type: text/xml');
	}
	
	echo $data;
}
