{extends file="page.tpl"}
{block name=PageTitle}Error{/block}
{block name=PageContents}
	<h3>JAMA:</h3> {$error->GetMessage()}
{/block}

{block name=RightMenu}
BAD!
{/block}