{extends file="page.tpl"}
{block name=PageContents}
<p>Siin saavad vastavate õigustega kasutajad manageerida turniire/kasutajaid jne.</p>

{if $UserManager->Can("manage_tournaments") && isset($TourneyManager)}

	<h3>Turniirid</h3>
	<table id="rounded-corner" style="width: 90%">
	
		<thead>
			<tr>
				<th>#</th>
				<th>Nimi</th>
				<th> </th>
			</tr>
		</thead>
		<tbody>
		{foreach $TourneyManager->tourneyTable->stdItems as $val}
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
		{foreachelse}
			<tr><td>Pole hetkel!</td></tr>
		{/foreach}
		</tbody>
	</table>
	
	{include 'add_tournament.tpl' Action=CreateTournament}
	
	<h3>Mängutüübid</h3>
	
	<h4>Lisa</h4>
	<form action="API.php?action=AddGameType" method="get" class="maskpost">
		<div class="error">Täida järgnevad väljad!</div><br />
		
		<label>Tag: </label> <input type="text" name="tag" /><br />
		<label>Based On:</label>
		<select name="Game" id="BaseClass">
			<option value="-"></option>
			{foreach $TourneyManager->GameClasses as $key => $val}
				<option value="{$key}">{$key}</option>
			{/foreach}
		</select>		
		<br />
		
		{foreach $TourneyManager->GameClasses as $key => $val}
			<div class="GameTypeData hideAfter" id="{$key}">
				{$vars=$TourneyManager->GetFields($key)}
				{foreach $vars as $k2 => $v2}
				
					{if $v2[0] == "text"}
					<label>{$v2[1]}</label> <input type="text" name="{$key}_args[]" /> <br />
					{else if $v2[0] == "select"}
					<label>{$v2[1][0]}</label> 
					<select name="{$key}_args[]">
						{foreach $v2[1][1] as $optv => $optl}
							<option value="{$optv}">{$optl}</option>
						{/foreach}
					</select>
					<br />
					{/if}
				
				{/foreach}
					<hr>
			</div>
		{/foreach}
		
		
		<input type="submit" disabled="disabled" value="Create" />
	</form>

{/if}

{/block}