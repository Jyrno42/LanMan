{if !$TournamentRenderer->GroupNameInTable}<h2>Group {$Group->name}</h2>{/if}

<table id="rounded-corner" {if isset($GroupWidth)}style="width: {$GroupWidth}"{/if}>

	<thead>
	
		{if $TournamentRenderer->GroupNameInTable}
		
			<tr>
				<th colspan="{math equation="size + 6" size=$Group->teams|@sizeof}" class="center">Group {$Group->name}</th>
			</tr>
		
		{/if}
	
		<tr>
		
			<th scope="col">#</td>
			<th scope="col">Name</td>
			{foreach $Group->teams as $val}
			
				{if is_object($val->team)}
					<th>{$val@iteration}. {$val->team->Abbrevation}</th>
				{else}
					<th>{$val@iteration}. {$val->team}</th>
				{/if}
			
			{/foreach}
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
				<td class="Team">{$val->team->Name}<div class="teamID hidden">{$val->team->uniqueID}</div></td>
				
				{foreach $Group->teams as $ops}
				
					{if $val->team == $ops->team}
						<td class="fill"> </td>
					{else if is_object($ops->team)}
						<td class="center">
							{$game=$tournament->GetGameBetweenTeams($val->team, $ops->team)}
							{if $game != null}
								{if $game->Team1 == $val->team}
									{$game->Score1}
								{else}
									{$game->Score2}
								{/if}
								-
								{if $game->Team1 != $val->team}
									{$game->Score1}
								{else}
									{$game->Score2}
								{/if}
								
								<hr>
								{if $game->Result() == null}
									<b>{$tournament->GroupStageConfig->TiePoints}</b>
								{else}
									{if $game->Result() == $val->team}
										<b>{$tournament->GroupStageConfig->WinPoints}</b>
									{else}
										<b>0</b>
									{/if}
								{/if}
							{else}
							 
							{/if}
						</td>
					{else}
						<td> </td>
					{/if}
				
				{/foreach}
				
				<td>{$val->won}</td>
				<td>{$val->tied}</td>
				<td>{$val->lost}</td>
				<td>{$val->points}</td>
			</tr>
			{else}
			<tr>
				<td>{$val@iteration}.</td>
				<td>{$val->team}</td>
				{foreach $Group->teams as $ops}
				
					{if is_object($ops->team)}
						<td> </td>
					{else}
						<td> </td>
					{/if}
				
				{/foreach}
				<td>0</td>
				<td>0</td>
				<td>0</td>
				<td>0</td>
			</tr>
			{/if}
		
		{/foreach}
	
	</tbody>

</table>