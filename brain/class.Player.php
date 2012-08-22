<?php


/**
 * Gameaccounts class.
 * @author TH3F0X
 *
 */
class Player
{
	/**
	 * The unique ID for each player, this is used to link players to teams etc.
	 * @var string
	 */
	public $uniqueID = "";
	
	/**
	 * 
	 */
	public $UserID = 0;
	
	/**
	 * The name of the player.
	 * @var string
	 */
	public $Name;
	
	/**
	 * The game of this gameAccount.
	 * @var string
	 */
	public $Game = "DefaultGame";
	
	/**
	 * The constructor for player object.
	 * @param string $uniqueID
	 * @param string $name
	 */
	public function Player($uniqueID, $name)
	{
		$this->uniqueID = $uniqueID;
		$this->Name = $name;
	}
};

?>