<?php

abstract class Game
{
	public $LNAME;
	public $SNAME;
	
	public $gameID = "DefaultGame";
	
	public function Game($lname, $sname)
	{
		$this->LNAME = $lname;
		$this->SNAME = $sname;
	}
	
	public abstract function CustomVersusCode(GameResult $game);	
	public abstract function ValidateTeamForGame(Team $team);
};

class DefaultGame extends Game
{
	public function DefaultGame($args)
	{
		$lname = isset($args[0])?$args[0]:"LongName";
		$sname = isset($args[1])?$args[1]:"SN";
		parent::__construct($lname, $sname);
	}
	
	public static function GetFields()
	{
		return array(
			array("text", "Nimi:"),
			array("text", "Lühend")
		);	
	}
	
	public static function ValidateArguments($args)
	{
		if(sizeof($args) != 2 && isset($args[0]) && isset($args[1]))
			throw new Exception("Arguments need to have two members!" . sizeof($args));
		
		if(strlen($args[0]) < 5)
			throw new Exception("Longname most be more than 4 characters!");
		
		if(strlen($args[1]) < 3)
			throw new Exception("Shortname most be more than 2 characters!");
	}
	
	public function CustomVersusCode(GameResult $game)
	{
		return NULL;
	}
	
	public function ValidateTeamForGame(Team $team)
	{
		//return (sizeof($team->Players) > 0);
		return true;
	}
}

class LeagueOfLegends extends Game
{
	public $LNAME = 'League Of Legends - %1$dv%1$d - %2$s - %3$s';
	public $SNAME = "LOL";
	
	public $teamSize = 5;
	public $mapType = TournamentCode::SUMMONERS_RIFT;
	public $pickType = TournamentCode::ALL_RANDOM;
	
	public function LeagueOfLegends($arr)
	{
		$this->teamSize = isset($arr[0])?$arr[0]:$this->teamSize;
		$this->mapType = isset($arr[1])?$arr[1]:$this->mapType;
		$this->pickType = isset($arr[2])?$arr[2]:$this->pickType;
		
		$this->LNAME = sprintf($this->LNAME, $this->teamSize, TournamentCode::MapName($this->mapType), TournamentCode::PickTypeStr($this->pickType));
	}
	
	public static function ValidateArguments($args)
	{
		if(sizeof($args) != 3 && isset($args[0]) && isset($args[1]) && isset($args[2]))
			throw new Exception("Arguments need to have three members!" . sizeof($args));
		
		$maps = TournamentCode::GetMaps();
		$types = TournamentCode::GetTypes();
		
		if(!array_key_exists($args[1], $maps))
			throw new Exception("Bad map provided!");
		
		if(!array_key_exists($args[2], $types))
			throw new Exception("Bad pickType provided!");
		
		if(!is_numeric($args[0]) || $args[0] < 1 || ($args[0] > 5 || ($args[0] > 3 && $args[1] == TournamentCode::TWISTED_TREELINE)))
			throw new Exception("Bad TeamSize provided!");
	}
	
	public static function GetFields()
	{
		return array(
				array("text", "Tiimi Suurus"),
				array("select", array("Kaart", TournamentCode::GetMaps())),
				array("select", array("Tüüp", TournamentCode::GetTypes()))
		);
	}
	
	public function ValidateTeamForGame(Team $team)
	{
		if($this->teamSize == 1)
		{
			return sizeof($team->Players) == 1;
		}
		return (sizeof($team->Players) >= $this->teamSize);
	}
	
	private function GetMyURL()
	{		
		return (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"?"https://":"http://") .
				PROJECT_PATH;
	}
	
	public function CustomVersusCode(GameResult $game)
	{		
		$c = new TournamentCode;
		$gConf = GameConfig::Get(
					$game->Team1->Abbrevation . " vs " . $game->Team2->Abbrevation,
					"SECUREPASS",
					$this->GetMyURL() . "/API.php?action=ReportGameResult",
				 	$game->uniqueID
				);
		$key = $c->Generate(
				$this->mapType,
				$this->pickType,
				5,
				null,
				$gConf
		);
		return "<a href='$key' class='pvpNET'>$key</a>";
	}
};

?>