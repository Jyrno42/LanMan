<?php 

class DefaultGame extends BaseGame
{
	const LName = "Other Games";
	const SName = "Other";

	public static function GetConfigForm()
	{
		return array(
			array("text", "None")	
		);
	}
	
	public function FromConfigForm($vals)
	{
		
	}
	public function ToStorage()
	{
		return array();
	}
	
	public function CustomVersusCode(GameResult $game)
	{
		
	}
	public function ValidateTeamForGame(Team $team)
	{
		
	}
}

?>