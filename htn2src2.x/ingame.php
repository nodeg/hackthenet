<?php

$starttime = microtime();

if ( !defined('IN_HTN') )
{
  die('Hacking attempt');
}

include 'gres.php';
include 'layout.php';

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s',time()-300).' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Cache-Control: post-check=0, pre-check=0', false);

if(isset($_GET['sid'])) 
{
  $sid = $_GET['sid'];
}
else 
{
  $sid = $_POST['sid'];
}

if(preg_match("/^[a-z0-9]+$/i",$sid) == false || $sid == '') die('FATAL ERROR: INVALID SESSION-ID!');

function badsession($s) 
{
  global $sid;
  $sid = '';
  simple_message('Sitzung ung&uuml;ltig!<br />Bitte auf der <a href="./">Startseite</a> neu einloggen!<br /><br /><span style="font-size:9pt;">Grund: '.$s.'</span>');
  exit;
}

$server = (int)$sid{0};

db_select(dbname($server));
$DATADIR = 'data/_server'.$server;
$no_ranking_users = $no_ranking_users['server' . $server];
$no_ranking_cluster = $no_ranking_cluster['server' . $server];

$usr = db_query('SELECT * FROM users WHERE sid=\''.mysql_escape_string($sid).'\' LIMIT 1');
if( mysql_num_rows($usr) != 1 )
{
  badsession('Session-ID unbekannt!');
}
$usr = mysql_fetch_assoc($usr);
$pcid = $usr['sid_pc'];
$usrid = $usr['id'];

$ip = GetIP();
$ip = ($ip['proxy'] == '' ? $ip['ip'] : $ip['ip'] . ' over ' . $ip['proxy']);

if($usr['sid_ip'] != $ip && $usr['sid_ip'] != 'noip') 
{
  /* falsche IP-Adresse */
  badsession('Deine IP ist nicht dieser Session-ID zugeordnet!<br />Aktiviere die Option "IP-Überprüfung deaktivieren" beim Login.');
}

if( $usr['locked'] == 'yes' && ($usr['locked_till'] > time() || $usr['locked_till'] == 0) )
{
  badsession('Neu einloggen, du böser Vogel!');
}

if($usr['login_time'] < time() - 5 * 3600 || $usr['sid_lastcall'] < time() - 3600)
{
  db_query('UPDATE `users` SET `sid`=\'\' WHERE `id`=' . $usr['id'] . ' LIMIT 1');
  badsession('Maximale Session-Dauer oder Inaktivitäts-Zeit überschritten!');
}

if($usr['last_verified'] + 3600 < time() && $enable_visual_confirmation)
{
  $ix = mt_rand(1, 1000);
  db_query('UPDATE `users` SET `verifyimg`=' . $ix . ' WHERE `id`=' . $usr['id'] . ' LIMIT 1');
  
  createlayout_top('Session bestätigen!');
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
  createlayout_bottom();
  exit;
}

if($usr['sid_lastcall'] < time() - 60)
{
  db_query('UPDATE `users` SET `sid_lastcall`=' . time() . ' WHERE `id`=' . $usr['id'] . ' LIMIT 1');
}

if($FILE_REQUIRES_PC == true) 
{
  $pc=@mysql_fetch_assoc(db_query('SELECT * FROM pcs WHERE id=\''.$pcid.'\' LIMIT 1'));
  if($pc['owner'] != $usrid) 
  {
    die('Das ist nicht dein PC!');
  }
}
if($usr['stat'] > 100 && is_noranKINGuser($usrid) == false) 
{
  $usr['stat'] = 0;
}
$STYLESHEET=$usr['stylesheet'];

if($usr['liu'] > $usr['lic']) 
{
  $unread = (int)@mysql_num_rows(db_query('SELECT mail FROM mails WHERE user=\''.$usrid.'\' AND box=\'in\' AND xread=\'no\';'));
  $unread += (int)@mysql_num_rows(db_query('SELECT msg FROM sysmsgs WHERE user=\''.$usrid.'\' AND xread=\'no\';'));
  $usr['newmail'] = $unread;
  db_query('UPDATE users SET newmail=\''.$unread.'\' WHERE id=\''.$usrid.'\';');
  db_query('UPDATE users SET lic=\''.time().'\' WHERE id=\''.$usrid.'\';');
}

