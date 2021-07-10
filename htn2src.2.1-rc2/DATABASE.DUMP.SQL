-- phpMyAdmin SQL Dump
-- version 2.6.1-rc1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost:3306
-- Erstellungszeit: 17. Februar 2005 um 14:55
-- Server Version: 4.0.22
-- PHP-Version: 5.0.2
-- 
-- Datenbank: `htn_server1`
-- 
CREATE DATABASE `htn_server1`;
USE htn_server1;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `abook_entrys`
-- 

CREATE TABLE `abook_entrys` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `user` smallint(5) unsigned NOT NULL default '0',
  `remote_user` smallint(5) unsigned NOT NULL default '0',
  `group` enum('1','2','3','4') NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`)
) TYPE=MyISAM AUTO_INCREMENT=9 ;

-- 
-- Daten für Tabelle `abook_entrys`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `attacks`
-- 

CREATE TABLE `attacks` (
  `id` bigint(20) NOT NULL auto_increment,
  `from_pc` smallint(6) default '0',
  `from_usr` smallint(6) default '0',
  `from_cluster` int(11) default '0',
  `to_pc` smallint(6) default '0',
  `to_usr` smallint(6) default '0',
  `to_cluster` int(11) default '0',
  `type` enum('block','hijack','scan','smash','trojan') NOT NULL default 'block',
  `option` enum('cpu','deactivate','defacement','firewall','sdk','transfer') NOT NULL default 'cpu',
  `success` tinyint(1) default '0',
  `noticed` tinyint(1) default '0',
  `time` int(11) NOT NULL default '0',
  `payload` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `from_cluster` (`from_cluster`),
  KEY `to_cluster` (`to_cluster`),
  KEY `success` (`success`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `attacks`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cboards`
-- 

