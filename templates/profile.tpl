{extends file="page.tpl"}
{block name=PageContents}

{if $UserManager->User->Valid}

<h4>Gameaccounts</h5>
{custom_table cols="#,Nimi,Game" empty="Pole ühtegi" vals=$TourneyManager->teamMan->PlayerManager->GetUserPlayers($UserManager->User->MySql->UserID) var=val}
<tr><td>{$val->uniqueID}</td><td>{$val->Name}</td><td>{$val->Game}</td></tr>
{/custom_table}

{/if}

{/block}
