<?php

/*
 * A class which manages groupstage logic.
 */
class GroupStage extends BaseStage
{
	/**
	 * The name of this stage.
	 *
	 * @var string
	 */
	const Name = "Cup";
	
	/**
	 * The description of this tournament stage.
	 */
	const Description = "Teams are arranged into <b class=\"Highlight\">groups.</b> Within each group, <b>every team plays against every other team.</b>";
	
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
		if($tournament->maxTeams % $tournament->stageConfig->SIZE != 0)
			throw new Exception("Tournament must have proper group sizes based on number of teams. Unequal group sizes not yet implemented!");
			
		$this->numGroups = $tournament->maxTeams / $tournament->stageConfig->SIZE;
		$playoffteams = $tournament->stageConfig->ADVANCE * $this->numGroups;
		
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
	
	public function CreateRounds($tournament)
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
	
	public function IsComplete(Tournament $tournament, &$log)
	{
		foreach($this->Groups as $k => $v)
		{
			$c = $v->IsComplete($tournament);
			if($c == 2)
			{
				$games = $v->ExtraGames();
				throw new Exception("Group $v->name has some blocking ties, handle it! Add extra games: <br><pre>" . var_export($games, true) . "</pre>");
			}
			else if($c == 2)
			{
				$log .= "Ties in Group $v->name...";
			}
		}
	}
};

class GroupStageConfig extends BaseStageConfig
{
	public $SIZE = 0;
	public $ADVANCE = 0;
	public $WinPoints = 3;
	public $TiePoints = 1;

	public function  __wakeup()
	{
		
	}
	
	public static function GetConfigForm()
	{
		return array(
			array("select", array("Max Teams", array_combine(range(2, 128), range(2, 128)))),
			array("select", array("Group Size", array_combine(range(1, 128), range(1, 128)))),
			array("text", "Group Advance"),
			array("text", "Win Points"),
			array("text", "Tie Points")
		);
	}
	
	public static function ValidateArguments($args)
	{
		if(sizeof($args) != 5)
			throw new Exception("Arguments need to have three members!" . sizeof($args));
		
		for($i = 0; $i < 5; $i++)
		{
			if(!isset($args[$i]))
				throw new Exception("Arguments need to have three members!" . sizeof($args));
		}
		
		$maxTeams = $args[0];
		$size = $args[1];
		$adv = $args[2];
		$wPoints = $args[3];
		$tiePoints = $args[4];
		
		if(!is_numeric($maxTeams) || !is_numeric($size) || !is_numeric($adv) || !is_numeric($wPoints) || !is_numeric($tiePoints))
			throw new Exception("Arguments must be numeric...");
		
		if($maxTeams % $size != 0)
			throw new Exception("Tournament must have proper group sizes based on number of teams. Unequal group sizes not yet implemented!");
		
		if($size < $adv)
			throw new Exception("Advancing team must be smaller than group size!");
	}
	
	public function FromConfigForm($vals)
	{
		$this->SIZE = $vals[0];
		$this->ADVANCE = $vals[1];
		$this->WinPoints = $vals[2];
		$this->TiePoints = $vals[3];
	}
	
	public static function GetNameForGroup($index)
	{
		$GroupNames = array(
				"A", "B", "C", "D", "E", "F", "G", "H",	"I",
				"J", "K", "L", "M", "N", "O", "P", "Q", "R",
				"S", "T", "U", "V", "W", "X", "Y", "Z"
		);

		if(isset($GroupNames[$index]))
		{
			return $GroupNames[$index];
		}
		else
		{
			return $index;
		}
	}

	public function GroupStageConfig($groupSize, $groupAdvance, $wPoints=3, $tPoints=1)
	{
		if($groupSize <= $groupAdvance)
			throw new Exception("Group cant be smaller or same size of the advancing teams.");

		$this->SIZE = $groupSize;
		$this->ADVANCE = $groupAdvance;
		$this->WinPoints = $wPoints;
		$this->TiePoints = $tPoints;
	}
}

