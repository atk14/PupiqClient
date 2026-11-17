<?php
if(preg_match("/proxied/",$_TEST["FILENAME"])){
	define("PUPIQ_PROXY_HOSTNAME","img.example.com");
}

define("PUPIQ_API_KEY","101.DemoApiKeyForAccountWithLimitedFunctions");
require(__DIR__."/../src/lib/pupiq.php");
require(__DIR__."/../src/lib/pupiq_utils.php");
require(__DIR__."/../src/lib/pupiq_attachment.php");

$HTTP_REQUEST = new HTTPRequest();
$HTTP_RESPONSE = new HTTPResponse();
