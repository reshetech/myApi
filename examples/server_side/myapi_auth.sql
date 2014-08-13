
--
-- Table structure for table `myapi_auth`
--

CREATE TABLE IF NOT EXISTS `myapi_auth` (
  `id` int(11) NOT NULL auto_increment,
  `key` varchar(64) NOT NULL,
  `pass` varchar(64) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `index_pass` (`pass`),
  UNIQUE KEY `index_key` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
