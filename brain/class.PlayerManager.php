<?php

require_once("class.DataMan.php");

class PlayerManager extends Dataman
{	
	public function PlayerManager($connection)
	{
		parent::__construct($connection, "players", "playerID", false, false);
	}
	
	public function GetPlayers($playerIds)
	{
		$arr = array();
		foreach($this->stdItems as $k => $v)
		{
			if(array_search($v->uniqueID, $playerIds) !== FALSE)
			{
				$arr[] = $v;
			}
		}
		return $arr;
	}
	
	public function LoadCode($k, $row)
	{
		$this->stdItems[$k] = new Player($row["playerID"], $row["nick"]);
		$this->stdItems[$k]->UserID = $row["userID"];
	}
	
	public function UpdateCode($k, $v)
	{
		$this->result[$k]["nick"] = $v->Name;
	}
	
	public function InsertCode($k, $v)
	{
		// Add player...
		$mId = $this->Insert(
				array(
						"nick" => $v->Name));
		$this->stdItems[$k]->uniqueID = $mId;
		
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