<?php
define("PUPIQ_API_KEY","101.DemoApiKeyForAccountWithLimitedFunctions");
require(__DIR__."/../src/lib/pupiq.php");
require(__DIR__."/../src/lib/pupiq_utils.php");
require(__DIR__."/../src/lib/pupiq_attachment.php");

$HTTP_REQUEST = new HTTPRequest();
$HTTP_RESPONSE = new HTTPResponse();
