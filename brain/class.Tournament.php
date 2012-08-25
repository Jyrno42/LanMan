<?php

/**
 * Tournament Status Added
 * @var define
 */
define("STATUS_ADDED", 0);

/**
 * Tournament Status Registering
 * @var define
 */
define("STATUS_REGISTERING", 1);

/**
 * Tournament Status Seeding
 * @var define
 */
define("STATUS_SEEDING", 2);

/**
 * Tournament Status Generating Games
 * @var define
 */
define("STATUS_GAMES", 3);

/**
 * Tournament Status Live
 * @var define
 */
define("STATUS_LIVE", 4);

/**
 * Tournament Status Live
 * @var define
 */
define("STATUS_COMPLETED", 5);

/**
 * Class that contains all tournament backbone logic.
 * @author TH3F0X
 * @category Tournaments
 */
class Tournament
{
	/**
	 * The unique ID of this tournament.
	 * @var int
	 */
	public $ID = 0;
	
	/**
	 * Array of statuses and their labels.
	 * @var array
	 */
	public $STATUSES = array(
	
		0 => "Added",
		1 => "Registering",
		2 => "Seeding",
		3 => "Generating Games",
		4 => "Ongoing",
		5 => "Completed",
	
	);

	/**
	 * The name of this tournament.
	 * @var string
	 */
	public $NAME = "";
	
	/**
	 * The game for this tournament.
	 * @var BaseGame
	 */
	public $GAME = null;
	
	/**
	 * The status for this tournament.
	 * @var int
	 */
	public $STATUS = STATUS_ADDED;
	
	/**
	 * The type bitfield for this tournament which contains configuration data.
	 * @var int
	 */
	public $TYPE = 0;
	
	/**
	 * The array which contains the stages of our tournament.
	 * @var array(GameStage)
	 */
	public $Stages = array();
	
	/**
	 * The array which contains references to all games already done in this tournament.
	 * @var array(GameResult)
	 */
	public $Games = array();
	
	/**
	 * The array which contains references to all teams in this tournament.
	 * @var array(Team)
	 */
	public $Teams = array();
	
	/**
	 * Configuration for group stage.
	 * @var BaseStageConfig
	 */
	public $stageConfig = null;
	
	/**
	 * 
	 * @param string $name
	 * @param string $game
	 * @param int $status
	 * @param string $type
	 * @param array(Team) $teams
	 * @param array(GameResult) $games
	 * @param string $stageConfig
	 * @param mixed null/array $gameconfig
	 * @throws Exception
	 */
	public function Tournament($name, $game, $status, $type, $teams, $games, $stageConfig, $gameConfig)
	{
		// Set some variables.
		$this->NAME = $name;
		$this->STATUS = $status;
		$this->TYPE = $type;
		
		$this->Teams = $teams;
		$this->Games = $games;
		
		Stages::instance()->LoadStage($type);
		Games::instance()->LoadGame($game);

		if($gameConfig == null || !is_array($gameConfig))
			throw new Exception("Bad GameConfig provided!");
		if($stageConfig == null || !is_array($stageConfig))
			throw new Exception("Bad StageConfig provided!");
		
		$this->GAME = Games::instance()->GetGame($game);
		$this->GAME->FromConfigForm($gameConfig);
	
		$this->stageConfig = Stages::instance()->GetConfig($type);
		$this->stageConfig->ValidateArguments($stageConfig);
		$this->stageConfig->FromConfigForm($stageConfig);
		
		$this->Stages[] = Stages::instance()->GetStage($type, $this);
		
		/*if($type & TYPE_GROUPSTAGE)
		{ 
			if($groupStageConfig == null)
				throw new Exception("Tournament must have a groupstageconfig if group stage is used.");
			
			$this->Stages["groups"] = new GroupStage($this);
		}
		else
		{
			throw new Exception("Not yet implemented.");
			
			if(!($type & TYPE_DOUBLEELIMINATION)) // SingleElimination
			{
				// http://en.wikipedia.org/wiki/Single-elimination_tournament
				if(!(($maxTeams != 0) && (($maxTeams & ($maxTeams - 1)) == 0)))
					throw new Exception("Number of teams must be a power of two");
			
				$rounds = log($maxTeams, 2); 
			
				print "PLAYOFFS ONLY $rounds";
				
				// Create a new playoffs stage and let it know about the rules we want to use by giving it the bitmask.
				//$this->Stages["playoffs"] = new PlayOffs($type);
			}
			else
			{
				//
				throw new Exception("Not yet implemented.");
			}
		}*/
	}
	
