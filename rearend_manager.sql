CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(50) DEFAULT NULL,
  `menuItems_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '-1',
  `menuItems_id` int(11) NOT NULL,
  `created` int(10) DEFAULT NULL,
  `lastmodified` int(10) DEFAULT NULL,
  `published` int(10) DEFAULT NULL,
  `publishState` char(1) DEFAULT '0',
  `coverUrl` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `documents_categories` (
  `documents_id` int(11) DEFAULT NULL,
  `categories_id` mediumint(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `documents_fields_multiline` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `value` longtext,
  `documents_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `documents_fields_singleline` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `value` varchar(255) DEFAULT NULL,
  `documents_id` int(11) NOT NULL,
  `fields_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `kind` varchar(10) DEFAULT NULL,
  `fieldtype` varchar(25) DEFAULT NULL,
  `inputtype` varchar(25) DEFAULT NULL,
  `default` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `label` (`label`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `galleries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `kind` varchar(50) NOT NULL DEFAULT '0',
  `documents_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `galleries_media` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `galleries_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `media` (
  `id` int(128) NOT NULL AUTO_INCREMENT,
  `kind` varchar(100) NOT NULL DEFAULT '',
  `imgUrl` varchar(100) DEFAULT '',
  `embedCode` mediumtext,
  `title` varchar(255) NOT NULL DEFAULT '',
  `caption` mediumtext,
  `created` int(10) NOT NULL,
  `uuid` char(26) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `menuitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL DEFAULT '',
  `position` int(11) NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL DEFAULT '',
  `menuItems_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `templates_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `templates_id` int(11) NOT NULL,
  `fields_id` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `digesta1` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `video_streams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stream_id` varchar(50) DEFAULT NULL,
  `kind` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stream_id` (`stream_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
