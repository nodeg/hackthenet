<?php

$starttime = microtime();

if ( !defined('IN_HTN') )
{
  die('Hacking attempt');
}
// Sid holen
if(isset($_GET['sid'])) 
{
  $sid = $_GET['sid'];
}
else 
{
  $sid = $_POST['sid'];
}

include 'gres.php';

$dbc = new dbc();
//$dbc->
$ingame = new ingame();
//$ingame->
$get = new get();
//$get->
$layout = new layout();
//$layout->
$gres = new gres();
//$gres->

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s',time()-300).' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Cache-Control: post-check=0, pre-check=0', false);


if(preg_match("/^[a-z0-9]+$/i",$sid) == false || $sid == '') die('FATAL ERROR: INVALID SESSION-ID!');

$server = (int)$sid{0};

$dbc->db_select($dbc->dbname($server));
$DATADIR = 'data/_server'.$server;
$no_ranking_users = $no_ranking_users['server' . $server];
$no_ranking_cluster = $no_ranking_cluster['server' . $server];

$usr = $dbc->db_query('SELECT * FROM users WHERE sid=\''.mysql_escape_string($sid).'\' LIMIT 1');
if( mysql_num_rows($usr) != 1 )
{
  $ingame->badsession('Session-ID unbekannt!');
}
$usr = mysql_fetch_assoc($usr);
$pcid = $usr['sid_pc'];
$usrid = $usr['id'];

$ip = $get->Get_IP();
$ip = ($ip['proxy'] == '' ? $ip['ip'] : $ip['ip'] . ' over ' . $ip['proxy']);

if($usr['sid_ip'] != $ip && $usr['sid_ip'] != 'noip') 
{
  /* falsche IP-Adresse */
  $ingame->badsession('Deine IP ist nicht dieser Session-ID zugeordnet!<br />Aktiviere die Option "IP-Überprüfung deaktivieren" beim Login.');
}

if( $usr['locked'] == 'yes' && ($usr['locked_till'] > time() || $usr['locked_till'] == 0) )
{
  $ingame->badsession('Neu einloggen, du böser Vogel!');
}

if($usr['login_time'] < time() - 5 * 3600 || $usr['sid_lastcall'] < time() - 3600)
{
  $dbc->db_query('UPDATE `users` SET `sid`=\'\' WHERE `id`=' . $usr['id'] . ' LIMIT 1');
  $ingame->badsession('Maximale Session-Dauer oder Inaktivitäts-Zeit überschritten!');
}

if($usr['last_verified'] + 3600 < time() && $enable_visual_confirmation)
{
  $ix = mt_rand(1, 1000);
  $dbc->db_query('UPDATE `users` SET `verifyimg`=' . $ix . ' WHERE `id`=' . $usr['id'] . ' LIMIT 1');
  
  $layout->createlayout_top('Session bestätigen!');
  echo '<div class="content"><h2>Session bestätigen</h2>
  <div id="confirm-session">
  <h3>Bitte die Session bestätigen</h3>
  <p><strong>Um das Spiel vor Bots zu schützen, muss jeder Spieler einmal pro Stunde seine Anwesenheit/Menschlichkeit
  bestätigen!</strong><br />Tippen Sie die Zahlen/Buchstaben von links einfach rechts nochmal ein und klicken Sie dann auf OK.</p>
  <form action="verify.php?a=submit&amp;sid='.$sid.'" method="post">
  <table>
  <tr><th><img src="verify.php?a=image&amp;sid='.$sid.'&amp;rnd='.random_string(3).'" width="100" height="50" /></th>
  <td>Einmal abtippen!<br />
  <input type="text" maxlength="3" size="10" name="chars" /></td></tr>
  <tr><th colspan="2" style="text-align:right;"><input type="submit" value="  OK  " /></th></tr>
  </table>
  </form>
  </div></div>';
  $layout->createlayout_bottom();
  exit;
}

if($usr['sid_lastcall'] < time() - 60)
{
  $dbc->db_query('UPDATE `users` SET `sid_lastcall`=' . time() . ' WHERE `id`=' . $usr['id'] . ' LIMIT 1');
}

if($FILE_REQUIRES_PC == true) 
{
  $pc=@mysql_fetch_assoc($dbc->db_query('SELECT * FROM pcs WHERE id=\''.$pcid.'\' LIMIT 1'));
  if($pc['owner'] != $usrid) 
  {
    die('Das ist nicht dein PC!');
  }
}
if($usr['stat'] > 100 && $gres->is_noranKINGuser($usrid) == false) 
{
  $usr['stat'] = 0;
}
$STYLESHEET=$usr['stylesheet'];

if($usr['liu'] > $usr['lic']) 
{
  $unread = (int)@mysql_num_rows($dbc->db_query('SELECT mail FROM mails WHERE user=\''.$usrid.'\' AND box=\'in\' AND xread=\'no\';'));
  $unread += (int)@mysql_num_rows($dbc->db_query('SELECT msg FROM sysmsgs WHERE user=\''.$usrid.'\' AND xread=\'no\';'));
  $usr['newmail'] = $unread;
  $dbc->db_query('UPDATE users SET newmail=\''.$unread.'\' WHERE id=\''.$usrid.'\';');
  $dbc->db_query('UPDATE users SET lic=\''.time().'\' WHERE id=\''.$usrid.'\';');
}

// Der gefährliche Wurm wird von hier aus gestartet!
$modulo = time() % 60;
if(file_exists('data/worm.txt') === true && ($modulo == 0 || $modulo == 30)) 
{
  include 'worm.php';
}

define('MAX_CLUSTER_MEMBERS',$name['max_members'],false); # Maximale Anzahl von Mitgliedern eines Clusters

if($usr['bigacc']!='yes')
{
  define('UPGRADE_QUEUE_LENGTH', 3, false);
}
else
{
  define('UPGRADE_QUEUE_LENGTH', 5, false);
}

define('CS_ADMIN',1000,false);
define('CS_COADMIN',900,false);
define('CS_WAECHTER',20,false);
define('CS_JACKASS',10,false);
define('CS_WARLORD',90,false);
define('CS_KONVENTIONIST',80,false);
define('CS_SUPPORTER',70,false);
define('CS_MITGLIEDERMINISTER',50,false);
define('CS_MEMBER',0,false);
define('CS_EXMEMBER',-1,false);

$items = array('cpu','ram','mm','bb','lan','fw','mk','av','sdk','ips','ids','trojan','rh');

?>