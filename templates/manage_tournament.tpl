{extends file="page.tpl"}
{block name=PageContents}

	{if $UserManager->Can("manage_tournaments") && isset($TourneyManager)}
		
		{if $Tournament->STATUS == constant("STATUS_LIVE")}
			{if $Tournament->IsCompleted() != ""}
				{$Tournament->IsCompleted()}
			{else}
				<form action="API.php?action=CompleteTournament" method="get" class="maskpost">
					<div class="error"> </div><br />
					<input type="hidden" name="hash" value="{$Tournament->ID}" />
					<input type="submit" value="Set Completed" />
				</form>
			{/if}
		{/if}
		
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
		
		{if $Tournament->STATUS == constant("STATUS_REGISTERING") || $Tournament->STATUS == constant("STATUS_SEEDING")}
		<br />
		<h3>Tiimid</h3>
		
			<div class="seedError"></div>
			{custom_table cols=" ,Seed, ,Nimi, " empty="Pole ühtegi" vals=$Tournament->Teams var=val}
				<tr>
					<td>
						{if $val->Seeds[$Tournament->ID] != -1}
						<form method="get" action="API.php?action=SeedDecline&TeamID={$val->uniqueID}&hash={$Tournament->ID}" class="seedMask">
							<input type="submit" value="<-" />
						</form>
						{/if}
					</td>
					<td>
						{if $val->Seeds[$Tournament->ID] == -1}
							Random
						{else}
							{$val->Seeds[$Tournament->ID]+1}
						{/if}
					</td>
					<td>
						{if $val->Seeds[$Tournament->ID]+1 < $Tournament->maxTeams}
						<form method="get" action="API.php?action=SeedAdvance&TeamID={$val->uniqueID}&hash={$Tournament->ID}" class="seedMask">
							<input type="submit" value="->" />
						</form>
						{/if}
					</td>
					<td>{$val->Name}</td>
					<td>
						<form method="get" action="API.php?action=RemoveTeam&teamID={$val->uniqueID}&hash={$Tournament->ID}" class="maskpost">
							<div class="error"></div>
							<input type="submit" value="Eemalda" />
						</form> 
					</td>
				</tr>
			{/custom_table}
		
		{/if}
		{if $Tournament->STATUS == constant("STATUS_REGISTERING")}
			<h4>Loo Ajutine Tiim:</h4>{include 'add_team.tpl' TempTeam=true Tournament=$Tournament}
			<br />
			
			<h4>Lisa Tiim:</h4>
			<form action="API.php?action=RegisterTeam" method="get" class="maskpost">
				<input type="hidden" name="hash" value="{$Tournament->ID}" />
				
				<div class="error"> </div>
				<label>Vali</label>
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
		{if $Tournament->STATUS == constant("STATUS_LIVE")}
			<h4>Tulemused:</h4>
			
			<div class="seedError"></div>
			{custom_table cols="Mäng, Tulemus" empty="Pole ühtegi" vals=$Tournament->Games var=val}
				<tr>
					<td>
						{$val->Team1->Name} vs {$val->Team2->Name} 
					</td>
					<td>
						{if $val->played}
							{$val->Score1} - {$val->Score2}
						{else}
							<form method="get" action="API.php?action=SubmitResult" class="seedMask">
								<input type="hidden" name="ResultId" value="{$val->uniqueID}" />
								<input type="text" name="Score1" value="{$val->Score1}" style="width: 30px" /> - <input type="text" name="Score2" value="{$val->Score2}" style="width: 30px" />
								<input type="submit" />
							</form>
						{/if}
					</td>
				</tr>
			{/custom_table}
			
		{/if}
		
		{$Render}
		
		{include 'RenderApi.tpl'}
		
		
	{/if}

{/block}
