<?php

require_once("class.DataMan.php");

class TournamentDatabase extends Dataman
{
	
	public function TournamentDatabase($connection)
	{
		parent::__construct($connection, "tourneys", "tourneyID", false, false);
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
	
	public function GetTournament($id)
	{
		if(isset($this->stdItems[$id]))
		{				
			if($this->stdItems[$id]->STATUS == STATUS_ADDED)
			{
				//$this->stdItems[$id]->STATUS = STATUS_REGISTERING;
			}
				
			if($this->stdItems[$id]->STATUS == STATUS_REGISTERING && sizeof($this->stdItems[$id]->Teams) == $this->stdItems[$id]->stageConfig->maxTeams)
			{
				$this->stdItems[$id]->STATUS = STATUS_SEEDING;
			}
				
			if($this->stdItems[$id]->STATUS == STATUS_SEEDING)
			{
				$this->stdItems[$id]->Seed();
			}
			//$this->stdItems[$id]->Seed();
				
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
		/*try 
		{*/
			$this->stdItems[$k] = new Tournament(
					$row["Name"], 
					$row["Game"],
					$row["Status"], 
					$row["Type"], 
					array(), 
					array(),
					explode(",", $row["StageConfig"]),
					explode(",", $row["GameConfig"])
			);
			$this->stdItems[$k]->ID = $k;
				
			$teams = $this->connection->GetTeams($k);
			foreach($teams as $k2 => $v)
			{
				$this->stdItems[$k]->AddTeam($this->connection->teamMan->GetTeam($v->teamID));
			}
			$this->stdItems[$k]->CreateRounds();
		/*}
		catch (Exception $e)
		{
			unset($this->stdItems[$k]);	
		}*/
	}
	
	public function UpdateCode($k, $v)
	{
		// Update code
		$this->result[$k]["Name"] = $v->NAME;
		$this->result[$k]["Status"] = $v->STATUS;
		$this->result[$k]["Type"] = $v->TYPE;
		$this->result[$k]["Game"] = is_object($v->GAME) ? get_class($v->GAME) : $v->GAME;

		$this->result[$k]["GameConfig"] = implode(",", $v->GAME->ToStorage());
		$this->result[$k]["StageConfig"] = implode(",", $v->stageConfig->ToStorage());
	}
	
	public function InsertCode($k, $v)
	{		
		// Add tournament...
		$mId = $this->Insert(
				array(
						"Name" => $v->NAME,
						"Status" => $v->STATUS,
						"Type" => $v->TYPE,
						"Game" => is_object($v->GAME) ? get_class($v->GAME) : $v->GAME,
						"GameConfig" => implode(",", $v->GAME->ToStorage()),
						"StageConfig" => implode(",", $v->stageConfig->ToStorage())
				)
		);
		$this->stdItems[$k]->ID = $mId;
		
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
		{	
			unset($this->result[$k]);
			$this->connection->seedMan->ClearRelations($k);
			$this->connection->gamesManager->ClearRelations($k);
			$this->connection->teamMan->ClearRelations($k);
		}
	}
}

?>