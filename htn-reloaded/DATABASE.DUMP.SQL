-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 15. August 2008 um 13:50
-- Server Version: 5.0.51
-- PHP-Version: 5.2.5

--
-- Datenbank: `htn`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `abook_entrys`
--

CREATE TABLE IF NOT EXISTS `abook_entrys` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `user` smallint(5) unsigned NOT NULL default '0',
  `remote_user` smallint(5) unsigned NOT NULL default '0',
  `group` enum('1','2','3','4') NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`)
) TYPE=MyISAM  AUTO_INCREMENT=10 ;

--
-- Daten für Tabelle `abook_entrys`
--

INSERT INTO `abook_entrys` (`id`, `user`, `remote_user`, `group`) VALUES
(9, 1, 1, '1');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `attacks`
--

CREATE TABLE IF NOT EXISTS `attacks` (
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

CREATE TABLE IF NOT EXISTS `cboards` (
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
-- Tabellenstruktur für Tabelle `clusters`
--

CREATE TABLE IF NOT EXISTS `clusters` (
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
) TYPE=MyISAM  AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `clusters`
--

INSERT INTO `clusters` (`id`, `name`, `code`, `events`, `tax`, `money`, `infotext`, `points`, `logofile`, `homepage`, `box1`, `box2`, `box3`, `acceptnew`, `rank`, `notice`, `srate_total_cnt`, `srate_success_cnt`, `srate_noticed_cnt`) VALUES
(1, 'Administration', '=ADM!N=', ' 19:31 Der Cluster wird durch Administrator gegr&uuml;ndet!', 1, 8, NULL, 33, NULL, NULL, 'Wichtig', 'Allgemein', 'Alte Beiträge', 'yes', 0, NULL, 0, 0, 0),
(2, 'TestCluster', '-=TC=-', ' 14:44 Der Cluster wird durch TestUser gegr&uuml;ndet!', 1, 2, NULL, 0, NULL, NULL, 'Wichtig', 'Allgemein', 'Alte Beiträge', 'yes', 1, NULL, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cl_pacts`
--

