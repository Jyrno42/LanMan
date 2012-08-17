<?php

function require_lib($fName, $error)
{
	if(!file_exists($fName))
		die($error);
	require_once($fName);
}

/**
 * This file includes all libaries used in our project
 */

require_lib("libs/class.DeploymentConfigLoader.php", "Missing Installer libary.");
require_lib("libs/TournamentCode/LOL_TournamentCode.php", "Missing TournamentCode Libary, get it from https://github.com/Jyrno42/LOL_TournamentCode.");

require_lib("libs/Smarty/Smarty.class.php", "Missing Smarty libary!");
require_lib("libs/class.GetAllGitData.php", "Missing GitData libary!");

?>