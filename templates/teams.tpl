{extends file="page.tpl"}
{block name=PageContents}
	<h2>Kõik tiimid</h2>
	
	{custom_table cols="#,Nimi" empty="Pole ühtegi" vals=$TourneyManager->teamMan->stdItems var=val}
	<tr>
		<td>{$val->uniqueID}</td>
		<td><a href="?page=team&teamid={$val->uniqueID}">{$val->Name}</a></td>
	</tr>
	{/custom_table}
	
	{if $UserManager->User->Valid}
		{*foreach $TourneyManager->teamMan->GetUserTeams($UserManager->User->MySql->UserID) as $val}
			{include 'teaminfo.tpl' Team=$val modify=true}
		{foreachelse}
			<p>Sul pole ühtegi tiimi!</p>
		{/foreach*}
		
		{if $UserManager->Can("create_teams")}
			{include 'add_team.tpl'}
		{/if}
	
	{/if}
	
{/block}