{extends file="layout.tpl"}
{block name=title}Tournament {if isset($tournament)}{$tournament->NAME}{/if} - GroupStage{/block}
{block name=body}
{if isset($tournament)}

	{if isset($tournament->Stages["groups"])}
	
		{if $TournamentRenderer->ShowName}<h1>{$tournament->NAME}</h1><sub>{$tournament->STATUSES[$tournament->STATUS]}</sub>{/if}

		{foreach $tournament->Stages["groups"]->Groups as $val}
			{if !isset($TournamentRenderer->GroupModifier[$val@key])}
				{if $TournamentRenderer->GroupRenderType != constant("TournamentRenderer::GAMES_INSIDE")}
				  {include 'group.tpl' Group=$val}
				{else}
				  {include 'group_inside.tpl' Group=$val}
				{/if}
			{/if}
		{foreachelse}
			No groups created yet? WHATTAFACK?
		{/foreach}
	
	{else}
		This tournament doesen't have a groupstage. DAFUQ IS WRONG WITH YA?
	{/if}
	
{else}
You dont even supply me a tournament? What do you want from me? Cmon, are you retarded or something?
{/if}
{/block}