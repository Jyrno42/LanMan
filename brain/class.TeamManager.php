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
		// TODO: WTF!!! EE gets teamcarrys members now???
		$this->stdItems[$k] = new Team($row["teamID"], $row["Name"], $row["Abbrevation"], $this->PlayerManager->GetPlayers(explode(",", $row["players"])));
		$this->stdItems[$k]->OwnerID = $row["teamOwner"];
		print $k . 	": " . $row["players"] . "<br>";
 	}
	
	public function UpdateCode($k, $v)
	{
		$this->result[$k]["Name"] = $v->Name;
		$this->result[$k]["Abbrevation"] = $v->Abbrevation;
		
		$players = array();
		foreach($v->Players as $k => $v)
		{
			$players[] = $v->uniqueID;
		}
		$this->result[$k]["players"] = implode(",", $players);
	}
	
	public function InsertCode($k, $v)
	{
		$players = array();
		foreach($v->Players as $k => $v)
		{
			$players[] = $v->uniqueID;
		}
		
		// Add team...
		$this->stdItems[$k]->uniqueID = $this->Insert(
			array(
				"Name" => $v->Name,
				"Abbrevation" => $v->Abbrevation,
				"players" => implode(",", $players)
			)
		);
	}
	
	public function DeleteCode($k, $v)
	{
		if(!isset($this->stdItems[$k]))
			unset($this->result[$k]);
	}
}

?>