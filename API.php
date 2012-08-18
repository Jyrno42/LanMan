<?php


/**
 * The api for our tournament usage in different platforms.
 */

require_once("config/config.php");

$LanMan = null;
$LanManApi = new LanManAPI();
 
try
{
	$LanMan = new BootStrap();
	$LanMan->Strap();
	$LanMan->Datamanager->errorCallback = array($LanManApi, "Error");
	
	$LanManApi->SetLanMan($LanMan);
	$LanManApi->Strap($LanMan);
}
catch(Exception $e)
{
	$LanManApi->Error($e);
}

if($LanMan != null)
	$LanMan->Detach();

die();

ob_start();

/**
 * The api for our tournament usage in different platforms.


require_once("class.TourneyManager.php");
require_once("class.DataManager.php");
require_once("class.ApiHelper.php");
require_once("class.Tournament.php");
require_once("class.TournamentRenderer.php");
require_once 'Smarty/libs/Smarty.class.php';

try
{
	$action = ApiHelper::GetParam("action", true);
	
	$Datamanager = new TourneyManager("localhost", "root", "", "lolz");
	$userMan = new UserManager($Datamanager);

	if($action == "CreateTournament")
	{
		//ApiHelper::RequestValidate($_PRIVATEKEY, $_PUBLICKEY);
		if(!$userMan->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
		
		$name = ApiHelper::GetParam("name", true);
		ApiHelper::StringMin($name, 3, "name");
		
		$gameN = ApiHelper::GetParam("Game", true);
		$game = $Datamanager->GetGameType($gameN);
		
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
		
		$Datamanager->tourneyTable->stdItems[] = $tourney;
		//print $Datamanager->StoreTourney($tourney);
		print ApiHelper::ReturnJson(array("result" => "Created tournament!"));
	}
	else if($action == "UpdateTournament")
	{
		if(!$userMan->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
		
		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $Datamanager->tourneyTable->GetTournament($hash);
		
		$name = ApiHelper::GetParam("name", false, null, false);
		if($name !== false)
		{
			ApiHelper::StringMin($name, 3, "name");
			$Datamanager->tourneyTable->stdItems[$tournament->ID]->NAME = $name;
		}
		
		// In add status, can modify type/game
		if($tournament->STATUS == STATUS_ADDED)
		{
			$game = ApiHelper::GetParam("Game", false, null, false);
			if($game !== false)
			{
				$Datamanager->tourneyTable->stdItems[$tournament->ID]->GAME = $Datamanager->GetGameType($game);
			}
		
			$type = ApiHelper::GetParam("type", false, null, false);
			if($type !== false)
			{
				$Datamanager->tourneyTable->stdItems[$tournament->ID]->TYPE = $type;
			}
		}
		
		// Is groupstage and we can modify config.
		if($tournament->STATUS < STATUS_SEEDING)
		{
			$maxTeams = ApiHelper::GetParam("maxTeams", false, null, false);
			if($maxTeams !== false)
			{
				$Datamanager->tourneyTable->stdItems[$tournament->ID]->maxTeams = $maxTeams;
			}
			
			if($tournament->TYPE & TYPE_GROUPSTAGE)
			{
				$groupSize = ApiHelper::GetParam("groupSize", false, null, false);
				if($groupSize !== false)
				{
					$Datamanager->tourneyTable->groupConfTable->stdItems[$tournament->GroupStageConfig->uniqueID]->SIZE = $groupSize;
				}
				
				$groupAdvance = ApiHelper::GetParam("groupAdvance", false, null, false);
				if($groupAdvance !== false)
				{
					$Datamanager->tourneyTable->groupConfTable->stdItems[$tournament->GroupStageConfig->uniqueID]->ADVANCE = $groupAdvance;
				}
			}
		}
		
		print ApiHelper::ReturnJson(array("result" => "Tournament updated! "));
	}
	else if($action == "DeleteTournament")
	{
		if(!$userMan->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
		
		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $Datamanager->tourneyTable->GetTournament($hash);
		
		unset($Datamanager->tourneyTable->stdItems[$tournament->ID]);
		print ApiHelper::ReturnJson(array("result" => "Tournament deleted!"));
	}
	else if($action == "AddGameType")
	{
		if(!$userMan->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
		
		$tag = ApiHelper::GetParam("tag", true);
		ApiHelper::StringMin($tag, 3, "tag");
		if(isset($Datamanager->CustomGames[$tag]))
		{
			throw new Exception("Tag must be unique!");
		}
		
		$bClass = ApiHelper::GetParam("Game", true);
		
		if(!isset($Datamanager->GameClasses[$bClass]))
		{
			throw new Exception("Invalid baseClass provided!");
		}
		
		$args = ApiHelper::GetParam($bClass ."_args", true);
		
		$bClass::ValidateArguments($args);
		
		$Datamanager->gameTypesManager->Insert(
			array(
				"tag" => $tag,
				"className" => $bClass,
				"arguments" => implode(",", $args)
			)
		);
		print ApiHelper::ReturnJson(array("result" => "Done"));
	}
	else if($action == "GetTeamInfo")
	{	
		$id = ApiHelper::GetParam("teamID", true);
		
		$team = $Datamanager->teamMan->GetTeam($id);

		$smarty = new Smarty;
		$smarty->assign("Team", $team);
		$smarty->display("teaminfo.tpl");		
	}
	else if($action == "GetTeam")
	{
		$id = ApiHelper::GetParam("teamID", true);
		print ApiHelper::ReturnJson($Datamanager->teamMan->GetTeam($id));
	}
	else if($action == "GetPlayers")
	{
		$id = ApiHelper::GetParam("teamID", true);
		$team = $Datamanager->teamMan->GetTeam($id);
		print ApiHelper::ReturnJson($team->Players);
	}
	else if($action == "OpenRegistration")
	{
		//ApiHelper::RequestValidate($_PRIVATEKEY, $_PUBLICKEY);
		if(!$userMan->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
		
		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $Datamanager->tourneyTable->GetTournament($hash);
		$Datamanager->RegistrationMode($tournament);
		print ApiHelper::ReturnJson(array("result" => "Registration opened!"));
	}
	else if($action == "SeedComplete")
	{
		//ApiHelper::RequestValidate($_PRIVATEKEY, $_PUBLICKEY);
		if(!$userMan->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
		
		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $Datamanager->tourneyTable->GetTournament($hash);
		$Datamanager->SeedComplete($tournament);
		print ApiHelper::ReturnJson(array("result" => "Seeding done!"));
	}
	else if($action == "AddTeam")
	{
		//ApiHelper::RequestValidate($_PRIVATEKEY, $_PUBLICKEY);
		if(!$userMan->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
		
		$teamname = ApiHelper::GetParam("name", true);
		$abbr = ApiHelper::GetParam("abbrevation", true);
		
		$team = new Team(0, $teamname, $abbr, array());
		$Datamanager->teamMan->stdItems[] = $team;
		
		print ApiHelper::ReturnJson(array("result" => "Team added!"));
	}
	else if($action == "RegisterTeam")
	{
		if(!$userMan->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
		
		$hash = ApiHelper::GetParam("hash", true);
		$tournament = $Datamanager->tourneyTable->GetTournament($hash);
		
		if($tournament->STATUS != STATUS_REGISTERING)
			throw new Exception("Cant add a team to a tournament that is not in registering mode.");
		
		$TeamID = ApiHelper::GetParam("TeamID", true);
		$team = $Datamanager->teamMan->GetTeam($TeamID);
		
		if(!$Datamanager->TeamCanRegisterToTournament($tournament, $team))
		{
			throw new Exception("Team is not eligible for this tournament!");
		}
		
		$seed = new Seed();
		$seed->tournamentId = $tournament->ID;
		$seed->teamID = $team->uniqueID;		
		$Datamanager->seedMan->stdItems[] = $seed;
		
		print ApiHelper::ReturnJson(array("result" => "Team registered for this tournament!"));
	}
	else if($action == "UpdateTeam")
	{
		if(!$userMan->User->Valid)
			throw new Exception("Bad auth key.");
		
		$TeamID = ApiHelper::GetParam("TeamID", true);
		$team = $Datamanager->teamMan->GetTeam($TeamID);
		
		if(!$userMan->Can("manage") && !$userMan->User->MySql->UserID != $team->OwnerID)
			throw new Exception("Cant do this to that team.");
		
		$teamname = ApiHelper::GetParam("name", true);
		ApiHelper::StringMin($teamname, 3, "name");
		ApiHelper::StringMax($teamname, 32, "name");
		$abbr = ApiHelper::GetParam("abbrevation", true);
		ApiHelper::StringMin($abbr, 2, "abbrevation");
		ApiHelper::StringMax($abbr, 6, "abbrevation");
		
		$Datamanager->teamMan->stdItems[$team->uniqueID]->Name = $teamname;
		$Datamanager->teamMan->stdItems[$team->uniqueID]->Abbrevation = $abbr;
		
		print ApiHelper::ReturnJson(array("result" => "Team updated!"));
	}
	else if($action == "DeleteTeam")
	{
		if(!$userMan->User->Valid)
			throw new Exception("Bad auth key.");
		
		$TeamID = ApiHelper::GetParam("TeamID", true);
		$team = $Datamanager->teamMan->GetTeam($TeamID);
		
		if(!$userMan->Can("manage") && !$userMan->User->MySql->UserID != $team->OwnerID)
			throw new Exception("Cant do this to that team.");
		
		$confirm = ApiHelper::GetParam("validate", true);
		if($confirm != "KUSTUTA")
		{
			throw new Exception("Must write KUSTUTA to the field!");
		}
		
		unset($Datamanager->teamMan->stdItems[$team->uniqueID]);
		
		print ApiHelper::ReturnJson(array("result" => "Deleted!"));
	}
	else if($action == "AddPlayer")
	{
		if(!$userMan->Can("manage_tournaments"))
			throw new Exception("Bad auth key.");
		
		$teamID = ApiHelper::GetParam("teamID", true);
		$team = $Datamanager->teamMan->GetTeam($teamID);
		
		$pName = ApiHelper::GetParam("name", true);
		
		$player = new Player(0, $pName);
		$player->teamID = $teamID;
		
		$Datamanager->teamMan->PlayerManager->stdItems[] = $player;
	}
	else if($action == "Render")
	{
		$hash = ApiHelper::GetParam("hash", true);
		
		$tourney = $Datamanager->tourneyTable->GetTournament($hash);
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
			print ApiHelper::ReturnJson(ApiHelper::Error($var));
		}
		else
		{
			$render->Render();
		}
	}
	else
		throw new Exception("Action $action is not implemented.");	
}
catch(Exception $e)
{	
	print ApiHelper::ReturnJson(ApiHelper::Error($e->getMessage()));
}

$userMan->__destruct();
unset($Datamanager);
ob_flush();
*/

?>