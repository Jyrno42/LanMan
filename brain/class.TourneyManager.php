<?php

require_once("class.DataManager.php");
require_once("class.DataMan.php");

require_once("class.SeedManager.php");
require_once("class.TeamManager.php");
require_once("class.GamesManager.php");
require_once("class.TournamentDatabase.php");
require_once("class.UserManager.php");

class TourneyManager extends DataManager
{
	public $seedMan;	
	public $teamMan;
	public $gamesManager;
	public $tourneyTable = null;
	
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
		
		$this->teamMan = new TeamManager($this);
		$this->seedMan = new SeedManager($this, $this->teamMan);
		$this->gamesManager = new GamesManager($this);
		$this->tourneyTable = new TournamentDatabase($this);
	}
	
	public function TeamCanRegisterToTournament(Tournament $tournament, Team $team)
	{	
		// If team is already registered then return false!
		foreach($tournament->Teams as $k => $v)
		{
			if($v->uniqueID == $team->uniqueID)
			{
				return false;
			}
		}
		// Override for tournament specific teams!
		if($team->TournamentID != 0)
		{
			return $team->TournamentID == $tournament->ID;
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
		if($this->tourneyTable != null)
			$this->tourneyTable->__destruct();
		
		if($this->gamesManager != null)
			$this->gamesManager->__destruct();
		
		if($this->teamMan != null)
			$this->teamMan->__destruct();
		
		if($this->seedMan != null)
			$this->seedMan->__destruct();
		
		exit();
	}
}

?>
