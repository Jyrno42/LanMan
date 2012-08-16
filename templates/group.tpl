{if !$TournamentRenderer->GroupNameInTable}<h2>Group {$Group->name}</h2>{/if}

<table id="rounded-corner" {if isset($GroupWidth)}style="width: {$GroupWidth}"{/if}>

	<thead>
	
		{if $TournamentRenderer->GroupNameInTable}
		
			<tr>
				<th colspan="6" class="center">Group {$Group->name}</th>
			</tr>
		
		{/if}
	
		<tr {if $TournamentRenderer->GroupNameInTable}class="noRound"{/if}>
		
			<th scope="col">#</td>
			<th scope="col">Name</td>
			<th scope="col">Wins</td>
			<th scope="col">Ties</td>
			<th scope="col">Losses</td>
			<th scope="col">Points</td>
		
		</tr>
	
	</thead>
	
	<tbody>
	
		{foreach $Group->teams as $val}
		
			{if is_object($val->team)}
			<tr>
				<td>{$val->place}.</td>
				<td class="Team">
					{$val->team->Name}
					<div class="teamID">
						{include 'teaminfo.tpl' Team=$val->team}
					</div>
				</td>
				<td>{$val->won}</td>
				<td>{$val->tied}</td>
				<td>{$val->lost}</td>
				<td>{$val->points}</td>
			</tr>
			{else}
			<tr>
				<td>{$val@iteration}.</td>
				<td>{$val->team}</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
			</tr>
			{/if}
		
		{/foreach}
	
	</tbody>

</table>

{if $TournamentRenderer->GroupRenderType == constant("TournamentRenderer::GAMES_OUTSIDE")}
<ul>
	{foreach $tournament->GetGamesForGroup($Group) as $val}
	<li class="game">
		{if $val->Result() == $val->Team1}<b>{/if}
		{$val->Team1->Name}
		{if $val->Result() == $val->Team1}</b>{/if}
		
		{if $val->played}
		{$val->Score1} - {$val->Score2}
		{else}
		vs
		{/if}
		
		{if $val->Result() == $val->Team2}<b>{/if}
		{$val->Team2->Name}
		{if $val->Result() == $val->Team2}</b>{/if}
		
		{if !$val->played}
			<div class="pvpLink">
				{$tournament->GAME->CustomVersusCode($val)}
			</div>
		{/if}
	</li>
	{/foreach}
</ul>
{/if}