class Group
{
	private $RealTies = array();
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

		$startindex = $this->index * $tournament->stageConfig->SIZE;
		$limit = $tournament->stageConfig->SIZE;

		for($i = $startindex, $x = 0; $i < $startindex + $limit; $i++, $x++)
		{
			if(isset($tournament->Teams[$i]))
			{
				$this->teams[$x] = new GroupResult($tournament->Teams[$i]);
			}
			else
			{
				$this->Full = false;
				$this->teams[$x] = new GroupResult("Team" . ($i+1));
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

					$t1->points += $winner == $t1->team ? $tournament->stageConfig->WinPoints : 0;
					$t2->points += $winner == $t2->team ? $tournament->stageConfig->WinPoints : 0;
				}
				else
				{
					// Tie give all points
					$t1->tied++;
					$t2->tied++;
						
					$t1->points += $tournament->stageConfig->TiePoints;
					$t2->points += $tournament->stageConfig->TiePoints;
				}

				$t1->vv += $v->Score1 - $v->Score2;
				$t2->vv += $v->Score2 - $v->Score1;
			}
		}


		$this->CalculatePlaces();
		return true;
	}

	private function RealTieRemoval($i, $j)
	{
		// Won is not equal...
		if($i->won != $j->won)
		{
			return ($i->won < $j->won) ? 1 : -1;
		}

		if($i->vv != $j->vv)
		{
			return ($i->vv < $j->vv) ? 1 : -1;
		}

		// Lost is not equal...
		if($i->lost != $j->lost)
		{
			return ($i->lost > $j->lost) ? 1 : -1;
		}

		$iGames = $i->won + $i->lost + $i->tied;
		$jGames = $j->won + $j->lost + $j->tied;

		if($iGames == 0 && $jGames == 0)
		{
			return 1;
		}

		// One team has less games.
		if($iGames != $jGames)
		{
			return ($iGames > $jGames) ? 1 : -1;
		}
		return 0;
	}

	private function comparer($i, $j)
	{
		if($i->points == $j->points)
		{
			$t = $this->RealTieRemoval($i, $j);
			if($t != 0)
			{
				return $t;
			}
				
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
		$this->RealTies = array();
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
					$this->RealTies[] = $place;
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

	/**
	 *
	 * @param Tournament $tournament
	 * @return int 0 - Is Complete, 1 - Some Realties, 2 - Blocking realties
	 */
	public function IsComplete(Tournament $tournament)
	{
		if(sizeof($this->RealTies) != 0)
		{
			$lPlace = sizeof($this->teams)+1;
			foreach ($this->RealTies as $k => $v)
			{
				if($v < $lPlace)
					$lPlace = $v;
			}
				
			if($lPlace <= $tournament->stageConfig->ADVANCE)
			{
				return 2;
			}
			return 1;
		}
		return 0;
	}

	public function ExtraGames()
	{
		if(sizeof($this->RealTies) != 0)
		{
			$games = array();
				
			foreach($this->teams as $k => $team1)
			{
				foreach ($this->RealTies as $k2 => $v2)
				{
					if($team1->place == $v2)
					{
						foreach($this->teams as $k3 => $team2)
						{
							if($k != $k3 && $team2->place == $v2)
							{
								$key = $k < $k3 ? "$k$k3" : "$k3$k";
								if(!isset($games[$key]))
								{
									$games[$key] = array($k, $k3);
								}
							}
						}
					}
				}
			}
			return $games;
		}
		return "";
	}
}

class GroupResult
{
	public $team = null;

	public $won = 0;
	public $lost = 0;
	public $tied = 0;
	public $points = 0;
	public $place = 1;

	public $vv = 0; // The scorediff of this team.

	public $tiedWith = array();

	public function GroupResult($team)
	{
		$this->team = $team;
	}

	public function ResetScore()
	{
		$this->won = 0;
		$this->lost = 0;
		$this->tied = 0;
		$this->points = 0;
		$this->place = 0;
	}
}

?>