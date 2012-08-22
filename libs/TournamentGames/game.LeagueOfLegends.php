<?php


class LeagueOfLegends extends BaseGame
{
	const LName = "League Of Legends";
	const SName = "LOL";

	public $mapType = 0;
	public $pickType = 0;
	public $teamSize = 0;
	
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

	public static function GetConfigForm()
	{
		return array(
				array("select", array("Team Size", array_combine(range(1, 5), range(1, 5)))),
				array("select", array("Map", TournamentCode::GetMaps())),
				array("select", array("Type", TournamentCode::GetTypes()))
		);
	}

	public function FromConfigForm($vals)
	{
		$this->mapType = $vals[1];
		$this->pickType = $vals[2];
		$this->teamSize = $vals[0];
	}
	
	public function ToStorage()
	{
		return array(
			$this->teamSize,
			$this->mapType,
			$this->pickType
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
				$this->teamSize,
				null,
				$gConf
		);
		return "<a href='$key' class='pvpNET'>$key</a>";
	}
};

?>