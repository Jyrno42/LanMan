{if !isset($Modify)}<h4>Lisa</h4>{/if}

<form action="API.php?action={$Action}" method="get" class="maskpost {if !isset($Modify)}addTournament{/if}">

{if isset($Modify)}

	<input type="hidden" name="hash" value="{$Modify->ID}" />

{/if}

<p class="error">Täida järgnevad väljad!</p>

<label>Nimi:</label> <input type="text" name="name" value="{if isset($Modify)}{$Modify->NAME}{/if}" /><br>

<label>Mäng:</label> 

<select name="Game" {if isset($Modify) && $Modify->STATUS != constant("STATUS_ADDED")}disabled="disabled"{/if}>
	{foreach $TourneyManager->CustomGames as $key => $val}
		<option value="{$key}" {if isset($Modify) && $Modify->GAME->gameID == $key}selected="selected"{/if}>{$val->LNAME}</option>
	{/foreach}
</select>
<br>

<label>Tüüp:</label> 
<input type="hidden" name="type" {if isset($Modify)}value="{$Modify->TYPE}"{/if} />

<select name="Groups" {if isset($Modify) && $Modify->STATUS != constant("STATUS_ADDED")}disabled="disabled"{/if}>
	{if !isset($Modify)}<option value="-">-</option>{/if}
	<option value="1" {if isset($Modify) && ($Modify->TYPE & constant("TYPE_GROUPSTAGE"))}selected="selected"{/if}>Gruppid</option>
	<option value="0" {if isset($Modify) && !($Modify->TYPE & constant("TYPE_GROUPSTAGE"))}selected="selected"{/if}>Playoffid</option>
</select><br />

<div class="lMargin {if !isset($Modify) || ($Modify->TYPE & constant("TYPE_GROUPSTAGE"))}hideAfter{/if}" id="OtherTypeOptions">
	<input type="checkbox" name="ThirdPlaceMatch" value="1" {if isset($Modify) && ($Modify->TYPE & constant("TYPE_THIRDPLACEMATCH"))}checked="checked"{/if} /> 3 koha m2ng<br />
	<input type="checkbox" name="DoubleElimination" value="1" {if isset($Modify) && ($Modify->TYPE & constant("TYPE_DOUBLEELIMINATION"))}checked="checked"{/if}/> double elimination<br />
</div>

<label>Tiimide arv</label>
<input type="text" name="maxTeams" {if isset($Modify) && $Modify->STATUS > constant("STATUS_REGISTERING")}disabled="disabled"{/if} {if isset($Modify)}value="{$Modify->maxTeams}"{/if} /><br/>

<div class="{if !isset($Modify) || !($Modify->TYPE & constant("TYPE_GROUPSTAGE"))}hideAfter{/if}" id="groupConfig">
	<label>Alagrupi suurus</label>
	<input type="text" name="groupSize" {if isset($Modify) && $Modify->STATUS > constant("STATUS_REGISTERING")}disabled="disabled"{/if} {if isset($Modify)}value="{$Modify->GroupStageConfig->SIZE}"{/if} /><br/>
	
	<label>Alagrupist Edasi Saajate Arv</label>
	<input type="text" name="groupAdvance" {if isset($Modify) && $Modify->STATUS > constant("STATUS_REGISTERING")}disabled="disabled"{/if} {if isset($Modify)}value="{$Modify->GroupStageConfig->ADVANCE}"{else}value="2"{/if} />
</div>

<input class="lMargin" type="submit" {if !isset($Modify)}disabled="disabled"{/if} value="Saada" />

</form>