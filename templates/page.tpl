{extends file="layout.tpl"}
{block name=title}LanMan - {block name=PageTitle}Home{/block}{/block}
{block name=body}

<div class="container">

	<div id="header">
	
		<div class="logo">
			<h1>{block name=header}LanMan{/block}<h1>
		</div>
	
		<div class="menu">

			<a href="?page=index">Home</a>
			{if isset($UserManager) && $UserManager->User->Valid}			
				<a href="?page=teams">Teams</a>
				
				{if $UserManager->Can("manage")}
					<a href="?page=manage">Manage</a>
				{/if}
				<a href="{$UserManager->logoutUrl}">Logout</a>
			{else if isset($UserManager)}
				<a href="{$UserManager->loginUrl}">Login with Facebook</a>
			{/if}

		</div>
	</div>
	
	<div class="colmask rightmenu">
	
		<div class="colleft">
		
			<div class="col1">{block name=PageContents}Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sagittis accumsan lectus ac semper. Nullam venenatis varius diam, ac interdum lacus rhoncus ac. Fusce in quam elit, et ornare erat. Quisque condimentum volutpat massa at dignissim. Integer ornare vehicula congue. Nunc eget neque a tortor placerat tincidunt vel eu neque. Nulla lorem lacus, semper a egestas vel, rutrum id lectus. Proin auctor gravida enim sollicitudin mollis. Sed condimentum aliquam nisl, ac pretium purus rutrum sit amet. Integer tempus malesuada fringilla. Vivamus a tortor sit amet sapien pretium venenatis sed ac sapien. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla facilisi. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam mattis libero a diam lacinia vehicula.{/block}</div>
			
			<div class="col2">
			
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
			
			</div>
		
		</div>
	
	</div>
	
	<div id="footer">
		<p>Tournament bracket engine by FOX</b>
	</div>

</div>

{/block}