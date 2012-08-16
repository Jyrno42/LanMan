
{if $UserManager->Can("manage_tournaments") && isset($TourneyManager)}

{if $Tournament->STATUS == constant("STATUS_REGISTERING")}
<h3>Lisa tiim</h3>

<form action="API.php?action=AddTeam" method="get" class="maskpost">

	<div class="error">Täida väljad!</div><br />
	<input type="hidden" name="hash" value="{$Tournament->ID}" />
	
	<label>Nimi: </label>
	<input type="text" name="name" /><br />
	
	<label>Lühend: </label>
	<input type="text" name="abbrevation" />
	
	<input class="lMargin" type="submit" value="Add Team" />

</form>

<br />
{/if}

{/if}