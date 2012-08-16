<?php

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
	 * The game accounts associated with this player. TODO: Add the gameaccount logic.
	 * @var array(GameAccount)
	 */
	public $gameAccounts = array();
	
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