<?php

class GroupConfigDatabase extends Dataman
{
	public function GroupConfigDatabase($connection)
	{
		parent::__construct($connection, "groupstageconfig", "confID", false, false);
	}
	
	public function ClearRelations($tournamentId)
	{
		foreach($this->stdItems as $k => $v)
		{
			if($v->tournamentID == $tournamentId)
			{
				$this->stdItems[$k] = null;
			}	
		}	
	}
	
	public function Get($id)
	{
		foreach($this->stdItems as $k => $v)
		{
			if($v->tournamentID == $id)
			{
				//return 
				return $v;
			}
		}
		return null;
	}
	
	public function LoadCode($k, $row)
	{
		$this->stdItems[$k] = new GroupStageConfig($row["GroupSize"], $row["AdvanceTeams"], $row["WinPoints"], $row["TiePoints"]);
		$this->stdItems[$k]->uniqueID = $row["confID"];
		$this->stdItems[$k]->tournamentID = $row["tourneyID"];
	}
	
	public function UpdateCode($k, $v)
	{
		// Update code
		$this->result[$k]["GroupSize"] = $v->SIZE;
		$this->result[$k]["AdvanceTeams"] = $v->ADVANCE;
		$this->result[$k]["WinPoints"] = $v->WinPoints;
		$this->result[$k]["TiePoints"] = $v->TiePoints;
	}
	
	public function InsertCode($k, $v)
	{
		// Add tournament...
		$this->stdItems[$k]->uniqueID = $this->Insert(
				array(
						"tourneyID" => $v->tournamentID,
						"GroupSize" => $v->SIZE,
						"AdvanceTeams" => $v->ADVANCE,
						"WinPoints" => $v->WinPoints,
						"TiePoints" => $v->TiePoints));
	}
	public function DeleteCode($k, $v)
	{
		if(!isset($this->stdItems[$k]) || $this->stdItems[$k] === null)
		{
 			unset($this->result[$k]);
		}
	}
}

?>