{extends file="page.tpl"}
{block name=PageContents}
<p>Siin saavad vastavate õigustega kasutajad manageerida turniire/kasutajaid jne.</p>

{if $UserManager->Can("manage_tournaments") && isset($TourneyManager)}

	<h3>Turniirid</h3>
	
	{custom_table cols="#,Nimi, " empty="Pole ühtegi" vals=$TourneyManager->tourneyTable->stdItems var=val}
	<tr>
		<td>{$val->ID}</td>
		<td><a href="?page=manage&tournamentID={$val->ID}">{$val->NAME}</td>
		<td>
			<form action="API.php?action=DeleteTournament&hash={$val->ID}" method="get" class="maskpost">
				<div class="error"> </div>
				<input type="submit" value="Kustuta" />
			</form>
		</td>
	</tr>
	{/custom_table}

{/if}

{/block}