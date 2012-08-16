<?php

class GroupResult
{
	public $team = null;
	
	public $won = 0;
	public $lost = 0;
	public $tied = 0;
	public $points = 0;
	public $place = 1;
	
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