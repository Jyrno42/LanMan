<?php

class GameResult
{
	public $played = false;
	
	public $uniqueID = FALSE;
	public $tournamentID = 0;
	
	public $Team1 = null;
	public $Team2 = null;

	public $Score1 = 0;
	public $Score2 = 0;
	
	/**
	 * Is this the first game between teams, this should make creating home/away games in groups available.
	 * TODO: GamesInGroups to GroupStageConfig.
	 * 
	 * @var int
	 */
	public $GameNumber = 0;
	
	public function GetUniqueHash()
	{
		$v1 = $this->Team1->uniqueID > $this->Team2->uniqueID ? $this->Team1->uniqueID : $this->Team2->uniqueID;
		$v2 = $this->Team1->uniqueID < $this->Team2->uniqueID ? $this->Team1->uniqueID : $this->Team2->uniqueID; 
		return sprintf("%d|%d|%d|%d", $this->tournamentID, $v1, $v2, $this->GameNumber);
	}
	
	/**
	 * Create a new gameresult and set its data...
	 * @param Team $team1
	 * @param Team $team2
	 * @param int $score1
	 * @param int $score2
	 */
	public function GameResult(Team $team1, Team $team2, $score1, $score2, $played=false, $tid=0)
	{
		$this->Team1 = $team1;
		$this->Team2 = $team2;
		$this->Score1 = $score1;
		$this->Score2 = $score2;
		$this->played = $played;
		$this->tournamentID = $tid;
	}
	
	public function Team1()
	{
		return $this->Score1 > $this -> Score2;
	}
	
	public function Team2()
	{
		return $this->Score1 < $this -> Score2;
	}
	
	/**
	 * Returns the winning team or null if the game ended with a tie.
	 * @return mixed Team/null
	 */
	public function Result()
	{
		return $this->Team1() ? $this->Team1 : ($this->Team2() ? $this->Team2 : null); 
	}
};

?>