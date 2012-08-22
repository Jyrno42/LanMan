<?php

require_once("class.DataMan.php");

class Seed
{
	public $seedId = null;
	public $tournamentId = null;
	public $teamID = null;
	public $Seed = -1;
}

class SeedManager extends Dataman
{
	public function SeedManager($connection, TeamManager $teamMan)
	{
		parent::__construct($connection, "tournamentseed", "seedId", false, false);
	}
	
	public function __destruct()
	{
		parent::__destruct();
		
		
	}
	
	public function LoadCode($k, $row)
	{
		$this->stdItems[$k] = new Seed();
		foreach($row as $key => $val)
		{
			$this->stdItems[$k]->$key = $val;
		} 
	}
	
	public function UpdateCode($k, $v)
	{		
		foreach($v as $key => $val)
		{
			$this->result[$k][$key] = $v->$key;			
		}
	}
	
	public function InsertCode($k, $v)
	{
		$arr = array();
		foreach($v as $key => $val)
		{
			if($val !== null && $key != "seedId")
			{
				$arr[$key] = $val;
			}
		}
		$mId = $this->Insert($arr);
		$this->stdItems[$k]->seedId = $mId;
		if($k != $mId)
		{
			$this->stdItems[$mId] = $this->stdItems[$k];
			unset($this->stdItems[$k]);
			$k = $mId;
		}
	}
	
	public function DeleteCode($k, $v)
	{
		if(!isset($this->stdItems[$k]) || $this->stdItems[$k] === null)
			unset($this->result[$k]);
	}
	
	public function ClearRelations($tournamentID)
	{
		foreach($this->stdItems as $k => $v)
		{
			if($v->tournamentId == $tournamentID)
			{
				$this->stdItems[$k] = null;
			}
		}
	}
	
	public function GetSeedId($tournamentId, $teamId)
	{
		foreach ($this->stdItems as $k => $v)
		{
			if($v->tournamentId == $tournamentId && $v->teamID == $teamId)
			{
				return $k;
			}
		}
		throw new Exception("TournamentSeed not found!");
		return FALSE;
	}
	
	public function AddTournamentTeam($team)
	{
		if($team->TournamentID != 0)
		{
			// check
			foreach ($this->stdItems as $k => $v)
			{
				if($v->tournamentId == $team->TournamentID && $v->teamID == $team->uniqueID)
				{
					return false;
				}
			}
			$seed = new Seed();
			$seed->teamID = $team->uniqueID;
			$seed->tournamentId = $team->TournamentID;
			$this->stdItems[] = $seed;
		}
	}
	

	public function OrderSeed(Tournament $Tournament, $seedId, $modifier=1)
	{
		$oSeed = $this->stdItems[$seedId]->Seed;
		$nSeed = $oSeed;
		
		if($oSeed + $modifier < $Tournament->maxTeams && $oSeed + $modifier >= -1)
		{
			$nSeed = $oSeed + $modifier;
		}
		
		if($nSeed != $oSeed)
		{
			$otherSeed = null;
			
			foreach($this->stdItems as $k => $v)
			{
				if($v->tournamentId == $Tournament->ID && $v->Seed == $nSeed)
				{
					$otherSeed = $k;
					break;
				}
			}
			$this->stdItems[$seedId]->Seed = $nSeed;
			
			// Switch seeds
			if($otherSeed != null && $nSeed != -1)
			{
				$this->stdItems[$otherSeed]->Seed = $oSeed;
			}
		}
	}
};


?>