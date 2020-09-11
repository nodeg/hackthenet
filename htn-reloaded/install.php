<?php
/**
* Install Script
* Installiert das Spiel und erstellt eine neue Runde.
* @author Seberoth
* @version 0.1.1
**/
 
//error_reporting(E_ALL);
//@chmod("config.php", 0777);

define('DEFAULT_LANG', 'de');

include('config.php');
if ($htn_installed) { echo "<br>Wenn du HTN 2.1 neu installieren willst l&öuml;sche bitte diese Zeile aus deiner config.php:<br><i>\$htn_installed = true;</i>"; 
	exit; }
 
$step = $_GET['step'];
if ($step == "") { $step = 1; }
 
 
function chmod_R($path, $filemode) {
    if (!is_dir($path))    return chmod($path, $filemode);
 
    $dh = opendir($path);
    while ($file = readdir($dh)) {
        if($file != '.' && $file != '..') {
            $fullpath = $path.'/'.$file;
            if(!is_dir($fullpath)) {
                if (!chmod($fullpath, $filemode))
                return FALSE;
            } else {
                if (!chmod_R($fullpath, $filemode))
                return FALSE;
            }
        }
    }
 
    closedir($dh);
 
    if(chmod($path, $filemode))
    return TRUE;
    else
    return FALSE;
}
 
?>
<html>
<head>
  <title>HackTheNet 2.1 Install</title>
  <meta name="author" content="Seberoth">
  <link rel="stylesheet" href="styles/crystal/style.css" type="text/css">
  <link rel="SHORTCUT ICON" href="favicon.ico" />
