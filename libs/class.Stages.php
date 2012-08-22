<?php

/**
 * Class which manages the loading of our stages. Implements singleton design pattern.
 * 
 * @author TH3F0X
 */
class Stages
{
	/**
	 * Singelton instance.
	 * @var Stages
	 */
	protected static $_instance = null;
	
	/**
	 * Returns an instance of Stages
	 *
	 * Singleton pattern implementation
	 *
	 * @return Stages
	 */
	public static function instance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function LoadStage($stageName)
	{
		// Test if file exists
		$fName = sprintf("libs/TournamentStages/stage.%s.php", $stageName);
		
		if(file_exists($fName))
		{
			include_once($fName);
			if(class_exists($stageName))
			{
				return $stageName;
			}
		}
		throw new Exception("Stage $stageName not yet implemented!");
		return null;
	}
	
	/**
	 * Get a TournamentStage based on the name provided.
	 * 
	 * @param string $stageName
	 * @param Tournament $Tournament
	 * @return null/BaseStage
	 */
	public function GetStage($stageName, Tournament $Tournament)
	{
		if($this->LoadStage($stageName) != null)
		{
			return new $stageName($Tournament);
		}
		return null;
	}
	
	public function GetConfig($stageName)
	{
		return $this->LoadStage($stageName) != null;
	}
	
	public function GetAllStages()
	{
		$ret = array();
		
		$files = scandir("libs/TournamentStages/");
		foreach($files as $fName)
		{
			if($fName == "." || $fName == "..")
				continue;
			
			if(substr($fName, 0, 6) == "stage.")
			{
				include_once("libs/TournamentStages/$fName");
				$cName = substr($fName, 6, -4);
				if(class_exists($cName))
				{
					$ret[] = array($cName, $cName::Name, $cName::Description);
				}
			}
		}
		
		return $ret;
	}
}

?>