<?php

class Team
{
	/**
	 * The unique ID for this team, used to differenciate between teams internally.
	 * @var string
	 */
	public $uniqueID = "";
	
	/**
	 * The name of this team.
	 * @var string
	 */
	public $Name = "";
	
	/**
	 * The abbrevation for this team.
	 * @var string
	 */
	public $Abbrevation = "";
	
	/**
	 * The players in this team.
	 * @var array(Player)
	 */
	public $Players = array();
	
	/**
	 * The tournamentID => Seed of this team.
	 * @var array(tournamentID => Seed)
	 */
	public $Seeds = array();
	
	/**
	 * The ownerid of this team(used for users team management).
	 * @var int
	 */
	public $OwnerID = 0;

	/**
	 * A key used to join the team.
	 * 
	 * @var string
	 */
	public $JoinKey = "";
	
	/**
	 * A key used to join the team as administrator.
	 * 
	 * @var string
	 */
	public $AdminKey = "";
	
	public $TournamentID = 0;
	
	/**
	 * Create a team.
	 * 
	 * @param int $uniqueID
	 * @param string $name
	 * @param string $abbrevation
	 * @param array $players
	 */
	public function Team($uniqueID, $name, $abbrevation, $players)
	{
		$this->uniqueID = $uniqueID;
		$this->Name = $name;
		$this->Abbrevation = $abbrevation;
		$this->Players = $players;
	}
};

?>