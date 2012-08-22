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
		
		{section name="hed" start=0 loop=6 step=1}
			
			{cycle name="headers" values="#,Name,Wins,Ties,Losses,Points" assign=Header}
			{cycle name="values" values="place,name,won,tied,lost,points" assign=Value}
			{cycle name="defvals" values="iteration,name,0,0,0,0" assign=DefValues}
			{cycle name="widts" values="1,7,45,60,75,90" assign=ColWidth}
				
			{if $val@iteration == 1}				
				<text x="50%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039" text-anchor="middle">Group {$Group->name}</text>
				{break}
			{else if $val@iteration == 2}
				<text x="{$ColWidth}%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039">{$Header}</text>
			{else if $val@iteration != $val@last}
			
				{if is_object($Team->team)}
					<text x="{$ColWidth}%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039" onmouseover="show(evt, 'team_{$Team->team->uniqueID}')" onmouseout="hide(evt, 'team_{$Team->team->uniqueID}')">
						{if $Value == "name"}
							{$Team->team->Name}
						{else}
							{$Team->$Value}
						{/if}
					</text>
				{else}
					<text x="{$ColWidth}%" y="{$val["text"]}" height="{$SVGHelper->rowH}" fill="#039">
						{if $DefValues == "iteration"}
							{$val@iteration-2}
						{else if $DefValues == "name"}
							{$Team->team}
						{else}
							{$DefValues}
						{/if}
					</text>
				{/if}
			{/if}
		{/section}
		
		{cycle name="headers" reset="TRUE" print=false}
		{cycle name="values" reset="TRUE" print=false}
		{cycle name="defvals" reset="TRUE" print=false}
		{cycle name="widts" reset="TRUE" print=false}
		
		{if $val@iteration > 2 && $idx + 1 < sizeof($Group->teams)}
			{* Get Next Team *}
			{$idx = $idx + 1}
			{$Team = $Group->teams[$idx]}
		{/if}
		
	{/foreach}

{if $TournamentRenderer->GroupRenderType == constant("TournamentRenderer::GAMES_OUTSIDE")}
<g transform="translate(0, {$SVGHelper->groups[$Group->index]["gY"]})">
	
	{foreach $tournament->GetGamesForGroup($Group) as $val}
		
		<text x="" y="{($val@iteration * $SVGHelper->rowH) - 15}" height="{$SVGHelper->rowH}" fill="black">
		
		{if $val->Result() == $val->Team1}{/if}
		{$val->Team1->Name}
		
		{if $val->played}
		{$val->Score1} - {$val->Score2}
		{else}
		vs
		{/if}
		
		{if $val->Result() == $val->Team2}{/if}
		{$val->Team2->Name}
		</text>
		
	{/foreach}
	
</g>
{/if}

</g>