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
		$this->AddAction("SeedAdvance", array($this, "SeedAdvance"));
		$this->AddAction("SeedDecline", array($this, "SeedDecline"));
		$this->AddAction("SeedComplete", array($this, "SeedComplete"));
		
		$this->AddAction("SubmitResult", array($this, "SubmitResult"));
		
		$this->AddAction("AddTeam", array($this, "AddTeam"));
		$this->AddAction("RegisterTeam", array($this, "RegisterTeam"));
		$this->AddAction("RemoveTeam", array($this, "RemoveTeam"));
		$this->AddAction("JoinTeam", array($this, "JoinTeam"));
		$this->AddAction("UpdateTeam", array($this, "UpdateTeam"));
		$this->AddAction("DeleteTeam", array($this, "DeleteTeam"));
		$this->AddAction("AddPlayer", array($this, "AddPlayer"));
		$this->AddAction("Render", array($this, "Render"));
		
		$this->AddAction("Login", array($this, "Login"));
		
		$this->AddAction("ShowActions", array($this, "ShowActions"), true);
		
		// New stuff for new style
		
		$this->AddAction("WizardStageConfig", array($this, "WizardStageConfig"));
		$this->AddAction("WizardGameConfig", array($this, "WizardGameConfig"));
		$this->AddAction("WizardValidate", array($this, "WizardValidate"));
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
					$this->LanMan->Datamanager->tourneyTable->groupConfTable->stdItems[$tournament->stageConfig->uniqueID]->SIZE = $groupSize;
				}
	
				$groupAdvance = ApiHelper::GetParam("groupAdvance", false, null, false);
				if($groupAdvance !== false)
				{
					$this->LanMan->Datamanager->tourneyTable->groupConfTable->stdItems[$tournament->stageConfig->uniqueID]->ADVANCE = $groupAdvance;
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
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
	
		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $this->LanMan->Datamanager->tourneyTable->GetTournament($hash);
		$this->LanMan->Datamanager->RegistrationMode($tournament);
		return array("result" => "Registration opened!");
	}
	
	public function SeedAdvance()
	{
		return $this->SeedMod(1);
	}
	
	public function SeedDecline()
	{
		return $this->SeedMod(-1);
	}
	
	public function SeedMod($mod=1)
	{
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
		
		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $this->LanMan->Datamanager->tourneyTable->GetTournament($hash);
		
		if($tournament->STATUS != STATUS_REGISTERING && $tournament->STATUS != STATUS_SEEDING)
			throw new Exception("Tournament status is totally wrong");
		
		$TeamID = ApiHelper::GetParam("TeamID", true);
		$team = $this->LanMan->Datamanager->teamMan->GetTeam($TeamID);
	
		$seedUID = $this->LanMan->Datamanager->seedMan->GetSeedId($hash, $TeamID);
		
		$this->LanMan->Datamanager->seedMan->OrderSeed($tournament, $seedUID, $mod);
		
		return array("result" => "Seeeeds");
	}
	
	public function SeedComplete()
	{
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
	
		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $this->LanMan->Datamanager->tourneyTable->GetTournament($hash);
		$this->LanMan->Datamanager->SeedComplete($tournament);
		return array("result" => "Seeding done!");
	}
	
	public function SubmitResult()
	{
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
		
		$ResultId = ApiHelper::GetParam("ResultId", true);
		$Score1 = ApiHelper::GetParam("Score1", true);
		$Score2 = ApiHelper::GetParam("Score2", true);
		
		$game = $this->LanMan->Datamanager->gamesManager->GetGame($ResultId);
		$game->Score1 = $Score1;
		$game->Score2 = $Score2;
		$game->played = true;
		
		$this->LanMan->Datamanager->gamesManager->stdItems[$ResultId] = $game;
		return array("result" => "Game $ResultId updated. ");
	}
	
	public function AddTeam()
	{
		// FIXME: Add ownerids and more...
		
		//ApiHelper::RequestValidate($_PRIVATEKEY, $_PUBLICKEY);
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
	
		$teamname = ApiHelper::GetParam("name", true);
		ApiHelper::StringMin($teamname, 3, "name");
		ApiHelper::StringMax($teamname, 32, "name");
		
		$abbr = ApiHelper::GetParam("abbrevation", true);
		ApiHelper::StringMin($abbr, 2, "abbrevation");
		ApiHelper::StringMax($abbr, 6, "abbrevation");
		
		$joinKey = ApiHelper::GenerateRandomness(8);
		$adminKey = ApiHelper::GenerateRandomness(16);

		$TournamentID = ApiHelper::GetParam("TournamentID", false, null, 0);
		$pArr = array();
		
		if($TournamentID !== 0)
		{
			// Test if is valid...
			$this->LanMan->Datamanager->tourneyTable->GetTournament($TournamentID);
			
			$players = ApiHelper::GetParam("players", true, "array");
			foreach($players as $k => $v)
			{
				if(strlen($v) > 0)
				{
					$pArr[] = new Player(0, $v);
				}
			}
		}
	
		$team = new Team(0, $teamname, $abbr, $pArr);
		$team->TournamentID = $TournamentID;
		
		$team->JoinKey = $joinKey;
		$team->AdminKey = $adminKey;
		
		$this->LanMan->Datamanager->teamMan->stdItems[] = $team;
		return array("result" => "Team added! $TournamentID");
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
		
		// TODO: Make this work with new system.
	
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

		// Disable cache
		$this->LanMan->Smarty->setCaching(Smarty::CACHING_OFF); 
		
		$render = new TournamentRenderer($tourney, $this->LanMan->Smarty);
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
				
			$var = SITE_HOST . "/API.php?" . substr($_SERVER['QUERY_STRING'], 0, $fp) . substr($_SERVER['QUERY_STRING'], $ep);
				
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
	
	public function Login()
	{
		$vars = array(
			ApiHelper::GetParam("id", true),
			ApiHelper::GetParam("email", true),
			ApiHelper::GetParam("name", true)	
		);
		setcookie("authHash", implode(",", $vars));
		return true;
	}
	
	public function JoinTeam()
	{
		// FIXME: Freeze this until i rewrite the players stuff.
		// TODO: also needs needs user check...
		
		$teamid = ApiHelper::GetParam("teamID", true);
		$key = ApiHelper::GetParam("JoinKey", true);
		ApiHelper::StringMin($key, 7, "Key");
		
		$team = $this->LanMan->Datamanager->teamMan->GetTeam($teamid);
		if($key != $team->JoinKey && $key != $team->AdminKey)
			throw new Exception("Bad key provided!");
		
		$player = ApiHelper::GetParam("playerID", false);
		
		if($key == $team->AdminKey)
		{
			
		}
		else
		{
			$arr=$this->LanMan->Datamanager->teamMan->PlayerManager->GetPlayers(array(6));
			$this->LanMan->Datamanager->teamMan->stdItems[$teamid]->Players[] = $arr[0];
		}
		return array("error" => "BAD");
	}
	
	public function RemoveTeam()
	{
		if(!$this->LanMan->UserManager->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");

		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $this->LanMan->Datamanager->tourneyTable->GetTournament($hash);
		
		$TeamID = ApiHelper::GetParam("teamID", true);
		$team = $this->LanMan->Datamanager->teamMan->GetTeam($TeamID);
		
		foreach ($this->LanMan->Datamanager->seedMan->stdItems as $k => $v)
		{
			if($v->tournamentId == $hash && $v->teamID == $TeamID)
			{
				unset($this->LanMan->Datamanager->seedMan->stdItems[$k]);
				
				if($team->TournamentID != 0)
				{
					unset($this->LanMan->Datamanager->teamMan->stdItems[$TeamID]);
				}
				break;
			}
		}
		
		return array("result" => "Team $TeamID removed from tournament $hash.");
	}
	
	public function WizardStageConfig()
	{
		$type = ApiHelper::GetParam("type", true);

		Stages::instance()->LoadStage($type);

		$cName = $type . "Config";
		$this->LanMan->Smarty->assign("StageName", $type::Name);
		$this->LanMan->Smarty->assign("StageConfig", $cName::GetConfigForm());
		
		$this->LanMan->Smarty->display("twizard_stageconfig.tpl");
		
		return null;
	}
	
	public function WizardGameConfig()
	{
		$type = ApiHelper::GetParam("game", true);
		Games::instance()->LoadGame($type);
		
		$this->LanMan->Smarty->assign("GameName", $type::LName);
		$this->LanMan->Smarty->assign("GameConfig", $type::GetConfigForm());
	
		$this->LanMan->Smarty->display("twizard_gameconfig.tpl");
	
		return null;
	}
	
	public function WizardValidate()
	{
		$tab = ApiHelper::GetParam("tab", true);
		
		// Validate types
		if($tab == 0)
		{
			$type = ApiHelper::GetParam("type", true);
			if(strlen($type) < 1 || Stages::instance()->LoadStage($type) == null)
				throw new Exception("Bad Type selected.");
		}
		else if($tab == 1)
		{
			$tournamentName = ApiHelper::GetParam("tournamentName", true);
			ApiHelper::StringMin($tournamentName, 4, "Tournament Name");
			ApiHelper::StringMax($tournamentName, 32, "Tournament Name");
			
			$Game = ApiHelper::GetParam("Game", true);
			ApiHelper::StringMin($Game, 2, "Game");
			Games::instance()->LoadGame($Game);
		}
		else if($tab == 3)
		{
			$sConf = ApiHelper::GetParam("stage_args", true);
			$type = ApiHelper::GetParam("type", true);
			if(strlen($type) < 1 || Stages::instance()->LoadStage($type) == null)
				throw new Exception("Bad Type selected.");
			$type = $type . "Config";
			$type::ValidateArguments($sConf);
		}
		else if($tab == 4)
		{
			$sConf = ApiHelper::GetParam("game_args", true);
			$Game = ApiHelper::GetParam("Game", true);
			ApiHelper::StringMin($Game, 2, "Game");
			Games::instance()->LoadGame($Game);
			$Game::ValidateArguments($sConf);
		}
		return array("result" => "Everything OK");
	}
}

?>