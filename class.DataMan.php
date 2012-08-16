<?php

abstract class Dataman
{
	public $idField;
	public $table;
	public $debug;
	
	public $result = array();
	public $stdItems = array();
	private $backup = array();
	
	protected $connection = null;
	
	public function Dataman($connection, $table, $idfield = "id", $where = false, $debug = false)
	{
		$this->connection = $connection;
		$this->table = $table;
		$this->idField = $idfield;
		$this->debug = $debug;
		
		$this->result = $this->GetItems($where);
		$this->backup = $this->result;
		
		foreach($this->result as $k => $row)
		{
			$this->LoadCode($k, $row);
		}
	}
	
	public function AddNoInsertRow( $rowId, $rowArr )
	{
		if(!isset($this->backup[$rowId]))
		{
			$this->result[$rowId] = $rowArr;
		}
	}
	
	public function __destruct()
	{
		foreach($this->stdItems as $k => $v)
		{
			if($v === null)
				continue;
			
			if(isset($this->result[$k]))
			{
				$this->UpdateCode($k, $v);
			}
			else
			{
				$this->InsertCode($k, $v);
			}
		}
		
		// Mark removed for deletion
		foreach($this->result as $k => $v)
		{
			$this->DeleteCode($k, $v);
		}
		
		foreach($this->backup as $k => $v)
		{
			if(is_array($v))
			{
				if(!isset($this->result[$k]))
				{
					$val = $this->connection->mysql_escape_string($v[$this->idField]);
					$query = "DELETE FROM $this->table WHERE $this->idField = '" . $val . "'";
					$this->connection->mysql_query($query);
				}
				else
				{
					$upd = false;
					foreach($v as $k2 => $v2)
					{
						if($v2 != $this->result[$k][$k2])
						{
							if(!$upd) $upd = "";
							else $upd .= ", ";
							$theVal = $this->connection->mysql_escape_string($this->result[$k][$k2]);
							$upd .= "$k2 = '" . $theVal . "'";
						}
					}
					
					if($upd)
					{
						$query = "UPDATE $this->table SET $upd WHERE $this->idField = '" . $v[$this->idField] . "'";
						$this->connection->mysql_query($query);
					}
				}
			}
		}
	}
	
	private function GetItems($where)
	{
		$ret = array();
		$query = "SELECT * FROM $this->table";
		if($where) $query .= " WHERE $where";
		if($this->debug) print $query;
		
		$result = $this->connection->mysql_query($query);
		if($result !== FALSE && $this->connection->mysql_num_rows($result) > 0)
		{
			while(($row = $this->connection->mysql_fetch_assoc($result)) !== NULL)
			{
				$ret[$row[$this->idField]] = $row;
			}
		}
		return $ret;
	}
	
	public function Insert($array)
	{
		$fields = false;
		$values = false;
		
		foreach($array as $k => $v)
		{
			if(!$fields) $fields = "";
			else $fields .= ", ";
			if(!$values) $values = "";
			else $values .= ", ";
			
			$theVal = $this->connection->mysql_escape_string($v);
			
			$fields .= "`$k`";
			$values .= "'$theVal'";
		}
		
		if($fields && $values)
		{
			$query = "INSERT INTO $this->table($fields) VALUES($values)";
			$this->connection->mysql_query($query);
			
			$id = $this->connection->mysql_insert_id();
			$array[$this->idField] = $id;
			$this->result[$id] = $array;
			$this->backup = $this->result[$id];
			return $id;
		}
		return false;
	}
	
	public abstract function LoadCode($k, $row);
	public abstract function UpdateCode($k, $v);
	public abstract function InsertCode($k, $v);
	public abstract function DeleteCode($k, $v);
}

class DefaultDataman extends Dataman
{
	public function Dataman($connection, $table, $idfield = "id", $where = false, $debug = false)
	{
		parent::__construct($connection, $table, $idfield, $where, $debug);
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}

	public function LoadCode($k, $row)
	{
	}
	public function UpdateCode($k, $v)
	{
	}
	public function InsertCode($k, $v)
	{
		
	}
	public function DeleteCode($k, $v)
	{
		
	}
}

?>
