<?php

class SVGHelper
{
	public $ContainerOffset = 0;
	public $Height = 0;
	
	public $rowH = 40;
	
	/**
	 * Debug option for svg, if true shows red lines accross table row breaks.
	 * @var bool
	 */
	public $showLines = false;
	
	private $tournament;
	private $renderer;
	
	public function SVGHelper(TournamentRenderer $tRender, Tournament $t)
	{
		$this->tournament = $t;
		$this->renderer = $tRender;
	}
	
	public function Parse()
	{
		// Basic checks for errors
		if(!isset($this->tournament) || !isset($this->tournament->Stages["groups"]) || sizeof($this->tournament->Stages["groups"]->Groups) < 1) 
		{
			$this->Height = 40;	
		}
		
		if($this->renderer->ShowName)
		{
			$this->ContainerOffset = 40;
			$this->Height = 40;
		}
		
		if($this->renderer->GroupRenderType == TournamentRenderer::GAMES_INSIDE)
		{
			$this->CalcGroupsInside();
		}
		else
		{
			$this->CalcGroups();
		}
		
		$this->Height += 100;
	}
	
	private function CalcGroups()
	{
		$this->groups = array();
		
		$idx = 0;
		foreach($this->tournament->Stages["groups"]->Groups as $k => $Group)
		{
			if(!isset($this->renderer->GroupModifier[$k]))
			{
				$rowC = sizeof($Group->teams) + 1;
				$myX = $idx * ($this->rowH * $rowC + 2 * $this-> rowH);
				$myH = $this->renderer->GroupNameInTable ? $this->rowH * ($rowC + 1) : $myH = $this->rowH * $rowC;
				$gH = 0;
				
				if($this->renderer->GroupRenderType == TournamentRenderer::GAMES_OUTSIDE)
				{
					// Modify heights for outside render
					$n = sizeof($this->tournament->GetGamesForGroup($Group));
					$gH = $n * $this->rowH;
					$myX += $idx * $gH;
				}
				
				$this->groups[$k] = array();
				
				$this->groups[$k]["head"] = $this->renderer->GroupNameInTable ? 35 : 25;
				$this->groups[$k]["textMod"] = $this->rowH - $this->groups[$k]["head"];
				$this->groups[$k]["height"] = $myH;
				$this->groups[$k]["y"] = $this->renderer->GroupNameInTable ? 10 : 40;
				$this->groups[$k]["x"] = $myX;
				$this->groups[$k]["rowC"] = $rowC;
				$this->groups[$k]["rowH"] = $this->rowH;
				$this->groups[$k]["gH"] = $gH;
				
				$this->groups[$k]["rows"] = array();
				for($i = 0; $i < $rowC + 2; $i++)
				{
					$this->groups[$k]["rows"][$i] = array();
					$this->groups[$k]["rows"][$i]["y"] = $i * $this->rowH;
					
					$this->groups[$k]["rows"][$i]["text"] = $this->groups[$k]["rows"][$i]["y"] + $this->groups[$k]["head"];
				}
				
				$myH += $this->rowH;
				if(!$this->renderer->GroupNameInTable)
					$myH += $this->rowH;
				
				$this->Height += $myH;
				
				$this->groups[$k]["gY"] = $myH - $this->rowH;
				if($this->renderer->GroupNameInTable)
					$this->groups[$k]["gY"] += 10;
				
				$this->Height += $gH;
				
				$idx++;
			}
		}
		
	}
	private function CalcGroupsInside()
	{
		$this->groups = array();
	
		$idx = 0;
		foreach($this->tournament->Stages["groups"]->Groups as $k => $Group)
		{
			if(!isset($this->renderer->GroupModifier[$k]))
			{
				$rowC = sizeof($Group->teams) + 1;
				$myX = $idx * ($this->rowH * $rowC + 2 * $this-> rowH);
				$myH = $this->renderer->GroupNameInTable ? $this->rowH * ($rowC + 1) : $myH = $this->rowH * $rowC;
				$gH = 0;
	
				if($this->renderer->GroupRenderType == TournamentRenderer::GAMES_OUTSIDE)
				{
					// Modify heights for outside render
					$n = sizeof($this->tournament->GetGamesForGroup($Group));
					$gH = $n * $this->rowH;
					$myX += $idx * $gH;
				}
	
				$this->groups[$k] = array();
	
				$this->groups[$k]["head"] = $this->renderer->GroupNameInTable ? 35 : 25;
				$this->groups[$k]["textMod"] = $this->rowH - $this->groups[$k]["head"];
				$this->groups[$k]["height"] = $myH;
				$this->groups[$k]["y"] = $this->renderer->GroupNameInTable ? 10 : 40;
				$this->groups[$k]["x"] = $myX;
				$this->groups[$k]["rowC"] = $rowC;
				$this->groups[$k]["rowH"] = $this->rowH;
				$this->groups[$k]["gH"] = $gH;
	
				$this->groups[$k]["rows"] = array();
				for($i = 0; $i < $rowC + 2; $i++)
				{
				$this->groups[$k]["rows"][$i] = array();
				$this->groups[$k]["rows"][$i]["y"] = $i * $this->rowH;
					
				$this->groups[$k]["rows"][$i]["text"] = $this->groups[$k]["rows"][$i]["y"] + $this->groups[$k]["head"];
				}
	
				$myH += $this->rowH;
				if(!$this->renderer->GroupNameInTable)
					$myH += $this->rowH;
	
					$this->Height += $myH;
	
					$this->groups[$k]["gY"] = $myH - $this->rowH;
					if($this->renderer->GroupNameInTable)
					$this->groups[$k]["gY"] += 10;
	
					$this->Height += $gH;
	
				$idx++;
			}
		}
	}	
};

?>