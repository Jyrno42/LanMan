{extends file="page.tpl"}
{block name=PageContents}

	{if $UserManager->Can("manage_tournaments") && isset($TourneyManager)}
		
		{if $Tournament->STATUS == constant("STATUS_ADDED")}
			<form action="API.php?action=OpenRegistration" method="get" class="maskpost">
				<div class="error"> </div><br />
				<input type="hidden" name="hash" value="{$Tournament->ID}" />
				<input type="submit" value="Open Registration" />
			</form>
			
			<br />
		{/if}
		{if $Tournament->STATUS == constant("STATUS_SEEDING")}
			<form action="API.php?action=SeedComplete" method="get" class="maskpost">
				<div class="error"> </div><br />
				<input type="hidden" name="hash" value="{$Tournament->ID}" />
				<input type="submit" value="End Seeding" />
			</form>
			
			<br />
		{/if}
		
		{include 'add_tournament.tpl' Action=UpdateTournament Modify=$Tournament}
		
		{if $Tournament->STATUS == constant("STATUS_REGISTERING")}
		<br />
		<b>Tiimid</b>
		<form action="API.php?action=RegisterTeam" method="get" class="maskpost">
			<input type="hidden" name="hash" value="{$Tournament->ID}" />
			
			<div class="error"> </div>
			<label>Lisa Tiim:</label>
			<select name="TeamID">
				{foreach $TourneyManager->teamMan->stdItems as $val}
				
					{if $TourneyManager->TeamCanRegisterToTournament($Tournament, $val)}
					<option value="{$val->uniqueID}">{$val->Name}</option>
					{/if}
				
				{/foreach}
			</select>
			<br />
			<input class="lMargin" type="submit" value="Lisa" />
		</form>
		{/if}
		
		{$Render}
		
		{include 'RenderApi.tpl'}
		
		
	{/if}

{/block}
