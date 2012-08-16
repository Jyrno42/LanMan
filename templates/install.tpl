{extends file="page.tpl"}
{block name=PageContents}
<h2>Install</h2>

{if isset($DeployConfig->errors)}
{foreach $DeployConfig->errors as $val}

	{$val->getMessage()}<br />
	
{/foreach}
{/if}

<form method="post" action="">

	<input type="hidden" value="install" name="install" />
	
	{foreach $DeployConfig->params as $val}
		<label>{$val["display"]}</label>
		<input type="text" name="{$val["id"]}" value="{$val["defaultvalue"]}" />
		<br />
	{/foreach}
	
	<input type="submit" class="lMargin" />

</form>
{/block}