<?php

abstract class BaseStage
{
	/**
	 * The name of this stage.
	 * 
	 * @var string
	 */
	const Name = "BaseStage";
	
	/**
	 * The description of this tournament stage.
	 */
	const Description = "none";
	
	abstract function CalculateFromResults(Tournament $tournament);
	abstract function CreateRounds($tournament);
	abstract function GenerateGames(Tournament $tournament);
	abstract function IsComplete(Tournament $tournament, &$log);
}

abstract class BaseStageConfig
{
	public static function GetConfigForm()
	{
		throw new Exception("GetConfigForm needs to be overridden.");
	}
	
	public static function ValidateArguments($args)
	{
		throw new Exception("ValidateArguments needs to be overridden.");
	}
	
	abstract function FromConfigForm($vals);
}

?>