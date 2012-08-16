<?php

class DataManager
{
	public $connection = null;
	protected $mysqli = false;
	
	public $errorCallback = null;
	
	private function ThrowError(Exception $e)
	{
		if($this->errorCallback !== null)
		{
			call_user_func($this->errorCallback, $e);
		}
	}
	
	public function DataManager($host, $user, $pass, $db)
	{
		if(function_exists("mysqli_connect"))
		{
			$this->mysqli = true;
			if(($this->connection = @mysqli_connect($host, $user, $pass, $db)) === FALSE)
			{
				$this->ThrowError(new Exception("MYSQLI: Failed to connect(" . mysqli_connect_errno() . "). " . mysqli_connect_error()));
			}
		}
		else
		{
			if(($this->connection = mysql_connect($host, $user, $pass, true)) !== FALSE)
			{
				if(!mysql_select_db($db, $this->connection))
				{
					$this->ThrowError(new Exception("MYSQL: Failed to select database!"));
				}
			}
			else
			{
				$this->ThrowError(new Exception("MYSQL: Failed to connect!"));
			}
		}
	}
	
	public function mysql_error()
	{
		return $this->mysqli ?
			 mysqli_error($this->connection) . ". (" . mysqli_errno($this->connection) . ")" : 
			 mysql_errno($this->connection) . ". (" . mysql_errno($this->connection) . ")"; 
	}
	
	public function mysql_query($a1)
	{
		$ret = $this->mysqli ? mysqli_query($this->connection, $a1) : mysql_query($a1, $this->connection);
		if($ret === FALSE)
		{
			$this->ThrowError(new Exception($this->mysql_error() . " " . $a1));
		}
		return $ret;
	}
	
	public function mysql_escape_string($str)
	{
		return $this->mysqli ? mysqli_escape_string($this->connection, $str) : mysql_real_escape_string($str, $this->connection);
	}
	
	public function mysql_insert_id()
	{
		return $this->mysqli ? mysqli_insert_id($this->connection) : mysql_insert_id($this->connection);
	}
	
	public function mysql_num_rows($result)
	{
		return $this->mysqli ? mysqli_num_rows($result) : mysql_num_rows($result);
	}
	public function mysql_free_result($result)
	{
		return $this->mysqli ? mysqli_free_result($result) : mysql_free_result($result);
	}
	public function mysql_fetch_assoc($result)
	{
		return $this->mysqli ? mysqli_fetch_assoc($result) : mysql_fetch_assoc($result);
	}
}

?>