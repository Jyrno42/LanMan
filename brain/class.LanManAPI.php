<?php

class LanManAPI extends API
{
	/**
	 * The lanman bootstrap we use.
	 * @var BootStrap
	 */
	private $LanMan = null;
	
	public function LanManAPI()
	{
		$this->AddAction("CreateTournament", array($this, "CreateTournament"));
		$this->AddAction("TypeBuilder", array($this, "TypeBuilder"));

		$this->AddAction("UpdateTournament", array($this, "UpdateTournament"));
		$this->AddAction("DeleteTournament", array($this, "DeleteTournament"));
		$this->AddAction("AddGameType", array($this, "AddGameType"));
		$this->AddAction("GetTeamInfo", array($this, "GetTeamInfo"));
		$this->AddAction("GetTeam", array($this, "GetTeam"));
		$this->AddAction("GetPlayers", array($this, "GetPlayers"));
		$this->AddAction("OpenRegistration", array($this, "OpenRegistration"));
		$this->AddAction("SeedComplete", array($this, "SeedComplete"));
		$this->AddAction("AddTeam", array($this, "AddTeam"));
		$this->AddAction("RegisterTeam", array($this, "RegisterTeam"));
		$this->AddAction("UpdateTeam", array($this, "UpdateTeam"));
		$this->AddAction("DeleteTeam", array($this, "DeleteTeam"));
		$this->AddAction("AddPlayer", array($this, "AddPlayer"));
		$this->AddAction("Render", array($this, "Render"));
		
		$this->AddAction("ShowActions", array($this, "ShowActions"), true);
	}
	
	public function SetLanMan($LanMan)
	{
		$this->LanMan = $LanMan;
	}
	
	public function CreateTournament()
	{
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
		
		$name = ApiHelper::GetParam("name", true);
		ApiHelper::StringMin($name, 3, "name");
		
		$gameN = ApiHelper::GetParam("Game", true);
		$game = $this->LanMan->Datamanager->GetGameType($gameN);
		
		$type = ApiHelper::GetParam("type", true);
		$maxTeams = ApiHelper::GetParam("maxTeams", true, "int");
		
		$groupStageConfig = null;
		if($type & TYPE_GROUPSTAGE)
		{
			$gSize = ApiHelper::GetParam("groupSize", true);
			$gAdvance = ApiHelper::GetParam("groupAdvance", true);
				
			$groupStageConfig = new GroupStageConfig($gSize, $gAdvance);
		}
		
		$tourney = new Tournament($name, $game, STATUS_ADDED, $type, $maxTeams, array(), array(), $groupStageConfig);
		$this->LanMan->Datamanager->tourneyTable->stdItems[] = $tourney;
		return array("result" => "Created tournament!");
	}
	
	public function TypeBuilder()
	{
		$groups = ApiHelper::GetParam("Groups", true);
		$tPlace = ApiHelper::GetParam("ThirdPlaceMatch");
		$dElim = ApiHelper::GetParam("DoubleElimination");
		
		$type = $groups ? TYPE_GROUPSTAGE : TYPE_PLAYOFFS;
		
		if($tPlace)
			$type = $type | TYPE_THIRDPLACEMATCH;
		if($dElim)
			$type = $type | TYPE_DOUBLEELIMINATION;
		
		return array("type" => $type);
	}
	
	public function ShowActions()
	{
		$arr = array();
		$arr["actions"] = array();
		
		foreach($this->Actions as $k => $v)
		{
			$arr["actions"][] = $k;
		}
		return $arr;
	}
	
