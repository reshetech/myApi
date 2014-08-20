
--
-- Table structure for table `myapi_watches`
--

CREATE TABLE IF NOT EXISTS `myapi_watches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `myapi_auth_id` int(11) NOT NULL,
  `num_watches` int(11) NOT NULL,
  `last_visit` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `myapi_auth_id_index` (`myapi_auth_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
