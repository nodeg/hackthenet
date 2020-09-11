<?php

ignore_user_abort();

/**
 * New Round Script
 * Erstellt eine neue Runde und beendet die aktuelle.
 * @author Rio
 * @version 0.1
 * @param bool $fill_subnets Sollen die Subnets mit NoName PCs gefüllt werden?
 * @param integer $round_duration Wie lange dauert die Runde? (in Sekunden)
 * @param string $admin_password MD5 verschlüsseltes Passwort für den Administrator Account.
 **/
include("data/static/country_data.inc.php");
include('config.php');

$fill_subnets=true;
$round_duration=$round_duration-(60*60);

if (md5($_GET['pw'])!=$newround_pw) { exit; }

/**
 * Funktion zum löschen der Dateien in einem Verzeichnises.
 * @param string $verz Welches Verzeichnis soll gelöscht werden?
 **/

function cleardir($verz) {
  $h=@opendir($verz);
  while($fn=@readdir($h)) {
    if(@is_file($verz.'/'.$fn)) {
      @unlink($verz.'/'.$fn);
    }
  }
  @closedir($h);
}

/**
 * Funktion zum schreiben eines Textes in eine Datei
 * @param string $filename Name der Datei
 * @param string $strContent Text welcher in die Datei kommt
 * @param string $type Modi für fopen()
 **/

function file_put($filename,$strContent, $type) {
       $file = @fopen($filename, $type);
       if($file) {
           fwrite($file,$strContent);
           fclose($file);
           $r=true;
       } else $r=false;
  return $r;
   }
   
function generateMnemonicPassword() {

$charset[0] = array('a', 'e', 'i', 'o', 'u');
$charset[1] = array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'z');
$specials=array('!', '$', '%', '&', '/', '=', '?', '+', '-', '.', ':', ',', ';', '*', '#','_');

$password = '';

for ($i = 1; $i <= 2; $i++) {
$password .= $charset[$i % 2][array_rand($charset[$i % 2])];
}
$password.=$specials[mt_rand(0,count($specials)-1)];
for ($i = 1; $i <= 2; $i++) {
$password .= $charset[$i % 2][array_rand($charset[$i % 2])];
}

for ($i = 1; $i <= 2; $i++) {
$password .= rand(0, 9);
}
$password.=$charset[1][mt_rand(0,count($charset[1])-1)];

return $password;
}

/**
 * Prüft ob die Runde beendet ist
 **/
