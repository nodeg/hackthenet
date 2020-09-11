<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<?php
/**
* Install Script
* Installiert das Spiel und erstellt eine neue Runde.
* @author xXxUnKnownxXx
* @version 0.1
**/

//error_reporting(E_ALL);
//@chmod("config.php", 0777);

include('config.php');
if ($htnlan_installed) { echo "HTN.Lan wurde bereits installiert.<br>Wenn du HTN.Lan neu installieren willst lösche bitte diese Zeile aus deiner config.php:<br><i>\$htnlan_installed = true;</i>"; exit; }

$step = $_GET['step'];
if ($step == "") { $step = 1; }


function chmod_R($path, $filemode) {
	if (!is_dir($path))	return chmod($path, $filemode);

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
  <title>HackTheNet.LAN Install</title>
  <meta name="author" content="xXxUnKnownxXx">
  <link rel="stylesheet" href="styles/crystal/style.css" type="text/css">
  <link rel="SHORTCUT ICON" href="favicon.ico" />
</head>
<body text="#000000" bgcolor="#FFFFFF" link="#000080" alink="#000080" vlink="#000080">
  <!-- Start Head -->
  <div class="header">
  <h1>HTN.LAN V1.5 by Schnitzel</h1>
  <!-- Start Navi -->
   <ul class="navigation">
    <!-- Schritt 1 -->
    <li>
     <a href="#" title="">
      <? if ($step==1) { echo '<font color="red">'; } ?><strong>Schritt 1</strong><? if ($step==1) { echo '</font>'; } ?>
      <br />
      <em>Prüfen der Einstellungen</em>
     </a>
     <div class="help">In diesem Schritt werden verschiedene Einstellungen geprüft.<div>
    </li>
    <!-- Schritt 2 -->
    <li>
     <a href="#" title="">
      <? if ($step==2) { echo '<font color="red">'; } ?><strong>Schritt 2</strong><? if ($step==2) { echo '</font>'; } ?>
      <br />
      <em>Eingabe Ihrer Daten</em>
     </a>
     <div class="help">In diesem Schritt <i>müssen</i> die Daten angeben, mit denen HTN.Lan später laufen wird.</div>
    </li>
    <!-- Schritt 3 -->
    <li>
     <a href="#" title="">
      <? if ($step==3) { echo '<font color="red">'; } ?><strong>Schritt 3</strong><? if ($step==3) { echo '</font>'; } ?>
      <br />
      <em>Konfigurieren von HTN.Lan.</em>
     </a>
     <div class="help">In diesem Schritt wird HTN.Lan konfiguriert.</div>
    </li>
    <!-- Schritt 5 -->
    <li>
     <a href="#" title="">
      <? if ($step==5) { echo '<font color="red">'; } ?><strong>Schritt 4</strong><? if ($step==5) { echo '</font>'; } ?>
      <br />
      <em>Subnetze f&uuml;llen</em>
     </a>
     <div class="help">In diesem Schritt werden die Subnetzte mit herrenlose(NoName) Computern gefüllt.</div>
    </li>
    <!-- Schritt 6 -->
    <li>
     <a href="#" title="">
      <? if ($step==6) { echo '<font color="red">'; } ?><strong>Schritt 5</strong><? if ($step==6) { echo '</font>'; } ?>
      <br />
      <em>Erstellen der Anti-Bot-Bilder.</em>
     </a>
     <div class="help">In diesem Schritt werden die Anit-Bot-Bilder erstellt.</div>
    </li>
   </ul>
  <!-- End Navi -->
  </div>
  <!-- End Head -->
  <!-- Start Body -->
  <div id="abook-selpage">
  <?php
  if (!is_writable('config.php') || !is_writable('verifyimgs') || !is_writable('data') || !is_writable('data/_server1')) { $step = ''; }
  switch ($step) {
  	default:
  	$failure = 0;
  	$phpversion = explode(".", phpversion());
  	echo '   <h3>Einstellungen prüfen</h3>'."\n";
  	if ($phpversion[0]==5) {
  		echo '    <p>PHP Version: <font color="red">PHP5</font>'."\n";
  		$failure++;
  	} else {
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
  	if (is_writable('data/newround.txt')) {
  		echo '    <br>Datei "data/newround.txt" Beschreibbar (777 Rechte): <font color="green">an</font>'."\n";
  	} else {
  		echo '    <br>Datei "data/newround.txt" Beschreibbar (777 Rechte): <font color="red">aus</font>'."\n";
  		$failure++;  	
  	}
  	if (is_writable('data/roundend.txt')) {
  		echo '    <br>Datei "data/roundend.txt" Beschreibbar (777 Rechte): <font color="green">an</font>'."\n";
  	} else {
  		echo '    <br>Datei "data/roundend.txt" Beschreibbar (777 Rechte): <font color="red">aus</font>'."\n";
  		$failure++;  	
  	}
  	if (is_writable('data/install.log')) {
  		echo '    <br>Datei "data/install.log" Beschreibbar (777 Rechte): <font color="green">an</font>'."\n";
  	} else {
  		echo '    <br>Datei "data/install.log" Beschreibbar (777 Rechte): <font color="red">aus</font>'."\n";
  		$failure++;  	
  	}  	
  	if (is_writable('verifyimgs')) {
  		echo '    <br>Ordner "verifyimgs" Beschreibbar (777 Rechte): <font color="green">an</font>'."\n";
  	} else {
  		echo '    <br>Ordner "verifyimgs" Beschreibbar (777 Rechte): <font color="red">aus</font>'."\n";
  		$failure++;  	
  	}  	
  	if (is_writable('data')) {
  		echo '    <br>Ordner "data" Beschreibbar  (777 Rechte): <font color="green">an</font>'."\n";
  	} else {
  		echo '    <br>Ordner "data" Beschreibbar  (777 Rechte): <font color="red">aus</font>'."\n";
  		$failure++;  	
  	}    	
  	if (is_writable('data/login')) {
  		echo '    <br>Ordner "data/login" Beschreibbar  (777 Rechte): <font color="green">an</font>'."\n";
  	} else {
  		echo '    <br>Ordner "data/login" Beschreibbar  (777 Rechte): <font color="red">aus</font>'."\n";
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
  		echo '    <p><a href="?step=2"><b>Weiter</b></a> mit Eingabe der Daten</p>'."\n";
  	} else {
  		echo '    <p>Bitte ändern Sie die aktuellen Ordnerrechte-Einstellungen, bei den rot markierten Ordnern/Dateien</p>'."\n";
  	}
  	break;

  	case "2":
  	echo '   <h3>Bitte geben Sie Ihre Daten ein</h3>'."\n";
  	echo '    <form method="post" action="?step=3">'."\n";
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
  	echo '    </form>'."\n";
  	break;

  	case "3":
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
          	 * Erstellt eine Datenbank und füllt diese
          	 **/
  		mysql_query("CREATE DATABASE IF NOT EXISTS `".$database."`",$handler);
  		$dbselect = mysql_select_db($database, $handler); // Datenbank auswählen
  		if (!$dbselect) {
  		echo '   <h3>Die Datenbank konnte nicht erstellt werden!</h3><br /><p> <a href="javascript:history.back(\'-1\')"><b>Hier</b></a> klicken um die Daten nochmals einzugeben.</p>'."\n";
  		exit;
  		}
		
  		mysql_query("DROP TABLE IF EXISTS abooks", $handler);
  		mysql_query("DROP TABLE IF EXISTS attacks", $handler);
  		mysql_query("DROP TABLE IF EXISTS blacklist", $handler);
  		mysql_query("DROP TABLE IF EXISTS cboards", $handler);
  		mysql_query("DROP TABLE IF EXISTS changelog", $handler);
  		mysql_query("DROP TABLE IF EXISTS cl_pacts", $handler);
  		mysql_query("DROP TABLE IF EXISTS cl_reqs", $handler);
  		mysql_query("DROP TABLE IF EXISTS clusters", $handler);
  		mysql_query("DROP TABLE IF EXISTS da_participants", $handler);
  		mysql_query("DROP TABLE IF EXISTS distr_attacks", $handler);
  		mysql_query("DROP TABLE IF EXISTS hof", $handler);
  		mysql_query("DROP TABLE IF EXISTS logins", $handler);
  		mysql_query("DROP TABLE IF EXISTS logs", $handler);
  		mysql_query("DROP TABLE IF EXISTS mails", $handler);
  		mysql_query("DROP TABLE IF EXISTS msg_ignore", $handler);
  		mysql_query("DROP TABLE IF EXISTS news", $handler);
  		mysql_query("DROP TABLE IF EXISTS news_comment", $handler);
  		mysql_query("DROP TABLE IF EXISTS pcs", $handler);
  		mysql_query("DROP TABLE IF EXISTS rank_clusters", $handler);
  		mysql_query("DROP TABLE IF EXISTS rank_users", $handler);
  		mysql_query("DROP TABLE IF EXISTS sysmsgs", $handler);
  		mysql_query("DROP TABLE IF EXISTS todo", $handler);
  		mysql_query("DROP TABLE IF EXISTS transfers", $handler);
  		mysql_query("DROP TABLE IF EXISTS upgrades", $handler);
  		mysql_query("DROP TABLE IF EXISTS users", $handler);
  		mysql_query("DROP TABLE IF EXISTS verify_imgs", $handler);
  		mysql_query("DROP TABLE IF EXISTS win", $handler);

  		mysql_query("CREATE TABLE `abooks` (
  `user` smallint(6) default NULL,
  `set1` text NOT NULL,
  `set2` text NOT NULL,
  `set3` text NOT NULL,
  `set4` text NOT NULL,
  UNIQUE KEY `user` (`user`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `attacks` (
  `from_pc` smallint(6) default '0',
  `from_usr` smallint(6) default '0',
  `from_cluster` int(11) default '0',
  `to_pc` smallint(6) default '0',
  `to_usr` smallint(6) default '0',
  `to_cluster` int(11) default '0',
  `type` enum('block','hijack','scan','smash','trojan') NOT NULL default 'block',
  `option` enum('cpu','deactivate','defacement','firewall','sdk','transfer','send anna') NOT NULL default 'cpu',
  `success` tinyint(1) default '0',
  `noticed` tinyint(1) default '0',
  `time` int(11) NOT NULL default '0'
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `blacklist` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `email` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `bugtracker` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `admin` varchar(50) NOT NULL default '',
  `date` int(20) NOT NULL default '0',
  `text` text,
  `type` varchar(30) NOT NULL default '',
  `titel` varchar(50) NOT NULL default '',
  `status` varchar(40) NOT NULL default '',
  `type_number` int(1) NOT NULL default '0',
  `status_number` int(1) NOT NULL default '0',
  `n` int(10) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `bugtracker_comments` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(40) NOT NULL default '',
  `text` tinytext NOT NULL,
  `parentid` int(10) NOT NULL default '0',
  `date` int(30) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);


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
  `closed` enum('yes','no') default 'no',
  PRIMARY KEY  (`thread`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `changelog` (
  `id` int(10) NOT NULL auto_increment,
  `time` int(10) NOT NULL default '0',
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `cl_pacts` (
  `cluster` smallint(6) default NULL,
  `convent` tinyint(4) default NULL,
  `partner` smallint(6) default NULL
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `cl_reqs` (
  `user` smallint(6) default NULL,
  `cluster` smallint(6) default NULL,
  `comment` text,
  `dealed` char(3) default 'no'
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `clusters` (
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
  `box4` varchar(50) default 'Only Admins',
  `boardurl` varchar(50) default NULL,
  `ircchannel` varchar(50) default NULL,
  `ircnetwork` varchar(50) default NULL,
  `externb` varchar(50) default 'nein',
  PRIMARY KEY  (`id`)
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
  `id` bigint(100) NOT NULL auto_increment,
  `cluster` smallint(6) default NULL,
  `initiator_pc` smallint(6) default NULL,
  `initiator_usr` smallint(6) default NULL,
  `target` smallint(6) default NULL,
  `item` enum('av','cpu','fw') NOT NULL default 'av',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `hof` (
  `id` int(11) NOT NULL auto_increment,
  `user` varchar(25) NOT NULL default '',
  `cluster` varchar(25) NOT NULL default '',
  `why` text NOT NULL,
  `round` char(3) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `logins` (
  `id` bigint(20) NOT NULL auto_increment,
  `ip` tinytext NOT NULL,
  `usr_id` smallint(6) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `logs` (
  `id` bigint(20) NOT NULL auto_increment,
  `type` enum('other','worm_clmoney','worm_blockpc','worm_pcsendmoney','worm_destroypc','delcluster','deluser','lockuser','badlogin','chclinfo') NOT NULL default 'other',
  `usr_id` smallint(6) NOT NULL default '0',
  `payload` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);


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
  PRIMARY KEY  (`mail`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `msg_ignore` (
  `id` bigint(20) NOT NULL auto_increment,
  `user` smallint(6) default NULL,
  `user2` smallint(6) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `news` (
  `id` int(10) NOT NULL auto_increment,
  `time` int(14) NOT NULL default '0',
  `autor` varchar(40) NOT NULL default '',
  `autor_id` smallint(40) NOT NULL default '0',
  `title` varchar(40) NOT NULL default '',
  `kategorie` varchar(40) NOT NULL default 'HTN.Lan',
  `url1` varchar(40) NOT NULL default '',
  `link1` varchar(40) NOT NULL default '',
  `url2` varchar(40) NOT NULL default '',
  `link2` varchar(40) NOT NULL default '',
  `url3` varchar(40) NOT NULL default '',
  `link3` varchar(40) NOT NULL default '',
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `news_comment` (
  `id` int(10) NOT NULL auto_increment,
  `news_id` smallint(6) NOT NULL default '0',
  `autor` varchar(40) NOT NULL default '',
  `autor_id` int(11) NOT NULL default '0',
  `ip` varchar(30) NOT NULL default '',
  `time` int(10) NOT NULL default '0',
  `text` text NOT NULL,
  `titel` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);

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
  `country` enum('afghanistan','antarktis','antigua','argentinien','australien','austria','brasilien','china','deutschland','egypt','england','finnland','frankreich','griechenland','groenland','indien','irak','iran','irland','island','italien','japan','kanada','kuba','lybien','madagaskar','mexico','monaco','namibia','neuseeland','nordkorea','pakistan','peru','portugal','quatar','russland','saudi-arabien','schweden','schweiz','sierraleone','spanien','suedafrika','thailand','tuerkei','usa','vanuatu','vietnam','marlboro','atlantis','myth','quest') NOT NULL default 'myth',
  `points` smallint(6) default NULL,
  `la` varchar(10) default NULL,
  `buildstat` tinytext,
  `di` varchar(10) default NULL,
  `dt` varchar(10) default NULL,
  `lrh` varchar(10) default NULL,
  `blocked` varchar(10) default NULL,
  `upgrcode` varchar(32) NOT NULL default '',
  KEY `id` (`id`)
) TYPE=MyISAM", $handler);


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
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `rank_users` (
  `platz` smallint(6) NOT NULL auto_increment,
  `id` smallint(6) default NULL,
  `name` varchar(50) default NULL,
  `points` int(11) default '0',
  `cluster` smallint(6) default NULL,
  PRIMARY KEY  (`platz`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `sysmsgs` (
  `msg` int(11) NOT NULL auto_increment,
  `user` smallint(6) default NULL,
  `time` varchar(10) default NULL,
  `text` text,
  `xread` char(3) default NULL,
  PRIMARY KEY  (`msg`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `todo` (
  `id` int(10) NOT NULL auto_increment,
  `time` int(10) NOT NULL default '0',
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);


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
) TYPE=MyISAM", $handler);


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
  `points` mediumint(9) default '0',
  `sig_mails` tinytext,
  `sig_board` tinytext,
  `cluster` int(6) default NULL,
  `cm` varchar(6) default NULL,
  `login_time` int(11) NOT NULL default '0',
  `sid` tinytext,
  `sid_ip` tinytext,
  `locked` varchar(100) default NULL,
  `stylesheet` enum('anti-ie','crystal','standard','konsole','modern','modern-ie','blackcs','darkblue') NOT NULL default 'crystal',
  `inbox_full` tinytext,
  `avatar` tinytext,
  `rank` smallint(6) default '0',
  `da_avail` enum('no','yes') NOT NULL default 'no',
  `acode` varchar(16) default NULL,
  `tcode` varchar(16) default NULL,
  `pcview_ext` enum('yes','no') NOT NULL default 'yes',
  `notiz` text,
  `pcsort` varchar(100) default NULL,
  `verwarnung` int(1) default '0',
  `sid_pc` smallint(6) NOT NULL default '0',
  `lca` int(20) default '0',
  `clustertitle` varchar(50) default NULL,
  `danotice` enum('yes','no') default 'yes',
  `lopc` varchar(10) default '0',
  `user_agent` text,
  `send_sysmsg` enum('no','yes') default 'no',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `verify_imgs` (
  `id` smallint(6) NOT NULL auto_increment,
  `key` varchar(7) NOT NULL default '',
  `chars` varchar(4) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `key` (`key`)
) TYPE=MyISAM", $handler);


mysql_query("CREATE TABLE `win` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `tipp` int(3) NOT NULL default '0',
  `time` int(10) NOT NULL default '0',
  `jackpot` int(100) default '0',
  `lastwin` int(10) default '0',
  `wintipp` int(3) default '0',
  `lastwinner` varchar(100) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM", $handler);

  		if (!mysql_error())
  		{
	  		echo '   <h3>Datenbank wurde erstellt und gefüllt</h3>'."\n";
	  		echo '   <form method="post" action="?step=3&amp;do=config">'."\n";
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
  		echo '   <h3>HTN.Lan Einstellungen</h2>'."\n";
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
  		echo '    <form method="post" action="?step=3&amp;do=conf_write">'."\n";
  		echo '      <tr>'."\n";
  		echo '       <th colspan="2" style="background-color=none;height:5;border:1px soild black;">&nbsp;</th>'."\n";
  		echo '      </tr>'."\n";
  		echo '      <tr>'."\n";
  		echo '       <td>New Round Passwort</td>'."\n";
  		echo '       <td><input type="text" maxlenght="30" size="20" value="" name="newroundpw"></input></td>'."\n";
  		echo '      </tr>'."\n";
  		echo '      <tr>'."\n";
  		echo '       <td>Spielgeschwindigkeit</td>'."\n";
  		echo '       <td><input type="text" maxlenght="5" size="20" value="30" name="speed"></input></td>'."\n";
  		echo '      </tr>'."\n";
  		echo '      <tr>'."\n";
  		echo '       <td>Startgeld</td>'."\n";
  		echo '       <td><input type="text" maxlenght="20" size="20" value="10000" name="money"></input></td>'."\n";
  		echo '      </tr>'."\n";
  		
  		echo '      <tr>'."\n";
  		echo '       <td>Immer Big-Accounts erstellen?</td>'."\n";
  		echo '       <td><select name="only_big_accounts"><option value="true">Ja</option><option value="false">Nein</option></select></td>'."\n";
  		echo '      </tr>'."\n";
  		
  		echo '      <tr>'."\n";
  		echo '       <td>Interval für<br /> Punkte-Updates in Sekunden</td>'."\n";
  		echo '       <td><input name="game_update" value="10800" id="_game_update" /></td>'."\n";
  		echo '      </tr>'."\n";
  		echo '      <tr>'."\n";
  		echo '       <td>Inaktive Zeit<br /> vor möglichem Angriff in Sekunden</td>'."\n";
  		echo '       <td><input name="game_inactive" value="259200" id="_game_inactive" /></td>'."\n";
  		echo '      </tr>'."\n";
  		echo '      <tr>'."\n";
  		echo '       <td>Wartezeit zwischen<br /> zwei Remote Hijacks in Sekunden</td>'."\n";
  		echo '       <td><input name="game_hijack" value="172800" id="_game_hijack" /></td>'."\n";
  		echo '      </tr>'."\n";
  		echo '      <tr>'."\n";
  		echo '       <td>Maximale Anzahl<br /> von Spielern pro Server</td>'."\n";
  		echo '       <td><input name="game_user/server" value="4444" id="_game_user/server" /></td>'."\n";
  		echo '      </tr>'."\n";
  		echo '      <tr>'."\n";
  		echo '       <td>Minimale Anzahl von Punkten<br /> bis ein Spieler angegriffen werden kann</td>'."\n";
  		echo '       <td><input name="game_attack_points" value="500" id="_game_attack_points" /></td>'."\n";
  		echo '      </tr>'."\n";
  		echo '      <tr>'."\n";
  		echo '       <td>Zeit in der kein Geld überwiesen<br /> und der Clusterbeitrag nicht erhöht werden kann in Sekunden.</td>'."\n";
  		echo '       <td><input name="game_notrans" value="21600" id="_game_notrans" /></td>'."\n";
  		echo '      </tr>'."\n";
  		echo '      <tr>'."\n";
  		echo '       <td>Startzeit der Runde (leer lassen für jetzt)<br /><b>Unix-Timestamp!</b></td>'."\n";
  		echo '       <td><input name="game_starttime" value="" id="_game_notrans" /></td>'."\n";
  		echo '      </tr>'."\n";   		
  		echo '      <tr>'."\n";
  		echo '       <td>Dauer einer Runde in <b>Tagen</b></td>'."\n";
  		echo '       <td><input name="game_roundduration" value="7" id="_game_notrans" /></td>'."\n";
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

  		$dbselect = mysql_select_db($database, $handler); // Datenbank auswählen
  		if (!$dbselect) {
  			echo '   <h3><a href="javascript:history.back(\'-1\')">Datenbank konnte nicht ausgewählt werden.</a></h3>'."\n";
  			exit;
  		}
  		mysql_query("INSERT INTO users
                     VALUES ( 1, '".$_POST['admin_name']."', '".$_POST['admin_mail']."', '".md5($_POST['admin_pass'])."', '1', 'x', '0.0.0', 1000, '1093129845', '1093129845', 1000, NULL , NULL , NULL , NULL , 'no', 'yes', 'no', 'no', 'cluster points ranking', 'no', 0, NULL , 6518, NULL , NULL , 2, '02.09.', 1094132687, '', '127.0.0.1', 'no', 'crystal', NULL , NULL , 0, 'yes', NULL , NULL , 'no', NULL , NULL )",$handler
  		);
  		mysql_query("INSERT INTO `users` ( `id` , `name` , `email` , `password` , `pcs` , `gender` , `birthday` , `stat` , `liu` , `lic` , `clusterstat` , `homepage` , `infotext` , `wohnort` , `la` , `ads` , `bigacc` , `usessl` , `enable_usrimg` , `usrimg_fmt` , `noipcheck` , `newmail` , `lastmail` , `points` , `sig_mails` , `sig_board` , `cluster` , `cm` , `login_time` , `sid` , `sid_ip` , `locked` , `stylesheet` , `inbox_full` , `avatar` , `rank` , `da_avail` , `acode` , `tcode` , `pcview_ext` , `notiz` , `pcsort` , `verwarnung` , `sid_pc` , `lca` )
VALUES (
'1', '".mysql_escape_string($_POST['admin_name'])."', '".mysql_escape_string($_POST['admin_mail'])."', '".md5($_POST['admin_pass'])."', '1', 'x', '0.0.0', '1000', NULL , NULL , '2', NULL , NULL , NULL , NULL , 'yes', 'yes', 'no', 'no', 'cluster points ranking', 'no', '0', NULL , '0', NULL , NULL , NULL , NULL , '0', NULL , NULL , NULL , 'crystal', NULL , NULL , '0', 'no', NULL , NULL , 'yes', NULL , NULL , '0', '0', '0'
)");
  		mysql_query("INSERT INTO `pcs` ( `id` , `name` , `ip` , `owner` , `owner_name` , `owner_points` , `owner_cluster` , `owner_cluster_code` , `cpu` , `ram` , `lan` , `mm` , `bb` , `ads` , `dialer` , `auctions` , `bankhack` , `fw` , `mk` , `av` , `ids` , `ips` , `rh` , `sdk` , `trojan` , `credits` , `lmupd` , `country` , `points` , `la` , `buildstat` , `di` , `dt` , `lrh` , `blocked` , `upgrcode` )
				VALUES (
				'1', 'NoName', '102.1', '1', '".$_POST['admin_name']."', NULL , NULL , NULL , '21', '9', '10', '10', '10', '9', '9', '9', '9', '10', '10', '10', '10', '10', '10', '5', '5', '1000000', '".time()."', 'usa', NULL , '".time()."', NULL , NULL , NULL , '".time()."', NULL , ''
				);",$handler
  		);
  		mysql_query("INSERT INTO `clusters` ( `id` , `name` , `code` , `events` , `tax` , `money` , `infotext` , `points` , `logofile` , `homepage` , `box1` , `box2` , `box3` , `acceptnew` , `rank` , `notice` , `srate_total_cnt` , `srate_success_cnt` , `srate_noticed_cnt` , `box4` , `boardurl` , `ircchannel` , `ircnetwork` , `externb` )
VALUES (
'2', '::root::', '::root::', NULL , '1', '0', NULL , NULL , NULL , NULL , 'Wichtig', 'Allgemein', 'Alte Beiträge', 'yes', '0', NULL , '0', '0', '0', 'Only Admins', NULL , NULL , NULL , 'nein'
);",$handler
  		);

  		//unlink('config.php');
  		$fp=fopen("config.php", "w+");
  		fwrite($fp, "<?php
// HTN.LAN by Schnitzel
define('geschwindigkeit',".$_POST['speed'].",false); #Geschwindigkeit des Spieles Standart: 10;
define('startgeld',".$_POST['money'].",false); #Startgeld für neue PC's

\$only_big_accounts='".$_POST['only_big_accounts']."';
\$database_prefix='".$database."';
\$database_suffix='';
\$db_use_this_values=true;
\$db_host='".$_POST['host']."';
\$db_username='".$_POST['name']."';
\$db_password='".$_POST['pass']."';

\$standard_stylesheet='crystal';
\$stylesheets['standard']=array('id'=>'standard', 'name'=>'HackTheNet Standard', 'author'=>'HackTheNet-Team', 'bigacc'=>'no');
\$stylesheets['crystal']=array('id'=>'crystal', 'name'=>'HackTheNet Crystal', 'author'=>'HackTheNet-Team', 'bigacc'=>'no');
\$stylesheets['blackcs']=array('id'=>'blackcs', 'name'=>'Hackthenet Black-Crystal (FF only)', 'author'=>'xXxUnKnownxXx', 'bigacc'=>'no');
\$stylesheets['konsole']=array('id'=>'konsole', 'name'=>'Konsole', 'author'=>'Volkmar', 'bigacc'=>'no');
\$stylesheets['anti-ie']=array('id'=>'anti-ie', 'name'=>'Anti-IE', 'author'=>'Volkmar', 'bigacc'=>'no');
\$stylesheets['modern'] = array('id'=>'modern', 'name'=>'Modern', 'author'=>'Volkmar und xXxUnKnownxXx', 'bigacc'=>'no');
\$stylesheets['modern-ie'] = array('id'=>'modern-ie', 'name'=>'Modern für Internet Explorer', 'author'=>'Volkmar und xXxUnKnownxXx', 'bigacc'=>'yes');\n
\$no_ranking_users='1';
\$no_ranking_clusters='2'; # Nur eine Angabe möglich

define('UPDATE_INTERVAL',".$_POST['game_update'].", false); # Interval für Punkte-Updates in Sekunden
define('MIN_ATTACK_XDEFENCE',9, false);
define('MIN_INACTIVE_TIME',(".$_POST['game_inactive']."/geschwindigkeit), false); # Inaktive Zeit vor möglichem Angriff
define('REMOTE_HIJACK_DELAY',(".$_POST['game_hijack']."/geschwindigkeit), false); # Wartezeit zwischen zwei Remote Hijacks
define('MAX_USERS_PER_SERVER',".$_POST['game_user/server'].", false); # Maximale Anzahl von Spielern pro Server
define('MIN_ATTACK_POINTS',".$_POST['game_attack_points'].", false); # Minimale Anzahl von Punkten bis ein Spieler angegriffen werden kann
define('NO_TRANSFER',".$_POST['game_notrans'].",false); #Anzahl Sekunden in denen kein Geld überwiesen und der Clusterbeitrag nicht erhöht werden kann
define('LOGIN_TIME',259200,false); # Letzte Loginzeit < aktuelle Zeit - LOGIN_TIME = PCs des Users werden zum hijacken freigegeben

\$REMOTE_FILES_DIR='.'; # dreck
\$STYLESHEET_BASEDIR='styles/';

\$round_duration='".($_POST['game_roundduration']*24*60*60)."';

\$admin_password='".md5($_POST['admin_pass'])."';
\$admin_username='".$_POST['admin_name']."';
\$admin_mail='".$_POST['admin_mail']."';
\$newround_pw='".md5($_POST['newroundpw'])."';
");
  		fclose($fp);
  		if ($_POST['game_starttime']){
  			$fp=fopen("data/newround.txt", "w+");
  			fwrite($fp, $_POST['game_starttime']);
  			fclose($fp);
  			$fp=fopen("data/roundend.txt", "w+");
  			fwrite($fp, ($_POST['game_starttime']+($_POST['game_roundduration']*24*60*60)-3600));
  			fclose($fp);  			
  		} else {
  			$fp=fopen("data/newround.txt", "w+");
  			fwrite($fp, time());
  			fclose($fp);  
  			$fp=fopen("data/roundend.txt", "w+");
  			fwrite($fp, (time()+($_POST['game_roundduration']*24*60*60)-3600));
  			fclose($fp);  		  						
  		}
  		echo '   <h3>HTN.Lan wurde erfolgreich konfiguriert.</h3>'."\n";
  		echo '    <p><a href="?step=5">Weiter</a> zum füllen der Subnets</p>'."\n";
  		break;
  	}
  	break;

  	case "5":
  	$do=$_GET['do'];
  	switch($do) {

  		default:
  		echo '   <h3>Bitte geben Sie Ihre Daten ein</h3>'."\n";
  		echo '    <form method="post" action="?step=5&do=fill">'."\n";
  		echo '     <table>'."\n";
  		echo '      <tr>'."\n";
  		echo '       <th>Wieviele Computer pro Subnet?<br><i>freilassen für nicht füllen</i></th>'."\n";
  		echo '       <td><input type="text" size="20" name="x"></input></td>'."\n";
  		echo '      </tr>'."\n";
  		echo '      <tr>'."\n";
  		echo '       <td><input type="submit" value="   Weiter   "></input></td>'."\n";
  		echo '       <td><input type="reset"  value="   L&ouml;schen   "></input></td>'."\n";
  		echo '      </tr>'."\n";
  		echo '     </table>'."\n";
  		echo '    </form>'."\n";
  		break;

  		case 'fill':
  		$x = $_POST['x'];
  		if ($x!="" && $x!=0) {

            echo '   <h3>Subnets wurden gefüllt</h2><p>'."\n";
            include('fill.php');
  			echo '    <a href="?step=6">Weiter</a> mit erstellen der Anti-Bot Bilder</p>'."\n";
  		} else {
  			echo '   <h3>Subnets wurden nicht gefüllt</h2>'."\n";
  			echo '    <p><a href="?step=6">Weiter</a> mit erstellen der Anti-Bot Bilder</p>'."\n";
  		}
  		$fp=fopen("config.php", "a+");
  		if ($x=="" or $x==0){
   	    	fwrite($fp, "\$computers_to_fill = 0;
");
  		} else {
  	    	fwrite($fp, "\$computers_to_fill = ".$x.";
");
  		}
  	    fclose($fp);
  		break;

  	}
  	break;

  	case "6":
  	
  	$fp=fopen("config.php", "a+");
  	fwrite($fp, "\$htnlan_installed = true; 
?>");
  	fclose($fp);
  	echo '   <h3>Anti-Bot Bilder wurden erstellt</h2>'."\n";
	include('new_images.php');
  	echo '   <p><a href="pub.php">Weiter</a> zu HTN.Lan</p>'."\n";
  	break;
  }
  ?>
  </div>
</body>
</html> 
