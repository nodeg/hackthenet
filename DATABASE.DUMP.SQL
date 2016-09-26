# phpMyAdmin SQL Dump
# version 2.5.7-pl1
# http://www.phpmyadmin.net
#
# Host: localhost
# Erstellungszeit: 02. September 2004 um 15:50
# Server Version: 4.0.20
# PHP-Version: 4.3.8
# 
# Datenbank: `htn_server1`
# 
CREATE DATABASE `htn_server1`;
USE htn_server1;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `abooks`
#

CREATE TABLE `abooks` (
  `user` smallint(6) default NULL,
  `set1` text NOT NULL,
  `set2` text NOT NULL,
  `set3` text NOT NULL,
  `set4` text NOT NULL,
  UNIQUE KEY `user` (`user`)
) TYPE=MyISAM;

#
# Daten für Tabelle `abooks`
#


# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `attacks`
#

CREATE TABLE `attacks` (
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
  `time` int(11) NOT NULL default '0'
) TYPE=MyISAM;

#
# Daten für Tabelle `attacks`
#


# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `cboards`
#

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
  PRIMARY KEY  (`thread`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#
# Daten für Tabelle `cboards`
#


# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `cl_pacts`
#

CREATE TABLE `cl_pacts` (
  `cluster` smallint(6) default NULL,
  `convent` tinyint(4) default NULL,
  `partner` smallint(6) default NULL
) TYPE=MyISAM;

#
# Daten für Tabelle `cl_pacts`
#


# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `cl_reqs`
#

CREATE TABLE `cl_reqs` (
  `user` smallint(6) default NULL,
  `cluster` smallint(6) default NULL,
  `comment` text,
  `dealed` char(3) default 'no'
) TYPE=MyISAM;

#
# Daten für Tabelle `cl_reqs`
#


# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `clusters`
#

CREATE TABLE `clusters` (
  `id` int(6) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `code` varchar(16) default NULL,
  `events` text,
  `tax` int(11) NOT NULL default '1',
  `money` bigint(20) NOT NULL default '0',
  `infotext` text,
  `points` mediumint(9) default NULL,
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
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

#
# Daten für Tabelle `clusters`
#

INSERT INTO `clusters` VALUES (1, 'TestCluster', '=HTNTC=', ' 00:11 Der Cluster wird durch TestUser gegr&uuml;ndet!', 1, 1, NULL, 30, NULL, NULL, 'Wichtig', 'Allgemein', 'Alte Beiträge', 'yes', 1, NULL, 0, 0, 0);
INSERT INTO `clusters` VALUES (2, 'Administratoren', '::root::', '24.08. um 00:13 [usr=2]Administrator2[/usr] erh&auml;lt durch [usr=1]Administrator[/usr] den Status LiteAdmin.\n24.08. um 00:13 [usr=2]Administrator2[/usr] tritt dem Cluster bei.\n 00:12 Der Cluster wird durch Administrator gegr&uuml;ndet!', 1, 2, NULL, 13036, NULL, NULL, 'Wichtig', 'Allgemein', 'Alte Beiträge', 'yes', 0, NULL, 0, 0, 0);

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `da_participants`
#

CREATE TABLE `da_participants` (
  `relative` int(11) NOT NULL default '0',
  `pc` smallint(6) NOT NULL default '0',
  `ip` varchar(7) NOT NULL default '',
  `owner` smallint(6) NOT NULL default '0',
  `owner_name` varchar(40) NOT NULL default ''
) TYPE=MyISAM;

#
# Daten für Tabelle `da_participants`
#


# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `distr_attacks`
#

CREATE TABLE `distr_attacks` (
  `id` smallint(6) NOT NULL auto_increment,
  `cluster` smallint(6) default NULL,
  `initiator_pc` smallint(6) default NULL,
  `initiator_usr` smallint(6) default NULL,
  `target` smallint(6) default NULL,
  `item` enum('av','cpu','fw') NOT NULL default 'av',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#
# Daten für Tabelle `distr_attacks`
#


# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `logins`
#

CREATE TABLE `logins` (
  `id` bigint(20) NOT NULL auto_increment,
  `ip` tinytext NOT NULL,
  `usr_id` smallint(6) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=18 ;

#
# Daten für Tabelle `logins`
#

INSERT INTO `logins` VALUES (1, '127.0.0.1', 1, 1093129915);
INSERT INTO `logins` VALUES (2, '127.0.0.1', 1, 1093173133);
INSERT INTO `logins` VALUES (3, '127.0.0.1', 2, 1093173225);
INSERT INTO `logins` VALUES (4, '127.0.0.1', 3, 1093173229);
INSERT INTO `logins` VALUES (5, '127.0.0.1', 3, 1093173243);
INSERT INTO `logins` VALUES (6, '127.0.0.1', 1, 1093173450);
INSERT INTO `logins` VALUES (7, '127.0.0.1', 2, 1093173462);
INSERT INTO `logins` VALUES (8, '127.0.0.1', 3, 1093299090);
INSERT INTO `logins` VALUES (9, '127.0.0.1', 1, 1093299119);
INSERT INTO `logins` VALUES (10, '127.0.0.1', 2, 1093299153);
INSERT INTO `logins` VALUES (11, '127.0.0.1', 1, 1093299171);
INSERT INTO `logins` VALUES (12, '127.0.0.1', 2, 1093299179);
INSERT INTO `logins` VALUES (13, '127.0.0.1', 1, 1093299198);
INSERT INTO `logins` VALUES (14, '127.0.0.1', 1, 1093299331);
INSERT INTO `logins` VALUES (15, '127.0.0.1', 1, 1093300234);
INSERT INTO `logins` VALUES (16, '127.0.0.1', 1, 1094132552);
INSERT INTO `logins` VALUES (17, '127.0.0.1', 1, 1094132687);

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `logs`
#

CREATE TABLE `logs` (
  `id` bigint(20) NOT NULL auto_increment,
  `type` enum('other','worm_clmoney','worm_blockpc','worm_pcsendmoney','delcluster','deluser','lockuser','badlogin','chclinfo') NOT NULL default 'other',
  `usr_id` smallint(6) NOT NULL default '0',
  `payload` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

#
# Daten für Tabelle `logs`
#

INSERT INTO `logs` VALUES (1, 'badlogin', 0, 'nick=, pwd=');

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `mails`
#

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
  PRIMARY KEY  (`mail`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#
# Daten für Tabelle `mails`
#


# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `pcs`
#

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
  `country` enum('afghanistan', 'antarktis', 'antigua', 'argentinien', 'australien', 'austria', 'brasilien', 'china', 'deutschland', 'egypt', 'england', 'finnland', 'frankreich', 'griechenland', 'groenland', 'indien', 'irak', 'iran', 'irland', 'island', 'italien', 'japan', 'kanada', 'kuba', 'lybien', 'madagaskar', 'mexico', 'monaco', 'namibia', 'neuseeland', 'nordkorea', 'pakistan', 'peru', 'portugal', 'quatar', 'russland', 'saudi-arabien', 'schweden', 'schweiz', 'sierraleone', 'spanien', 'suedafrika', 'thailand', 'tuerkei', 'usa', 'vanuatu', 'vietnam', 'marlboro', 'atlantis', 'myth') NOT NULL default 'myth',
  `points` smallint(6) default NULL,
  `la` varchar(10) default NULL,
  `buildstat` tinytext,
  `di` varchar(10) default NULL,
  `dt` varchar(10) default NULL,
  `lrh` varchar(10) default NULL,
  `blocked` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;

#
# Daten für Tabelle `pcs`
#

INSERT INTO `pcs` VALUES (1, 'NoName', '102.1', 1, 'Administrator', 0, 0, '', '21', 9, '10', '10', '10', '9', '9', '9', '9', '20', '10', '10', '20', '10', '10', '5', '5', 2000000000, '2000000000', 'usa', 6518, '', '', '', '', '', '');
INSERT INTO `pcs` VALUES (2, 'NoName', '47.1', 2, 'Administrator2', 0, 0, '', '21', 9, '10', '10', '10', '9', '9', '9', '9', '20', '10', '10', '20', '10', '10', '5', '5', 2000000000, '2000000000', 'deutschland', 6518, '', '', '', '', '', '');
INSERT INTO `pcs` VALUES (3, 'NoName', '80.1', 3, 'TestUser', 0, 0, '', '1', 2, '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 6716, '1094132685', 'russland', 30, '', NULL, '', '', '', NULL);

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `rank_clusters`
#

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

#
# Daten für Tabelle `rank_clusters`
#

INSERT INTO `rank_clusters` VALUES (1, 1, 1, 30, '30', 1, '1', '0');

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `rank_users`
#

CREATE TABLE `rank_users` (
  `platz` smallint(6) NOT NULL auto_increment,
  `id` smallint(6) default NULL,
  `name` varchar(50) default NULL,
  `points` int(11) default '0',
  `cluster` smallint(6) default NULL,
  PRIMARY KEY  (`platz`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

#
# Daten für Tabelle `rank_users`
#

INSERT INTO `rank_users` VALUES (1, 3, 'TestUser', 30, 1);

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `sysmsgs`
#

CREATE TABLE `sysmsgs` (
  `msg` int(11) NOT NULL auto_increment,
  `user` smallint(6) default NULL,
  `time` varchar(10) default NULL,
  `text` text,
  `xread` char(3) default NULL,
  PRIMARY KEY  (`msg`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `transfers`
#

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

#
# Daten für Tabelle `transfers`
#


# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `upgrades`
#

CREATE TABLE `upgrades` (
  `id` bigint(20) NOT NULL auto_increment,
  `pc` smallint(6) NOT NULL default '0',
  `start` int(11) NOT NULL default '0',
  `end` int(11) NOT NULL default '0',
  `item` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=11 ;

#
# Daten für Tabelle `upgrades`
#


# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `users`
#

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
  `points` mediumint(9) default '0',
  `sig_mails` tinytext,
  `sig_board` tinytext,
  `cluster` int(6) default NULL,
  `cm` varchar(6) default NULL,
  `login_time` int(11) NOT NULL default '0',
  `sid` tinytext,
  `sid_ip` tinytext,
  `locked` enum('no','yes') NOT NULL default 'no',
  `stylesheet` enum('anti-ie','crystal','standard','konsole') NOT NULL default 'crystal',
  `inbox_full` tinytext,
  `avatar` tinytext,
  `rank` smallint(6) default '0',
  `da_avail` enum('no','yes') NOT NULL default 'no',
  `acode` varchar(16) default NULL,
  `tcode` varchar(16) default NULL,
  `pcview_ext` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;

#
# Daten für Tabelle `users`
#

INSERT INTO `users` VALUES (1, 'Administrator', 'admin@local.host', 'd41d8cd98f00b204e9800998ecf8427e', '1', 'x', '0.0.0', 1000, '1093129845', '1093129845', 1000, NULL, NULL, NULL, NULL, 'no', 'yes', 'no', 'no', 'cluster points ranking', 'no', 0, NULL, 6518, NULL, NULL, 2, '02.09.', 1094132687, '', '127.0.0.1', 'no', 'crystal', NULL, NULL, 0, 'yes', NULL, NULL, 'no');
INSERT INTO `users` VALUES (2, 'Administrator2', 'admin2@local.host', 'd41d8cd98f00b204e9800998ecf8427e', '2', 'x', '0.0.0', 1, '1093173165', '1093299187', 900, NULL, NULL, NULL, NULL, 'no', 'yes', 'no', 'no', 'cluster points ranking', 'no', 0, NULL, 6518, NULL, NULL, 2, '', 1093299179, '', '127.0.0.1', 'no', 'crystal', NULL, NULL, 0, 'no', NULL, NULL, 'yes');
INSERT INTO `users` VALUES (3, 'TestUser', 'test@local.host', 'd41d8cd98f00b204e9800998ecf8427e', '3', 'x', '0.0.0', 1, '1093173180', '1093173180', 1000, NULL, NULL, NULL, NULL, 'no', 'yes', 'no', 'no', 'cluster points ranking', 'no', 0, NULL, 30, NULL, NULL, 1, '24.08.', 1093299090, '', '127.0.0.1', 'no', 'crystal', NULL, NULL, 1, 'no', NULL, NULL, 'yes');
