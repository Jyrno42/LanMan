CREATE TABLE IF NOT EXISTS `games` (
  `gameID` int(11) NOT NULL AUTO_INCREMENT,
  `tournamentID` int(11) NOT NULL,
  `Played` int(1) NOT NULL,
  `team1ID` int(11) NOT NULL,
  `team2ID` int(11) NOT NULL,
  `score1` int(11) NOT NULL,
  `score2` int(11) NOT NULL,
  `uniqueHash` varchar(32) NOT NULL,
  PRIMARY KEY (`gameID`),
  UNIQUE KEY `uniqueHash` (`uniqueHash`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `gametypes` (
  `gtID` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(64) NOT NULL,
  `className` varchar(32) NOT NULL,
  `arguments` varchar(128) NOT NULL,
  PRIMARY KEY (`gtID`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;

INSERT INTO `gametypes` (`gtID`, `tag`, `className`, `arguments`) VALUES
(1, 'lol_1x1_provinggrounds_blind', 'LeagueOfLegends', '1,7,1'),
(2, 'lol_1x1_provinggrounds_draft', 'LeagueOfLegends', '1,7,2'),
(3, 'lol_1x1_provinggrounds_random', 'LeagueOfLegends', '1,7,4'),
(4, 'lol_1x1_provinggrounds_tournament_draft', 'LeagueOfLegends', '1,7,6'),
(5, 'lol_2x2_provinggrounds_blind', 'LeagueOfLegends', '2,7,1'),
(6, 'lol_2x2_provinggrounds_draft', 'LeagueOfLegends', '2,7,2'),
(7, 'lol_2x2_provinggrounds_random', 'LeagueOfLegends', '2,7,4'),
(8, 'lol_2x2_provinggrounds_tournament_draft', 'LeagueOfLegends', '2,7,6'),
(9, 'lol_3x3_provinggrounds_blind', 'LeagueOfLegends', '3,7,1'),
(10, 'lol_3x3_provinggrounds_draft', 'LeagueOfLegends', '3,7,2'),
(11, 'lol_3x3_provinggrounds_random', 'LeagueOfLegends', '3,7,4'),
(12, 'lol_3x3_provinggrounds_tournament_draft', 'LeagueOfLegends', '3,7,6'),
(13, 'lol_5x5_provinggrounds_blind', 'LeagueOfLegends', '5,7,1'),
(14, 'lol_5x5_provinggrounds_draft', 'LeagueOfLegends', '5,7,2'),
(15, 'lol_5x5_provinggrounds_random', 'LeagueOfLegends', '5,7,4'),
(16, 'lol_5x5_provinggrounds_tournament_draft', 'LeagueOfLegends', '5,7,6'),
(17, 'lol_1x1_twistedtreeline_blind', 'LeagueOfLegends', '1,4,1'),
(18, 'lol_1x1_twistedtreeline_draft', 'LeagueOfLegends', '1,4,2'),
(19, 'lol_1x1_twistedtreeline_random', 'LeagueOfLegends', '1,4,4'),
(20, 'lol_1x1_twistedtreeline_tournament_draft', 'LeagueOfLegends', '1,4,6'),
(21, 'lol_2x2_twistedtreeline_blind', 'LeagueOfLegends', '2,4,1'),
(22, 'lol_2x2_twistedtreeline_draft', 'LeagueOfLegends', '2,4,2'),
(23, 'lol_2x2_twistedtreeline_random', 'LeagueOfLegends', '2,4,4'),
(24, 'lol_2x2_twistedtreeline_tournament_draft', 'LeagueOfLegends', '2,4,6'),
(25, 'lol_3x3_twistedtreeline_blind', 'LeagueOfLegends', '3,4,1'),
(26, 'lol_3x3_twistedtreeline_draft', 'LeagueOfLegends', '3,4,2'),
(27, 'lol_3x3_twistedtreeline_random', 'LeagueOfLegends', '3,4,4'),
(28, 'lol_3x3_twistedtreeline_tournament_draft', 'LeagueOfLegends', '3,4,6'),
(29, 'lol_1x1_summonersrift_blind', 'LeagueOfLegends', '1,1,1'),
(30, 'lol_1x1_summonersrift_draft', 'LeagueOfLegends', '1,1,2'),
(31, 'lol_1x1_summonersrift_random', 'LeagueOfLegends', '1,1,4'),
(32, 'lol_1x1_summonersrift_tournament_draft', 'LeagueOfLegends', '1,1,6'),
(33, 'lol_2x2_summonersrift_blind', 'LeagueOfLegends', '2,1,1'),
(34, 'lol_2x2_summonersrift_draft', 'LeagueOfLegends', '2,1,2'),
(35, 'lol_2x2_summonersrift_random', 'LeagueOfLegends', '2,1,4'),
(36, 'lol_2x2_summonersrift_tournament_draft', 'LeagueOfLegends', '2,1,6'),
(37, 'lol_3x3_summonersrift_blind', 'LeagueOfLegends', '3,1,1'),
(38, 'lol_3x3_summonersrift_draft', 'LeagueOfLegends', '3,1,2'),
(39, 'lol_3x3_summonersrift_random', 'LeagueOfLegends', '3,1,4'),
(40, 'lol_3x3_summonersrift_tournament_draft', 'LeagueOfLegends', '3,1,6'),
(41, 'lol_5x5_summonersrift_blind', 'LeagueOfLegends', '5,1,1'),
(42, 'lol_5x5_summonersrift_draft', 'LeagueOfLegends', '5,1,2'),
(43, 'lol_5x5_summonersrift_random', 'LeagueOfLegends', '5,1,4'),
(44, 'lol_5x5_summonersrift_tournament_draft', 'LeagueOfLegends', '5,1,6'),
(45, 'fifa_1v1', 'DefaultGame', 'Fifa 2012,Fifa'),
(46, 'DefaultGame', 'DefaultGame', 'DefaultGame,Short'),
(47, 'Demigod', 'DefaultGame', 'Demigod,Dem');

CREATE TABLE IF NOT EXISTS `groupstageconfig` (
  `confID` int(11) NOT NULL AUTO_INCREMENT,
  `tourneyID` int(11) NOT NULL,
  `GroupSize` int(3) NOT NULL,
  `AdvanceTeams` int(3) NOT NULL,
  `WinPoints` int(3) NOT NULL DEFAULT '3',
  `TiePoints` int(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`confID`),
  UNIQUE KEY `tourneyID` (`tourneyID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `players` (
  `playerID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL DEFAULT '0',
  `nick` varchar(32) NOT NULL,
  `game` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`playerID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `teams` (
  `teamID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) NOT NULL,
  `Abbrevation` varchar(6) NOT NULL,
  `teamOwner` int(11) NOT NULL DEFAULT '0',
  `players` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`teamID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tournamentseed` (
  `seedId` int(11) NOT NULL AUTO_INCREMENT,
  `tournamentId` int(11) NOT NULL,
  `teamID` int(11) NOT NULL,
  `Seed` int(11) NOT NULL,
  PRIMARY KEY (`seedId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tourneys` (
  `tourneyID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `Status` int(1) NOT NULL,
  `Type` char(1) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `maxTeams` int(3) NOT NULL,
  `Game` varchar(64) NOT NULL,
  PRIMARY KEY (`tourneyID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Email` varchar(128) NOT NULL,
  `Rights` varchar(32) NOT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `users` (`UserID`, `Email`, `Rights`) VALUES
(1, 'ADMIN_EMAIL_PLACEHOLDER', '65535');