{extends file="page.tpl"}
{block name=PageContents}

<script type="text/javascript">
	$(document).ready(function() { 
	
		var validation = function(event, ui) 
		{
			var ret = true;
			
			$(".createTournament").ajaxForm();
			var selected = $(".tabpanel").tabs('option', 'selected');
			
			jQuery.ajax({
				
				url: "API.php?action=WizardValidate&tab=" + selected + "&" + $(".createTournament").formSerialize(),
				dataType: "json",
				success: 
					function(responceText)
					{
						if(responceText.error)
						{
							$(".wizardError").html(responceText.error);
							ret = false;
						}
						else if(responceText.result)
						{
							$(".wizardError").html(responceText.result);
							ret = true;
						}
						else
						{
							$(".wizardError").html("Something went incredibad!");
							ret = false;
						}
					},
				async: false
				
			});
			
        	return ret;
    	}
	
		$(".tabpanel").tabs(
			{
				selected: 0,
				select: validation
			}
		);
	
		$("input[name=type]").change(
			function ()
			{
				if($(this).val())
				{					
					$.get("API.php?action=WizardStageConfig&type=" + $(this).val(), 
						function (ret)
						{
							$("#tabs-4").html(ret);
						}
					);
				}
				else
				{
					$("#tabs-4").html("stageconf:");
				}
			}
		);
		
		$("select[name=Game]").change(
			function ()
			{
				if($(this).val() && $(this).val() != "-")
				{
					$.get("API.php?action=WizardGameConfig&game=" + $(this).val(),
						function (ret)
						{
							$("#tabs-5").html(ret);
						}
					);
				}
				else
				{
					$("#tabs-5").html("gameconf:");
				}
			}
		);
		
	});
</script>

<h2>Create a Tournament</h2>

<div class="wizardError">
</div>
<form method="get" action="API.php?action=CreateWizarded" class="createTournament">
<div class="tabpanel">
	
	<ul>
		<li><a href="#tabs-1">Type</a></li>
		<li><a href="#tabs-2">General Data</a></li>
		<li><a href="#tabs-3">Reports</a></li>
		<li><a href="#tabs-4" class="stageConfLoader">Stage Config</a></li>
		<li><a href="#tabs-5">Game Config</a></li>
		<li><a href="#tabs-6">Participants</a></li>
	</ul>

	<div class="tab" id="tabs-1">
		
		<h3>Pick type</h3>
		
		{foreach Stages::instance()->GetAllStages() as $val}
			<div class="h125">
				<div class="leftCentered">
					<span>
						<input type="radio" name="type" value="{$val[0]}" />{$val[1]}
					</span>
				</div>
				<div class="rightCentered">
					<p>
						<span>
						{$val[2]}
						</span>
					</p> 
				</div>
			</div>
		{/foreach}
			<div class="h125">
				<div class="leftCentered">
					<span>
						<input type="radio" name="type" value="TOURNAMENT::BRACKETS" />Brackets
					</span>
				</div>
				<div class="rightCentered">
					<p>
						<span>
							Teams are placed into <b class="Highlight">brackets</b>, and <b class="Highlight">eliminate</b> eachother until only one remains 
							<br/>
							<br/>
							<b class="Highlight">Single</b> and <b class="Highlight">double</b>
 							elimination both supported
						</span>
					</p> 
				</div>
			</div>
			<div class="h125">
				<div class="leftCentered">
					<span>
						<input type="radio" name="type" value="TOURNAMENT::CHAMPIONSHIP" />Championship
					</span>
				</div>
				<div class="rightCentered">
					<p>
						<span>
							Teams are placed into a <b class="Highlight">league</b>, and compete <b class="Highlight">rounds</b> until season is over.
							<br />
							<br />
							Teams get <b class="HighLight">points</b> based on their place in each round. 
						</span>
					</p> 
				</div>
			</div>
		
	</div>
	<div class="tab" id="tabs-2">
		
		<fieldset>
			
			<h3>Tournament General Data</h3>
			<div style="margin-left: 20px" <label="" for="tournamentName">
				Title 
				<div class="PaddedInput">
					<input type="text" id="Title" name="tournamentName" value=""/>
				</div>
			</div>
			<div style="margin-left: 20px" <label="" for="Game">
				Game TODO: like stages are now
				<div class="PaddedInput">
					<select name="Game">
						<option>-</option>
						{foreach Games::instance()->GetAll() as $val}
							<option value="{$val[0]}">{$val[1]}</option>
						{/foreach}
					</select>
				</div>
			</div>
			
		</fieldset>
	</div>
	<div class="tab" id="tabs-3">
		Later can be set if we want teams to be able to report games themselves.
		Also set post screenshots is required or optional.
		DISALBED
	</div>
	
	<div class="tab" id="tabs-4">
		stageconf:
	</div>
	
	<div class="tab" id="tabs-5">
		roundconf:
	</div>
	
	<div class="tab" id="tabs-6">
		Participants
	</div>

</div>

</form>

{/block}