<?php

/**
 * Require the helper classes.
 */
require_once("class.Game.php");
require_once("class.GameStage.php");
require_once("class.GameResult.php");
require_once("class.Group.php");
require_once("class.GroupStage.php");
require_once("class.GroupStageConfig.php");
require_once("class.Team.php");
require_once("class.Player.php");

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
 * Tournament Type GroupStage and Playoffs
 * @var bitfield[0]
 */
define("TYPE_GROUPSTAGE", 1 << 0); // Groups only

/**
 * Tournament Type PlayOffs Only
 * @var bitfield[0]
 */
define("TYPE_PLAYOFFS", 0 << 0); // Only playoffs

/**
 * Tournament Uses ThirdPlaceMatch
 * @var bitfield[2]
 */
define("TYPE_THIRDPLACEMATCH", 1 << 2); // Use Thrid Place Match

/**
 * Tournament Type SingleElimination
 * @var bitfield
 */

/**
 * Single or double elimination(use loserbracket or not)
 * @var bitfield[3]
 */
define("TYPE_SINGLEELIMINATION", 0 << 3);

/**
 * Single or double elimination(use loserbracket or not)
 * @var bitfield[3]
 */
define("TYPE_DOUBLEELIMINATION", 1 << 3);

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
	 * @var Game
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
	 * The maximum number of teams.
	 * @var int
	 */
	public $maxTeams = 0;
	
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
	 * @var GroupStageConfig
	 */
	public $GroupStageConfig = null;
	
	/**
	 * 
	 * @param string $name
	 * @param Game $game
	 * @param int $status
	 * @param bitfield $type
	 * @param int $maxTeams
	 * @param array(Team) $teams
	 * @param array(GameResult) $games
	 * @param GroupStageConfig $groupStageConfig
	 * @throws Exception
	 */
	public function Tournament($name, Game $game, $status, $type, $maxTeams, $teams, $games, $groupStageConfig=null)
	{
		// Debug function for coding.
		//self::BitDebug($type);
		
		// Set some variables.
		$this->NAME = $name;
		$this->GAME = $game;
		$this->STATUS = $status;
		$this->TYPE = $type;
		
		$this->maxTeams = $maxTeams;
		
		$this->Teams = $teams;
		$this->Games = $games;
		
		$this->GroupStageConfig = $groupStageConfig;
		
		if($type & TYPE_GROUPSTAGE)
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
		}
	}
	
	/**
	 * Adds a team to the list.
	 * @param Team $team
	 */
	public function AddTeam(Team $team)
	{
		if(isset($team->Seed[$this->ID]) && $team->Seed[$this->ID] !== FALSE)
		{
			$this->Teams[$team->Seed[$this->ID]] = $team;
		}
		else
			$this->Teams[] = $team;
	}
	
	public function CreateGroups()
	{
		if($this->TYPE & TYPE_GROUPSTAGE)
		{
			$this->Stages["groups"]->CreateGroups($this);
		}
	}
	
	/**
	 * Reseed the teams.
	 */
	public function Seed()
	{
		if(shuffle($this->Teams))
		{
			$this->CreateGroups();
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
		if($this->TYPE & TYPE_GROUPSTAGE)
		{
			return $this->Stages["groups"]->GenerateGames($this);
		}
		return array();
	}
	public function CalculateFromResults()
	{
		if($this->TYPE & TYPE_GROUPSTAGE)
		{
			$this->Stages["groups"]->CalculateFromResults($this);
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
	
	
	public static function BitDebug($type)
	{
		if($type & TYPE_GROUPSTAGE)
			print "TYPE_GROUPSTAGE<br>";
		if(!($type & TYPE_GROUPSTAGE))
			print "TYPE_PLAYOFFS<br>";
		if($type & TYPE_THIRDPLACEMATCH)
			print "TYPE_THIRDPLACEMATCH<br>";
		if(!($type & TYPE_DOUBLEELIMINATION))
			print "TYPE_SINGLEELIMINATION<br>";
		if($type & TYPE_DOUBLEELIMINATION)
			print "TYPE_DOUBLEELIMINATION<br>";
	}
};

?>