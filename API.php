<?php


/**
 * The api for our tournament usage in different platforms.
 */

require_once("config/config.php");

$LanMan = null;
$LanManApi = new LanManAPI();
 
try
{
	$LanMan = new BootStrap();
	$LanMan->Strap();
	$LanMan->Datamanager->errorCallback = array($LanManApi, "Error");
	
	$LanManApi->SetLanMan($LanMan);
	$LanManApi->Strap($LanMan);
}
catch(Exception $e)
{
	$LanManApi->Error($e);
}

if($LanMan != null)
	$LanMan->Detach();

?>