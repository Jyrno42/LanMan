<!DOCTYPE html>
<html>
	<head>
		<title>{block name=title}Default Page Title{/block}</title>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="style/jquery.zclip.min.js"></script>
		<script src="http://malsup.github.com/jquery.form.js"></script>
		<script type="text/javascript" src="style/jquery.maskPost.js"></script>
  
		<link rel='stylesheet' id='smash-commentratings-css'  href="style/style.css" type='text/css' media='all' />
		<link rel="stylesheet" href="style/screen.css" type="text/css" media="screen" /> 
		
		<script type="text/javascript">
			$(document).ready(function() {  
			    var max = 0;  
			    $("label").each(function(){  
			        if ($(this).width() > max)  
			            max = $(this).width();     
			    });  
			    $("label").width(max);
			    $(".lMargin").css("marginLeft", max+10);
			    
			    $("select[name=Groups],#OtherTypeOptions input").change(
			    	function()
			    	{
			    		var vrs = "";
			    		
			    		if($("#OtherTypeOptions [name=ThirdPlaceMatch]").is(':checked'))
			    		{
			    			vrs = vrs + "&ThirdPlaceMatch=1";
			    		}
			    		if($("#OtherTypeOptions [name=DoubleElimination]").is(':checked'))
			    		{
			    			vrs = vrs + "&DoubleElimination=1";
			    		}
			    		
			    		jQuery.getJSON("API.php?action=TypeBuilder&Groups=" + $("select[name=Groups]").val() + vrs, function (ret){
			    			if(ret.error)
			    			{
			    				$(".error").html(ret.error);
			    			}
			    			else
			    			{
			    				$("input[name=type]").val(ret.type);
			    			}
			    		});
			    		if($("select[name=Groups]").val() == 0)
			    		{
			    			$("#OtherTypeOptions").show();
			    			$("#groupConfig").hide();
			    			$(".addTournament input[type=submit]").enable();
			    		}
			    		else if($("select[name=Groups]").val() == 1)
			    		{
			    			$("#OtherTypeOptions").hide();
			    			$("#groupConfig").show();
			    			$(".addTournament input[type=submit]").enable();
		    			}
		    			else
		    			{
			    			$("#OtherTypeOptions").hide();
			    			$("#groupConfig").hide();
		    				$(".addTournament input[type=submit]").attr("disabled", "disabled");
	    				}
			    	}
			    );
			    $(".hideAfter").hide();
			    $(".maskpost").maskPost();
			    $(".renderApiBuilder").maskPost({ selector: "#buildResult", errorLabel: "" });
			    $(".confirmpost").maskPost({ confirm: "Oled kindel?" });
			    $("#BaseClass").change(function (){
			    	
			    	$(".GameTypeData").hide();
			    	
			    	if($(this).val() != "-")
			    	{
			    		$("#" +$(this).val()).show(); 
			    		$(this).parent("form").children("input[type=submit]").enable();
			    	}
			    	else
			    	{
			    		$(this).parent("form").children("input[type=submit]").attr("disabled", "disabled");
			    	}
			    }); 
			    
			    $(".teammanager .head").click(
			    	function ()
			    	{
			    		$(this).parent(".teammanager").children(".body").toggle();
			    	}	
			    );
			});
		</script>
  
	</head>
	<body>
		{block name=body}{/block}
	</body>
</html>