<?php

class DeploymentConfigLoader
{
	public $Name = "";
	
	public $params = array();
	public $errors = array();
	
	public function DeploymentConfigLoader()
	{
		if(!file_exists("deployment.xml"))
			throw new Exception("Deployment configuration file not found!");
		
		$fileStr = @file_get_contents("deployment.xml");
		if($fileStr === FALSE)
			throw new Exception("Deployment configuration file failed to open!");
		
		$xml = simplexml_load_string($fileStr);
		
		$this->ParseParameters($xml->parameters->parameter);
		
		$this->Release = $xml->version->release;
	}
	
	public function ParseParameters($params)
	{
		foreach($params as $k => $v)
		{
			$this->params[(string)$v["id"]] = array(
				"display" => (string)$v["display"],
				"id" => (string)$v["id"],
				"required" => $v["required"] == "true",
				"defaultvalue" => $v->defaultvalue
			);
		}
	}
	
	public function HandleInstall()
	{
		if(isset($_POST["install"]))
		{
			foreach($_POST as $k => $v)
			{
				if(isset($this->params[$k]))
				{
					if($this->params[$k]["required"] && strlen($v) < 1)
					{
						$this->errors[] = new Exception($this->params[$k]["display"] . " is required!");
					}
					else
					{
						$varName = "ZS_" . strtoupper($k);
						putenv("$varName=$v");
				//		print $varName;
					}
				}
			}
			
			if(sizeof($this->errors) == 0)
			{				
				define("WEB_INSTALL", true);
				putenv("ZS_BASE_URL=" . $_SERVER["DOCUMENT_ROOT"]);
				putenv("ZS_APPLICATION_BASE_DIR=" . getcwd());
				putenv("ZS_RUN_ONCE_NODE=1");

				include("scripts/post_stage.php");
				include("scripts/post_activate.php");
			}
		}
	}
}

?>