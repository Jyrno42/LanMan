<?php

// TODO: Deleting team => deletes players! NOPE: Players should be linked to teams in team tables.
// TODO: Team registration. (players can be linked to users/or not)
// TODO: Team join/leave from players.
// TODO: API?action=KickPlayer 

session_start();
ob_start();

require_once("class.TourneyManager.php");
require_once("class.Tournament.php");
require_once("class.TournamentRenderer.php");
require_once("Smarty/libs/Smarty.class.php");
require_once("facebook/src/facebook.php");
require_once("class.ApiHelper.php");

if(ApiHelper::GetParam("do") == "logout")
{
	session_destroy();
	session_regenerate_id();
	header("Location: " . PROJECT_PATH);
}

$smarty = new Smarty;
$tourneyMan = null;

try
{
	$tourneyMan = new TourneyManager("localhost", "root", "", "lolz");
	$userMan = new UserManager($tourneyMan);
	
	$smarty->assign("TourneyManager", $tourneyMan);
	$smarty->assign("UserManager", $userMan);
	
	$page = ApiHelper::GetParam("page", false, null, "index");
	if($page == "manage")
	{
		if(!$userMan->Can("manage"))
		{
			throw new Exception("Pole piisavalt õigusi.");
		}
		else
		{
			$tournamentID = ApiHelper::GetParam("tournamentID", false, null, FALSE);
			if($tournamentID !== FALSE)
			{
				if(!$userMan->Can("manage_tournaments"))
				{
					throw new Exception("Pole piisavalt õigusi.");					
				}
				$tournament = $tourneyMan->tourneyTable->GetTournament($tournamentID); 
				$page = "manage_tournament";
				$smarty->assign("Tournament", $tournament);
				
				$render = new TournamentRenderer($tournament, $smarty);
				$helper = new SVGHelper($render, $tournament);
				$helper->Parse();
				
				
				//$smarty->assign("Render", $render->RenderStr());
				$smarty->assign("Render", "<object data='API.php?action=Render&hash=" . $tournamentID . "&type=SVG&NameInTable=1&ShowName=1' type='image/svg+xml' width='100%' height='$helper->Height'></object>");
				
			}
		}
	}
	else if($page == "teams")
	{
		if(!$userMan->User->Valid)
		{
			throw new Exception("Pole piisavalt õigusi.");
		}
		
		// TODO: Teams CreateReadUpdateDelete.
	}
	else 
	{
		$page = "index";
	}
		
	$smarty->display("templates/$page.tpl");
}
catch(Exception $e)
{	
	$smarty->assign("error", $e);
	$smarty->display("error.tpl");
}

$userMan->__destruct();
unset($tourneyMan);
ob_flush();

/*$render = new TournamentRenderer($test, $smarty);
//$render->GroupRenderType = TournamentRenderer::$GAMES_INSIDE;
//$render->GroupWidth = "300px";
$render->ShowGames = true;
//$render->ShowName = false;
$render->GroupNameInTable = true;
//$render->ShowGroup(0);
$render->Render();*/

?>
