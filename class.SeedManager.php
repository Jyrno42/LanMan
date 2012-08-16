<?php

require_once("class.DataMan.php");

class Seed
{
	public $seedId = null;
	public $tournamentId = null;
	public $teamID = null;
	public $Seed = FALSE;
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
		$this->stdItems[$k]->seedId = $this->Insert($arr);
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
};


?>