<?php

/** 
 * @author TH3F0X
 * 
 */
abstract class BaseGame 
{
	const SName = "";
	const LName = "";
	
	public static function GetConfigForm()
	{
		throw new Exception("GetConfigForm needs to be overridden.");
	}
	
	public static function ValidateArguments($args)
	{
		throw new Exception("ValidateArguments needs to be overridden.");
	}

	abstract function FromConfigForm($vals);
	abstract function ToStorage();
	abstract function CustomVersusCode(GameResult $game);
	abstract function ValidateTeamForGame(Team $team);
	
}

?>