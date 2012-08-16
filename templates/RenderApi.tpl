
{if $Tournament->TYPE & constant("TYPE_GROUPSTAGE")}

<h3>API Builder</h3>

<form action="API.php?action=Render" method="get" class="renderApiBuilder">

	<input type="hidden" name="hash" value="{$Tournament->ID}" />
	<input type="hidden" name="type" value="SVG" />
	<input type="hidden" name="GetURL" value="1" />

	<div class="error">Kasuta seda, et ehitada vajalik turniiri kuvamise objekt.</div>
	<label>Turniiri nimi:</label><input type="radio" value="1" name="ShowName" />Näita
	<input type="radio" value="0" name="ShowName" />Peida<br />
	
	<label>Grupinimed:</label> <input type="radio" value="1" name="NameInTable" />Sees
	<input type="radio" value="0" name="NameInTable" />Väljas<br />
	
	<label>Mängude Kuvamine:</label>
	<input type="radio" value="-1" name="GroupRenderType" />Ei kuva 
	<input type="radio" value="1" name="GroupRenderType" />Tabelis
	<input type="radio" value="0" name="GroupRenderType" />Peale tabelit<br />
	
	<label>Eraldi grupid</label>
	<select name="ShowGroup[]" multiple="multiple">
		{if isset($Tournament->Stages["groups"])}
		
			{foreach $Tournament->Stages["groups"]->Groups as $val}
				<option value="{$val->index}">Group {$val->name}</option>				
			{/foreach}
		
		{/if}
	</select>
	
	<input type="submit" />

</form>

<b>SvgURL:</b> <br /> 
<input type="text" style="width: 90%" id="buildResult" />



{else}

Bracketite liimimis kood.

{/if}