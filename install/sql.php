<?php
//echo "test prefix ".$TABLE_PREFIX;
	mysql_query("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
	//tables
	//categories
	$sqlImport="CREATE TABLE IF NOT EXISTS `".$TABLE_PREFIX."categories` (
	  `idCategory` int(10) unsigned NOT NULL auto_increment,
	  `name` varchar(64) NOT NULL,
	  `order` int(2) unsigned NOT NULL default '0',
	  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	  `idCategoryParent` int(10) unsigned NOT NULL default '0',
	  `friendlyName` varchar(64) NOT NULL,
	  `description` text,
	  `price` FLOAT NOT NULL default '0',
	  PRIMARY KEY  USING BTREE (`idCategory`),
	  KEY `Index_fname` (`friendlyName`)
	) ENGINE=InnoDB  DEFAULT CHARSET=$DB_CHARSET AUTO_INCREMENT=1;";
	mysql_query($sqlImport);
	//posts
	$sqlImport="CREATE TABLE IF NOT EXISTS `".$TABLE_PREFIX."posts` (
	  `idPost` int(10) unsigned NOT NULL auto_increment,
	  `isAvailable` int(1) NOT NULL default '1',
	  `isConfirmed` int(1) NOT NULL default '0',
	  `idCategory` int(10) unsigned NOT NULL default '0',
	  `type` int(10) unsigned NOT NULL default '0',
	  `title` varchar(145) NOT NULL,
	  `description` text NOT NULL,
	  `email` varchar(145) NOT NULL,
      `idLocation` int(10) unsigned NOT NULL DEFAULT '0',
	  `place` varchar(145) default '0',
	  `name` varchar(50) NOT NULL,
	  `price` FLOAT NOT NULL default '0',
	  `ip` varchar(18) NOT NULL default '',
	  `insertDate` timestamp NOT NULL default CURRENT_TIMESTAMP,
	  `password` varchar(8) NOT NULL,
	  `phone` varchar(11) default NULL,
	  `hasImages` BOOLEAN  NOT NULL DEFAULT '0',
	  PRIMARY KEY  USING BTREE (`idPost`),
	  KEY `FK_posts_categories` (`idCategory`),
	  KEY `Index_title` (`title`)
	) ENGINE=InnoDB  DEFAULT CHARSET=$DB_CHARSET ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;";
	mysql_query($sqlImport);
	//postshits
	$sqlImport="CREATE TABLE IF NOT EXISTS `".$TABLE_PREFIX."postshits` (
	  `idHit` int(10) unsigned NOT NULL auto_increment,
	  `idPost` int(10) unsigned NOT NULL,
	  `hitTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
	  `ip` varchar(18) NOT NULL,
	  PRIMARY KEY  (`idHit`),
	  KEY `Index_idPost` (`idPost`),
	  KEY `Index_hitTime` (`hitTime`)
	) ENGINE=MyISAM  DEFAULT CHARSET=$DB_CHARSET AUTO_INCREMENT=1;";
	mysql_query($sqlImport);
	//locations
	$sqlImport="CREATE TABLE IF NOT EXISTS `".$TABLE_PREFIX."locations` (
	  `idLocation` int(10) unsigned NOT NULL auto_increment,
	  `name` varchar(64) NOT NULL,
	  `idLocationParent` int(10) unsigned NOT NULL default '0',
      `friendlyName` varchar(64) NOT NULL,
	  PRIMARY KEY (`idLocation`)
	) ENGINE=InnoDB  DEFAULT CHARSET=$DB_CHARSET AUTO_INCREMENT=1;";
	mysql_query($sqlImport);
	//accounts
	$sqlImport="CREATE TABLE IF NOT EXISTS `".$TABLE_PREFIX."accounts` (
      `idAccount` int(11) NOT NULL auto_increment,
      `name` varchar(250) NOT NULL,
      `email` varchar(145) NOT NULL,
      `password` varchar(145) NOT NULL,
      `active` int(1) NOT NULL default '0',
      `idLocation` int(10) unsigned default NULL,
      `createdDate` timestamp NOT NULL default CURRENT_TIMESTAMP,
      `lastModifiedDate` datetime default NULL,
      `lastSigninDate` datetime default NULL,
      `activationToken` varchar(225) NOT NULL,
      PRIMARY KEY  (`idAccount`)
	) ENGINE=InnoDB  DEFAULT CHARSET=$DB_CHARSET AUTO_INCREMENT=1;";
	mysql_query($sqlImport);
    //constraints
	$sqlImport="ALTER TABLE `".$TABLE_PREFIX."posts` ADD CONSTRAINT `FK_posts_categories` FOREIGN KEY (`idCategory`) REFERENCES `".$TABLE_PREFIX."categories` (`idCategory`) ON DELETE CASCADE ON UPDATE CASCADE;";
	mysql_query($sqlImport);	
		
	if ($_POST["SAMPLE_DB"]==1){
	    $sample_import="INSERT INTO `".$TABLE_PREFIX."categories` (`idCategory`, `name`, `order`, `created`, `idCategoryParent`, `friendlyName`, `description`) VALUES
                        (10, 'Jobs', 2, '2009-04-22 19:25:11', 0, 'jobs', 'The best place to find work is with our job offers. Also you can ask for work in the ''Need'' section.'),
                        (11, 'Full Time', 1, '2009-04-22 19:31:43', 10, 'full-time', 'Are you looking for a fulltime job? Or do you have a fulltime job to offer? Post your Ad here!'),
                        (12, 'Part Time', 2, '2009-04-22 19:32:15', 10, 'part-time', 'Are you looking for a parttime job? Or do you have a partime job to offer? Post your Ad here!'),
                        (13, 'Internship', 3, '2009-04-22 19:33:05', 10, 'internship', 'Are you looking for a internship? Or do you have an internship to offer? Post it here!'),
                        (14, 'Languages', 3, '2009-04-22 19:26:26', 0, 'languages', 'You want to learn a new language? Or can you teach a language? This is your section!'),
                        (15, 'English', 1, '2009-04-22 19:33:52', 14, 'english', 'Do you speak English? Or can you teach it? Do you want to learn? This is your category.'),
                        (16, 'Spanish', 2, '2009-04-22 19:34:29', 14, 'spanish', 'You want to learn Spanish? Or can you teach Spanish? This is your section!'),
                        (32, 'Events', 1, '2009-04-22 19:36:13', 36, 'events', 'Upcoming Parties, Cinema, Museums, Parades, Birthdays, Dinners.... Everything!'),
                        (33, 'Other Languages', 3, '2009-04-22 19:35:34', 14, 'other-languages', 'Are you interested in learning or teaching any other language that is not listed? Post it here!'),
                        (36, 'Others', 5, '2009-04-22 19:26:50', 0, 'others', 'Whatever you can imagine is in this section.'),
                        (39, 'Housing', 1, '2009-04-22 19:28:50', 0, 'housing', 'Do you need a place to sleep, or you have something to offer; rooms, shared apartments, houses... etc.\r\n\r\nFind your perfect roommate here!'),
                        (41, 'Apartment', 1, '2009-04-22 19:39:32', 39, 'apartment', 'Apartments, flats, monthly rentals, long terms, for days... this is the section to have your apartment!'),
                        (43, 'Shared Apartments - Rooms', 2, '2009-05-03 23:53:57', 39, 'shared-apartments-rooms', 'You want to share an apartment? Then you need a room! Ask for rooms or add yours in this section.'),
                        (46, 'House', 3, '2009-04-22 19:40:50', 39, 'house', 'Rent a house, or offer your house for rent! Here you can find your beach house!'),
                        (49, 'Hobbies', 2, '2009-04-22 19:36:55', 36, 'hobbies', 'Share your hobby with someone! Football, running, cinema, music, cinema, party ... Post it here!'),
                        (51, 'Market', 4, '2009-04-22 19:30:42', 0, 'market', 'Buy or sell things that you don`t need anymore, you will find someone interested, or maybe you are going to find exactly what you need.'),
                        (54, 'TV', 1, '2009-04-22 19:41:39', 51, 'tv', 'TV, Video Games, TFT, Plasma, your old TV, or your new one can find a new owner!'),
                        (56, 'Audio', 2, '2009-04-22 19:42:13', 51, 'audio', 'HI-FI systems, iPod, MP3 players, MP4, if you dont use it anymore sell it! If you try to find a second hand one, this is your place!'),
                        (59, 'Furniture', 3, '2009-04-22 19:43:16', 51, 'furniture', 'Do you need to furnish your home? Or would you like to sell your furniture? Post it here!'),
                        (62, 'IT', 4, '2009-04-22 19:43:48', 51, 'it', 'You need a computer? Laptop? Or do you have some old components? This is the IT market!'),
                        (65, 'Other Market', 5, '2009-04-22 19:44:12', 51, 'other-market', 'In this market you can sell everything you want! Or search for it!'),
                        (68, 'Services', 3, '2009-04-22 19:38:33', 36, 'services', 'Do you need a service? Relocation? Insurance? Doctor? Cleaning? Here you can ask for it or offer services!'),
                        (70, 'Friendship', 4, '2009-04-22 19:38:52', 36, 'friendship', 'Are you alone in the city? Here you can find new friends!'),
                        (73, 'Au pair', 4, '2009-06-19 11:26:22', 10, 'au-pair', 'Find or require for a Au Pair service. Here is the best place');";
        mysql_query($sample_import);

	}
	
?>
