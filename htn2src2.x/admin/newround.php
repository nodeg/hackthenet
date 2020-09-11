<?php
############ Newround Script für HackTheNet coded by BODY-SNATCHER
if ($usr['stat']<1000) 

{

	simple_message('Wir wollen doch nicht hacken?!?');

	exit;

}
if ($_POST['stopserver'] == "1") {
$start_h = $_POST['start_t'] + 16;
$start_t = $_POST['stop_t'];
while ( $start_h >= 24 ) {
$start_h = $start_h - 24;
$start_t = $start_t + 1;
}

$stoptime = mktime(16, 0, 0, $_POST['stop_m'], $_POST['stop_t'], $_POST['stop_j']);
$starttime = mktime($start_h, 0, 0, $_POST['stop_m'], $start_t, $_POST['stop_j']);


unlink('data/-serverstop.txt');
file_put('data/serverstop.txt',''.$stoptime.'');
unlink('data/-newround.txt');
file_put('data/newround.txt',''.$starttime.'');

####### Letzte Runde und aktuelle Version auslesen!!
  $sql['system']=db_query('SELECT * FROM system');
  $system=mysql_fetch_array($sql['system']);
####### 
$system['runde']++;

echo 'Starte mit dem Eintragen der Daten für HackTheNet (Version '.$system['version'].') Runde<font color="red">'.$system['runde'].'</font>.<br>';
### Tabellenstruktur für Tabelle`abooks`
 

db_query("CREATE TABLE `r".$system['runde']."_abooks` (
  `user` smallint(6) default NULL,
  `set1` text NOT NULL,
  `set2` text NOT NULL,
  `set3` text NOT NULL,
  `set4` text NOT NULL,
  UNIQUE KEY `user` (`user`));");
echo 'Datenbank `abooks` anlegen.... <font color="#00FF00">OK</font><br>';

### Tabellenstruktur für Tabelle`attacks`
 

db_query("CREATE TABLE `r".$system['runde']."_attacks` (
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
);");
echo 'Datenbank `attacks` anlegen.... <font color="#00FF00">OK</font><br>';

 
### Tabellenstruktur für Tabelle`cboards`
 

db_query("CREATE TABLE `r".$system['runde']."_cboards` (
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
);");
echo 'Datenbank `cboards` anlegen.... <font color="#00FF00">OK</font><br>';
 
### Tabellenstruktur für Tabelle`cl_pacts`
 

db_query("CREATE TABLE `r".$system['runde']."_cl_pacts` (
  `cluster` smallint(6) default NULL,
  `convent` tinyint(4) default NULL,
  `partner` smallint(6) default NULL
);");
echo 'Datenbank `cl_pacts` anlegen.... <font color="#00FF00">OK</font><br>';

 
### Tabellenstruktur für Tabelle`cl_reqs`
 

db_query("CREATE TABLE `r".$system['runde']."_cl_reqs` (
  `user` smallint(6) default NULL,
  `cluster` smallint(6) default NULL,
  `comment` text,
  `dealed` char(3) default 'no'
);");
echo 'Datenbank `cl_reqs` anlegen.... <font color="#00FF00">OK</font><br>';
 
### Tabellenstruktur für Tabelle`clusters`
 

db_query("CREATE TABLE `r".$system['runde']."_clusters` (
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
  `battle` varchar(255) NOT NULL default '1',
  PRIMARY KEY  (`id`)
);");
echo 'Datenbank `clusters` anlegen.... <font color="#00FF00">OK</font><br>';

### Tabellenstruktur für Tabelle `da_participants`
 

db_query("CREATE TABLE `r".$system['runde']."_da_participants` (
  `relative` int(11) NOT NULL default '0',
  `pc` smallint(6) NOT NULL default '0',
  `ip` varchar(7) NOT NULL default '',
  `owner` smallint(6) NOT NULL default '0',
  `owner_name` varchar(40) NOT NULL default ''
);");
echo 'Datenbank `da_participants` anlegen.... <font color="#00FF00">OK</font><br>';
### Tabellenstruktur für Tabelle `distr_attacks`


db_query("CREATE TABLE `r".$system['runde']."_distr_attacks` (
  `id` smallint(6) NOT NULL auto_increment,
  `cluster` smallint(6) default NULL,
  `initiator_pc` smallint(6) default NULL,
  `initiator_usr` smallint(6) default NULL,
  `target` smallint(6) default NULL,
  `item` enum('av','cpu','fw') NOT NULL default 'av',
  PRIMARY KEY  (`id`)
);");
echo 'Datenbank `distr_attacks` anlegen.... <font color="#00FF00">OK</font><br>';
### Tabellenstruktur für Tabelle `gewinnspiel`


db_query("CREATE TABLE `r".$system['runde']."_gewinnspiel` (
  `id` int(11) NOT NULL auto_increment,
  `jackpot` varchar(255) NOT NULL default '',
  `zahl` varchar(255) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
);");
echo 'Datenbank `gewinnspiel` anlegen.... <font color="#00FF00">OK</font><br>';


### Tabellenstruktur für Tabelle `gewinnspiel_tipps`
 

db_query("CREATE TABLE `r".$system['runde']."_gewinnspiel_tipps` (
  `tipp` varchar(255) NOT NULL default '',
  `userid` int(11) NOT NULL default '0',
  `time` varchar(255) NOT NULL default ''
);");
echo 'Datenbank `gewinnspiel_tipps` anlegen.... <font color="#00FF00">OK</font><br>';
### Tabellenstruktur für Tabelle `logins`


db_query("CREATE TABLE `r".$system['runde']."_logins` (
  `id` bigint(20) NOT NULL auto_increment,
  `ip` tinytext NOT NULL,
  `usr_id` smallint(6) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);");
echo 'Datenbank `logins` anlegen.... <font color="#00FF00">OK</font><br>';
### Tabellenstruktur für Tabelle `logs`

db_query("CREATE TABLE `r".$system['runde']."_logs` (
  `id` bigint(20) NOT NULL auto_increment,
  `type` enum('other','worm_clmoney','worm_blockpc','worm_pcsendmoney','delcluster','deluser','lockuser','badlogin','chclinfo') NOT NULL default 'other',
  `usr_id` smallint(6) NOT NULL default '0',
  `payload` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
);");
echo 'Datenbank `logs` anlegen.... <font color="#00FF00">OK</font><br>';

### Tabellenstruktur für Tabelle `mails`
 

db_query("CREATE TABLE `r".$system['runde']."_mails` (
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
);");
echo 'Datenbank `mails` anlegen.... <font color="#00FF00">OK</font><br>';
### Tabellenstruktur für Tabelle `pcs`

db_query("CREATE TABLE `r".$system['runde']."_pcs` (
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
  `points` smallint(6) default NULL,
  `la` varchar(10) default NULL,
  `buildstat` tinytext,
  `di` varchar(10) default NULL,
  `dt` varchar(10) default NULL,
  `lrh` varchar(10) default NULL,
  `blocked` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
);");
echo 'Datenbank `pcs` anlegen.... <font color="#00FF00">OK</font><br>';


### Tabellenstruktur für Tabelle `rank_clusters`


db_query("CREATE TABLE `r".$system['runde']."_rank_clusters` (
  `platz` smallint(6) NOT NULL auto_increment,
  `cluster` smallint(6) default '0',
  `members` tinyint(4) default '0',
  `points` int(11) default '0',
  `av_points` float default '0',
  `pcs` mediumint(9) default '0',
  `av_pcs` float default '0',
  `success_rate` float default '0',
  PRIMARY KEY  (`platz`)
);");
echo 'Datenbank `rank_clusters` anlegen.... <font color="#00FF00">OK</font><br>';
### Tabellenstruktur für Tabelle `rank_users`
 

db_query("CREATE TABLE `r".$system['runde']."_rank_users` (
  `platz` smallint(6) NOT NULL auto_increment,
  `id` smallint(6) default NULL,
  `name` varchar(50) default NULL,
  `points` int(11) default '0',
  `cluster` smallint(6) default NULL,
  PRIMARY KEY  (`platz`)
);");
echo 'Datenbank `rank_users` anlegen.... <font color="#00FF00">OK</font><br>';
### Tabellenstruktur für Tabelle `sysmsgs`

db_query("CREATE TABLE `r".$system['runde']."_sysmsgs` (
  `msg` int(11) NOT NULL auto_increment,
  `user` smallint(6) default NULL,
  `time` varchar(10) default NULL,
  `text` text,
  `xread` char(3) default NULL,
  PRIMARY KEY  (`msg`)
);");
echo 'Datenbank `sysmsgs` anlegen.... <font color="#00FF00">OK</font><br>';

### Tabellenstruktur für Tabelle `transfers`


db_query("CREATE TABLE `r".$system['runde']."_transfers` (
  `from_id` smallint(6) default '0',
  `from_type` enum('cluster','user') NOT NULL default 'cluster',
  `from_usr` smallint(6) default NULL,
  `to_id` smallint(6) default '0',
  `to_type` enum('cluster','user') NOT NULL default 'cluster',
  `to_usr` smallint(6) default NULL,
  `credits` bigint(11) default '0',
  `time` varchar(10) default NULL
);");
echo 'Datenbank `transfers` anlegen.... <font color="#00FF00">OK</font><br>';

### Tabellenstruktur für Tabelle `upgrades`


db_query("CREATE TABLE `r".$system['runde']."_upgrades` (
  `id` bigint(20) NOT NULL auto_increment,
  `pc` smallint(6) NOT NULL default '0',
  `start` int(11) NOT NULL default '0',
  `end` int(11) NOT NULL default '0',
  `item` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
);");
echo 'Datenbank `upgrades` anlegen.... <font color="#00FF00">OK</font><br>';
### Tabellenstruktur für Tabelle `users`

db_query("CREATE TABLE `r".$system['runde']."_users` (
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
  `newmail` varchar(10) default '0',
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
  `notiz` text,
  `pcsort` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
);");
echo 'Datenbank `users` anlegen.... <font color="#00FF00">OK</font><br>';

db_query("CREATE TABLE `r".$system['runde']."_system` (
  `runde` varchar(255) default NULL,
  `version` varchar(255) default NULL
);");
echo 'Datenbank `system` anlegen.... <font color="#00FF00">OK</font><br>';

############# Erste Angaben anlegen!

db_query("INSERT INTO `gewinnspiel` VALUES (1, '', '\"noch keine Runde\"');");
db_query("INSERT INTO `gewinnspiel` VALUES (2, '20000', '');");
echo 'Benötigte Daten für das Gewinnspiel anlegen.... <font color="#00FF00">OK</font><br>';

db_query("INSERT INTO `users` VALUES (1, 'Administrator', 'admin@htn.fun-synchro.de', '434d09302266ad231082529167fa4736', '1,392', 'x', '0.0.0', 1000, '1115822781', '1115918127', 1000, '', 'Die ist der Account des Admins von HackTheNet.....\r\n\r\nEr existiert nur zu Testzwecken und wird nicht zu aktiviem spielen genutzt.\r\n\r\nMfg BODY-SNATCHER\r\n\r\nPS: Ja ihr k&ouml;nnt angreifen es gibt keine Bans oder sonstige Strafen darauf...... ich schie&szlig;e auch net zur&uuml;ck ;). Seht die PCs einfach als NoNames.', '', '1115641828', 'yes', 'yes', 'no', 'yes', 'cluster points ranking', '', '0', '1115918181', 2048, '', '', 8, '12.05.', 1115918106, '175uTdLL399H7562', '217.186.38.212', 'no', 'crystal', '', '', 4, 'yes', 'Ik8v99316771JPt9', 'X33L97Kto6', 'yes', '', 'country ASC');");
echo 'Administratoraccount anlegen.... <font color="#00FF00">OK</font><br>';
db_query("INSERT INTO `system` VALUES ('".$system['runde']."', '".$system['version']"');");
echo 'Systeminfo eintragen.... <font color="#00FF00">OK</font><br>';

echo 'Server für eine neue Runde vorbereitet, du hast '.$_POST['start_t'].' Stunden zeit um die Änderungen durchzuführen.';

}
echo '

<div id="settings-settings"><h3>Neue Runde starten?</h3>
<p>
<form action="user.php" method="POST">

<input type="hidden" name="action" value="acp">

<input type="hidden" name="do" value="newround">

<input type="hidden" name="sid" value='.$sid.'>

<p><b>Eine neue Runde gestartet werden?</b></p>
<p>Ab Wann?<br /></p>
<p>Tag <input type="text" name="stop_t"> Monat <input type="text" name="stop_m"> Jahr <input type="text" name="stop_j"><br /></p>
<p>Wieviele Stunden sollen bis zum Start vergehen? <input type="text" name="start_t"><br /></p>
<p><input type="radio" value="1" name="stopserver">Ja<br /></p>
<p><input type="submit" value="Los" name="baschick"><input type="reset" value="Zurücksetzen" name="B3">
</form>
</p>
</div>
';
############ Ende
?>
