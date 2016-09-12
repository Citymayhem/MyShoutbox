CREATE TABLE `{MYBB_TABLE_PREFIX}mysb_version` (
	`version` int(10) NOT NULL,
	`dateTime` DATETIME NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE `{MYBB_TABLE_PREFIX}mysb_shouts` (
	`id` int(10) NOT NULL auto_increment,
	`uid` int(10) NOT NULL,
	`shout_msg` text NOT NULL,
	`shout_date` int(10) NOT NULL,
	`shout_ip` varchar(30) NOT NULL,
	`hidden` varchar(10) NOT NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE `{MYBB_TABLE_PREFIX}mysb_reports` (
	`rid` int(10) NOT NULL auto_increment,
	`username` varchar(100) NOT NULL DEFAULT '',
	`uid` int(10) NOT NULL DEFAULT 0,
	`reason` varchar(255) NOT NULL DEFAULT '',
	`date` bigint(30) NOT NULL DEFAULT 0,
	`sid` int(10) NOT NULL DEFAULT 0,
	`marked` tinyint(1) NOT NULL DEFAULT 0,
	`author_uid` int(10) NOT NULL DEFAULT 0,
	`author_username` varchar(30) NOT NULL DEFAULT '',
	PRIMARY KEY  (`rid`), KEY(`date`)
) ENGINE=MyISAM CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

INSERT INTO `{MYBB_TABLE_PREFIX}mysb_shouts` VALUES (NULL, 1, 'Test Shout! Without any shout, shoutbox will display Loading... forever.. you need at least one shout, so here it is.', UTC_TIMESTAMP(), '127.0.0.1', 'no');
INSERT INTO `{MYBB_TABLE_PREFIX}mysb_version` VALUES (1, UTC_TIMESTAMP());

ALTER TABLE `{MYBB_TABLE_PREFIX}users` ADD `mysb_banned` smallint(1) NOT NULL DEFAULT 0;
ALTER TABLE `{MYBB_TABLE_PREFIX}users` ADD `mysb_banned_reason` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `{MYBB_TABLE_PREFIX}users` ADD `mysb_order_desc` TINYINT(1) NOT NULL DEFAULT 1;