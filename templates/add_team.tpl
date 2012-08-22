{if $UserManager->Can("manage_tournaments") && isset($TourneyManager)}

{if !isset($TempTeam)}<h3>Loo tiim</h3>{/if}

<form action="API.php?action=AddTeam" method="get" class="maskpost">
	<div class="error">{if !isset($TempTeam)}Täida väljad!{/if}</div>
	<label>Nimi: </label>
	<input type="text" name="name" /><br />
	
	<label>Lühend: </label>
	<input type="text" name="abbrevation" /><br />
	
	{if isset($TempTeam)}
	<label>Liikmed:</label>
	
		<input type="text" name="players[]" /><input type="button" value="+" class="newRow" />
		<div class="ply lMargin"></div>
	
		<input type="hidden" name="TournamentID" value="{$Tournament->ID}" /><br />
	{/if}
	
	<input class="lMargin" type="submit" value="Add Team" />

</form>
{/if}