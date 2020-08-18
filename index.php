<?php
session_start();

define('WEBROOT', $_SERVER["SCRIPT_NAME"].'/');
define('ROOT', str_replace("index.php","",$_SERVER['SCRIPT_FILENAME']));
define('base_url', 'https://csunix.mohawkcollege.ca/~000378816/private/clouddx2/');

require(ROOT . 'Config/core.php');

// Requires all these files to properly route the URL and include the proper controller
require(ROOT . 'router.php');
require(ROOT . 'request.php');
require(ROOT . 'dispatcher.php');

$dispatch = new Dispatcher();
$dispatch->dispatch();

?>