CREATE TABLE `cboards` (
  `cluster` smallint(6) default NULL,
  `thread` int(9) NOT NULL auto_increment,
  `relative` int(9) default NULL,
  `user` smallint(6) default NULL,
  `user_name` tinytext,
  `user_cstat` smallint(6) default NULL,
  `time` varchar(10) default NULL,
  `subject` tinytext,
  `content` text,
  `box` tinyint(4) default NULL,
  PRIMARY KEY  (`thread`),
  KEY `cluster` (`cluster`),
  KEY `thread` (`thread`),
  KEY `relative` (`relative`),
  KEY `box` (`box`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `cboards`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cl_pacts`
-- 

CREATE TABLE `cl_pacts` (
  `cluster` smallint(6) default NULL,
  `convent` tinyint(4) default NULL,
  `partner` smallint(6) default NULL,
  KEY `cluster` (`cluster`),
  KEY `partner` (`partner`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `cl_pacts`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `cl_reqs`
-- 

CREATE TABLE `cl_reqs` (
  `user` smallint(6) default NULL,
  `cluster` smallint(6) default NULL,
  `comment` text,
  `dealed` char(3) default 'no',
  KEY `cluster` (`cluster`),
  KEY `dealed` (`dealed`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `cl_reqs`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `clusters`
-- 

CREATE TABLE `clusters` (
  `id` int(6) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `code` varchar(16) default NULL,
  `events` text,
  `tax` int(11) NOT NULL default '1',
  `money` bigint(20) NOT NULL default '0',
  `infotext` text,
  `points` int(9) default NULL,
  `logofile` tinytext,
  `homepage` tinytext,
  `box1` varchar(50) default 'Wichtig',
  `box2` varchar(50) default 'Allgemein',
  `box3` varchar(50) default 'Alte Beiträge',
  `acceptnew` char(3) default 'yes',
  `rank` smallint(6) default '0',
  `notice` text,
  `srate_total_cnt` int(11) NOT NULL default '0',
  `srate_success_cnt` int(11) default '0',
  `srate_noticed_cnt` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `code` (`code`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Daten für Tabelle `clusters`
-- 

INSERT INTO `clusters` VALUES (1, 'Administration', '=ADM!N=', ' 19:31 Der Cluster wird durch Administrator gegr&uuml;ndet!', 1, 1, NULL, 23, NULL, NULL, 'Wichtig', 'Allgemein', 'Alte Beiträge', 'yes', 0, NULL, 0, 0, 0);
INSERT INTO `clusters` VALUES (2, 'TestCluster', '-=TC=-', ' 14:44 Der Cluster wird durch TestUser gegr&uuml;ndet!', 1, 0, NULL, NULL, NULL, NULL, 'Wichtig', 'Allgemein', 'Alte Beiträge', 'yes', 0, NULL, 0, 0, 0);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `da_participants`
-- 

CREATE TABLE `da_participants` (
  `relative` int(11) NOT NULL default '0',
  `pc` smallint(6) NOT NULL default '0',
  `ip` varchar(7) NOT NULL default '',
  `owner` smallint(6) NOT NULL default '0',
  `owner_name` varchar(40) NOT NULL default '',
  KEY `relative` (`relative`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `da_participants`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `distr_attacks`
-- 

CREATE TABLE `distr_attacks` (
  `id` smallint(6) NOT NULL auto_increment,
  `cluster` smallint(6) default NULL,
  `initiator_pc` smallint(6) default NULL,
  `initiator_usr` smallint(6) default NULL,
  `target` smallint(6) default NULL,
  `item` enum('av','cpu','fw') NOT NULL default 'av',
  PRIMARY KEY  (`id`),
  KEY `cluster` (`cluster`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `distr_attacks`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `logins`
-- 

CREATE TABLE `logins` (
  `id` bigint(20) NOT NULL auto_increment,
  `ip` varchar(64) NOT NULL default '',
  `usr_id` smallint(6) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `user_agent` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `usr_id` (`usr_id`),
  KEY `ip` (`ip`)
) TYPE=MyISAM AUTO_INCREMENT=10 ;

-- 
-- Daten für Tabelle `logins`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `logs`
-- 

CREATE TABLE `logs` (
  `id` bigint(20) NOT NULL auto_increment,
  `type` enum('other','worm_clmoney','worm_blockpc','worm_pcsendmoney','delcluster','deluser','lockuser','badlogin','chclinfo','qubug','adminedit') NOT NULL default 'other',
  `usr_id` smallint(6) NOT NULL default '0',
  `payload` tinytext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `usr_id` (`usr_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `logs`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `mails`
-- 

CREATE TABLE `mails` (
  `mail` bigint(20) NOT NULL auto_increment,
  `user` smallint(6) default NULL,
  `user2` smallint(6) default NULL,
  `user2_name` tinytext NOT NULL,
  `time` varchar(10) default NULL,
  `subject` tinytext,
  `text` text,
  `box` char(3) default NULL,
  `xread` char(3) default NULL,
  PRIMARY KEY  (`mail`),
  KEY `user` (`user`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Daten für Tabelle `mails`
-- 

INSERT INTO `mails` VALUES (1, 1, 1, 'Administrator', '1107786776', 'cool', 'hallo,<br />\n<br />\ncool!<br />\n<br />\ncu', 'in', 'yes');
INSERT INTO `mails` VALUES (2, 1, 1, 'Administrator', '1107786776', 'cool', 'hallo,<br />\n<br />\ncool!<br />\n<br />\ncu', 'out', 'yes');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `pcs`
-- 

CREATE TABLE `pcs` (
  `id` smallint(6) NOT NULL auto_increment,
  `name` tinytext,
  `ip` varchar(7) default NULL,
  `owner` smallint(6) default NULL,
  `owner_name` tinytext,
  `owner_points` int(11) default NULL,
  `owner_cluster` smallint(6) default NULL,
  `owner_cluster_code` tinytext,
  `cpu` varchar(4) NOT NULL default '',
  `ram` tinyint(4) default NULL,
  `lan` varchar(4) default NULL,
  `mm` varchar(4) default NULL,
  `bb` varchar(4) default NULL,
  `ads` char(1) default NULL,
  `dialer` char(1) default NULL,
  `auctions` char(1) default NULL,
  `bankhack` char(1) default NULL,
  `fw` varchar(4) default NULL,
  `mk` varchar(4) default NULL,
  `av` varchar(4) default NULL,
  `ids` varchar(4) default NULL,
  `ips` varchar(4) default NULL,
  `rh` varchar(4) default NULL,
  `sdk` varchar(4) default NULL,
  `trojan` varchar(4) default NULL,
  `credits` int(11) default NULL,
  `lmupd` varchar(10) default NULL,
  `country` enum('afghanistan','antarktis','antigua','argentinien','australien','austria','brasilien','china','deutschland','egypt','england','finnland','frankreich','griechenland','groenland','indien','irak','iran','irland','island','italien','japan','kanada','kuba','lybien','madagaskar','mexico','monaco','namibia','neuseeland','nordkorea','pakistan','peru','portugal','quatar','russland','saudi-arabien','schweden','schweiz','sierraleone','spanien','suedafrika','thailand','tuerkei','usa','vanuatu','vietnam','marlboro','atlantis','myth') NOT NULL default 'myth',
  `points` mediumint(8) unsigned default NULL,
  `la` varchar(10) default NULL,
  `buildstat` tinytext,
  `di` varchar(10) default NULL,
  `dt` varchar(10) default NULL,
  `lrh` varchar(10) default NULL,
  `blocked` varchar(10) default NULL,
  `upgrcode` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `ip` (`ip`),
  KEY `owner` (`owner`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Daten für Tabelle `pcs`
-- 

INSERT INTO `pcs` VALUES (1, 'NoName', '92.1', 1, 'Administrator', 0, 0, '', '1', 1, '1', '2.5', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 13129, '1108647400', 'afghanistan', 23, '', NULL, '', '', '', NULL, 'c297a36ad6c99671');
INSERT INTO `pcs` VALUES (2, 'NoName', '92.2', 2, 'TestUser', 0, 0, '', '0', 0, '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 13130, '1108647813', 'afghanistan', 0, '', NULL, '', '', '', NULL, '');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rank_clusters`
-- 

CREATE TABLE `rank_clusters` (
  `platz` smallint(6) NOT NULL auto_increment,
  `cluster` smallint(6) default '0',
  `members` tinyint(4) default '0',
  `points` int(11) default '0',
  `av_points` float default '0',
  `pcs` mediumint(9) default '0',
  `av_pcs` float default '0',
  `success_rate` float default '0',
  PRIMARY KEY  (`platz`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- 
-- Daten für Tabelle `rank_clusters`
-- 

INSERT INTO `rank_clusters` VALUES (1, 2, 1, 0, 0, 1, 1, 0);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rank_users`
-- 

CREATE TABLE `rank_users` (
  `platz` smallint(6) NOT NULL auto_increment,
  `id` smallint(6) default NULL,
  `name` varchar(50) default NULL,
  `points` int(11) default '0',
  `cluster` smallint(6) default NULL,
  PRIMARY KEY  (`platz`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- 
-- Daten für Tabelle `rank_users`
-- 

INSERT INTO `rank_users` VALUES (1, 2, 'TestUser', 0, 0);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `sysmsgs`
-- 

CREATE TABLE `sysmsgs` (
  `msg` int(11) NOT NULL auto_increment,
  `user` smallint(6) default NULL,
  `time` varchar(10) default NULL,
  `text` text,
  `xread` char(3) default NULL,
  PRIMARY KEY  (`msg`),
  KEY `user` (`user`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `sysmsgs`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `transfers`
-- 

CREATE TABLE `transfers` (
  `from_id` smallint(6) default '0',
  `from_type` enum('cluster','user') NOT NULL default 'cluster',
  `from_usr` smallint(6) default NULL,
  `to_id` smallint(6) default '0',
  `to_type` enum('cluster','user') NOT NULL default 'cluster',
  `to_usr` smallint(6) default NULL,
  `credits` bigint(11) default '0',
  `time` varchar(10) default NULL
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `transfers`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `upgrades`
-- 

CREATE TABLE `upgrades` (
  `id` bigint(20) NOT NULL auto_increment,
  `pc` smallint(6) NOT NULL default '0',
  `start` int(11) NOT NULL default '0',
  `end` int(11) NOT NULL default '0',
  `item` varchar(10) NOT NULL default '',
  `uniqueid` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniqueid` (`uniqueid`),
  KEY `pc` (`pc`)
) TYPE=MyISAM AUTO_INCREMENT=6 ;

-- 
-- Daten für Tabelle `upgrades`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `users`
-- 

CREATE TABLE `users` (
  `id` smallint(6) NOT NULL auto_increment,
  `name` varchar(40) default NULL,
  `email` varchar(50) default NULL,
  `password` tinytext,
  `pcs` text,
  `gender` enum('m','w','x') NOT NULL default 'x',
  `birthday` varchar(10) default '0.0.0',
  `stat` smallint(6) default '1',
  `liu` varchar(10) default NULL,
  `lic` varchar(10) default NULL,
  `clusterstat` smallint(6) default NULL,
  `homepage` tinytext,
  `infotext` text,
  `wohnort` tinytext,
  `la` varchar(10) default NULL,
  `ads` enum('no','yes') NOT NULL default 'yes',
  `bigacc` enum('no','yes') NOT NULL default 'no',
  `usessl` enum('no','yes') NOT NULL default 'no',
  `enable_usrimg` enum('yes','no') NOT NULL default 'no',
  `usrimg_fmt` enum('points','ranking','points ranking','cluster points','cluster ranking','cluster points ranking') NOT NULL default 'cluster points ranking',
  `noipcheck` enum('no','yes') NOT NULL default 'no',
  `newmail` tinyint(4) default '0',
  `lastmail` varchar(10) default NULL,
  `points` int(11) default '0',
  `sig_mails` tinytext,
  `sig_board` tinytext,
  `cluster` int(6) default NULL,
  `cm` varchar(6) default NULL,
  `login_time` int(11) NOT NULL default '0',
  `sid` varchar(32) default NULL,
  `sid_ip` varchar(128) default NULL,
  `sid_pc` smallint(6) NOT NULL default '0',
  `sid_lastcall` int(10) NOT NULL default '0',
  `locked` enum('no','yes') NOT NULL default 'no',
  `locked_till` int(11) NOT NULL default '0',
  `locked_by` varchar(255) NOT NULL default '',
  `locked_reason` varchar(255) NOT NULL default '',
  `stylesheet` enum('anti-ie','crystal','standard','konsole','modern','modern-ie') NOT NULL default 'crystal',
  `inbox_full` tinytext,
  `avatar` tinytext,
  `rank` smallint(6) default '0',
  `da_avail` enum('no','yes') NOT NULL default 'no',
  `acode` varchar(16) default NULL,
  `tcode` varchar(16) default NULL,
  `pcview_ext` enum('yes','no') NOT NULL default 'yes',
  `pcview_sorttype` enum('','name ASC','points ASC','country ASC','lrh ASC') NOT NULL default '',
  `calcrank` enum('yes','no') NOT NULL default 'yes',
  `last_verified` int(11) NOT NULL default '0',
  `verifyimg` smallint(6) NOT NULL default '0',
  `extacc_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_2` (`name`),
  KEY `name` (`name`),
  KEY `sid` (`sid`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Daten für Tabelle `users`
-- 

INSERT INTO `users` VALUES (1, 'Administrator', 'admin@localhost.lh', 'd41d8cd98f00b204e9800998ecf8427e', '1', 'x', '0.0.0', 1, '1107786776', '1107786877', 1000, NULL, NULL, NULL, NULL, 'no', 'yes', 'no', 'no', 'cluster points ranking', 'no', 0, '1107786776', 23, NULL, NULL, 1, '17.02.', 1108647401, '', '127.0.0.1', 1, 0, 'no', 0, '', '', 'crystal', NULL, NULL, 0, 'no', NULL, NULL, 'yes', '', 'yes', 0, 0, '');
INSERT INTO `users` VALUES (2, 'TestUser', 'test@localhost.lh', 'd41d8cd98f00b204e9800998ecf8427e', '2', 'x', '0.0.0', 1, '1107786798', '1107786798', 1000, NULL, NULL, NULL, NULL, 'no', 'yes', 'no', 'no', 'cluster points ranking', 'no', 0, NULL, 0, NULL, NULL, 2, NULL, 1108647813, '19607212aa', '127.0.0.1', 2, 1108648214, 'no', 0, '', '', 'crystal', NULL, NULL, 1, 'no', NULL, NULL, 'yes', '', 'yes', 0, 0, '');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `verifyimgs`
-- 

CREATE TABLE `verifyimgs` (
  `id` smallint(6) NOT NULL default '0',
  `chars` char(3) NOT NULL default '',
  KEY `id` (`id`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `verifyimgs`
-- 