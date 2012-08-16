<?php

define("FORCE_USER", "jyrno42@gmail.com");
define("USER_ID", "1");
define("USER_FB_ID", "100000994269157");

require_once("facebook/src/facebook.php");

class UserManager extends Dataman
{
	public $User = NULL;
	
	public $loginUrl = null;
	public $logoutUrl = null;
	
	private $facebook = null;
	
	public function UserManager($connection)
	{
		$this->facebook = new Facebook(array(
				'appId'  => '138356739524724',
				'secret' => '016160ae274a3883f13a0815b70f7b54',
		));
		
		$this->User = (object)null;
		$this->Authenticate();
		
		if($this->User->Valid)
		{
			parent::__construct($connection, "users", "UserID", "Email = '" . $this->User->FacebookUser["email"] . "'", false);
			//var_dump($this->User->FacebookUser);
			$this->CheckForRegistration();
		}
		else
		{
			// Construct without selecting anything so we can still use it to update automatically.
			parent::__construct($connection, "users", "UserID", "Email = 'must.not@be.true'", false);
		}
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
	
	public function Authenticate()
	{
		try
		{
			if(defined("FORCE_USER"))
			{
				$this->User->Valid = true;
				$this->User->FacebookId = constant("USER_FB_ID")?USER_FB_ID:"13";
				$this->User->MySql = (object)null;
				$this->User->MySql->UserID = USER_ID;
				
				$this->User->FacebookUser = array("email" => FORCE_USER, "name" => "DefaultUser");
				$this->logoutUrl = "?logout";
				return;
			}
			
			$user = $this->facebook->getUser();
			if($user)
			{
				$this->User->FacebookId = $user;
				$user_profile = $this->facebook->api('/me');
				$this->User->FacebookUser = $user_profile;
				
				$this->User->Valid = true;
								
				$this->logoutUrl = $this->facebook->getLogoutUrl(array('next' => PROJECT_PATH . "?do=logout"));
			}
			else
			{
				$this->loginUrl = $this->facebook->getLoginUrl(array("scope" => "user_status,email"));
				$this->User->Valid = false;
			}
		}
		catch (FacebookApiException $e)
		{
			echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
			$user = null;
			$this->User->Valid = false;
		}
	}
	
	public function Can($var)
	{
		return $this->User->Valid && isset($this->User->Rights) && isset($this->User->Rights[$var]) && $this->User->Rights[$var];
	}
	
	public function CheckForRegistration()
	{
		if(isset($this->User->MySql))
		{
			$r = new Rights();
			$this->User->Rights = $r->ParseRights($this->User->MySql->Rights);
		}
		else
		{
			$this->User->MySql = (object)null;
			$this->User->MySql->Email = $this->User->FacebookUser["email"];
			
			// Register
			$this->stdItems[] = $this->User->MySql;
		}
	}
	
	public function LoadCode($k, $row)
	{
		$this->User->MySql = (object)null;
		
		foreach($row as $k2 => $v2)
		{
			$this->User->MySql->$k2 = $v2;
		}
		$this->stdItems[$k] = $this->User->MySql;
	}
	
	public function UpdateCode($k, $v)
	{
		$r = new Rights();
		$v->Rights = $r->GetRights($this->User->Rights);
		
		foreach($v as $k2 => $v2)
		{
			$this->result[$k][$k2] = $v2;
		}
	}
	
	public function InsertCode($k, $v)
	{
		$this->stdItems[$k]->UserID = $this->Insert(array(
			"Email"=>$v->Email,
			"Rights" =>"0"		
		));
	}
	
	public function DeleteCode($k, $v)
	{
	}
};

class Rights
{
	public $Rights = array(
		"manage" => 0,			
		"manage_tournaments" => 1,			
		"manage_users" => 2,			
		"create_teams" => 3,			
		"unused_4" => 4,			
		"unused_5" => 5,			
		"unused_6" => 6,			
		"unused_7" => 7,			
		"unused_8" => 8,			
		"unused_9" => 9,			
		"unused_10" => 10,			
		"unused_11" => 11,			
		"unused_12" => 12,			
		"unused_13" => 13,			
		"unused_14" => 14,			
		"unused_15" => 15			
	);
	
	public function GetRights($array)
	{
		$ret = 0;
		foreach ($this->Rights as $k => $v)
		{
			$ret |= $array[$k] ? 1 << $v : 0 << $v;
		}
		return $ret;
	}
	
	public function ParseRights($val)
	{
		$ret = array();
		foreach ($this->Rights as $k => $v)
		{
			$ret[$k] = (bool)($val & 1 << $v);
		}
		return $ret;
	}
}
?>