	public function UpdateTournament()
	{
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
	
		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $this->LanMan->Datamanager->tourneyTable->GetTournament($hash);
	
		$name = ApiHelper::GetParam("name", false, null, false);
		if($name !== false)
		{
			ApiHelper::StringMin($name, 3, "name");
			$this->LanMan->Datamanager->tourneyTable->stdItems[$tournament->ID]->NAME = $name;
		}
	
		// In add status, can modify type/game
		if($tournament->STATUS == STATUS_ADDED)
		{
			$game = ApiHelper::GetParam("Game", false, null, false);
			if($game !== false)
			{
				$this->LanMan->Datamanager->tourneyTable->stdItems[$tournament->ID]->GAME = $this->LanMan->Datamanager->GetGameType($game);
			}
	
			$type = ApiHelper::GetParam("type", false, null, false);
			if($type !== false)
			{
				$this->LanMan->Datamanager->tourneyTable->stdItems[$tournament->ID]->TYPE = $type;
			}
		}
	
		// Is groupstage and we can modify config.
		if($tournament->STATUS < STATUS_SEEDING)
		{
			$maxTeams = ApiHelper::GetParam("maxTeams", false, null, false);
			if($maxTeams !== false)
			{
				$this->LanMan->Datamanager->tourneyTable->stdItems[$tournament->ID]->maxTeams = $maxTeams;
			}
				
			if($tournament->TYPE & TYPE_GROUPSTAGE)
			{
				$groupSize = ApiHelper::GetParam("groupSize", false, null, false);
				if($groupSize !== false)
				{
					$this->LanMan->Datamanager->tourneyTable->groupConfTable->stdItems[$tournament->GroupStageConfig->uniqueID]->SIZE = $groupSize;
				}
	
				$groupAdvance = ApiHelper::GetParam("groupAdvance", false, null, false);
				if($groupAdvance !== false)
				{
					$this->LanMan->Datamanager->tourneyTable->groupConfTable->stdItems[$tournament->GroupStageConfig->uniqueID]->ADVANCE = $groupAdvance;
				}
			}
		}
	
		return array("result" => "Tournament updated! ");
	}
	public function DeleteTournament()
	{
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
	
		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $this->LanMan->Datamanager->tourneyTable->GetTournament($hash);
	
		unset($this->LanMan->Datamanager->tourneyTable->stdItems[$tournament->ID]);
		return array("result" => "Tournament deleted!");
	}
	public function AddGameType()
	{
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
	
		$tag = ApiHelper::GetParam("tag", true);
		ApiHelper::StringMin($tag, 3, "tag");
		if(isset($this->LanMan->Datamanager->CustomGames[$tag]))
		{
			throw new Exception("Tag must be unique!");
		}
	
		$bClass = ApiHelper::GetParam("Game", true);
	
		if(!isset($this->LanMan->Datamanager->GameClasses[$bClass]))
		{
			throw new Exception("Invalid baseClass provided!");
		}
	
		$args = ApiHelper::GetParam($bClass ."_args", true);
	
		$bClass::ValidateArguments($args);
	
		$this->LanMan->Datamanager->gameTypesManager->Insert(
				array(
						"tag" => $tag,
						"className" => $bClass,
						"arguments" => implode(",", $args)
				)
		);
		return array("result" => "Done");
	}
	public function GetTeamInfo()
	{
		$id = ApiHelper::GetParam("teamID", true);
	
		$team = $this->LanMan->Datamanager->teamMan->GetTeam($id);
	
		$smarty = new Smarty;
		$smarty->assign("Team", $team);
		$smarty->display("teaminfo.tpl");
		return null;
	}
	public function GetTeam()
	{
		$id = ApiHelper::GetParam("teamID", true);
		return $this->LanMan->Datamanager->teamMan->GetTeam($id);
	}
	public function GetPlayers()
	{
		$id = ApiHelper::GetParam("teamID", true);
		$team = $this->LanMan->Datamanager->teamMan->GetTeam($id);
		return $team->Players;
	}
	public function OpenRegistration()
	{
		//ApiHelper::RequestValidate($_PRIVATEKEY, $_PUBLICKEY);
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
	
		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $this->LanMan->Datamanager->tourneyTable->GetTournament($hash);
		$this->LanMan->Datamanager->RegistrationMode($tournament);
		return array("result" => "Registration opened!");
	}
	public function SeedComplete()
	{
		//ApiHelper::RequestValidate($_PRIVATEKEY, $_PUBLICKEY);
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
	
		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $this->LanMan->Datamanager->tourneyTable->GetTournament($hash);
		$this->LanMan->Datamanager->SeedComplete($tournament);
		return array("result" => "Seeding done!");
	}
	public function AddTeam()
	{
		//ApiHelper::RequestValidate($_PRIVATEKEY, $_PUBLICKEY);
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
	
		$teamname = ApiHelper::GetParam("name", true);
		ApiHelper::StringMin($teamname, 3, "name");
		ApiHelper::StringMax($teamname, 32, "name");
		
		$abbr = ApiHelper::GetParam("abbrevation", true);
		ApiHelper::StringMin($abbr, 2, "abbrevation");
		ApiHelper::StringMax($abbr, 6, "abbrevation");
	
		$team = new Team(0, $teamname, $abbr, array());
		$this->LanMan->Datamanager->teamMan->stdItems[] = $team;
	
		return array("result" => "Team added!");
	}
	public function RegisterTeam()
	{
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
	
		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $this->LanMan->Datamanager->tourneyTable->GetTournament($hash);
	
		if($tournament->STATUS != STATUS_REGISTERING)
			throw new Exception("Cant add a team to a tournament that is not in registering mode.");
	
		$TeamID = ApiHelper::GetParam("TeamID", true);
		$team = $this->LanMan->Datamanager->teamMan->GetTeam($TeamID);
	
		if(!$this->LanMan->Datamanager->TeamCanRegisterToTournament($tournament, $team))
		{
			throw new Exception("Team is not eligible for this tournament!");
		}
	
		$seed = new Seed();
		$seed->tournamentId = $tournament->ID;
		$seed->teamID = $team->uniqueID;
		$this->LanMan->Datamanager->seedMan->stdItems[] = $seed;
	
		return array("result" => "Team registered for this tournament!");
	}
	public function UpdateTeam()
	{
		if(!$this->LanMan->UserManager->User->Valid)
			throw new Exception("Bad auth key.");
	
		$TeamID = ApiHelper::GetParam("TeamID", true);
		$team = $this->LanMan->Datamanager->teamMan->GetTeam($TeamID);
	
		if(!$this->LanMan->UserManager->Can("manage") && !$this->LanMan->UserManager->User->MySql->UserID != $team->OwnerID)
			throw new Exception("Cant do this to that team.");
	
		$teamname = ApiHelper::GetParam("name", true);
		ApiHelper::StringMin($teamname, 3, "name");
		ApiHelper::StringMax($teamname, 32, "name");
		$abbr = ApiHelper::GetParam("abbrevation", true);
		ApiHelper::StringMin($abbr, 2, "abbrevation");
		ApiHelper::StringMax($abbr, 6, "abbrevation");
	
		$this->LanMan->Datamanager->teamMan->stdItems[$team->uniqueID]->Name = $teamname;
		$this->LanMan->Datamanager->teamMan->stdItems[$team->uniqueID]->Abbrevation = $abbr;
	
		return array("result" => "Team updated!");
	}
	public function DeleteTeam()
	{
		if(!$this->LanMan->UserManager->User->Valid)
			throw new Exception("Bad auth key.");
	
		$TeamID = ApiHelper::GetParam("TeamID", true);
		$team = $this->LanMan->Datamanager->teamMan->GetTeam($TeamID);
	
		if(!$this->LanMan->UserManager->Can("manage") && !$this->LanMan->UserManager->User->MySql->UserID != $team->OwnerID)
			throw new Exception("Cant do this to that team.");
	
		$confirm = ApiHelper::GetParam("validate", true);
		if($confirm != "KUSTUTA")
		{
			throw new Exception("Must write KUSTUTA to the field!");
		}
	
		unset($this->LanMan->Datamanager->teamMan->stdItems[$team->uniqueID]);
	
		return array("result" => "Deleted!");
	}
	public function AddPlayer()
	{
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
	
		$teamID = ApiHelper::GetParam("teamID", true);
		$team = $this->LanMan->Datamanager->teamMan->GetTeam($teamID);
	
		$pName = ApiHelper::GetParam("name", true);
	
		$player = new Player(0, $pName);
		$player->teamID = $teamID;
	
		$this->LanMan->Datamanager->teamMan->PlayerManager->stdItems[] = $player;
		return array("result" => "Done!");
	}
	public function Render()
	{
		$hash = ApiHelper::GetParam("hash", true);
	
		$tourney = $this->LanMan->Datamanager->tourneyTable->GetTournament($hash);
		$smarty = new Smarty;
	
		$render = new TournamentRenderer($tourney, $smarty);
		$render->GroupRenderType = ApiHelper::GetParam("RenderType", false, null, TournamentRenderer::NO_GAMES);
		$render->GroupWidth = ApiHelper::GetParam("GroupWidth", false, null, "300px");
		$render->ShowName = ApiHelper::GetParam("ShowName", false, null, false);
		$render->GroupNameInTable = ApiHelper::GetParam("NameInTable", false, null, true);
		$render->Type = ApiHelper::GetParam("type", false, null, "TABLE") == "SVG" ? TournamentRenderer::SVG : TournamentRenderer::TABLE;
	
		if($render->Type == TournamentRenderer::TABLE)
			throw new Exception("Table rendering is deprecated!");
	
		$showGroup = ApiHelper::GetParam("ShowGroup", false, null, FALSE);
		if($showGroup !== FALSE)
		{
			if(!is_array($showGroup) && strpos($showGroup, ",") >= 0)
			{
				$showGroup = explode(",", $showGroup);
			}
			$render->ShowGroup($showGroup);
		}
	
		$getUrl = ApiHelper::GetParam("GetURL", false, null, false);
		if($getUrl !== false)
		{
			$fp = strpos($_SERVER['QUERY_STRING'], "GetURL=1");
			$ep = $fp + strlen("GetURL=1");
				
			$var = PROJECT_PATH . "/API.php?" . substr($_SERVER['QUERY_STRING'], 0, $fp) . substr($_SERVER['QUERY_STRING'], $ep);
				
			$render->RenderStr();
				
			$var = sprintf(SVG_FORMAT_STR, $var, $render->svg->Height);
			return ApiHelper::Error($var);
		}
		else
		{
			$render->Render();
		}
		
		return null;
	}
}

?>