{if isset($modify) && $modify || isset($more) && $more}
<div class="teammanager">

<div class="head">{$Team->Name}</div>

<div class="body">

{if isset($modify) && $modify}

<h4>Muuda</h4>

<label><b>Parool Liitumiseks:</b></label> {$Team->JoinKey}<br /><br />

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

﻿{if isset($modify) && $modify || isset($more) && $more}

{if !isset($modify) || !$modify}
	<h4>Liitu: </h4>
	<form action="Api.php?action=JoinTeam" method="get" class="maskpost">
		<div class="error"> </div>
		
		<input type="hidden" name="teamID" value="{$Team->uniqueID}" />
		<label>Liitumiskood</label><input type="text" name="JoinKey" /> <br />
		
		<input type="submit" class="lMargin" />
	</form>
{/if}

</div>
</div>
{/if}