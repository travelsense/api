
--
-- Database: `vacarious`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE IF NOT EXISTS `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `travel` int(11) NOT NULL,
  `action` mediumtext NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `checkins`
--

CREATE TABLE IF NOT EXISTS `checkins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `travel` int(11) NOT NULL,
  `latitude` mediumtext NOT NULL,
  `longitude` mediumtext NOT NULL,
  `end_latitude` mediumtext NOT NULL,
  `end_longitude` mediumtext NOT NULL,
  `country` int(11) NOT NULL,
  `name` mediumtext NOT NULL,
  `type` int(11) NOT NULL,
  `city` mediumtext NOT NULL,
  `address` mediumtext NOT NULL,
  `zip` mediumtext NOT NULL,
  `place` int(11) NOT NULL,
  `action` int(11) NOT NULL,
  `notes` mediumtext NOT NULL,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `travel` (`travel`),
  KEY `country` (`country`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` mediumtext NOT NULL,
  `travel` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`travel`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `interest`
--

CREATE TABLE IF NOT EXISTS `interest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `todos`
--

CREATE TABLE IF NOT EXISTS `todos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `checkin` int(11) NOT NULL,
  `complete` int(11) NOT NULL,
  `text` mediumtext NOT NULL,
  PRIMARY KEY (`id`,`checkin`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `travels`
--

CREATE TABLE IF NOT EXISTS `travels` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `image` mediumtext NOT NULL,
  `description` varchar(512) NOT NULL,
  `rating` int(6) NOT NULL,
  `path` varchar(256) DEFAULT NULL,
  `country` varchar(32) DEFAULT NULL,
  `views` int(32) DEFAULT '0',
  `user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `travel_favorite`
--

CREATE TABLE IF NOT EXISTS `travel_favorite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `travel` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`,`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fbid` mediumtext NOT NULL,
  `first` mediumtext NOT NULL,
  `last` mediumtext NOT NULL,
  `fbtoken` mediumtext NOT NULL,
  `email` mediumtext NOT NULL,
  `password` mediumtext NOT NULL,
  `image` mediumtext NOT NULL,
  `token` mediumtext NOT NULL,
  `hometown` mediumtext NOT NULL,
  `about` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_interest`
--

CREATE TABLE IF NOT EXISTS `user_interest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `interest` int(11) NOT NULL,
  PRIMARY KEY (`id`,`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
