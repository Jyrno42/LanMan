<?php

require_once("config/config.php");

if(ApiHelper::GetParam("do") == "logout")
{
	session_destroy();
	session_regenerate_id();
	header("Location: " . SITE_HOST);
}

$LanMan = null;
try 
{
	$LanMan = new BootStrap();
	$LanMan->Strap();

	$LanMan->Smarty->assign("TourneyManager", $LanMan->Datamanager);
	$LanMan->Smarty->assign("UserManager", $LanMan->UserManager);
	
	$page = ApiHelper::GetParam("page", false, null, "index");

	if($page == "manage")
	{
		if(!$LanMan->UserManager->Can("manage"))
		{
			throw new Exception("Pole piisavalt õigusi.");
		}
		else
		{
			$tournamentID = ApiHelper::GetParam("tournamentID", false, null, FALSE);
			if($tournamentID !== FALSE)
			{
				if(!$LanMan->UserManager->Can("manage_tournaments"))
				{
					throw new Exception("Pole piisavalt õigusi.");
				}
				$tournament = $tourneyMan->tourneyTable->GetTournament($tournamentID);
				$page = "manage_tournament";
				$LanMan->Smarty->assign("Tournament", $tournament);
	
				$render = new TournamentRenderer($tournament, $LanMan->Smarty);
				$helper = new SVGHelper($render, $tournament);
				$helper->Parse();
	
				//$LanMan->Smarty->assign("Render", $render->RenderStr());
				$LanMan->Smarty->assign("Render", "<object data='API.php?action=Render&hash=" . $tournamentID . "&type=SVG&NameInTable=1&ShowName=1' type='image/svg+xml' width='100%' height='$helper->Height'></object>");
			}
		}
	}
	else if($page == "teams")
	{
		if(!$LanMan->UserManager->User->Valid)
		{
			throw new Exception("Pole piisavalt õigusi.");
		}
	
		// TODO: Teams CreateReadUpdateDelete.
	}
	else
	{
		$page = "index";
	}

	$LanMan->Smarty->display("$page.tpl");
}
catch(Exception $e)
{
	$LanMan->Smarty->assign("error", $e);
	$LanMan->Smarty->display("error.tpl");
}

if($LanMan != null)
	$LanMan->Detach();

// TODO: Deleting team => deletes players! NOPE: Players should be linked to teams in team tables.
// TODO: Team registration. (players can be linked to users/or not)
// TODO: Team join/leave from players.
// TODO: API?action=KickPlayer 

?>