$newround=file_get_contents("data/newround.txt");
echo 'Time: '.date("d.m.Y H:i", $newround+$round_duration).'<br>';
if (time()>=$newround+$round_duration-(60*60) && $newround!='') {
	
	if (!file_exists('data/install.log')) { touch('data/install.log'); }
	file_put('data/install.log', date("d.m.Y H:i", time())." Newround Script aktiviert..\n", 'a+');

	/**
 	 * Connectet zum MySQL Server und wählt
	 * die Datenbank aus.
	 **/
	
	file_put('data/install.log', date("d.m.Y H:i", time())." Connecte zur MySQL Datenbank...\n", 'a+');
	mysql_connect($db_host, $db_username, $db_password) OR file_put('data/install.log', date("d.m.Y H:i", time())."Konnte nicht zum MySQL Server connecten.\n");
	mysql_select_db($database_prefix.$database_suffix) OR file_put('data/install.log', date("d.m.Y H:i", time())."Konnte Datenbank nicht auswählen.\n");
	
	/**
	 * Erstellt den Timestamp für die newround.txt.
	 **/ 
	
	$new_round=$newround+$round_duration+(60*60);

	/**
	 * Erstellt die work.txt im data Ordner so, dass man sich beim
	 * leeren nicht schon registrieren kann.
 	**/

	file_put('data/install.log', date("d.m.Y H:i", time())." Sperre den Server...\n", 'a+');
	touch('data/work.txt');

	/**
	 * Loggt alle User aus und leer die
	 * Temp Ordern von HTN.Lan
	 **/

	file_put('data/install.log', date("d.m.Y H:i", time())." Leere die Temp Ordner..\n", 'a+');
	cleardir('data/login');
	cleardir('data/_server1'); touch('./data/_server1/user_count.dat');
	cleardir('data/_server1/usrimgs');
	cleardir('data/_server1/tmp');
	cleardir('data/regtmp');

	/**
	 * Liest die Ranglisten aus.
	 **/
	$rank_users=mysql_query('SELECT users.platz AS platz,users.name AS name,users.points AS points,clusters.name AS cluster_name FROM `rank_users` AS users, `clusters` where clusters.id=users.cluster || clusters.id="" order by platz limit 3;');
	$rank_clusters=mysql_query('SELECT ranking.platz AS platz,clusters.name AS name,ranking.points AS points FROM `rank_clusters` AS ranking, `clusters` AS clusters WHERE ranking.cluster=clusters.id || clusters.id="" order by platz limit 3;');
	file_put('data/install.log', "\nRanking Users\n", 'a+');
	while($users=mysql_fetch_assoc($rank_users)) {
		file_put('data/install.log', $users['platz'].' '.$users['name'].' '.$users['points'].' '.$users['cluster_name']."\n", 'a+');
	}

	file_put('data/install.log', "\nRanking Clusters\n", 'a+');
	while($clusters=mysql_fetch_assoc($rank_clusters)) {
		file_put('data/install.log', $clusters['platz'].' '.$clusters['name'].' '.$clusters['points']."\n", 'a+');
	}	

	/**
 	* Leer die MySQL Tabellen und fügt
 	* einen Administrator hinzu.
 	**/

	file_put('data/install.log', date("d.m.Y H:i", time())." Leere die MySQL Datenbank...\n", 'a+');
	$sql="TRUNCATE TABLE `abooks`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `attacks`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `cboards`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `cl_pacts`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `cl_reqs`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `clusters`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `da_participants`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `distr_attacks`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `logins`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `logs`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `mails`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `msg_ignore`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `pcs`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `rank_clusters`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `rank_users`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `sysmsgs`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `transfers`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `upgrades`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `users`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `verify_imgs`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="TRUNCATE TABLE `win`;"; $db_result=mysql_query($sql) OR die('Fehler: '.mysql_error()); 
	$sql="INSERT INTO `win` VALUES(1, 1, 0, ".$new_round.", 0, ".$new_round.", 0, '');";
	if ($_GET['adminsql']=="") {
		$sql="INSERT INTO `clusters` VALUES (2, \"Administratoren\", \"::root::\", \"00:12 Der Cluster wird durch Administrator gegr&uuml;ndet!\", 1, 19, \"\", 3595, \"\", \"\", \"Wichtig\", \"Allgemein\", \"Alte Beitr&auml;ge\", \"yes\", 0, \"\", 0, 0, 0, \"Only Admins\", \"\", \"\", \"\", \"nein\");"; 
		$db_result=mysql_query($sql) OR die('Fehler cluster: '.mysql_error()); 
		
		$sql="INSERT INTO `pcs` VALUES (1, \"NoName\", \"102.1\", 1, \"".$admin_username."\", 0, 0, \"\", \"21\", 9, \"10\", \"10\", \"10\", \"9\", \"9\", \"9\", \"9\", \"1\", \"10\", \"1\", \"20\", \"10\", \"10\", \"5\", \"5\", 1999999983, \"2000000000\", \"usa\", 3595, \"\", \"\", \"\", \"\", \"\", \"\", \"\");"; 
		$db_result=mysql_query($sql) OR die('Fehler pcs: '.mysql_error());
		 
		$sql="INSERT INTO `users` ( `id` , `name` , `email` , `password` , `pcs` , `gender` , `birthday` , `stat` , `liu` , `lic` , `clusterstat` , `homepage` , `infotext` , `wohnort` , `la` , `ads` , `bigacc` , `usessl` , `enable_usrimg` , `usrimg_fmt` , `noipcheck` , `newmail` , `lastmail` , `points` , `sig_mails` , `sig_board` , `cluster` , `cm` , `login_time` , `sid` , `sid_ip` , `locked` , `stylesheet` , `inbox_full` , `avatar` , `rank` , `da_avail` , `acode` , `tcode` , `pcview_ext` , `notiz` , `pcsort` , `verwarnung` , `sid_pc` , `lca` , `clustertitle` , `danotice` , `lopc` , `user_agent`, `send_sysmsg` ) 
		VALUES (1, \"".$admin_username."\", \"".$admin_mail."\", \"".$admin_password."\", \"1\", \"x\", \"0.0.0\", 1000, \"1116536890\", \"1116542845\", 1000, \"\", \"\", \"\", \"\", \"no\", \"yes\", \"no\", \"no\", \"cluster points ranking\", \"no\", 0, \"1116526264\", 3595, \"\", \"\", 2, \"20.05.\", 1116542803, \"12P39676S7706675\", \"84.157.59.159\", \"\", \"crystal\", \"\", \"\", 0, \"yes\", \"\", \"\", \"no\", \"Wer das liest ist doof :P\", \"\", 0, 0, 0, \"\", \"yes\", 0, \"\", \"yes\");";  
		$db_result=mysql_query($sql) OR die('Fehler users asf: '.mysql_error());  
	} 

	/**
	 * Fügt die Datei für das füllen der Subnets ein nach belieben.
	 * @param string $fill_password Das Passwort zum ausführen der fill.php
	 **/

	if ($fill_subnets) {
		file_put('data/install.log', date("d.m.Y H:i", time())." Fülle Subnets...\n", 'a+');
		if (file_exists('fill.php')) { include('fill.php'); }
		else { file_put('data/install.log', date("d.m.Y H:i", time())."Konnte Subnets nicht befüllen. fill.php wurde nicht gefunden.\n", 'a+'); }
	}
	
	/**
	 * Fügt die Datei für das erstellen der Anti-Bot Bilder ein.
	 **/

	if (file_exists('new_images.php')) { file_put('data/install.log', date("d.m.Y H:i", time())." Erstelle Anti-Bot Bilder...\n", 'a+'); include('new_images.php'); }
	else { file_put('data/install.log', date("d.m.Y H:i", time())." Konnte keine Anti-Bot Bilder anlegen. new_images.php wurde nicht gefunden.\n", 'a+'); }
		
	/**
	 * Schreibt die Timestamps für newround und stopround
	 * in die jeweiligen Dateien
	 **/
	
	file_put('data/install.log', date("d.m.Y H:i", time())." Schreibe newround Timestamp...\n", 'a+');
	file_put('data/newround.txt', $new_round, 'w+');
	file_put('data/roundend.txt', $new_round+$round_duration, 'w+');

	/**
	 * Löscht die work.txt
	 **/
	
	file_put('data/install.log', date("d.m.Y H:i", time())." Entsperre den Server...\n", 'a+');
	unlink('data/work.txt');
	
} else { echo 'No Newround detected.'; }



?>