CREATE TABLE IF NOT EXISTS `cl_pacts` (
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

CREATE TABLE IF NOT EXISTS `cl_reqs` (
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
-- Tabellenstruktur für Tabelle `da_participants`
--

CREATE TABLE IF NOT EXISTS `da_participants` (
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

CREATE TABLE IF NOT EXISTS `distr_attacks` (
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

CREATE TABLE IF NOT EXISTS `logins` (
  `id` bigint(20) NOT NULL auto_increment,
  `ip` varchar(64) NOT NULL default '',
  `usr_id` smallint(6) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `user_agent` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `usr_id` (`usr_id`),
  KEY `ip` (`ip`)
) TYPE=MyISAM  AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `logins`
--

INSERT INTO `logins` (`id`, `ip`, `usr_id`, `time`, `user_agent`) VALUES
(1, '127.0.0.1', 1, 1218795341, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1'),
(2, '127.0.0.1', 1, 1218799419, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1'),
(3, '127.0.0.1', 1, 1218800031, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1'),
(4, '127.0.0.1', 1, 1218800159, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
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

CREATE TABLE IF NOT EXISTS `mails` (
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
) TYPE=MyISAM  AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `mails`
--

INSERT INTO `mails` (`mail`, `user`, `user2`, `user2_name`, `time`, `subject`, `text`, `box`, `xread`) VALUES
(1, 1, 1, 'Administrator', '1107786776', 'cool', 'hallo,<br />\n<br />\ncool!<br />\n<br />\ncu', 'in', 'yes'),
(2, 1, 1, 'Administrator', '1107786776', 'cool', 'hallo,<br />\n<br />\ncool!<br />\n<br />\ncu', 'out', 'yes');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pcs`
--

CREATE TABLE IF NOT EXISTS `pcs` (
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
) TYPE=MyISAM  AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `pcs`
--

INSERT INTO `pcs` (`id`, `name`, `ip`, `owner`, `owner_name`, `owner_points`, `owner_cluster`, `owner_cluster_code`, `cpu`, `ram`, `lan`, `mm`, `bb`, `ads`, `dialer`, `auctions`, `bankhack`, `fw`, `mk`, `av`, `ids`, `ips`, `rh`, `sdk`, `trojan`, `credits`, `lmupd`, `country`, `points`, `la`, `buildstat`, `di`, `dt`, `lrh`, `blocked`, `upgrcode`) VALUES
(1, 'NoName', '92.1', 1, 'Administrator', 0, 0, '', '2', 1, '1', '2.5', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 13130, '1218800524', 'afghanistan', 33, '', NULL, '', '', '', NULL, '8192be0cb55c5696'),
(2, 'NoName', '92.2', 2, 'TestUser', 0, 0, '', '0', 0, '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 13130, '1218795339', 'afghanistan', 0, '', NULL, '', '', '', NULL, ''),
(3, 'NoName', '92.3', 3, 'test', 0, 0, '', '0', 0, '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 13130, '1218710280', 'afghanistan', 0, '', NULL, '', '', '', NULL, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rank_clusters`
--

CREATE TABLE IF NOT EXISTS `rank_clusters` (
  `platz` smallint(6) NOT NULL auto_increment,
  `cluster` smallint(6) default '0',
  `members` tinyint(4) default '0',
  `points` int(11) default '0',
  `av_points` float default '0',
  `pcs` mediumint(9) default '0',
  `av_pcs` float default '0',
  `success_rate` float default '0',
  PRIMARY KEY  (`platz`)
) TYPE=MyISAM  AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `rank_clusters`
--

INSERT INTO `rank_clusters` (`platz`, `cluster`, `members`, `points`, `av_points`, `pcs`, `av_pcs`, `success_rate`) VALUES
(1, 2, 1, 0, 0, 1, 1, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rank_users`
--

CREATE TABLE IF NOT EXISTS `rank_users` (
  `platz` smallint(6) NOT NULL auto_increment,
  `id` smallint(6) default NULL,
  `name` varchar(50) default NULL,
  `points` int(11) default '0',
  `cluster` smallint(6) default NULL,
  PRIMARY KEY  (`platz`)
) TYPE=MyISAM  AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `rank_users`
--

INSERT INTO `rank_users` (`platz`, `id`, `name`, `points`, `cluster`) VALUES
(1, 2, 'TestUser', 0, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `setting_id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY  (`setting_id`)
) TYPE=MyISAM  AUTO_INCREMENT=10 ;

--
-- Daten für Tabelle `setting`
--

INSERT INTO `setting` (`setting_id`, `name`, `value`) VALUES
(1, 'max_pc', '25'),
(2, 'update_int', '10800'),
(3, 'min_attack_def', '9'),
(4, 'min_incative', '259200'),
(5, 'remote_hijack', '172800'),
(6, 'max_members', '32'),
(7, 'server_limit', '1000'),
(8, 'online_time', '600'),
(9, 'default_style', 'crystal');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sysmsgs`
--

CREATE TABLE IF NOT EXISTS `sysmsgs` (
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

CREATE TABLE IF NOT EXISTS `transfers` (
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

CREATE TABLE IF NOT EXISTS `upgrades` (
  `id` bigint(20) NOT NULL auto_increment,
  `pc` smallint(6) NOT NULL default '0',
  `start` int(11) NOT NULL default '0',
  `end` int(11) NOT NULL default '0',
  `item` varchar(10) NOT NULL default '',
  `uniqueid` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniqueid` (`uniqueid`),
  KEY `pc` (`pc`)
) TYPE=MyISAM  AUTO_INCREMENT=7 ;

--
-- Daten für Tabelle `upgrades`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
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
  `level` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_2` (`name`),
  KEY `name` (`name`),
  KEY `sid` (`sid`)
) TYPE=MyISAM  AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `pcs`, `gender`, `birthday`, `stat`, `liu`, `lic`, `clusterstat`, `homepage`, `infotext`, `wohnort`, `la`, `ads`, `bigacc`, `usessl`, `enable_usrimg`, `usrimg_fmt`, `noipcheck`, `newmail`, `lastmail`, `points`, `sig_mails`, `sig_board`, `cluster`, `cm`, `login_time`, `sid`, `sid_ip`, `sid_pc`, `sid_lastcall`, `locked`, `locked_till`, `locked_by`, `locked_reason`, `stylesheet`, `inbox_full`, `avatar`, `rank`, `da_avail`, `acode`, `tcode`, `pcview_ext`, `pcview_sorttype`, `calcrank`, `last_verified`, `verifyimg`, `extacc_id`, `level`) VALUES
(1, 'Administrator', 'admin@localhost.lh', 'd41d8cd98f00b204e9800998ecf8427e', '1', 'x', '0.0.0', 1, '1107786776', '1218800319', 1000, '', '', '', '', 'no', 'yes', 'no', 'no', 'cluster points ranking', 'no', 0, '1107786776', 33, '', '', 1, '15.08.', 1218800159, '1af728cc48', '127.0.0.1', 1, 1218800789, 'no', 0, '', '', 'anti-ie', '', '', 0, 'no', '', '', 'yes', '', 'yes', 0, 0, '', 5),
(2, 'TestUser', 'test@localhost.lh', 'd41d8cd98f00b204e9800998ecf8427e', '2', 'x', '0.0.0', 1, '1107786798', '1107786798', 1000, NULL, NULL, NULL, NULL, 'no', 'yes', 'no', 'no', 'cluster points ranking', 'no', 0, NULL, 0, NULL, NULL, 2, '12.08.', 1218540141, '', '127.0.0.1', 2, 0, 'no', 0, '', '', 'crystal', NULL, NULL, 1, 'no', NULL, NULL, 'yes', '', 'yes', 0, 0, '', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `verifyimgs`
--

CREATE TABLE IF NOT EXISTS `verifyimgs` (
  `id` smallint(6) NOT NULL default '0',
  `chars` char(3) NOT NULL default '',
  KEY `id` (`id`)
) TYPE=MyISAM;

--
-- Daten für Tabelle `verifyimgs`
--

