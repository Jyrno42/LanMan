<?php

class FrontPageGit
{	
	public function FrontPageGit($smarty)
	{
		$git = new GetAllGitData("jyrno42", "LanMan", 30);
		
		if(!$smarty->isCached("index.tpl", $git->latestSha))
		{
			$git->Get();
			$smarty->assign("GitData", $git->commits);
		}
		
		$smarty->display("index.tpl", $git->latestSha);
	}
}

class GetAllGitData
{
	private $limit = 15;
	private $times = 0;
	
	public $commits = array();
	
	public $latestSha = null;

	public function GetAllGitData($user, $project, $limit = 15)
	{
		$this->user	= $user;
		$this->project = $project;
		$this->limit = $limit;
		
		$projectData = @file_get_contents(sprintf("https://api.github.com/repos/%s/%s/branches", $user, $project));
		if($projectData !== FALSE)
		{
			$branches = json_decode($projectData);
			foreach($branches as $k => $v)
			{
				if($v->name == "master")
				{
					$this->latestSha = $v->commit->sha;
					//$this->GetCommitData($v->commit->sha, $user, $project);
					break;
				}
			}
		}
	}
	
	public function Get()
	{
		$this->GetCommitData($this->latestSha, $this->user, $this->project);
	}
	
	private function GetCommitData($sha, $user, $project, $recurse=true)
	{
		$commitData = @file_get_contents(sprintf("https://api.github.com/repos/%s/%s/commits/%s", $user, $project, $sha));
		if($commitData !== FALSE)
		{
			$commit = json_decode($commitData);
			
			$this->commits[] = $commit->commit;
			
			if($recurse && $this->times < $this->limit && sizeof($commit->parents) > 0 && isset($commit->parents[0]))
			{
				$this->times++;
				return $this->GetCommitData($commit->parents[0]->sha, $user, $project);
			}
			
			return true;
		}
		return false;
	}
};

//$git = new GetAllGitData("jyrno42", "LanMan");
//var_dump($git);

?>
