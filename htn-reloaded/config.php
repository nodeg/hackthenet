<?php
 
 
//******************// BEGINN DER HAUPT-KONFIGURATION //******************//
 
# DATENBANKEN:
 
$enable_multi_server_support = false; // Soll es mehrere Spielwelten geben?
 
if( !$enable_multi_server_support )
{
  // Hier DB-Name eintragen, falls es nur einen Server (=nur eine Datenbank gibt)
  $database_name = 'htn2';
}
 
$database_prefix='';
$database_suffix = '';
 
$db_use_this_values = true; // Folgende Werte zum connecten auf den MySQL-Server benutzen?
$db_host='localhost';
$db_username='root';
$db_password='';
 
// Zugangs Konstanen
define('dbname', $database_name);
define('dbServ', $db_host);
define('dbuid', $db_username);
define('dbPw', $db_password);
define('prefix', $database_prefix);
 
# VERSCHIEDENE EINSTELLUNGEN:
$admin_email='ich@seberoth-online.de'; // Email-Adresse des Administrators!
$account_activation_per_email = false; // Account-Freischaltung per Email ?
$enable_visual_confirmation = false; // Anti-Bot-Bildchen?
// Um Anti-Bot-Bildchen generieren zu lassen, /new_images.php?code=sdap432i aufrufen.
 
$GAME_MODE = '2.1';
// hier 2.0 oder 2.1 eintragen:
// 2.1: max. 25 PCs, undendlich weit upgradebare Items
// 2.0: unendliche viele PCs, Upgrades begrenzt -> pro PC max. 1024 Punkte
 
# KEINE MITSPIELER:
$no_ranking_users['server1'] = '1'; // durch Komma getrennte User-IDs von nicht in der Rangliste zu fuhrenden Spielern
 
$no_ranking_cluster['server1'] = 1; // Nur eine Angabe möglich für ID des Admin-Clusters
 
//******************// ENDE DER HAUPT-KONFIGURATION //******************//
 
include_once('include/startup.php');
// Konfiguration Einstellungen auslesen
$dbpdo = new Dbpdo();
$db = $dbpdo->get_db();
$sql='SELECT name,value From setting';
$stmt=$db->prepare($sql);
$stmt->execute();
 
while (($row = $stmt->fetch(PDO::FETCH_OBJ)))
{
 $name[$row->name]=$row->value;
}
 
# KONSTANTEN
define('MAX_PCS_PER_USER', $name['max_pc']); # Maximale Anzahl von PCs pro Benutzer (nur im 2.1er-GAME_MODE)
 
define('UPDATE_INTERVAL', $name['update_int']); # Interval für Punkte-Updates in Sekunden
define('MIN_ATTACK_XDEFENCE', $name['min_attack_def']);
define('MIN_INACTIVE_TIME', $name['min_incative']); # Inaktive Zeit vor möglichem Angriff
define('REMOTE_HIJACK_DELAY', $name['remote_hijack']); # Wartezeit zwischen zwei Remote Hijacks
 
// int mktime ( [int Stunde [, int Minute [, int Sekunde [, int Monat [, int Tag [, int Jahr [, int is_dst]]]]]]])
 
$t_limit_server = 1; // Server-ID eines Servers der erst sp&auml;ter startet
$ts_server_start = mktime(10, 0, 0, 12, 18, 2004); // Wann startet er?
$transfer_ts = mktime(10, 0, 0, 1, 1, 2005); // Ab wann kann man auf ihm Überweisen? 
 
# DIVERSES
$STYLESHEET_BASEDIR = 'styles/';
define('MAX_USERS_PER_SERVER', $name['server_limit']); # Maximale Anzahl von Spielern pro Server
define('SID_ONLINE_TIMEOUT', $name['online_time']);
 
# STYLESHEETS:
$standard_stylesheet = $name['default_style'];
$stylesheets['standard'] = array('id'=>'standard', 'name'=>'HackTheNet Standard', 'author'=>'HackTheNet-Team', 'bigacc'=>'no');
$stylesheets['crystal'] = array('id'=>'crystal', 'name'=>'HackTheNet Crystal', 'author'=>'HackTheNet-Team', 'bigacc'=>'no');
$stylesheets['konsole'] = array('id'=>'konsole', 'name'=>'Konsole', 'author'=>'Volkmar', 'bigacc'=>'yes');
$stylesheets['anti-ie'] = array('id'=>'anti-ie', 'name'=>'Anti-IE', 'author'=>'Volkmar', 'bigacc'=>'yes');
$stylesheets['modern'] = array('id'=>'modern', 'name'=>'Modern', 'author'=>'Volkmar und xXxUnKnownxXx', 'bigacc'=>'no');
$stylesheets['modern-ie'] = array('id'=>'modern-ie', 'name'=>'Modern f&üuml;r Internet Explorer', 'author'=>'Volkmar und xXxUnKnownxXx', 'bigacc'=>'yes'); 
 
$htn_installed = $name['re_install'];
?>