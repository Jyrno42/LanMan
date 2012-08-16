<?php

require_once("class.Group.php");

/*
 * A class which manages groupstage logic.
 */
class GroupStage
{
	/**
	 * Number of groups.
	 * @var int
	 */
	public $numGroups = 0;
	
	/**
	 * Groups array.
	 * @var array
	 */
	public $Groups = array();
	
	/**
	 * Initialize a groupstage from tournament.
	 * @param Tournament $tournament
	 * @throws Exception
	 */
	public function GroupStage(Tournament $tournament)
	{
		if($tournament->maxTeams % $tournament->GroupStageConfig->SIZE != 0)
			throw new Exception("Tournament must have proper group sizes based on number of teams. Unequal group sizes not yet implemented!");
			
		$this->numGroups = $tournament->maxTeams / $tournament->GroupStageConfig->SIZE;
		$playoffteams = $tournament->GroupStageConfig->ADVANCE * $this->numGroups;
		
		for($i = 0; $i < $this->numGroups; $i++)
		{
			$this->Groups[$i] = new Group($i, $tournament->maxTeams, $tournament);
		}
	}
	
	public function CalculateFromResults(Tournament $tournament)
	{
		foreach($this->Groups as $k => $v)
		{
			if(!$v->CalculateFromResults($tournament))
			{
				// something bad?
			}
		}
	}
	
	public function CreateGroups($tournament)
	{
		foreach($this->Groups as $k => $v)
		{
			$v->CreateGroup($tournament);
		}
	}

	public function Debug()
	{
		foreach($this->Groups as $v)
		{
			var_dump($v);
		}
	}
	
	public function GenerateGames(Tournament $tournament)
	{
		$arr = array();
		foreach($this->Groups as $k => $v)
		{
			$v->GenerateGames($tournament, $arr);
		}
		return $arr;
	}
};

?>