<?php

/**
 * Class which manages the loading of our games. Implements singleton design pattern.
 * 
 * @author TH3F0X
 */
class Games
{
	/**
	 * Singelton instance.
	 * @var Stages
	 */
	protected static $_instance = null;
	
	/**
	 * Returns an instance of Games
	 *
	 * Singleton pattern implementation
	 *
	 * @return Games
	 */
	public static function instance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function LoadGame($gameName)
	{
		// Test if file exists
		$fName = sprintf("libs/TournamentGames/game.%s.php", $gameName);
		
		if(file_exists($fName))
		{
			include_once($fName);
			if(class_exists($gameName))
			{
				return $gameName;
			}
		}
		throw new Exception("Game $gameName not yet implemented!");
		return null;
	}
	
	/**
	 * Get a BaseGame based on the name provided.
	 * 
	 * @param string $gameName
	 * @param Tournament $Tournament
	 * @return null/BaseGame
	 */
	public function GetGame($gameName)
	{
		if($this->LoadGame($gameName) != null)
		{
			return new $gameName();
		}
		return null;
	}
	
	public function GetAll()
	{
		$ret = array();
		
		$files = scandir("libs/TournamentGames/");
		foreach($files as $fName)
		{			
			if($fName == "." || $fName == "..")
				continue;	
			
			if(substr($fName, 0, 5) == "game.")
			{
				include_once("libs/TournamentGames/$fName");
				$cName = substr($fName, 5, -4);
				if(class_exists($cName))
				{
					$ret[] = array($cName, $cName::LName, $cName::SName);
				}
			}
		}
		
		return $ret;
	}
}

?>