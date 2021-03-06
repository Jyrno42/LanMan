<?php

/**
 * A class which provides the API functionalities to our program. Allows to create different actions,
 * set result type, uses handlers and more.
 * 
 * @author TH3F0X
 *
 */
class API
{
	/**
	 * The registered actions in our application are stored here.
	 * 
	 * @var array(string => handler, ..)
	 * @access protected
	 */
	protected $Actions = array();
	
	/**
	 * The $_GET key we will get our action from, defaults to 'action'.
	 * @var string
	 */
	public $ActionKey = "action";
	
	/**
	 * The default action we want to use.
	 * @var mixed/string
	 * @access private
	 */
	private $defAction = null;

	/**
	 * A constant returnType value for JSON.
	 * @static
	 */
	const RETURN_JSON = 0;

	/**
	 * A constant returnType value for SVG.
	 * @static
	 */
	const RETURN_SVG = 1;
	
	/**
	 * The type of return we want to use.
	 * @var int
	 */
	public $Type = self::RETURN_JSON;
	
	/**
	 * The main constructor of API class. You can use this to fill the Actions array from child classes.
	 */
	public function API()
	{
		
	}
	
	/**
	 * Will use the Actions array to check actions and call appropriate handlers if needed.
	 */
	public function Strap($arg)
	{
		$action = ApiHelper::GetParam($this->ActionKey, false, null, null);
		
		if($action !== null && strlen($action) > 0)
		{
			foreach($this->Actions as $k => $v)
			{
				if($k == $action)
				{
					$res = call_user_func_array($v, array());
					if($res !== null)
					{
						print $this->SendResult($res);
					}
					return true; 
				}
			}
			throw new Exception("Action $action is not yet implemented.");
		}
		
		if($this->defAction !== null)
		{
			print $this->SendResult(call_user_func_array($this->Actions[$this->defAction], array()));
			return true;
		}
		throw new Exception("Action parameter is invalid.");
	}
	
	public function AddAction($action, $callback, $default=false)
	{
		$this->Actions[$action] = $callback;
		if($default)
		{
			$this->defAction = $action;
		}
	}
	
	/**
	 * Send a result to user using the selected output type.
	 * 
	 * @param mixed $object
	 * @return mixed|string
	 */
	private function SendResult($object)
	{
		if($this->Type == self::RETURN_SVG)
		{
			return ApiHelper::ReturnSVG($object);
		}
		else
		{
			return ApiHelper::ReturnJson($object);
		}
	}
	
	public function Error(Exception $e)
	{
		ob_end_clean();
		die($this->SendResult(ApiHelper::Error($e->getMessage())));
	}
}

?>
