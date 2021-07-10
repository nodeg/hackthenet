<?php

define('IN_HTN',1);
include('gres.php');

$ref=$_SERVER['HTTP_REFERER'];
$refd=parse_url($ref);
#echo $refd['host'].' '.$_SERVER['HTTP_HOST'];
if($ref!='' && $refd['host']!=$_SERVER['HTTP_HOST'] && $refd['host']!='ssl-id1.de' && count($_GET)<2) die('Rejected! Please don\'t use your own log in pages.');

$action=$_REQUEST['page'];
if($action=='') $action=$_REQUEST['mode'];
if($action=='') $action=$_REQUEST['action'];
if($action=='') $action=$_REQUEST['a'];
if($action=='') $action=$_REQUEST['m'];

if($action=='login')
{
 
 if((int)@file_get('data/calc-time.dat')<=time() || @file_get('data/calc-running.dat')=='yes' && $_POST['bypass']!=1)
 {
  $pwd = htmlentities($_REQUEST['pwd']);
  $nick = htmlentities($_REQUEST['nick']);
  $server = (int)$_REQUEST['server'];
  $save = $_POST['save'];
  $key = $_POST['key'];
  $chars = $_POST['chars'];
  $ipcheck = $_REQUEST['noipcheck'];
  $stat=@file_get('data/calc-stat.dat');
  echo '<html>
  <head>
  <title>Sorry</title></head><body style="font-family:arial;text-align:center;vertical-align:middle;">
  <h1>Sorry</h1>
  <b>Der Server ist im Moment mit der Kalkulation der Punktest&auml;nde besch&auml;ftigt!</b>
  <br /><br />Aktueller Status: <tt>'.$stat.'</tt>
  <br /><br />
  <form action="login.php?a=login" method="post">
  <input type="hidden" value="1" name="bypass" />
  <input type="hidden" value="'.$nick.'" name="nick" />
  <input type="hidden" value="'.$pwd.'" name="pwd" />
  <input type="hidden" value="'.$server.'" name="server" />
  <input type="hidden" value="'.$save.'" name="save" />
  <input type="hidden" value="'.$key.'" name="key" />
  <input type="hidden" value="'.$chars.'" name="chars" />
  <input type="hidden" value="'.$ipcheck.'" name="noipcheck" />
  <input type="submit" value="Ignorieren" />
  </form>
  </body></html>';
  if(@file_get('data/calc-running.dat')=='') include('calc_points.php');
  exit;
 }
 
 $pwd=trim($_REQUEST['pwd']);
 $postpwd=$pwd;
 $usrname=trim($_REQUEST['nick']);
 $server=(int)$_REQUEST['server'];
 if($server!=1 && $server!=2)
 {
  simple_message('Falsche Server-Angabe!');
  exit;
 }
 
 $cookie=false;
 
 if(substr_count(($_COOKIE['htnLoginData4']),'|')==2 && $_POST['save']=='yes') 
 {
  list($serverdummy, $usrnamedummy, $pwd)=explode('|', $_COOKIE['htnLoginData4']);
  $cookie=true;
 }
 if($_POST['save']=='yes') 
 {
  if($cookie==false)
  {
   setcookie('htnLoginData4', $server.'|'.$usrname.'|'.md5($pwd), time()+10*365*24*60*60);
  }
  else 
  {
   if($usrnamedummy!=$usrname || $postpwd!='[xpwd]') $pwd=md5($postpwd);
   setcookie('htnLoginData4', $server.'|'.$usrname.'|'.$pwd, time()+10*365*24*60*60);
   $cookie=true;
  }
 }
 else
 {
  setcookie('htnLoginData4');
 }
 
 mysql_select_db(dbname($server));
 
 #echo $pwd.'<br>';
 if($cookie!==true)
 {
  $pwd=md5($pwd);
 }
 #echo $pwd;
 
 if(preg_match("/^[a-z0-9]+$/i", $pwd) == false || strlen($pwd) != 32 || preg_match("/(_|%)/", $pwd)) die('FUCK OFF!');
 
 $r=db_query('SELECT * FROM users WHERE name=\''.mysql_escape_string($usrname).'\' LIMIT 1;');
 
 
 if(@mysql_num_rows($r) == 1)
 {
  $usr = mysql_fetch_assoc($r);
  
  if($usr['password'] != $pwd)
  {
    simple_message('Passwort für diesen Account falsch!');
    db_query('INSERT INTO logs SET type=\'badlogin\', usr_id=\'' . $usr['id'] . '\', payload=\'pwd='.mysql_escape_string($_REQUEST['pwd']).', ip='.mysql_escape_string($_SERVER["REMOTE_ADDR"]).'\';'); #ip= added by zakx at Wed Oct 27 16:42:43 GMT-2 2004
    exit;
  }
  
  if($server == $t_limit_server && time() < $ts_server_start && $usr['stat'] != 1000)
  {
   simple_message('Server '.$t_limit_server.' startet erst '.nicetime($ts_server_start).'!');
   exit;
  }
  
  if(count($_GET)<2 || $usr['bigacc']=='yes') 
  {
   
   if($usr['locked']=='yes' && ($usr['locked_till']>time() || $usr['locked_till']==0))
   {
    if($usr['locked_till']==0) $time='auf unbegrenzte Zeit'; else $time=' bis '.nicetime($usr['locked_till']);
    echo '<html><head><title>Sorry</title></head><body style="font-family:arial;text-align:center;vertical-align:middle;">
    <br /><br /><br /><br />
    <h1>Account gesperrt!</h1>
    <b>Dieser Account wurde durch den Spiel-Administrator <em>'.$usr['locked_by'].'</em> '.$time.' gesperrt!</b>
    <br /><br />Der Grund dafür ist: <em>'.$usr['locked_reason'].'</em><br />Bei Fragen, Beschwerden und wenn
    du die genauen Gr&uuml;nde wissen willst, schick eine Email an <a href="mailto:'.$admin_email.'?subject='.urlencode('Sperrung des Accounts '.$usr['name']).'">'.$admin_email.'</a>!
    </body></html>';
    exit;
   }
   $sid=$server.random_string(9);
   $sidfile='data/login/'.$usr['sid'].'.txt';
   if(file_exists($sidfile))
   {
    @unlink($sidfile); 
   }
   elseif($usr['sid']!='')
   {
    $notloggedout='&nlo=1'; 
   }
   $usrid=$usr['id'];
   
   $p = strpos($usr['pcs'],',');
   if($p > 0)
   {
    $pcid = (int)substr($usr['pcs'], 0, $p);
   }
   else
   {
    $pcid = (int)$usr['pcs'];
   }
   #$pcid=mysql_result(db_query('SELECT id FROM pcs WHERE owner=\''.$usrid.'\' LIMIT 1'),'id');
   #write_session_data();
   
   $ip=GetIP();
   $ip2=$ip['ip'];
   $ip=($ip['proxy']=='' ? $ip['ip'] : $ip['ip'].' over '.$ip['proxy']);
   if($_REQUEST['noipcheck']!='yes') 
   {
    $noipcheck=$usr['noipcheck'];
   }
   else 
   {
    $ip='noip';
    $noipcheck='yes';
   }
   @db_query('INSERT INTO logins ( ip, usr_id, time, user_agent ) VALUES ( \''.mysql_escape_string($ip2).'\', \''.$usrid.'\', \''.mysql_escape_string(time()).'\', \''.mysql_escape_string($_SERVER['HTTP_USER_AGENT']).'\' );');
   db_query('UPDATE users SET sid=\''.mysql_escape_string($sid).'\', login_time=\''.time().'\', sid_ip=\''.mysql_escape_string($ip).'\', noipcheck=\''.$noipcheck.'\', last_verified=0, sid_pc=' . $pcid . ', sid_lastcall=' . time() . ' WHERE id=\''.$usrid.'\' LIMIT 1;');
   if(is_noranKINGuser($usrid)===false) 
   {
    $s='data/_server'.$server.'/logins_'.strftime('%x').'.txt';
    file_put($s,((int)@file_get($s))+1);
   }
   header('Location: game.php?m=start&sid='.$sid.$notloggedout);
   
  }
  else 
  {
   simple_message('Die Funktion des Direkt-LogIns &uuml;ber die Adresszeile steht ab sofort nur noch ExtendedAccount-Usern zur verf&uuml;gung.<br />Bitte log dich &uuml;ber die Startseite ein!','info');
  }
  
 }
 else 
 {
  simple_message('Benutzername unbekannt! Stelle sicher dass du deinen Account aktiviert hast!');
 }
 
}
elseif($action=='logout') 
{
 # ------------------------- LOG OUT ------------------------
 $sid = $_REQUEST['sid'];

 $ip = GetIP();
 $ip = ($ip['proxy']=='' ? $ip['ip'] : $ip['ip'].' over '.$ip['proxy']);
 
  $server = (int)$sid{0};
  db_select(dbname($server));
  $usr = getuser($sid, 'sid');
  if($usr !== false)
  {
   if($usr['sid_ip']!=$ip && $usr['sid_ip']!='noip')
   {
    exit;
   }
   db_query('UPDATE users SET sid=\'\', sid_lastcall=0 WHERE id=\'' . $usr['id'] . '\' AND sid=\'' . $sid . '\'');
  }
 if($_GET['redir'] == 'forum') 
 {
  header('Location: http://forum.hackthenet.org/'); 
 }
 else 
 {
  header('Location: pub.php'); 
 }
 
}

?>