	/**
	 * Adds a team to the list.
	 * @param Team $team
	 */
	public function AddTeam(Team $team)
	{
		if(isset($team->Seeds[$this->ID]) && $team->Seeds[$this->ID] != -1)
		{
			$this->Teams[$team->Seeds[$this->ID]] = $team;
		}
		else
			$this->Teams[] = $team;
	}
	
	public function CreateRounds()
	{
		foreach($this->Stages as $k => $v)
		{
			$v->CreateRounds($this);
		}
	}

	/**
	 * Swap two array member positions.
	 * 
	 * @param mixed $a
	 * @param mixed $b
	 */
	function swap(&$a, &$b) 
	{
		list($a, $b) = array($b, $a); 
	}
	
	/**
	 * Shuffles the provided array keeping values that are not provided in keys at their place.
	 * 
	 * @param array $arr The array we want to shuffle.
	 * @param array $keys The keys we want to use in shuffle
	 * @return boolean
	 */
	private function suffleSome(&$arr, $keys)
	{
		$len = count($keys); // Array size
		for($i = 0; $i < $len; $i++)  // For each array member
		{
			$j = rand(1, $len) - 1; // Get 1 random member in array
			$this->swap($arr[$keys[$i]], $arr[$keys[$j]]);
		}
		return true;
	}
	
	/**
	 * Reseed the teams.
	 */
	public function Seed()
	{
		// Store the keys we want to suffle.
		$keys = array();
		
		// Get all keys that dont have seeds already.
		foreach($this->Teams as $k => $v)
		{
			if($v->Seeds[$this->ID] == -1)
			{
				$keys[] = $k;
			}
		}
		
		if($this->suffleSome($this->Teams, $keys))
		{
			$this->CreateRounds();
		}
	}
	
	public function GetGamesForGroup($group)
	{
		$ret = array();
		foreach($this->Games as $k => $v)
		{
			if($group->HasTeam($v->Team1) && $group->HasTeam($v->Team2))
			{
				$ret[] = $v;
			}
		}
		return $ret;
	}
	
	public function GetGameBetweenTeams($team1, $team2)
	{
		foreach($this->Games as $k => $v)
		{
			if(($v->Team1 == $team1 && $v->Team2 == $team2) || ($v->Team1 == $team2 && $v->Team2 == $team1))
			{
				return $v;
			}
		}
		return null;		
	}
	
	public function GenerateGames()
	{
		foreach($this->Stages as $k => $v)
		{
			$v->GenerateGames($this);
		}
		return array();
	}
	public function CalculateFromResults()
	{
		foreach($this->Stages as $k => $v)
		{
			$v->CalculateFromResults($this);
		}
	}
	
	public function AddGame(GameResult $game)
	{
		
		if($game->uniqueID !== FALSE)
		{
			$this->Games[$game->uniqueID] = $game;
		}
		else
			$this->Games[] = $game;
	}
	
	public function IsCompleted()
	{
		try 
		{
			$log = "";
			$this->CheckForCompletion($log);
			return $log;
		}
		catch(Exception $e)
		{
			return "Problem: " . $e->getMessage();
		}
	}
	
	/**
	 * 
	 * @return boolean
	 */
	private function GamesDone()
	{
		$ret = true;
		foreach ($this->Games as $k => $v)
		{
			if(!$v->played)
			{
				$ret = false;
				break;
			}
		}
		return $ret;
	}
	
	private function CheckForCompletion(&$log)
	{
		// Bad Status
		if($this->STATUS != STATUS_LIVE)
			throw new Exception("This tournament is not in the right status.");

		if(!$this->GamesDone())
			throw new Exception("This tournament has some more games to finish...");
		
		foreach($this->Stages as $k => $v)
		{
			$v->IsComplete($this, $log);
		}
	}
};

?>