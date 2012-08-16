<?php

require_once("class.GroupResult.php");

class Group
{
	private $Full = true;
	
	public $index = 0;
	public $name = "0";
	
	public $teams = array();
	
	public function Group($index, $teams, Tournament $tournament)
	{
		$this->index = $index;
		$this->name = GroupStageConfig::GetNameForGroup($index);
		$this->CreateGroup($tournament);
		$this->CalculateFromResults($tournament);
	}
	
	public function CreateGroup($tournament)
	{
		$this->Full = true;
		$this->teams = array();
		
		$startindex = $this->index * $tournament->GroupStageConfig->SIZE;
		$limit = $tournament->GroupStageConfig->SIZE;
		
		for($i = $startindex, $x = 0; $i < $startindex + $limit; $i++, $x++)
		{
			if(isset($tournament->Teams[$i]))
			{
				$this->teams[$x] = new GroupResult($tournament->Teams[$i]);
			}
			else
			{
				$this->Full = false;
				$this->teams[$x] = new GroupResult("Team$i");
			}
		}
	}
	
	public function HasTeam($team)
	{
		foreach($this->teams as $k => $v)
		{
			if($v->team == $team)
				return $v;
		}
		return false;
	}
	
	/**
	 * This function calculates points for given team based on the completed games.
	 */
	public function CalculateFromResults(Tournament $tournament)
	{
		foreach($this->teams as $k => $v)
		{
			$v->ResetScore();
		}
		
		if(!$this->Full)
		{
			foreach($this->teams as $k => $v)
			{
				$v->place = 1;
			}
			return false;
		}
		
		foreach($tournament->Games as $k => $v)
		{
			if(!$v->played)
				continue;
			
			if(($t1 = $this->HasTeam($v->Team1)) !== false && ($t2 = $this->HasTeam($v->Team2)) !== false)
			{				
				$winner = $v->Result();
				if($winner != null)
				{
					$t1->won += $winner == $t1->team ? 1 : 0; 
					$t2->won += $winner == $t2->team ? 1 : 0;
					$t1->lost += $winner != $t1->team ? 1 : 0; 
					$t2->lost += $winner != $t2->team ? 1 : 0;

					$t1->points += $winner == $t1->team ? $tournament->GroupStageConfig->WinPoints : 0;
					$t2->points += $winner == $t2->team ? $tournament->GroupStageConfig->WinPoints : 0;
				}
				else
				{
					// Tie give all points
					$t1->tied++;
					$t2->tied++;
					
					$t1->points += $tournament->GroupStageConfig->TiePoints;
					$t2->points += $tournament->GroupStageConfig->TiePoints;
				}
			}
		}
		
		
		$this->CalculatePlaces();
		return true;
	}
	
	private function comparer($i, $j)
	{
		if($i->points == $j->points)
		{
			// TODO: Other means of realtie removal.
			$i->tiedWith[$j->team->Abbrevation] = $j;
			foreach($j->tiedWith as $k => $v)
			{
				if($v->team->Abbrevation != $i->team->Abbrevation)
				{
					$i->tiedWith[$k] = $v;
				}
			}
			$j->tiedWith[$i->team->Abbrevation] = $i;
			foreach($i->tiedWith as $k => $v)
			{
				if($v->team->Abbrevation != $j->team->Abbrevation)
				{
					$j->tiedWith[$k] = $v;
				}
			}
			return 0;
		}
		return ($i->points < $j->points) ? 1 : -1;
	}
	
	private function CalculatePlaces()
	{
		$temp = $this->teams; 
		uasort($temp, array($this, "comparer"));
		
		$place = 1;
		foreach($temp as $k => $v)
		{
			if($v->place == 0)
			{
				$v->place = $place;
				if(sizeof($v->tiedWith) > 0)
				{
					foreach($v->tiedWith as $k2 => $v2)
					{
						$v2->place = $place;
					}
					$place += sizeof($v->tiedWith);
				}
				$place++;
			}
		}
	}
	
	public function GenerateGames(Tournament $tournament, &$ret)
	{
		$games = array();
		
		foreach($this->teams as $k => $v)
		{
			foreach ($this->teams as $k2 => $v2)
			{
				if($k == $k2)
					continue;
				
				$key = $k < $k2 ? "$k$k2" : "$k2$k";
				if(!isset($games[$key]))
					$games[$key] = array($v, $v2);
			}			
		}
		
		foreach($games as $k => $v)
		{
			$ret[] = new GameResult($v[0]->team, $v[1]->team, 0, 0, false, $tournament->ID);
		}
	}
}

?>