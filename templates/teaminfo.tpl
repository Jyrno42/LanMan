{if isset($modify) && $modify}
<div class="teammanager">

<div class="head">{$Team->Name}</div>

<div class="body hideAfter">

<h4>Muuda</h4>
<form action="API.php?action=UpdateTeam" method="get" class="maskpost">
	<div class="error"> </div>
	<input type="hidden" name="TeamID" value="{$Team->uniqueID}" />
	<label>Nimi:</label> <input type="text" name="name" value="{$Team->Name}" /> <br />
	<label>Lühend:</label> <input type="text" name="abbrevation" value="{$Team->Abbrevation}" /> <br />
	<input type="submit" value="Salvesta" class="lMargin" />
</form>

<h4>Kustuta</h4>
<form action="API.php?action=DeleteTeam" method="get" class="confirmpost">
	
	<input type="hidden" name="TeamID" value="{$Team->uniqueID}" />
	
	<div class="error"> </div>
	<label>Kirjuta siia KUSTUTA:</label> <input type="text" name="validate" value="" /><br />
	<input type="submit" value="Kustuta" class="lMargin" />
</form>
<h4>Liikmed</h4>
{else}
<b>{$Team->Name}</b><br />
{$Team->Abbrevation}<br />
{/if}

<ul>
{foreach $Team->Players as $val}
	<li style="clear: both; width: 40%">
		{if ($Team->OwnerID != 0 && $val->UserID == $Team->OwnerID)}[X]{/if}
		{$val->Name}
		{if isset($modify) && $modify && ($Team->OwnerID != 0 && $val->UserID != $Team->OwnerID)}
			<form action="API.php?action=KickPlayer" method="get" class="confirmpost" style="float: right">
				<input type="submit" value="Eemalda" />
				<div class="error"> </div>
			</form>
		{/if}
	</li>
{foreachelse}
	<li>No players</li>
{/foreach}
</ul>

{if isset($modify) && $modify}
</div>
</div>
{/if}