</head>
<body text="#000000" bgcolor="#FFFFFF" link="#000080" alink="#000080" vlink="#000080">
  <!-- Start Head -->
  <div class="header">
  <h1>HTN 2.1</h1>
  <!-- Start Navi -->
   <ul class="navigation">
    <!-- Schritt 1 -->
    <li>
     <a href="#" title="">
      <? if ($step==1) { echo '<font color="red">'; } ?><strong>Schritt 1</strong><? if ($step==1) { echo '</font>'; } ?>
      <br />
      <em>Sprache w&auml;hlen</em>
     </a>
     <div class="help">In diesem Schritt wird die Standartsprache festgelegt.<div>
    </li>
    <!-- Schritt 2 -->
    <li>
     <a href="#" title="">
      <? if ($step==2) { echo '<font color="red">'; } ?><strong>Schritt 2</strong><? if ($step==2) { echo '</font>'; } ?>
      <br />
      <em>Pr&Uuml;fen der Einstellungen</em>
     </a>
     <div class="help">In diesem Schritt werden verschiedene Einstellungen gepr&üuml;ft.<div>
    </li>
    <!-- Schritt 3 -->
    <li>
     <a href="#" title="">
      <? if ($step==3) { echo '<font color="red">'; } ?><strong>Schritt 3</strong><? if ($step==3) { echo '</font>'; } ?>
      <br />
      <em>Eingabe Ihrer Daten</em>
     </a>
     <div class="help">In diesem Schritt <i>m&üuml;ssen</i> die Daten angeben, mit denen HTN 2.1 sp&auml;ter laufen wird.</div>
    </li>
    <!-- Schritt 4 -->
    <li>
     <a href="#" title="">
      <? if ($step==4) { echo '<font color="red">'; } ?><strong>Schritt 4</strong><? if ($step==4) { echo '</font>'; } ?>
      <br />
      <em>Konfigurieren von HTN 2.1 .</em>
     </a>
     <div class="help">In diesem Schritt wird HTN 2.1 konfiguriert.</div>
    </li>
   </ul>
  <!-- End Navi -->
  </div>
  <!-- End Head -->
  <!-- Start Body -->
  <div id="abook-selpage">
  <?php
  if (!is_writable('config.php') || !is_writable('data/_server1/verifyimgs') || !is_writable('data') || !is_writable('data/_server1')) { $step = ''; }
  switch ($step) {
      default:
      echo '   <h3>Bitte w&auml;hlen sie die Standart Sprache aus</h3>'."\n";
      echo '    <form method="post" action="?step=2">'."\n";
      echo '     <table>'."\n";
      echo '      <tr>'."\n";
      echo '       <th>Sprache</th>'."\n";
      echo '       <td><select name="lang"><option value="de">Deutsch</option></select></td>'."\n";
      echo '      </tr>'."\n";
      echo '      <tr>'."\n";
      echo '       <td><input type="submit" value="   Weiter   "></input></td>'."\n";
      echo '      </tr>'."\n";
      echo '     </table>'."\n";
      echo '    </form>'."\n";
      break;
      
      
      case "2":
      $failure = 0;
      $phpversion = explode(".", phpversion());
      echo '   <h3>Einstellungen pr&üuml;fen</h3>'."\n";
      if ($phpversion[0]==5) {
          echo '    <p>PHP Version: <font color="green">PHP'.$phpversion[0].'</font>'."\n";
      }
      if (get_magic_quotes_gpc() == 1){
          echo '    <br>Magic Quotes: <font color="orange">an</font>'."\n";
      } else {
          echo '    <br>Magic Quotes: <font color="green">aus</font>'."\n";
      }
      if (is_writable('config.php')) {
          echo '    <br>Datei "config.php" Beschreibbar (777 Rechte): <font color="green">an</font>'."\n";
      } else {
          echo '    <br>Datei "config.php" Beschreibbar (777 Rechte): <font color="red">aus</font>'."\n";
          $failure++;      
      }     
      if (is_writable('data/_server1/verifyimgs')) {
          echo '    <br>Ordner "data/_server1/verifyimgs" Beschreibbar (777 Rechte): <font color="green">an</font>'."\n";
      } else {
          echo '    <br>Ordner "data/_server1/verifyimgs" Beschreibbar (777 Rechte): <font color="red">aus</font>'."\n";
          $failure++;      
      }      
      if (is_writable('data')) {
          echo '    <br>Ordner "data" Beschreibbar  (777 Rechte): <font color="green">an</font>'."\n";
      } else {
          echo '    <br>Ordner "data" Beschreibbar  (777 Rechte): <font color="red">aus</font>'."\n";
          $failure++;      
      }        
       if (is_writable('data/regtmp')) {
          echo '    <br>Ordner "data/regtmp" Beschreibbar  (777 Rechte): <font color="green">an</font>'."\n";
      } else {
          echo '    <br>Ordner "data/regtmp" Beschreibbar  (777 Rechte): <font color="red">aus</font>'."\n";
          $failure++;      
      }        
      if (is_writable('data/_server1')) {
          echo '    <br>Ordner "data/_server1" Beschreibbar  (777 Rechte): <font color="green">an</font>'."\n";
      } else {
          echo '    <br>Ordner "data/_server1" Beschreibbar  (777 Rechte): <font color="red">aus</font>'."\n";
          $failure++;      
      }        
      if (is_writable('data/_server1/usrimgs')) {
          echo '    <br>Ordner "data/_server1/usrimgs" Beschreibbar  (777 Rechte): <font color="green">an</font>'."\n";
      } else {
          echo '    <br>Ordner "data/_server1/usrimgs" Beschreibbar  (777 Rechte): <font color="red">aus</font>'."\n";
          $failure++;      
      }  
      if (is_writable('data/_server1/tmp')) {
          echo '    <br>Ordner "data/_server1/tmp" Beschreibbar  (777 Rechte): <font color="green">an</font>'."\n";
      } else {
          echo '    <br>Ordner "data/_server1/tmp" Beschreibbar  (777 Rechte): <font color="red">aus</font>'."\n";
          $failure++;      
      }
      if ($failure == 0)
      {
      	  echo '    <form method="post" action="?step=3">'."\n";
      	  echo '    <input type="hidden" name="lang" value="'.$_POST['lang'].'">'."\n";
      	  echo '    <p><input type="submit" value="Weiter"></input> mit Eingabe der Daten</p>'."\n";
      } else {
          echo '    <p>Bitte &auml;ndern Sie die aktuellen Ordnerrechte-Einstellungen, bei den rot markierten Ordnern/Dateien</p>'."\n";
      }
      break;
 
      case "3":
      echo '   <h3>Bitte geben Sie Ihre Daten ein</h3>'."\n";
      echo '    <form method="post" action="?step=4">'."\n";
      echo '     <table>'."\n";
      echo '      <tr>'."\n";
      echo '       <th>MySQL Benutzername</th>'."\n";
      echo '       <td><input type="text" size="20" name="name"></input></td>'."\n";
      echo '      </tr>'."\n";
      echo '      <tr>'."\n";
      echo '       <th>MySQL Passwort</th>'."\n";
      echo '       <td><input type="password" size="20" name="pass"></input></td>'."\n";
      echo '      </tr>'."\n";
      echo '      <tr>'."\n";
      echo '       <th>MySQL Datenbank</th>'."\n";
      echo '       <td><input type="text" size="20" name="db"></input></td>'."\n";
      echo '      </tr>'."\n";
      echo '      <tr>'."\n";
      echo '       <th>MySQL Server</th>'."\n";
      echo '       <td><input type="text" size="20" name="host" value="localhost"></input></td>'."\n";
      echo '      </tr>'."\n";
      echo '      <tr>'."\n";
      echo '       <td><input type="submit" value="   Weiter   "></input></td>'."\n";
      echo '       <td><input type="reset"  value="   L&ouml;schen   "></input></td>'."\n";
      echo '      </tr>'."\n";
      echo '     </table>'."\n";
      echo '    <input type="hidden" name="lang" value="'.$_POST['lang'].'">'."\n";
      echo '    </form>'."\n";
      break;
 
      case "4":
      /**
      * @param string $_POST['db'] Datenbankname
      * @param string $_POST['name'] MySQL-Username
      * @param string $_POST['pass'] MySQL-Userpasswort
      * @param string $_POST['host'] MySQL-Server
      * @param string $_POST['port'] MySQL-Serverport
      * @param string $handler Stellt die Verbindung zum MySQL-Server her
      **/
 
 
      $handler = @mysql_connect($_POST['host'], $_POST['name'], $_POST['pass']);
      if (!$handler) {
          echo '   <h3>Es konnte keine Verbindung zur Datenbank hergestellt werden.</h3><br /><p> <a href="javascript:history.back(\'-1\')"><b>Hier</b></a> klicken um die Daten nochmals einzugeben.</p>'."\n";
          exit;
      }
      $database = mysql_escape_string($_POST['db']);
      $do = $_GET['do'];
      switch ($do) {
          default:
          /**
               * Erstellt eine Datenbank und f&üuml;llt diese
               **/
          mysql_query("CREATE DATABASE IF NOT EXISTS `".$database."`",$handler);
          $dbselect = mysql_select_db($database, $handler); // Datenbank ausw&auml;hlen
          if (!$dbselect) {
          echo '   <h3>Die Datenbank konnte nicht erstellt werden!</h3><br /><p> <a href="javascript:history.back(\'-1\')"><b>Hier</b></a> klicken um die Daten nochmals einzugeben.</p>'."\n";
          exit;
          }
 
          mysql_query("DROP TABLE IF EXISTS abooks_entrys", $handler);
          mysql_query("DROP TABLE IF EXISTS attacks", $handler);
          mysql_query("DROP TABLE IF EXISTS cboards", $handler);
          mysql_query("DROP TABLE IF EXISTS clusters", $handler);
          mysql_query("DROP TABLE IF EXISTS cl_pacts", $handler);
          mysql_query("DROP TABLE IF EXISTS cl_reqs", $handler);
          mysql_query("DROP TABLE IF EXISTS da_participants", $handler);
          mysql_query("DROP TABLE IF EXISTS distr_attacks", $handler);
          mysql_query("DROP TABLE IF EXISTS logins", $handler);
          mysql_query("DROP TABLE IF EXISTS logs", $handler);
          mysql_query("DROP TABLE IF EXISTS mails", $handler);
          mysql_query("DROP TABLE IF EXISTS pcs", $handler);
          mysql_query("DROP TABLE IF EXISTS rank_clusters", $handler);
          mysql_query("DROP TABLE IF EXISTS rank_users", $handler);
          mysql_query("DROP TABLE IF EXISTS setting", $handler);
          mysql_query("DROP TABLE IF EXISTS sysmsgs", $handler);
          mysql_query("DROP TABLE IF EXISTS transfers", $handler);
          mysql_query("DROP TABLE IF EXISTS upgrades", $handler);
          mysql_query("DROP TABLE IF EXISTS users", $handler);
          mysql_query("DROP TABLE IF EXISTS verify_imgs", $handler);
 
          mysql_query("CREATE TABLE `abooks_entrys` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `user` smallint(5) unsigned NOT NULL default '0',
  `remote_user` smallint(5) unsigned NOT NULL default '0',
  `group` enum('1','2','3','4') NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`)
) TYPE=MyISAM AUTO_INCREMENT=10", $handler);
 
 
mysql_query("CREATE TABLE `attacks` (
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
) TYPE=MyISAM AUTO_INCREMENT=1", $handler);
 
 
mysql_query("CREATE TABLE `cboards` (
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
) TYPE=MyISAM AUTO_INCREMENT=1", $handler);
 
 
mysql_query("CREATE TABLE `clusters` (
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
  `box3` varchar(50) default 'Alte Beitr&auml;ge',
  `acceptnew` char(3) default 'yes',
  `rank` smallint(6) default '0',
  `notice` text,
  `srate_total_cnt` int(11) NOT NULL default '0',
  `srate_success_cnt` int(11) default '0',
  `srate_noticed_cnt` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `code` (`code`)
) TYPE=MyISAM AUTO_INCREMENT=3", $handler);
 
 
mysql_query("CREATE TABLE `cl_pacts` (
  `cluster` smallint(6) default NULL,
  `convent` tinyint(4) default NULL,
  `partner` smallint(6) default NULL,
  KEY `cluster` (`cluster`),
  KEY `partner` (`partner`)
) TYPE=MyISAM", $handler);
 
 
mysql_query("CREATE TABLE `cl_reqs` (
  `user` smallint(6) default NULL,
  `cluster` smallint(6) default NULL,
  `comment` text,
  `dealed` char(3) default 'no',
  KEY `cluster` (`cluster`),
  KEY `dealed` (`dealed`)
) TYPE=MyISAM", $handler);
 
 
mysql_query("CREATE TABLE `da_participants` (
  `relative` int(11) NOT NULL default '0',
  `pc` smallint(6) NOT NULL default '0',
  `ip` varchar(7) NOT NULL default '',
  `owner` smallint(6) NOT NULL default '0',
  `owner_name` varchar(40) NOT NULL default '',
  KEY `relative` (`relative`)
) TYPE=MyISAM", $handler);
 
 
mysql_query("CREATE TABLE `distr_attacks` (
  `id` smallint(6) NOT NULL auto_increment,
  `cluster` smallint(6) default NULL,
  `initiator_pc` smallint(6) default NULL,
  `initiator_usr` smallint(6) default NULL,
  `target` smallint(6) default NULL,
  `item` enum('av','cpu','fw') NOT NULL default 'av',
  PRIMARY KEY  (`id`),
  KEY `cluster` (`cluster`)
) TYPE=MyISAM AUTO_INCREMENT=1", $handler);
 
 
mysql_query("CREATE TABLE `logins` (
  `id` bigint(20) NOT NULL auto_increment,
  `ip` varchar(64) NOT NULL default '',
  `usr_id` smallint(6) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `user_agent` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `usr_id` (`usr_id`),
  KEY `ip` (`ip`)
) TYPE=MyISAM AUTO_INCREMENT=5", $handler);
 
 
mysql_query("CREATE TABLE `logs` (
  `id` bigint(20) NOT NULL auto_increment,
  `type` enum('other','worm_clmoney','worm_blockpc','worm_pcsendmoney','delcluster','deluser','lockuser','badlogin','chclinfo','qubug','adminedit') NOT NULL default 'other',
  `usr_id` smallint(6) NOT NULL default '0',
  `payload` tinytext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `usr_id` (`usr_id`)
) TYPE=MyISAM AUTO_INCREMENT=1", $handler);
 
 
mysql_query("CREATE TABLE `mails` (
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
) TYPE=MyISAM AUTO_INCREMENT=3", $handler);


mysql_query("CREATE TABLE `news` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `titel` varchar(60) NOT NULL,
  `news` varchar(250) NOT NULL,
  `Datum` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1", $handler);
 
 
mysql_query("CREATE TABLE `pcs` (
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
) TYPE=MyISAM AUTO_INCREMENT=4", $handler);
 
 
mysql_query("CREATE TABLE `rank_clusters` (
  `platz` smallint(6) NOT NULL auto_increment,
  `cluster` smallint(6) default '0',
  `members` tinyint(4) default '0',
  `points` int(11) default '0',
  `av_points` float default '0',
  `pcs` mediumint(9) default '0',
  `av_pcs` float default '0',
  `success_rate` float default '0',
  PRIMARY KEY  (`platz`)
) TYPE=MyISAM AUTO_INCREMENT=2", $handler);
 
 
mysql_query("CREATE TABLE `rank_users` (
  `platz` smallint(6) NOT NULL auto_increment,
  `id` smallint(6) default NULL,
  `name` varchar(50) default NULL,
  `points` int(11) default '0',
  `cluster` smallint(6) default NULL,
  PRIMARY KEY  (`platz`)
) TYPE=MyISAM AUTO_INCREMENT=2", $handler);
 
 
mysql_query("CREATE TABLE `setting` (
  `setting_id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY  (`setting_id`)
) TYPE=MyISAM AUTO_INCREMENT=10", $handler);
 
 
mysql_query("INSERT INTO `setting` (`setting_id`, `name`, `value`) VALUES
(1, 'max_pc', '25'),
(2, 'update_int', '10800'),
(3, 'min_attack_def', '9'),
(4, 'min_incative', '259200'),
(5, 'remote_hijack', '172800'),
(6, 'max_members', '32'),
(7, 'server_limit', '1000'),
(8, 'online_time', '600'),
(9, 'default_style', 'anti-ie'),
(10, 're_install', 'false'),
(11, 'lang', '".$_POST['lang']."')
", $handler);
 
 
mysql_query("CREATE TABLE `sysmsgs` (
  `msg` int(11) NOT NULL auto_increment,
  `user` smallint(6) default NULL,
  `time` varchar(10) default NULL,
  `text` text,
  `xread` char(3) default NULL,
  PRIMARY KEY  (`msg`),
  KEY `user` (`user`)
) TYPE=MyISAM AUTO_INCREMENT=1", $handler);
 
 
mysql_query("CREATE TABLE `transfers` (
  `from_id` smallint(6) default '0',
  `from_type` enum('cluster','user') NOT NULL default 'cluster',
  `from_usr` smallint(6) default NULL,
  `to_id` smallint(6) default '0',
  `to_type` enum('cluster','user') NOT NULL default 'cluster',
  `to_usr` smallint(6) default NULL,
  `credits` bigint(11) default '0',
  `time` varchar(10) default NULL
) TYPE=MyISAM", $handler);
 
 
mysql_query("CREATE TABLE `upgrades` (
  `id` bigint(20) NOT NULL auto_increment,
  `pc` smallint(6) NOT NULL default '0',
  `start` int(11) NOT NULL default '0',
  `end` int(11) NOT NULL default '0',
  `item` varchar(10) NOT NULL default '',
  `uniqueid` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniqueid` (`uniqueid`),
  KEY `pc` (`pc`)
) TYPE=MyISAM AUTO_INCREMENT=7", $handler);
 
 
mysql_query("CREATE TABLE `users` (
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
  `lang` varchar(3) NOT NULL default 'de',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_2` (`name`),
  KEY `name` (`name`),
  KEY `sid` (`sid`)
) TYPE=MyISAM AUTO_INCREMENT=3", $handler);
 
 
mysql_query("CREATE TABLE `verify_imgs` (
  `id` smallint(6) NOT NULL default '0',
  `chars` char(3) NOT NULL default '',
  KEY `id` (`id`)
) TYPE=MyISAM", $handler);
 
          if (!mysql_error())
          {
              echo '   <h3>Datenbank wurde erstellt und gef&üuml;llt</h3>'."\n";
              echo '   <form method="post" action="?step=4&amp;do=config">'."\n";
              echo '    <input type="hidden" name="name"  value="'.$_POST['name'].'"></input>'."\n";
              echo '    <input type="hidden" name="pass"  value="'.$_POST['pass'].'"></input>'."\n";
              echo '    <input type="hidden" name="db"    value="'.$_POST['db'].'"></input>'."\n";
              echo '    <input type="hidden" name="host"  value="'.$_POST['host'].'"></input>'."\n";
              echo '    <p><a onClick="document.forms[0].submit();" href="#">Weiter</a> zu den Einstellungen</p>'."\n";
              echo '   </form>'."\n";
          } else {
          echo mysql_error();
          echo '   <h3>Die Datenbank konnte nicht erstellt werden!</h3><br /><p> <a href="javascript:history.back(\'-1\')"><b>Hier</b></a> klicken um die Daten nochmals einzugeben.</p>'."\n";
          exit;          
          }
          break;
 
          case "config":
          echo '   <h3>HTN 2.1 Einstellungen</h2>'."\n";
          echo '     <table>'."\n";
          echo '      <tr>'."\n";
          echo '       <th>MySQL Benutzername</th>'."\n";
          echo '       <td>'.$_POST['name'].'</td>'."\n";
          echo '      </tr>'."\n";
          echo '      <tr>'."\n";
          echo '       <th>MySQL Passwort</th>'."\n";
          echo '       <td>'.$_POST['pass'].'</td>'."\n";
          echo '      </tr>'."\n";
          echo '      <tr>'."\n";
          echo '       <th>MySQL Datenbank</th>'."\n";
          echo '       <td>'.$_POST['db'].'</td>'."\n";
          echo '      </tr>'."\n";
          echo '      <tr>'."\n";
          echo '       <th>MySQL Server</th>'."\n";
          echo '       <td>'.$_POST['host'].'</td>'."\n";
          echo '      </tr>'."\n";
          echo '    <form method="post" action="?step=4&amp;do=conf_write">'."\n";
          echo '      <tr>'."\n";
          echo '       <th colspan="2" style="background-color=none;height:5;border:1px soild black;">&nbsp;</th>'."\n";
          echo '      </tr>'."\n";
          echo '     </table>'."\n";
          echo '   <h3>Bitte geben Sie Ihre Daten ein</h3>'."\n";
          echo '     <table>'."\n";
          echo '      <tr>'."\n";
          echo '       <th>Admin Benutzername</th>'."\n";
          echo '       <td><input type="text" size="20" name="admin_name"></input></td>'."\n";
          echo '      </tr>'."\n";
          echo '      <tr>'."\n";
          echo '       <th>Admin Passwort</th>'."\n";
          echo '       <td><input type="password" size="20" name="admin_pass"></input></td>'."\n";
          echo '      </tr>'."\n";
          echo '      <tr>'."\n";
          echo '       <th>Admin EMail</th>'."\n";
          echo '       <td><input type="text" size="20" name="admin_mail"></input></td>'."\n";
          echo '      </tr>'."\n";
          echo '      <tr>'."\n";
          echo '       <td><input type="submit" value="   Weiter   "></input></td>'."\n";
          echo '       <td><input type="reset"  value="   L&ouml;schen   "></input></td>'."\n";
          echo '      </tr>'."\n";
          echo '     </table>'."\n";
          echo '    <input type="hidden" name="db" value="'.$_POST['db'].'">'."\n";
          echo '    <input type="hidden" name="name" value="'.$_POST['name'].'">'."\n";
          echo '    <input type="hidden" name="pass" value="'.$_POST['pass'].'">'."\n";
          echo '    <input type="hidden" name="host" value="'.$_POST['host'].'">'."\n";
          echo '    </form>'."\n";
          break;
 
          case "conf_write":
 
          $dbselect = mysql_select_db($database, $handler); // Datenbank ausw&auml;hlen
          if (!$dbselect) {
              echo '   <h3><a href="javascript:history.back(\'-1\')">Datenbank konnte nicht ausgew&auml;hlt werden.</a></h3>'."\n";
              exit;
          }
          mysql_query("INSERT INTO users
                     VALUES ( 1, '".$_POST['admin_name']."', '".$_POST['admin_mail']."', '".md5($_POST['admin_pass'])."', '1', 'x', '0.0.0', 1, '1107786776', '1218800319', 1000, '', '', '', '', 'no', 'yes', 'no', 'no', 'cluster points ranking', 'no', 0, '1107786776', 33, '', '', 1, '15.08.', 1218800159, '1af728cc48', '127.0.0.1', 1, 1218800789, 'no', 0, '', '', 'anti-ie', '', '', 0, 'no', '', '', 'yes', '', 'yes', 0, 0, '', 5 )",$handler
          );
          mysql_query("INSERT INTO `users` ( `id`, `name`, `email`, `password`, `pcs`, `gender`, `birthday`, `stat`, `liu`, `lic`, `clusterstat`, `homepage`, `infotext`, `wohnort`, `la`, `ads`, `bigacc`, `usessl`, `enable_usrimg`, `usrimg_fmt`, `noipcheck`, `newmail`, `lastmail`, `points`, `sig_mails`, `sig_board`, `cluster`, `cm`, `login_time`, `sid`, `sid_ip`, `sid_pc`, `sid_lastcall`, `locked`, `locked_till`, `locked_by`, `locked_reason`, `stylesheet`, `inbox_full`, `avatar`, `rank`, `da_avail`, `acode`, `tcode`, `pcview_ext`, `pcview_sorttype`, `calcrank`, `last_verified`, `verifyimg`, `extacc_id`, `level` )
VALUES (
'1', '".mysql_escape_string($_POST['admin_name'])."', '".mysql_escape_string($_POST['admin_mail'])."', '".md5($_POST['admin_pass'])."', '1', 'x', '0.0.0', 1, '1107786776', '1218800319', 1000, '', '', '', '', 'no', 'yes', 'no', 'no', 'cluster points ranking', 'no', 0, '1107786776', 33, '', '', 1, '15.08.', 1218800159, '1af728cc48', '127.0.0.1', 1, 1218800789, 'no', 0, '', '', 'anti-ie', '', '', 0, 'no', '', '', 'yes', '', 'yes', 0, 0, '', 5
)");
          mysql_query("INSERT INTO `pcs` ( `id`, `name`, `ip`, `owner`, `owner_name`, `owner_points`, `owner_cluster`, `owner_cluster_code`, `cpu`, `ram`, `lan`, `mm`, `bb`, `ads`, `dialer`, `auctions`, `bankhack`, `fw`, `mk`, `av`, `ids`, `ips`, `rh`, `sdk`, `trojan`, `credits`, `lmupd`, `country`, `points`, `la`, `buildstat`, `di`, `dt`, `lrh`, `blocked`, `upgrcode` )
                VALUES (
                1, 'NoName', '92.1', 1, '".$_POST['admin_name']."', 0, 0, '', '2', 1, '1', '2.5', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 13130, '1218800524', 'afghanistan', 33, '', NULL, '', '', '', NULL, '8192be0cb55c5696'
                );",$handler
          );
          mysql_query("INSERT INTO `clusters` ( `id`, `name`, `code`, `events`, `tax`, `money`, `infotext`, `points`, `logofile`, `homepage`, `box1`, `box2`, `box3`, `acceptnew`, `rank`, `notice`, `srate_total_cnt`, `srate_success_cnt`, `srate_noticed_cnt` )
VALUES (
1, 'Administration', '=ADM!N=', ' 19:31 Der Cluster wird durch Administrator gegr&uuml;ndet!', 1, 8, NULL, 33, NULL, NULL, 'Wichtig', 'Allgemein', 'Alte Beitr&auml;ge', 'yes', 0, NULL, 0, 0, 0
);",$handler
          );

 
          //unlink('config.php');
          $fp=fopen("config.php", "w+");
          fwrite($fp, "<?php
 
 
//******************// BEGINN DER HAUPT-KONFIGURATION //******************//
 
# DATENBANKEN:
 
\$enable_multi_server_support = false; // Soll es mehrere Spielwelten geben?
 
if( !\$enable_multi_server_support )
{
  // Hier DB-Name eintragen, falls es nur einen Server (=nur eine Datenbank gibt)
  \$database_name = '".$database."';
}
 
\$database_prefix='';
\$database_suffix = '';
 
\$db_use_this_values = true; // Folgende Werte zum connecten auf den MySQL-Server benutzen?
\$db_host='".$_POST['host']."';
\$db_username='".$_POST['name']."';
\$db_password='".$_POST['pass']."';
 
// Zugangs Konstanen
define('dbname', \$database_name);
define('dbServ', \$db_host);
define('dbuid', \$db_username);
define('dbPw', \$db_password);
define('prefix', \$database_prefix);
 
# VERSCHIEDENE EINSTELLUNGEN:
\$admin_email='".$_POST['admin_mail']."'; // Email-Adresse des Administrators!
\$account_activation_per_email = false; // Account-Freischaltung per Email ?
\$enable_visual_confirmation = false; // Anti-Bot-Bildchen?
// Um Anti-Bot-Bildchen generieren zu lassen, /new_images.php?code=sdap432i aufrufen.
 
\$GAME_MODE = '2.1';
// hier 2.0 oder 2.1 eintragen:
// 2.1: max. 25 PCs, undendlich weit upgradebare Items
// 2.0: unendliche viele PCs, Upgrades begrenzt -> pro PC max. 1024 Punkte
 
# KEINE MITSPIELER:
\$no_ranking_users['server1'] = '1'; // durch Komma getrennte User-IDs von nicht in der Rangliste zu fuhrenden Spielern
 
\$no_ranking_cluster['server1'] = 1; // Nur eine Angabe möglich für ID des Admin-Clusters
 
//******************// ENDE DER HAUPT-KONFIGURATION //******************//
 
include_once('include/startup.php');
// Konfiguration Einstellungen auslesen
\$dbpdo = new Dbpdo();
\$db = \$dbpdo->get_db();
\$sql='SELECT name,value From setting';
\$stmt=\$db->prepare(\$sql);
\$stmt->execute();
 
while ((\$row = \$stmt->fetch(PDO::FETCH_OBJ)))
{
 \$name[\$row->name]=\$row->value;
}
 
# KONSTANTEN
define('MAX_PCS_PER_USER', \$name['max_pc']); # Maximale Anzahl von PCs pro Benutzer (nur im 2.1er-GAME_MODE)
 
define('UPDATE_INTERVAL', \$name['update_int']); # Interval für Punkte-Updates in Sekunden
define('MIN_ATTACK_XDEFENCE', \$name['min_attack_def']);
define('MIN_INACTIVE_TIME', \$name['min_incative']); # Inaktive Zeit vor möglichem Angriff
define('REMOTE_HIJACK_DELAY', \$name['remote_hijack']); # Wartezeit zwischen zwei Remote Hijacks
 
// int mktime ( [int Stunde [, int Minute [, int Sekunde [, int Monat [, int Tag [, int Jahr [, int is_dst]]]]]]])
 
\$t_limit_server = 1; // Server-ID eines Servers der erst sp&auml;ter startet
\$ts_server_start = mktime(10, 0, 0, 12, 18, 2004); // Wann startet er?
\$transfer_ts = mktime(10, 0, 0, 1, 1, 2005); // Ab wann kann man auf ihm Überweisen? 
 
# DIVERSES
\$STYLESHEET_BASEDIR = 'styles/';
define('MAX_USERS_PER_SERVER', \$name['server_limit']); # Maximale Anzahl von Spielern pro Server
define('SID_ONLINE_TIMEOUT', \$name['online_time']);
 
# STYLESHEETS:
\$standard_stylesheet = \$name['default_style'];
\$stylesheets['standard'] = array('id'=>'standard', 'name'=>'HackTheNet Standard', 'author'=>'HackTheNet-Team', 'bigacc'=>'no');
\$stylesheets['crystal'] = array('id'=>'crystal', 'name'=>'HackTheNet Crystal', 'author'=>'HackTheNet-Team', 'bigacc'=>'no');
\$stylesheets['konsole'] = array('id'=>'konsole', 'name'=>'Konsole', 'author'=>'Volkmar', 'bigacc'=>'yes');
\$stylesheets['anti-ie'] = array('id'=>'anti-ie', 'name'=>'Anti-IE', 'author'=>'Volkmar', 'bigacc'=>'yes');
\$stylesheets['modern'] = array('id'=>'modern', 'name'=>'Modern', 'author'=>'Volkmar und xXxUnKnownxXx', 'bigacc'=>'no');
\$stylesheets['modern-ie'] = array('id'=>'modern-ie', 'name'=>'Modern f&üuml;r Internet Explorer', 'author'=>'Volkmar und xXxUnKnownxXx', 'bigacc'=>'yes'); 
 
\$htn_installed = \$name['re_install'];
?>");
	 
          echo '   <h3>HTN 2.1 wurde erfolgreich konfiguriert.</h3>'."\n";
          echo '    <p><a href="pub.php">Weiter</a> zu HTN 2.1</p>'."\n";
          break;
      }
      break;
 


  }
  ?>
  </div>
</body>
</html>