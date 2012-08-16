<g transform="translate(0,{$SVGHelper->groups[$Group->index]["x"]})">

	<rect x="0" y="{$SVGHelper->groups[$Group->index]["y"]}" rx="20" ry="20" width="100%" height="{$SVGHelper->groups[$Group->index]["height"]}" style="fill:#B9C9FE;"/>
	
	{if $SVGHelper->showLines}
	<g transform="translate(0, {if $TournamentRenderer->GroupNameInTable}10{else}0{/if})">
	{foreach $SVGHelper->groups[$Group->index]["rows"] as $val}
		<line x1="0" y1="{$val["y"]}" x2="100%" y2="{$val["y"]}" style="stroke:{if $val@iteration == $val@last}rgb(0,0,255){else}rgb(255,0,0){/if};stroke-width:2"/>
	{/foreach}
	</g>
	{/if}
	
	{* Generating table header *}
	{$idx = 0}{$Team = $Group->teams[$idx]}
	{foreach $SVGHelper->groups[$Group->index]["rows"] as $val}
		
		<g transform="translate(0, {if $TournamentRenderer->GroupNameInTable}10{else}0{/if})">
		{if $val@iteration > 2 && $val@iteration != $val@last}
		<rect x="0" y="{$val["y"]}" rx="0" ry="0" width="100%" height="{$SVGHelper->rowH}" style="fill:#fff; opacity: 0.5"/>
		{/if}
		</g>
		
		{if $val@iteration == 1}				
			<text x="50%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039" text-anchor="middle">Group {$Group->name}</text>
		{else if $val@iteration == 2}
			<text x="1%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039">#</text>
			<text x="7%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039">Name</text>
			
			{$precent = 40}
			{$t2 = 1}
			{$step = 60 / (sizeof($Group->teams[$idx]) + 5)}
			{foreach $Group->teams as $team2}
				<text x="{$precent + ($step/2)}%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039" text-anchor="middle">{if is_object($team2->team)}{$team2->team->Abbrevation}{else}{$t2}{/if}</text>
				{$precent = $precent + $step}
				{$t2 = $t2 + 1}
			{/foreach}
			
			{section name="hed" start=0 loop=4 step=1}
				<text x="{$precent + ($step/2)}%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039" text-anchor="middle">{cycle name="headers" values="Wins,Ties,Losses,Points"}</text>
				{$precent = $precent + $step}
			{/section}
			
		{else if !$val@last}
			<text x="1%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039">{if is_object($Team->team)}{$Team->place}{else}1{/if}</text>
			
			{if is_object($Team->team)}
				<text x="7%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039" onmouseover="show(evt, 'team_{$Team->team->uniqueID}')" onmouseout="hide(evt, 'team_{$Team->team->uniqueID}')">{$Team->team->Name}</text>
			{else}
				<text x="7%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039">{$Team->team}</text>
			{/if}
			
			{$precent = 40}
			{$t2 = 1}
			{$step = 60 / (sizeof($Group->teams[$idx]) + 5)}
			{foreach $Group->teams as $team2}
				{if $TournamentRenderer->GroupNameInTable}
					{$ypos = $val["y"]+10}
				{else}
					{$ypos = $val["y"]}
				{/if}
				
				{if $Team->team == $team2->team}
					<rect x="{$precent}%" y="{$ypos}" width="{$step}%" height="{$SVGHelper->rowH}" style="fill:#636c88" />
				{else}
					{$game = $tournament->GetGameBetweenTeams($Team->team, $team2->team)}
					{if $game != null && $game->played}
						<text x="{$precent + ($step/2)}%" y="{$val["text"]-10}" height="{$SVGHelper->rowH}" fill="#039" text-anchor="middle">
						{if $game->Team1 == $Team->team}
							{$game->Score1}
						{else}
							{$game->Score2}
						{/if}
						-
						{if $game->Team1 != $Team->team}
							{$game->Score1}
						{else}
							{$game->Score2}
						{/if}
						</text>
						
						<line x1="{$precent + ($step * 0.1)}%" y1="{$val["text"]-7}" x2="{$precent + $step - ($step * 0.1)}%" y2="{$val["text"]-7}" style="stroke: rgb(0,0,0);stroke-width:2"/>
						
						<text x="{$precent + ($step/2)}%" y="{$val["text"]+10}" height="{$SVGHelper->rowH}" fill="#039" text-anchor="middle">
						{if $game->Result() == null}
							{$tournament->GroupStageConfig->TiePoints}
						{else}
							{if $game->Result() == $Team->team}
								{$tournament->GroupStageConfig->WinPoints}
							{else}
								0
							{/if}
						{/if}
						</text>
					{else}
						
					{/if}
				{/if}
				
				{$precent = $precent + $step}
				{$t2 = $t2 + 1}
			{/foreach}
			
			<text x="{$precent + ($step/2)}%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039">{if is_object($Team->team)}{$Team->won}{else}0{/if}</text>
			{$precent = $precent + $step}
			
			<text x="{$precent + ($step/2)}%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039">{if is_object($Team->team)}{$Team->tied}{else}0{/if}</text>
			{$precent = $precent + $step}
			
			<text x="{$precent + ($step/2)}%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039">{if is_object($Team->team)}{$Team->lost}{else}0{/if}</text>
			{$precent = $precent + $step}
			
			<text x="{$precent + ($step/2)}%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039">{if is_object($Team->team)}{$Team->points}{else}0{/if}</text>
			{$precent = $precent + $step}
			
		{/if}
		
		{if $val@iteration > 2 && $idx + 1 < sizeof($Group->teams)}
			{* Get Next Team *}
			{$idx = $idx + 1}
			{$Team = $Group->teams[$idx]}
		{/if}
		
	{/foreach}

</g>
