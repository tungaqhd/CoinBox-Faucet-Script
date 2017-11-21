
CREATE TABLE IF NOT EXISTS `address_blocked` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;


CREATE TABLE IF NOT EXISTS `address_list` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `bitcoin_address` varchar(75) NOT NULL,
  `total_claimed` varchar(100) NOT NULL,
  `ref` varchar(75) NOT NULL,
  `last` int(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;


CREATE TABLE IF NOT EXISTS `ip_blocked` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

CREATE TABLE IF NOT EXISTS `ip_list` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(50) NOT NULL,
  `last` int(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;


CREATE TABLE IF NOT EXISTS `link` (
  `bitcoin_address` varchar(75) NOT NULL,
  `sec_key` varchar(75) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

