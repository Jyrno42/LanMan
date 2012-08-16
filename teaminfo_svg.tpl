<g transform="translate({$xPos}, {$yPos})" id="team_{$Team->uniqueID}" style="display: none;">

<rect x="0" y="0" width="300" height="{if sizeof($Team->Players) > 0}{80+((sizeof($Team->Players)-1)*20)}{else}80{/if}" style="fill:#ffffff; stroke: #B9C9FE" />

<text x="10" y="20" font-weight="bold">{$Team->Name}</text>
<text x="10" y="40" >{$Team->Abbrevation}</text>

{foreach $Team->Players as $val}
	<circle cx="30" cy="{35 + (20 * $val@iteration)}" r="3" stroke="black" stroke-width="2"/>
	<text x="40" y="{40 + (20 * $val@iteration)}" >{$val->Name}</text>
{foreachelse}
	<circle cx="30" cy="{40 + 15}" r="3" stroke="black" stroke-width="2"/>
	<text x="40" y="{40 + 20}" >No players.</text>
{/foreach}


</g>