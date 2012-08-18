
{if $UserManager->Can("manage_tournaments") && isset($TourneyManager)}

<h3>Lisa tiim</h3>

<form action="API.php?action=AddTeam" method="get" class="maskpost">

	<div class="error">Täida väljad!</div><br />
	
	<label>Nimi: </label>
	<input type="text" name="name" /><br />
	
	<label>Lühend: </label>
	<input type="text" name="abbrevation" /><br />
	
	<input class="lMargin" type="submit" value="Add Team" />

</form>

<br />

{/if}