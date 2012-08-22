<?php

class GamesManager extends Dataman
{
	private $memory = array();
	
	public function GamesManager($connection)
	{
		parent::__construct($connection, "games", "gameID", false, false);
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
	
	public function LoadCode($k, $row)
	{
		$this->stdItems[$k] = new GameResult($this->connection->teamMan->GetTeam($row["team1ID"]), $this->connection->teamMan->GetTeam($row["team2ID"]), $row["score1"], $row["score2"]);
		$this->stdItems[$k]->played = $row["Played"] == 1;
		$this->stdItems[$k]->tournamentID = $row["tournamentID"];
		$this->stdItems[$k]->uniqueID = $row["gameID"];
		$this->stdItems[$k]->GameNumber = $row["gameNr"];
		
		$this->memory[$this->stdItems[$k]->GetUniqueHash()] = true;
	}
	
	public function UpdateCode($k, $v)
	{
		// Update code
		$this->result[$k]["score1"] = $v->Score1;
		$this->result[$k]["score2"] = $v->Score2;
		$this->result[$k]["Played"] = $v->played ? "1" : "0";
		$this->result[$k]["tournamentID"] = $v->tournamentID;
	}
	
	/**
	 * 
	 * @param int $id
	 * @throws Exception
	 * @return GameResult
	 */
	public function GetGame($id)
	{
		if(isset($this->stdItems[$id]))
		{
			return $this->stdItems[$id];
		}
		throw new Exception("Gameresult $id not found!");
		return null;
	}
	
	public function Get($tournamentid)
	{
		$ret = array();
		foreach($this->stdItems as $k => $v)
		{
			if($v->tournamentID != $tournamentid)
				continue;
			
			$ret[$k] = $v;
		}
		return $ret;
	}
	
	public function InsertCode($k, $v)
	{
		// Add tournament...
		if(!isset($this->memory[$v->GetUniqueHash()]))
		{
			$mId = $this->Insert(
					array(
							"tournamentID" => $v->tournamentID,
							"Played" => ($v->played ? "1" : "0"),
							"team1ID" => $v->Team1->uniqueID,
							"team2ID" => $v->Team2->uniqueID,
							"score1" => $v->Score1,
							"score2" => $v->Score2,
							"uniqueHash" => $v->GetUniqueHash()));
			$this->stdItems[$k]->uniqueID = $mId;
			if($k != $mId)
			{
				$this->stdItems[$mId] = $this->stdItems[$k];
				unset($this->stdItems[$k]);
				$k = $mId;
			}
			
			$this->memory[$v->GetUniqueHash()] = true;
		}
	}
	public function DeleteCode($k, $v)
	{
		if(!isset($this->stdItems[$k]) || $this->stdItems[$k] === null)
		{
			unset($this->result[$k]);
		}
	}
	public function ClearRelations($tournamentID)
	{
		foreach($this->stdItems as $k => $v)
		{
			if($v->tournamentID == $tournamentID)
			{
				$this->stdItems[$k] = null;
			}
		}
	}
}

?>