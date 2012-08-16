<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">

<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="{$SVGHelper->Height}">

	<script type="text/javascript">
		<![CDATA[
		function show(evt, val)
		{
			//alert("asda" + val);
			var element = document.getElementById(val);
			if(element)
			{
				element.style.display = "block";
			}
			element.setAttribute('transform', 'translate(' + evt.clientX + ', ' + evt.clientY + ')');
		}
		function hide(evt, val)
		{
			//alert("asda" + val);
			var element = document.getElementById(val);
			element.style.display = "none";
		}
		]]>
	</script>

	{if $TournamentRenderer->ShowName}
	<text x="0" y="35" fill="Black" font-weight="bold" font-size="24">{$tournament->NAME}</text>
	{/if}
	
	<g transform="translate(0, {$SVGHelper->ContainerOffset})">
	{if isset($tournament)}
		{if isset($tournament->Stages["groups"])}
			{foreach $tournament->Stages["groups"]->Groups as $val}
				{if !isset($TournamentRenderer->GroupModifier[$val@key])}
					{if $TournamentRenderer->GroupRenderType != constant("TournamentRenderer::GAMES_INSIDE")}
					  {include 'svg_group.tpl' Group=$val}
					{else}
					  {include 'svg_group_inside.tpl' Group=$val}
					{/if}
				{/if}
			{foreachelse}
				<text x="0" y="15" fill="red">No groups created yet? WHATTAFACK?</text>
			{/foreach}
		
		{else}
			<text x="0" y="15" fill="red">This tournament doesen't have a groupstage. DAFUQ IS WRONG WITH YA?</text>
		{/if}
	
	{else}
		<text x="0" y="15" fill="red">You dont even supply me a tournament? What do you want from me? Cmon, are you retarded or something?</text>
	{/if}
	
	{if $SVGHelper->showLines}
	<line x1="0" y1="0" x2="100%" y2="0" style="stroke:rgb(0,0,0);stroke-width:4"/>
	<line x1="0" y1="100%" x2="100%" y2="100%" style="stroke:rgb(0,0,0);stroke-width:4"/>
	{/if}
	</g>
	
	{if isset($tournament) && isset($tournament->Stages["groups"])}
	{foreach $tournament->Stages["groups"]->Groups as $Group}
		{foreach $Group->teams as $Team}
			{if is_object($Team->team)}
			{include 'teaminfo_svg.tpl' Team=$Team->team xPos="10" yPos=30}
			{/if}
		{/foreach}
	{/foreach}
	{/if}
	
</svg>