{extends file="page.tpl"}
{block name=PageContents}

{if $UserManager->User->Valid && ($UserManager->CanModifyTeam($Team))}
{include 'teaminfo.tpl' more=true modify=true}
{else}
{include 'teaminfo.tpl' more=true}
{/if}


{/block}
