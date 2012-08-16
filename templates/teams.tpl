{extends file="page.tpl"}
{block name=PageContents}
	{if $UserManager->User->Valid}
	
		<h2>Minu tiimid</h2>
		
		{foreach $TourneyManager->teamMan->GetUserTeams($UserManager->User->MySql->UserID) as $val}
			{include 'teaminfo.tpl' Team=$val modify=true}
		{foreachelse}
			<p>Sul pole ühtegi tiimi!</p>
		{/foreach}
		
		{if $UserManager->Can("create_teams")}
		
			LISA
		
		{/if}
	
	{/if}
{/block}