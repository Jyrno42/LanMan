<?php

require_once("class.SvgHelper.php");

define("SVG_FORMAT_STR", "<object data='%s' type='image/svg+xml' width='100%%' height='%d'></object>");
define("SVG_API_URL", "API.php?action=Render&hash=%d&type=SVG&NameInTable=%d&GroupRenderType=%d&ShowGames=%d&ShowName=%d");

class TournamentRenderer
{
	/**
	 * The tournament we will be rendering.
	 * @var Tournament
	 */
	private $Tournament = null;
	
	const TABLE = 'groupstage';
	const SVG 	= 'svg_groupstage';
	
	const NO_GAMES = '-1';
	const GAMES_INSIDE = '0';
	const GAMES_OUTSIDE = '1';
	
	public $GroupWidth = null;
	public $ShowGames = true;
	
	public $ShowName = true;
	public $GroupNameInTable = false;
	
	public $GroupRenderType;
	
	public $Type = self::TABLE;
	
	// Keys of groups we do not want...
	public $GroupModifier = array();
	
	public function ShowGroup($id)
	{
		foreach ($this->Tournament->Stages["groups"]->Groups as $k=>$v)
		{
			if(
					(!is_array($id) && $k != $id) || // No array supplied, test for key match
					(is_array($id) && array_search($k, $id) === FALSE)
			)
			{
				$this->GroupModifier[$k] = true;
			}
		}
	}
	
	/**
	 * The Smarty instance we use.
	 * @var Smarty
	 */
	private $Smarty = null;
	
	public function TournamentRenderer(Tournament $t, Smarty $s)
	{
		$this->GroupRenderType = self::NO_GAMES;
		$this->Tournament = $t;
		$this->Smarty = $s;
	}
	
	public function Render()
	{
		$this->Assign();
		$this->Smarty->display($this->Type . ".tpl");
	}
	
	public function RenderStr()
	{
		$this->Assign();
		return $this->Smarty->fetch($this->Type . ".tpl");
	}
	
	private function Assign()
	{
		$this->Smarty->assign("TournamentRenderer", $this);
		$this->Smarty->assign("tournament", $this->Tournament);
		
		if($this->GroupWidth != null)
		{
			$this->Smarty->assign("GroupWidth", $this->GroupWidth);
		}
		
		if($this->Type == self::SVG)
		{
			$this->svg = new SVGHelper($this, $this->Tournament);
			$this->svg->Parse();
			$this->Smarty->assign("SVGHelper", $this->svg);
			
			header('Content-type: image/svg+xml');
		}
	}
}

?>