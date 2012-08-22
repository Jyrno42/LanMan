<?php

static $vals = array();

function smarty_block_custom_table($params, $content, &$smarty, &$repeat)
{
	if (isset($content)) 
	{ 
		$content = smarty_block_close__custom_table($params, $content, $smarty, $repeat); 
	}
	else 
	{ 
		smarty_block_init__custom_table($params, $content, $smarty, $repeat); 
	} 
	
	if ($repeat) 
	{
		smarty_block_open__custom_table($params, $content, $smarty, $repeat);
	}
	
	if(!$repeat) // Last one!
	{
		$emptyTxt = empty($params["empty"])?"None":$params["empty"];
		 
		$cols = empty($params["cols"])?array("None"):$params["cols"];
		if(!is_array($cols))
			$cols = explode(",", $cols);
		
		$_Html = "";
		$_Html .= "<table id=\"rounded-corner\"  style=\"width: 90%\"><thead><tr>";
		foreach($cols as $k => $v)
		{
			$_Html .= "<th>$v</th>";
		}
		
		$dat = $smarty->getTemplateVars("SecretContents");
		if($dat === null)
		{
			$dat = "<tr><td colspan=\"" . sizeof($cols) . "\">$emptyTxt</td></tr>";
		}
		$_Html .= $dat;
		
		$_Html .= "</tr></thead></table>";
		return $_Html;
	}
}

function smarty_block_init__custom_table($params, $content, &$smarty, &$repeat) 
{
	global $vals;
	$vals[0] = $params["vals"];
	reset($vals[0]);
}

/* called on each opening of a block, may set $repeat */ 
function smarty_block_open__custom_table($params, $content, &$smarty, &$repeat) 
{
	global $vals;
	if(!is_array($vals[0]) || sizeof($vals[0]) < 1)
	{
		$smarty->assign("SecretContents", null);
		$repeat = false;
	}
	else
	{
		$val = current($vals[0]);
		$smarty->assign("val", $val);
		$repeat = true;
	}
}

function smarty_block_close__custom_table($params, $content, &$smarty, &$repeat)
{
	global $vals;
	$repeat = next($vals[0]) !== FALSE;
	
	$C = $smarty->getTemplateVars("SecretContents") ?  $smarty->getTemplateVars("SecretContents") : "";
	$smarty->assign("SecretContents", $C . $content);
	
	return $content;
}

?>
