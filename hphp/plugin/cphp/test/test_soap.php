<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>

<?php
require_once('./lib/nusoap.php');  // include NuSOAP library
require_once('./lib/class.wsdlcache.php'); // include wsdl cache library

/*
Generate a wsdl cache object to cache WSDL.
Remark:
change your cache directory mode to 777 because the "wsdlcache" will write a cache file there.
*/
$cache = new wsdlcache('/home/portal/app/tmp/wsdl', 0);

/*
Request the WSDL.
*/
$wsdl = $cache->get('http://test.payment.net.tw/mpwebservice/main.asmx?WSDL');
if (is_null($wsdl)) {
	// if no cache file, to get it from website.
	$wsdl = new wsdl('http://test.payment.net.tw/mpwebservice/main.asmx?WSDL');
	// store in cache directory
	$cache->put($wsdl);
} else {
	$wsdl->debug_str = '';
	$wsdl->debug('Retrieved from cache');
}

// generate a soap client from WSDL
$client = new soapclient($wsdl, true);

// check if error happened.
$err = $client->getError();
if ($err) {
    // Display the error
    echo '<p><b>Constructor error: ' . $err . '</b></p>';
    // At this point, you know the call that follows will fail
}

// the parameters of OrderAuth operation
$params = array (
"ICPID" => "addwe",
"ICPOrderID" => "T09887654321",
"ICPProdID" =>"addwe_t001",
"MPID" => "839OTP",
"Memo" => "Test order",
"ICPUserID" => "addwe_test"
);

// Call the SOAP method
$result = $client->call('OrderAuth', array("parameters"=>$params), '', '', false, true);

// Check for a fault
if ($client->fault) {
    echo '<p><b>Fault: ';
    print_r($result);
    echo '</b></p>';
} else {
    // Check for errors
    $err = $client->getError();
    if ($err) {
        // Display the error
        echo '<p><b>Error: ' . $err . '</b></p>';
    } else {
        // Display the result
        print_r($result);
    }
}

// dump request, response, and dubug information
echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';

?>

</body>
</html>

