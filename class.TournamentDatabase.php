<?php

require_once("class.DataMan.php");
require_once("class.GroupConfigDatabase.php");

class TournamentDatabase extends Dataman
{	
	public $groupConfTable;
	
	public function TournamentDatabase($connection)
	{
		$this->groupConfTable = new GroupConfigDatabase($connection);
		parent::__construct($connection, "tourneys", "tourneyID", false, false);
	}
	
	public function __destruct()
	{
		$temp = $this->groupConfTable;
		parent::__destruct();
		$temp->__destruct();
	}
	
	public function GetTournament($id)
	{
		if(isset($this->stdItems[$id]))
		{				
			if($this->stdItems[$id]->STATUS == STATUS_ADDED)
			{
				//$this->stdItems[$id]->STATUS = STATUS_REGISTERING;
			}
				
			if($this->stdItems[$id]->STATUS == STATUS_REGISTERING && sizeof($this->stdItems[$id]->Teams) == $this->stdItems[$id]->maxTeams)
			{
				$this->stdItems[$id]->STATUS = STATUS_SEEDING;
			}
				
			if($this->stdItems[$id]->STATUS == STATUS_SEEDING)
			{
				$this->stdItems[$id]->Seed();
			}
				
			// Parse games and add them to table...
			if($this->stdItems[$id]->STATUS == STATUS_GAMES)
			{
				$games = $this->stdItems[$id]->GenerateGames();
				
				foreach($games as $k => $v)
				{
					$this->connection->gamesManager->stdItems[] = $v;
				}
				$this->stdItems[$id]->STATUS = STATUS_LIVE;
			}
			
			$this->stdItems[$id]->Games = $this->connection->gamesManager->Get($id);
			
			if($this->stdItems[$id]->STATUS == STATUS_LIVE)
			{
				$this->stdItems[$id]->CalculateFromResults();
			}
				
			return $this->stdItems[$id];
		}
		else
			throw new Exception("Tournament $id not found!");
	}
	
	public function LoadCode($k, $row)
	{
		$this->stdItems[$k] = new Tournament($row["Name"], $this->connection->GetGameType($row["Game"]), $row["Status"], $row["Type"], $row["maxTeams"], array(), array(), $this->groupConfTable->Get($k));
		$this->stdItems[$k]->ID = $k;
			
		$teams = $this->connection->GetTeams($k);
		foreach($teams as $k2 => $v)
		{
			$this->stdItems[$k]->AddTeam($this->connection->teamMan->GetTeam($v->teamID));
		}
		$this->stdItems[$k]->CreateGroups();
	}
	
	public function UpdateCode($k, $v)
	{
		// Update code
		$this->result[$k]["Name"] = $v->NAME;
		$this->result[$k]["Status"] = $v->STATUS;
		$this->result[$k]["Type"] = $v->TYPE;
		$this->result[$k]["maxTeams"] = $v->maxTeams;
		$this->result[$k]["Game"] = $v->GAME->gameID;
	}
	
	public function InsertCode($k, $v)
	{
		// Add tournament...
		$this->stdItems[$k]->ID = $this->Insert(
				array(
						"Name" => $v->NAME,
						"Status" => $v->STATUS,
						"Type" => $v->TYPE,
						"maxTeams" => $v->maxTeams,
						"Game" => $v->GAME->gameID));
		
		$v->GroupStageConfig->tournamentID = $this->stdItems[$k]->ID;
		$this->groupConfTable->stdItems[] = $v->GroupStageConfig;
	}
	public function DeleteCode($k, $v)
	{
		if(!isset($this->stdItems[$k]) || $this->stdItems[$k] === null)
		{
			unset($this->result[$k]);
			$this->groupConfTable->ClearRelations($k);
			$this->connection->seedMan->ClearRelations($k);
			$this->connection->gamesManager->ClearRelations($k);
		}
	}
}

?>