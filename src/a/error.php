<?php
require(__DIR__ . "/../../../../../atk14/load.php");

PupiqErrorHandler::HandleRequest($HTTP_REQUEST,$HTTP_RESPONSE);
$HTTP_RESPONSE->flushAll();
