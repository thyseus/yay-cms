CREATE TABLE IF NOT EXISTS `sitecontent` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL,
  `authorid` varchar(255) DEFAULT NULL,
	`language` varchar(5) NOT NULL,
  `position` int(10) unsigned NOT NULL,
  `metatags` text NULL,
  `redirect` varchar(255) NULL,
  `visible` int(10) NOT NULL,
  `title_url` varchar(80) NOT NULL,
  `title_browser` varchar(80) NOT NULL,
  `title` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  `images` varchar(4096) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `views` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE INDEX sitecontent_title ON `sitecontent` (title);
CREATE INDEX sitecontent_title_url ON `sitecontent` (title_url);

