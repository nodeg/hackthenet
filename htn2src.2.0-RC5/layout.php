<?php

if ( !defined('IN_HTN') )
{
  die('Hacking attempt');
}

$javascript='';
$bodytag='';

include_once('config.php');

function menu_entry($url,$text,$help='',$em='',$emclass='',$selftags=false,$inatag='')
{
  // refaktorisierte Funktion *muaahaahhaahaaa*
  echo "\n".'<li><a href="'.$url.'"'.$inatag.'><strong>'.$text.'</strong>';
  if($em!='')
  {
    if(!$selftags) echo "\n".'<br /><em class="'.$emclass.'">'.$em.'</em>'; else echo $em;
  }
  echo '</a>';
  if($help!='') echo "\n".'<div class="help">'.$help.'</div>'."\n";
  echo '</li>'."\n";
}

function basicheader($title) {
global $usr,$javascript,$STYLESHEET,$bodytag,$stylesheets;
global $STYLESHEET_BASEDIR,$standard_stylesheet;
$stylesheet=$STYLESHEET;
if($stylesheets[$stylesheet]['id']=='' || $stylesheet=='') $stylesheet=$standard_stylesheet;

$ts=time()+1;
echo '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n";
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<title>'.$title.'</title>
<link rel="stylesheet" type="text/css" href="'.$STYLESHEET_BASEDIR.$stylesheet.'/style.css" />
<script type="text/javascript">var stm=new Date(); stm.setTime('.$ts.'000); var sltm=new Date();</script>
<script type="text/javascript" src="global.js"></script>
<link rel="SHORTCUT ICON" href="favicon.ico" />
'.$javascript.'
</head>
';
}

function basicfooter() {
echo '</body>
</html>
';
}

function createlayout_top($title='HackTheNet', $nomenu=false, $pads=true)
{
global $usr, $javascript, $STYLESHEET, $bodytag, $localhost, $FILE_REQUIRES_PC, $pc, $pcid;
if ($usr[sid]!='') $sid='&amp;sid='.$usr['sid'];
$stylesheet=$STYLESHEET;
if(@is_dir('styles/'.$stylesheet)==false || $stylesheet=='') $stylesheet='crystal';

basicheader($title);

if($sid!='' && $usr['ads']!='no' && !$localhost && $pads==true) {
  // werbung hier ausgeben
}

echo '
<body'.$bodytag.'>
<div class="header">
<h1>HTN v2 PublicSource</h1>
'.$ads;
if ($nomenu==false) {
echo '<ul class="navigation">
';

if ($sid!='')
{
    // INGAME ITEMS

    menu_entry('game.htn?m=start'.$sid, '&Uuml;bersicht', '&Uuml;bersicht &uuml;ber alles Wichtige auf einen Blick.');

    if ($usr[newmail]>0)
    {
      $hw=($usr[newmail]==1 ? '' : 's');
      $hw='Du hast '.$usr['newmail'].' neue Message'.$hw;
    }
    menu_entry('mail.htn?m=start'.$sid, 'Messages', 'Hier kannst du Nachrichten verwalten und neue verfassen.', $hw, 'new-messages');

    $numberofpcs=count(explode(',', $usr['pcs']));
    $url=( $numberofpcs>1 ? 'game.htn?m=pcs'.$sid : 'game.htn?a=selpc&amp;pcid='.$usr['pcs'].$sid );
    $help=( $numberofpcs>1 ? 'Hier kommst du zu deinen PCs.' : 'Hier kommst du direkt zu deinem PC.' );
    if($FILE_REQUIRES_PC && $pc['id']==$pcid)
    {
      $pc['name']=safeentities($pc['name']);
      menu_entry($url, 'Computer', $help, '<br />10.47.'.$pc['ip'].' (<em>'.$pc['name'].'</em>)','',true);
    } else
      menu_entry($url, 'Computer', $help);

    $help= ( (int)$usr['cluster']>0 ? 'Hier kannst du dich &uuml;ber den aktuellen Stand deines Clusters informieren' : 'Hier kannst du einen neuen Cluster gr&uuml;nden oder einem existierenden beitreten.' );
    menu_entry('cluster.htn?a=start'.$sid, 'Cluster', $help);

    menu_entry('game.htn?m=subnet'.$sid, 'Subnet', 'Hier kannst du die Computer in deinem oder einem anderen Subnet einsehen.');
    menu_entry('user.htn?a=config'.$sid, 'Optionen');
    menu_entry('ranking.htn?m=ranking'.$sid, 'Rangliste');
    menu_entry('game.htn?m=kb'.$sid, 'Hilfe', 'Hier findest du die Hilfe zum Spiel.');

}
else
{
    // PUBLIC ITEMS
    menu_entry('pub.htn', 'Startseite', 'HackTheNet-Startseite (Log In)');
    menu_entry('pub.htn?a=register', 'Registrieren', 'Einen HackTheNet-Account anlegen');
    menu_entry('pub.htn?d=newpwd', 'Neues Passwort', 'Ein neues Passwort f&uuml;r deinen Account anfordern');
    menu_entry('pub.htn?d=credits', 'HTN-Team');
    menu_entry('pub.htn?d=impressum', 'Impressum');
    menu_entry('pub.htn?d=rules', 'Spielregeln', 'Wer die nicht beachtet, fliegt!');
    menu_entry('pub.htn?d=faq', 'FAQ', 'H&auml;ufig gestellte Fragen zu HTN');
    menu_entry('pub.htn?d=stats', 'Statistik', 'Statistische Daten &uuml;ber das Spiel');
    
    
}
 // COMMON ITEMS
#menu_entry('http://forum.hackthenet.org/', 'Forum');

if ($sid!='')
{
  // INGAME ITEMS 2
  menu_entry('login.htn?a=logout'.$sid, 'Log Out', 'Hier kannst du dich abmelden.', '<br />Angemeldet als: <em>'.$usr['name'].'</em>', '', true);
}
else
{
  // PUBLIC ITEMS 2

}
}

echo '</ul>
</div>

<!-- NAVI ENDE -->
';
}

function createlayout_bottom()
{
global $starttime;
if($starttime>0)
{
  $time=(float)calc_time($starttime,0,10);
  if($t<1) $time=number_format($time*1000,1,'.',',').' ms';
  else $time=number_format($time,2,',','.').' s';
}
  $time2=nicetime3(0,'');
  echo '
<div id="generation-time">'.$time.'</div>
<div id="server-time">'.$time2.'</div>
';
  
  basicfooter();
}

?>
