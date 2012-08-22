<?php

class ApiHelper
{
	public static function ToHash($val, $key, $type=MCRYPT_3DES)
	{
		return 	urlencode(base64_encode(mcrypt_encrypt($type, $key, $val, "ecb")));
	}
	
	public static function FromHash($val, $key, $type=MCRYPT_3DES)
	{
		return mcrypt_decrypt($type, $key, base64_decode(urldecode($val)), "ecb");
	}
	
	public static function GenerateRandomness($lenght)
	{
		$randomInt = rand(0, 100000);
		return substr(md5($randomInt), 0, $lenght);
	}
	
	public static function RequestValidate($private, $passCode)
	{
		$auth = self::FromHash(self::GetParam("auth", true), $private, MCRYPT_RIJNDAEL_128);
		if($auth != $passCode)
		{
			throw new Exception("Bad auth key.");
		}
	}
	
	public static function StringMin($str, $len, $key)
	{
		if(!is_string($str) || strlen($str) < $len)
		{
			throw new Exception("String $key must be longer than $len.");
		}
	}
	
	public static function StringMax($str, $len, $key)
	{
		if(!is_string($str) || strlen($str) > $len)
		{
			throw new Exception("String $key must be shorter than $len.");
		}
	}
	
	public static function GetParam($key, $required=false, $type=null, $default="")
	{
		if(isset($_GET[$key]))
		{
			if($type != null)
			{
				if($type == "array" && !is_array($_GET[$key]))
				{
					throw new Exception("Parameter $key must be type of $type, " . gettype($_GET[$key]) . " supplied.");
				}
				if($type == "int" && !is_numeric($_GET[$key]))
				{
					throw new Exception("Parameter $key must be type of $type, " . gettype($_GET[$key]) . " supplied.");
				}
			}
			return $_GET[$key];
		}
		else
		{
			if($required)
				throw new Exception("Parameter $key is required but not supplied!");
			return $default;
		}
	}
	
	public static function Error($message)
	{
		return array("error" => $message);
	}
	
	public static function ReturnJson($val)
	{
		return json_encode($val);
	}
	
	public static function ReturnSVG($val)
	{
		return var_export($val, true);
	}
}

?>