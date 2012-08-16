<?php

class GroupStageConfig
{
	public $uniqueID = 0;
	public $tournamentID = 0;
	
	public $SIZE = 0;
	public $ADVANCE = 0;
	public $WinPoints = 3;
	public $TiePoints = 1;
	
	public static $SortHelper_BetweenGame = '0';
	public static $SortHelper_WinsCount = '1';
	public static $SortHelper_ScoreDifference = '2'; // Games where people can score points in game(football)
	//public static $SortHelper_
	
	public static function GetNameForGroup($index)
	{
		$GroupNames = array(
				"A",
				"B",
				"C",
				"D",
				"E",
				"F",
				"G",
				"H",
				"I",
				"J",
				"K",
				"L",
				"M",
				"N",
				"O",
				"P",
				"Q",
				"R",
				"S",
				"T",
				"U",
				"V",
				"W",
				"X",
				"Y",
				"Z"
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
};

?>