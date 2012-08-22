<?php

require_once("class.DataMan.php");
require_once("class.PlayerManager.php");

class TeamManager extends Dataman
{	
	/**
	 * Player manager class.
	 * @var PlayerManager
	 */
	public $PlayerManager = null;
	
	public function TeamManager($connection)
	{
		$this->PlayerManager = new PlayerManager($connection);
		parent::__construct($connection, "teams", "teamID", false, false);
	}
	
	public function ClearRelations($TournamentID)
	{
		foreach($this->stdItems as $k => $v)
		{
			if($v->TournamentID != 0 && $v->TournamentID == $TournamentID)
			{
				$this->stdItems[$k] = null;
			}
		}
	}
	
	public function __destruct()
	{
		$this->PlayerManager->__destruct();
		parent::__destruct();
	}
	
	public function GetTeam($id)
	{
		if(isset($this->stdItems[$id]))
		{
			return $this->stdItems[$id];
		}
		else
			throw new Exception("Team $id not found!");
	}
	
	public function GetUserTeams($userID)
	{
		$ret = array();
		foreach($this->stdItems as $k => $v)
		{
			if($v->OwnerID == $userID)
				$ret[] = $v;
		}
		return $ret;
	}
	
	public function LoadCode($k, $row)
	{
		$this->stdItems[$k] = new Team($row["teamID"], $row["Name"], $row["Abbrevation"], $this->PlayerManager->GetPlayers(explode(",", $row["players"])));
		$this->stdItems[$k]->OwnerID = $row["teamOwner"];

		$this->stdItems[$k]->AdminKey = isset($row["AdminKey"]) && strlen($row["AdminKey"]) > 0 ? $row["AdminKey"] : ApiHelper::GenerateRandomness(16);
		$this->stdItems[$k]->JoinKey = isset($row["JoinKey"]) && strlen($row["JoinKey"]) > 0 ? $row["JoinKey"] : ApiHelper::GenerateRandomness(8);

		$this->stdItems[$k]->TournamentID = $row["TournamentID"];
 	}
	
	public function UpdateCode($k, $v)
	{
		$this->result[$k]["Name"] = $v->Name;
		$this->result[$k]["Abbrevation"] = $v->Abbrevation;
		$this->result[$k]["AdminKey"] = $v->AdminKey;
		$this->result[$k]["JoinKey"] = $v->JoinKey;
		$this->result[$k]["TournamentID"] = $v->TournamentID;
		
		$players = array();
		foreach($v->Players as $k2 => $v2)
		{
			$players[] = $v2->uniqueID != 0 ? $v2->uniqueID : $v2->Name;
		}
		$this->result[$k]["players"] = implode(",", $players);
	}
	
	public function InsertCode($k, $v)
	{
		$players = array();
		foreach($v->Players as $k2 => $v2)
		{
			$players[] = $v2->uniqueID != 0 ? $v2->uniqueID : $v2->Name;
		}
		
		// Add team... 
		$mId = $this->Insert(
			array(
				"Name" => $v->Name,
				"Abbrevation" => $v->Abbrevation,
				"players" => implode(",", $players),
				"AdminKey" => $v->AdminKey,
				"JoinKey" => $v->JoinKey,
				"TournamentID" => $v->TournamentID
			)
		);
		$this->stdItems[$k]->uniqueID = $mId;
		
		if($v->TournamentID != 0)
		{
			$this->connection->seedMan->AddTournamentTeam($v);
		}
		
		if($k != $mId)
		{
			$this->stdItems[$mId] = $this->stdItems[$k];
			unset($this->stdItems[$k]);
			$k = $mId;
		}
	}
	
	public function DeleteCode($k, $v)
	{
		if(!isset($this->stdItems[$k]))
			unset($this->result[$k]);
	}
}

?>