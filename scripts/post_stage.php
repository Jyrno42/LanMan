<?php

require_once ("deployment_params.inc");

//replace values in config File
$fileName = $appLocation . "/config/config.php";

$fileStr = file_get_contents($fileName . ".inc");

$fileStr = str_replace("INSTALLED_PLACEHOLDER", "Installed", $fileStr);
$fileStr = str_replace("ST_NAME_PLACEHOLDER", $siteName, $fileStr);
$fileStr = str_replace("ST_HOST_PLACEHOLDER", $siteHost, $fileStr);

$fileStr = str_replace("DB_HOST_PLACEHOLDER", $dbHost, $fileStr);
$fileStr = str_replace("DB_USER_PLACEHOLDER", $dbUsername, $fileStr);
$fileStr = str_replace("DB_PASS_PLACEHOLDER", $dbPassword, $fileStr);
$fileStr = str_replace("DB_NAME_PLACEHOLDER", $dbName, $fileStr);

if(file_put_contents($fileName, $fileStr) === FALSE)
{
	echo "Could not write to config file!";
	exit(1);
}

echo "Post Stage Succesful";

if(!defined("WEB_INSTALL"))
	exit(0);

?>
