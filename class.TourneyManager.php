<?php

require_once("class.DataManager.php");
require_once("class.DataMan.php");

require_once("class.SeedManager.php");
require_once("class.TeamManager.php");
require_once("class.GamesManager.php");
require_once("class.TournamentDatabase.php");
require_once("class.UserManager.php");

require_once("../tourney/index.php");

class TourneyManager extends DataManager
{
	public $GameClasses = array(
		"DefaultGame" => 1,
		"LeagueOfLegends" => 1	
	);
	
	public $CustomGames = array();
	
	public $seedMan;	
	public $teamMan;
	public $gamesManager;
	public $tourneyTable = null;
	public $gameTypesManager;
	
	public function GetFields($className)
	{
		if(isset($this->GameClasses[$className]))
		{
			return $className::GetFields();
		}
		return array();
	}
	
	public function TourneyManager($host, $user, $pass, $db)			
	{
		$this->errorCallback = array($this, "SqlException");
		parent::__construct($host, $user, $pass, $db);
		
		$this->gameTypesManager = new DefaultDataman($this, "gametypes", "gtID");
		foreach ($this->gameTypesManager->result as $k => $v)
		{
			if(isset($this->GameClasses[$v["className"]]))
			{
				$this->CustomGames[$v["tag"]] = new $v["className"](explode(",", $v["arguments"]));
				$this->CustomGames[$v["tag"]]->gameID = $v["tag"];
			}
		}
		
		$this->teamMan = new TeamManager($this);
		$this->seedMan = new SeedManager($this, $this->teamMan);
		$this->gamesManager = new GamesManager($this);
		$this->tourneyTable = new TournamentDatabase($this);
	}
	
	public function TeamCanRegisterToTournament(Tournament $tournament, $team)
	{
		// If team is already registered then return false!
		foreach($tournament->Teams as $k => $v)
		{
			if($v->uniqueID == $team->uniqueID)
			{
				return false;
			}
		}
		
		return $tournament->GAME->ValidateTeamForGame($team);
	}
	
	
	public function GetTeams($tournamentID)
	{
		$ret = array();
		foreach($this->seedMan->stdItems as $k => $v)
		{
			// Found a seed match
			if($v->tournamentId == $tournamentID)
			{
				// Get a team with the supplied id.
				$team = $this->teamMan->GetTeam($v->teamID);
				$team->Seeds[$v->tournamentId] = $v->Seed;
				$ret[] = $v;
			}
		}
		return $ret;
	}
	
	public function GetGameType($name)
	{ 
		return isset($this->CustomGames[$name]) ? $this->CustomGames[$name] : new DefaultGame(array("LongName", "LN"));
	}
	
	public function SeedComplete(Tournament $tournament)
	{
		// Store seeds for teams...
		foreach ($tournament->Teams as $k => $v)
		{
			$v->Seed = $k;
		}
		$tournament->STATUS = STATUS_GAMES;
	}
	
	public function RegistrationMode(Tournament $tournament)
	{
		if($tournament->STATUS != STATUS_ADDED)
			throw new Exception("Tournament status is totally wrong");
		$tournament->STATUS = STATUS_REGISTERING;
	}
	
	public function SqlException(Exception $e)
	{
		ob_end_clean();
		ApiHelper::Error($e->getMessage());
		die(ApiHelper::ReturnJson(ApiHelper::Error($e->getMessage())));
	}
	
	public function __destruct()
	{
		$this->tourneyTable->__destruct();
		$this->gamesManager->__destruct();
		$this->teamMan->__destruct();
		$this->seedMan->__destruct();
		$this->gameTypesManager->__destruct();
		exit();
	}
}

?>