// Der gefährliche Wurm wird von hier aus gestartet!
$modulo = time() % 60;
if(file_exists('data/worm.txt') === true && ($modulo == 0 || $modulo == 30)) 
{
  include 'worm.php';
}

define('MAX_CLUSTER_MEMBERS',32,false); # Maximale Anzahl von Mitgliedern eines Clusters

if($usr['bigacc']!='yes')
{
  define('UPGRADE_QUEUE_LENGTH', 7, false);
}
else
{
  define('UPGRADE_QUEUE_LENGTH', 7, false);
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

function SetUserVal($name,$val,$usr=-1) 
{
  global $usrid;
  if($usr==-1) $usr=$usrid;
  db_query('UPDATE users SET '.$name.'=\''.mysql_escape_string($val).'\' WHERE id=' . $usr . ' LIMIT 1');
}

function SaveUserData() 
{
  //------------------------- Save User Data -------------------------------
  global $usrid, $usr;
  SaveUser($usrid, $usr);
}

function SaveUser($usrid,$usr) 
{
  //------------------------- Save User -------------------------------
  $s='';
  while(list($bez,$val)=each($usr)) 
  {
    $s.=mysql_escape_string($bez).'=\''.mysql_escape_string($val).'\',';
  }
  $s=trim($s,',');
  if($s != '') db_query('UPDATE users SET '.$s.' WHERE id=\''.$usrid.'\'');
}

function SaveCluster($id,$dat) 
{
  //------------------------- Save User -------------------------------
  $s='';
  while(list($bez,$val)=each($dat)) 
  {
    $s.=mysql_escape_string($bez).'=\''.mysql_escape_string($val).'\',';
  }
  $s=trim($s,',');
  if($s != '') db_query('UPDATE clusters SET '.$s.' WHERE id=\''.$id.'\'');
}

function SavePC($pcid,$pc) 
{
  //------------------------- Save PC -------------------------------
  $s='';
  while(list($bez, $val)=each($pc)) 
  {
    $s.=mysql_escape_string($bez).'=\''.mysql_escape_string($val).'\',';
  }
  $s=trim($s,',');
  if(strlen($s) > 4) db_query('UPDATE pcs SET '.$s.' WHERE id=\''.$pcid.'\'');
}

function cscodetostring($code) 
{
  //----------------- Cluster Stat Code to String ------------------
  switch($code) 
  {
    case CS_ADMIN: $s='Admin'; break;
    case CS_COADMIN: $s='LiteAdmin'; break;
    case CS_WAECHTER: $s='W&auml;chter'; break;
    case CS_JACKASS: $s='JackAss'; break;
    case CS_WARLORD: $s='Warlord'; break;
    case CS_KONVENTIONIST: $s='Konventionist'; break;
    case CS_SUPPORTER: $s='Entwicklungsminister'; break;
    case CS_MEMBER: $s='Mitglied'; break;
    case CS_EXMEMBER: $s='Ex-Mitglied'; break;
    case CS_MITGLIEDERMINISTER: $s='Mitgliederminister';
  }
  return $s;
}



function getiteminfo($key,$stage) 
{
  //--------------------- Get Item Info --------------------------
  global $STYLESHEET,  $DATADIR, $pc;
  global $cpu_levels, $ram_levels, $server;
  $d; $c;
  if($stage<1) $stage=1;
  $stage=(float)$stage;
  switch($key) 
  {
    case 'cpu':
    switch($stage) 
    {
      case 0: $d=20; $c=60; break;
      case 1: $d=25; $c=80; break;
      case 2: $d=30; $c=90; break;
      case 3: $d=35; $c=110; break;
      case 4: $d=40; $c=120; break;
      case 5: $d=45; $c=140; break;
      case 6: $d=50; $c=150; break;
      case 7: $d=55; $c=255; break;
      case 8: $d=55; $c=300; break;
      case 9: $d=60; $c=512; break;
      case 10: $d=90; $c=768; break;
      case 11: $d=120; $c=1150; break;
      case 12: $d=150; $c=1730; break;
      case 13: $d=180; $c=2590; break;
      case 14: $d=210; $c=3890; break;
      case 15: $d=240; $c=5800; break;
      case 16: $d=300; $c=8500; break;
      case 17: $d=360; $c=12000; break;
      case 18: $d=420; $c=18000; break;
      case 19: $d=460; $c=25000; break;
      case 20: $d=580; $c=50000; break;
    }
    break;
    case 'ram':
    switch($stage) 
    {
      case 0: $d=30; $c=200; break;
      case 1: $d=45; $c=300; break;
      case 2: $d=60; $c=500; break;
      case 3: $d=70; $c=800; break;
      case 4: $d=90; $c=1000; break;
      case 5: $d=120; $c=1200; break;
      case 6: $d=150; $c=3000; break;
      case 7: $d=180; $c=4000; break;
      case 8: $d=210; $c=10000; break;
    }
    break;
    case 'mm':
    $stage+=0.5;
    $c=$stage*51;
    $d=$stage*10;
    break;
    case 'bb':
    $stage+=0.5;
    $c=$stage*45;
    $d=$stage*11;
    break;
    case 'lan':
    $stage+=0.5;
    $c=$stage*150;
    $d=$stage*25;
    break;
    case 'sdk':
    $stage+=0.5;
    $c=$stage*100;
    $d=$stage*15;
    break;
    case 'fw':
    $stage+=0.5;
    $c=$stage*49;
    $d=$stage*5;
    break;
    case 'av':
    $stage+=0.15;
    $c=$stage*50;
    $d=$stage*6;
    break;
    case 'mk':
    $stage+=0.5;
    $c=$stage*100;
    $d=$stage*16;
    break;
    case 'ips':
    $stage+=0.5;
    $c=$stage*33;
    $d=$stage*8;
    break;
    case 'ids':
    $stage+=0.5;
    $c=$stage*44;
    $d=$stage*7;
    break;
    case 'rh':
    $stage+=0.5;
    $c=$stage*400;
    $d=$stage*10;
    break;
    case 'trojan':
    $stage+=0.5;
    $c=$stage*39;
    $d=$stage*8;
    break;
  }
  
  
  $r['c'] = ceil($c); # Kosten
  $r['d'] = floor($d/geschwindigkeit); # Dauer in Minuten
  
  if($key != 'cpu' && $key != 'ram') 
  {
    $r['c'] *= 4.5;
    $df = duration_faktor($pc['cpu'],$pc['ram']);
    $r['d'] *= $df * 2;
    
    $r['c'] = floor($r['c']);
    $r['d'] = ceil($r['d']);
    
  }
  
  return $r;
  
}

function duration_faktor($cpu,$ram) 
{
  global $cpu_levels,$ram_levels;
  $r=(1 / (($cpu_levels[21]-$cpu_levels[0])/(3-1))) * ($cpu_levels[21] - $cpu_levels[$cpu]) + 1;
  $r=$r*2;
  $tmp=(1 / (($ram_levels[9]-$ram_levels[0])/(3-1))) * ($ram_levels[9] - $ram_levels[$ram]) + 1;
  $r+=$tmp;
  return round($r/2.8,5);
}

function IDToName($id) 
{
  //------------------------- ID to Name -------------------------------
  $s='';
  switch(strtolower($id)) 
  {
    case 'cpu': $s='Prozessor'; break;
    case 'ram': $s='Arbeitsspeicher'; break;
    case 'mm': $s='MoneyMarket'; break;
    case 'fw': $s='Firewall'; break;
    case 'lan': $s='Internet-Anbindung'; break;
    case 'mk': $s='Malware Kit'; break;
    case 'av': $s='Anti-Virus-Programm'; break;
    case 'sdk': $s='SDK (Software Development Kit)'; break;
    case 'ips': $s='IP-Spoofing'; break;
    case 'ids': $s='IDS (Intrusion Detection System)'; break;
    case 'bb': $s='BucksBunker'; break;
    case 'rh': $s='Remote Hijack'; break;
    case 'trojan': $s='Trojaner'; break;
    case 'da': $s='Distributed Attack'; break;
  }
  return $s;
}

function AddSysMsg($user, $msg, $save=true) 
{
  //----- ADD SYSTEM MESSAGE -----
  global $STYLESHEET,  $DATADIR, $usrid, $usr;
  
  $udat=getuser($user);
  if($udat!==false) 
  {
    $ts=time();
    db_query('INSERT INTO sysmsgs VALUES(\'0\',\''.mysql_escape_string($user).'\',\''.mysql_escape_string($ts).'\',\''.mysql_escape_string($msg).'\',\'no\');');
    if($save==true) 
    {
      if($user==$usrid) $u=$usr; else $u=$udat;
      $u[newmail]+=1;
      setuserval('newmail',$u[newmail],$user);
      if($user==$usrid) $usr=$u;
    }
    $r=db_query('SELECT * FROM sysmsgs WHERE user='.mysql_escape_string($user).' ORDER BY time ASC');
    $max=15;
    $cnt=mysql_num_rows($r);
    if($cnt>$max) 
    {
      $cnt=$cnt-$max;
      for($i=0;$i<$cnt;$i++) 
      {
        $id=mysql_result($r,$i,'msg');
        db_query('DELETE FROM sysmsgs WHERE msg='.mysql_escape_string($id));
      }
    }
  }
}

function isattackallowed(&$ret,&$ret2) 
{
  //---------------- IS ATTACK ALLOWED ----------------
  global $STYLESHEET,  $DATADIR, $usr,$pc,$usrid,$localhost;
  if($localhost || is_noranKINGuser($usrid)) return true;
  define('TO_1',2*60,false);
  $x=floor((5/3) * (10 - $pc['lan']) + 5)*60;
  define('TO_2',$x,false);
  $a=$usr['la']+TO_1;
  $b=$pc['la']+TO_2;
  if($a > $b) 
  {
    $ret=$a; $ret2=$pc['la']; 
  }
  else 
  {
    $ret=$b; $ret2=$usr['la']; 
  }
  if( (($a <= time()) && ($b <= time())) ) return true; else return false;
}

function write_pc_list($usrid) 
{
  //---------------- WRITE PC LIST ----------------
  $s='';
  $r=db_query('SELECT id FROM pcs WHERE owner=\''.$usrid.'\';');
  while($x=mysql_fetch_assoc($r)):
  $s.=$x['id'].',';
  endwhile;
  $s=trim($s,',');
  db_query('UPDATE users SET pcs=\''.mysql_escape_string($s).'\' WHERE id=\''.$usrid.'\';');
}


function tIsAvail($key,$_pc=-1) 
{
  //---------------- TROJANER IS AVAIL ----------------
  global $STYLESHEET,  $DATADIR, $pc;
  if($_pc==-1) $_pc=$pc;
  $b=false;
  if($_pc['trojan']>=1 && $key=='defacement') $b=true;
  elseif($_pc['trojan']>=2.5 && $key=='transfer') $b=true;
  elseif($_pc['trojan']>=5 && $key=='deactivate') $b=true;
  return $b;
}

function getmaxmailsforuser($box,$bigacc='no') 
{
  //---------------- GET MAX MAILS FOR USER ----------------
  switch($box) 
  {
    case 'in': $max=20; break;
    case 'arc': $max=25; break;
    case 'out': $max=10; break;
    case 'sys': $max=15; break;
  }
  if($bigacc=='yes') $max=$max*10;
  return $max;
}

function getmaxmails($box) 
{
  //---------------- GET MAX MAILS ----------------
  global $usr;
  return getmaxmailsforuser($box,$usr['bigacc']);
}

function is_pc_attackable($pcdat) //---------------- IS PC ATTACKABLE ? ----------------
{
  $xdefence = $pcdat['fw'] + $pcdat['av'] + $pcdat['ids']/2;
  $rscan = (int)(isavailh('scan',$pcdat));
  # ^^ 0 <= $xdefence <= 25 ^^
  #echo '<br />xdefence='.$xdefence.' min='.MIN_ATTACK_XDEFENCE.' scan='.(int)(isavailh('scan',$pcdat));
  if( count(explode(',',$owner['pcs'])) < 2 && (
  ($xdefence<=MIN_ATTACK_XDEFENCE && isavailh('scan',$pcdat)==false)
  ))
  {
    #echo '<br>p1='.(int)($xdefence<MIN_ATTACK_XDEFENCE XOR isavailh('scan',$pcdat));
    
    return false;
    
  }
  return true;
}

?>