{extends file="layout.tpl"}
{block name=title}LanMan - {block name=PageTitle}Home{/block}{/block}
{block name=body}

<div class="container">

	<div id="header">
	
		<div class="logo">
			<h1>{block name=header}LanMan{/block}<h1>
		</div>
	
		<div class="menu">
			{nocache}
			<ul>
				{foreach $Menus as $val}
					<li {if isset($val["liclass"])}class="{$val["liclass"]}"{/if}><a href="?page={$val["page"]}" class="{if isset($val["class"])}{$val["class"]}{/if} {if $val["page"] == $page}current{/if}">{$val["label"]}</a></li>
				{foreachelse}
					<li>Ei ole</li>
				{/foreach}
			</ul>
			{/nocache}
		</div>
	</div>
	
	<div class="colmask rightmenu">
	
		<div class="{if !isset($DisableRight)}colleft{else}disabledcolright{/if}">
		
			<div class="{if !isset($DisableRight)}col1{else}colNoRight{/if}">{block name=PageContents}Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sagittis accumsan lectus ac semper. Nullam venenatis varius diam, ac interdum lacus rhoncus ac. Fusce in quam elit, et ornare erat. Quisque condimentum volutpat massa at dignissim. Integer ornare vehicula congue. Nunc eget neque a tortor placerat tincidunt vel eu neque. Nulla lorem lacus, semper a egestas vel, rutrum id lectus. Proin auctor gravida enim sollicitudin mollis. Sed condimentum aliquam nisl, ac pretium purus rutrum sit amet. Integer tempus malesuada fringilla. Vivamus a tortor sit amet sapien pretium venenatis sed ac sapien. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla facilisi. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam mattis libero a diam lacinia vehicula.{/block}</div>
			
			{if !isset($DisableRight)}
			<div class="col2">
			{nocache}
				{if isset($UserManager) && $UserManager->User->Valid}
				
					Hei, <b>{$UserManager->User->FacebookUser["name"]}!</b><br />
					<img src="https://graph.facebook.com/{$UserManager->User->FacebookId}/picture">
					<br />	
				
				{/if}
			
				{block name=RightMenu}
					{if isset($TourneyManager)}
						<b>Turniirid</b>
						<ul>
						{foreach $TourneyManager->tourneyTable->stdItems as $val}
							<li>{$val->NAME}</li>
						{foreachelse}
							<li>Pole hetkel!</li>
						{/foreach}
						<ul>
					{/if}
				{/block}
			{/nocache}	
			</div>
			{/if}
		
		</div>
	
	</div>
	
	<div id="footer">
		<p>LanMan v{$DeployConfig->Release} Tournament bracket engine by FOX</b>
	</div>

</div>

{/block}