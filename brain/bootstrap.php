<?php

// Set working directory to home_dir.

/*
 * Require classes.
 */
require_once("config/config.php");

require_once("brain/class.ApiHelper.php");
require_once("brain/class.API.php");
require_once("brain/class.DataMan.php");
require_once("brain/class.DataManager.php");
require_once("brain/class.Game.php");
require_once("brain/class.GameResult.php");
require_once("brain/class.GamesManager.php");
require_once("brain/class.Group.php");
require_once("brain/class.GroupConfigDatabase.php");
require_once("brain/class.GroupResult.php");
require_once("brain/class.GroupStage.php");
require_once("brain/class.GroupStageConfig.php");
require_once("brain/class.Player.php");
require_once("brain/class.PlayerManager.php");
require_once("brain/class.SeedManager.php");
require_once("brain/class.SvgHelper.php");
require_once("brain/class.Team.php");
require_once("brain/class.TeamManager.php");
require_once("brain/class.Tournament.php");
require_once("brain/class.TournamentDatabase.php");
require_once("brain/class.TournamentRenderer.php");
require_once("brain/class.TourneyManager.php");
require_once("brain/class.UserManager.php");

/**
 * Require libs.
 */

require_once("libs/All.php");

class BootStrap
{
	public $Smarty = null;
	public $UserManager = null;
	
	/**
	 * 
	 * @var TourneyManager
	 */
	public $Datamanager = null;
	
	public function BootStrap()
	{
		session_start();
		ob_start();
		
		$this->Smarty = new Smarty();
		//$this->Smarty->setCacheLifetime(1);
		$this->Smarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
		
		$this->DeployConfig = new DeploymentConfigLoader();
		
		$this->Smarty->Assign("DeployConfig", $this->DeployConfig);
	}
	
	public function Strap()
	{	
		if(!constant("IS_INSTALLED"))
		{
			$this->DeployConfig->HandleInstall();
			
			$this->Smarty->display("install.tpl");
			
			//die("INSTALLPLX!");
		}
		else
		{
			$this->Datamanager = new TourneyManager(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
			$this->UserManager = new UserManager($this->Datamanager);
		}
	}
	
	public function Detach()
	{
		if($this->UserManager)
			$this->UserManager->__destruct();
		
		unset($this->Datamanager);
		ob_flush();
	}
}

?>