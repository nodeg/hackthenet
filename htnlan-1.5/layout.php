<?php


// HTN.LAN by Schnitzel
// Changelog layout.php
// 1. Unnötige Menüs rausgenommen

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
function menu_entry_neu($url,$text,$help='',$em='',$emclass='',$selftags=false,$inatag='')
{
  // refaktorisierte Funktion *muaahaahhaahaaa*
  echo "\n".'<li><a href="'.$url.'"'.$inatag.' target="_blank"><strong>'.$text.'</strong>';
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

#if($sid!='' && $usr['ads']!='no' && !$localhost && $pads==true) {

#include("ad.php");

#}

echo '
<body'.$bodytag.'>';
if ($nomenu==false) {
echo '
<div class="header">
<h1>HTN.LAN V1.5</h1>
'.$ads.'
<ul class="navigation">';

if ($sid!='')
{
    // INGAME ITEMS

    menu_entry('game.php?m=start'.$sid, '&Uuml;bersicht', '&Uuml;bersicht &uuml;ber alles Wichtige auf einen Blick.');

    if ($usr[newmail]>0)
    {
      $hw=($usr[newmail]==1 ? '' : 's');
      $hw='Du hast '.$usr['newmail'].' neue Message'.$hw;
    }
    menu_entry('mail.php?m=start'.$sid, 'Messages', 'Hier kannst du Nachrichten verwalten und neue verfassen.', $hw, 'new-messages');

    $numberofpcs=count(explode(',', $usr['pcs']));
    $url=( $numberofpcs>=1 ? 'game.php?m=pcs'.$sid : 'game.php?a=selpc&amp;pcid='.$usr['pcs'].$sid );
    $help=( $numberofpcs>1 ? 'Hier kommst du zu deinen PCs.' : 'Hier kommst du direkt zu deinem PC.' );
    if($FILE_REQUIRES_PC && $pc['id']==$pcid)
    {
      $pc['name']=safeentities($pc['name']);
      menu_entry($url, 'Computer', $help, '<br />10.47.'.$pc['ip'].' (<em>'.$pc['name'].'</em>)','',true);
    } else
      menu_entry($url, 'Computer', $help);

    $help= ( (int)$usr['cluster']>0 ? 'Hier kannst du dich &uuml;ber den aktuellen Stand deines Clusters informieren' : 'Hier kannst du einen neuen Cluster gr&uuml;nden oder einem existierenden beitreten.' );
    menu_entry('cluster.php?a=start'.$sid, 'Cluster', $help);

    menu_entry('game.php?m=subnet'.$sid, 'Subnet', 'Hier kannst du die Computer in deinem oder einem anderen Subnet einsehen.');
    menu_entry('game.php?m=tools'.$sid, 'Tools', 'Interessante und hilfreiche Tools für HTN.LAN.');
    menu_entry('user.php?a=config'.$sid, 'Optionen');
    menu_entry('ranking.php?m=ranking'.$sid, 'Rangliste');
    menu_entry('game.php?m=bugtracker'.$sid, 'Bugtracker', 'Hier kannst du Bugs, Verbesserungen und Feature Requests melden.');
    #menu_entry('game.php?m=team'.$sid, 'Team', 'Das HTN.Lan Team.');
    menu_entry('game.php?m=hof'.$sid, 'Hall of Fame', 'Die HTN.Lan Hall of Fame. <br> Hier findet ihr alle Legenden des Games.');
    menu_entry('game.php?m=changelog'.$sid, 'Changelog', 'Aktuelle Änderungen im Spiel');
    if ($usr['stat']>100) { menu_entry('game.php?m=todo'.$sid, 'not Multis', 'interne Liste welche nicht Multis Accounts enthält.'); }

    //menu_entry('game.php?m=win'.$sid, 'Gewinnspiel', 'Das HTN Lan Gewinnspiel');
    menu_entry('game.php?m=stats'.$sid, 'Statistik', 'Statistische Daten &uuml;ber das Spiel');
    menu_entry('game.php?m=kb'.$sid, 'Hilfe', 'Hier findest du die Hilfe zum Spiel.');
    if ($usr['stat']>100) {
            menu_entry('user.php?a=adminaufgaben'.$sid, 'Subnetze füllen / leeren');
    }
    if ($usr['stat']>100) {
            menu_entry('user.php?a=multi'.$sid, 'Multis suchen');
    }
    if ($usr['stat']>100) {
            menu_entry('user.php?a=calc'.$sid, 'Calc Points');
    };

}
else
{
    // PUBLIC ITEMS
    menu_entry('pub.php', 'Startseite', 'HackTheNet-Startseite (Log In)');
    menu_entry('pub.php?a=register', 'Registrieren', 'Einen HackTheNet-Account anlegen');
    menu_entry('pub.php?d=newpwd', 'Neues Passwort', 'Ein neues Passwort f&uuml;r deinen Account anfordern');
#    menu_entry('pub.php?d=credits', 'HTN-Team'); // 1. Auskommentiert
    menu_entry('pub.php?d=impressum', 'Impressum');
    menu_entry('pub.php?d=rules', 'Spielregeln', 'Wer die nicht beachtet, fliegt! Hier kannst du auch Regelverstösse melden!'); // 1. Auskommentiert
#    menu_entry('pub.php?d=faq', 'FAQ', 'H&auml;ufig gestellte Fragen zu HTN'); // 1. Auskommentiert
    menu_entry('pub.php?d=stats', 'Statistik', 'Statistische Daten &uuml;ber das Spiel');


}
 // COMMON ITEMS
#menu_entry_neu('http://ownage.name/cms/', 'HTN.LAN Forum');

if ($sid!='')
{
  // INGAME ITEMS 2
  menu_entry('login.php?a=logout'.$sid, 'Log Out', 'Hier kannst du dich abmelden.', '<br />Angemeldet als: <em>'.$usr['name'].'</em>', '', true);
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

function createlayout_top_single($title='HackTheNet')
{
global $STYLESHEET_BASEDIR,$javascript;

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<title>'.$title.'</title>
<link rel="stylesheet" type="text/css" href="'.$STYLESHEET_BASEDIR.'crystal/style.css" />
<script type="text/javascript" src="global.js"></script>
'.$javascript.'
</head>

<body>';
}

function createlayout_bottom_single()
{
echo '</body>
</html>
';
}

function createlayout_bottom()
{
global $starttime,$usr,$db_querys, $db_querys_time;
if($starttime>0)
{
  $time=(float)calc_time($starttime,0,10);
  if($t<1) $time=number_format($time*1000,1,'.',',').' ms';
  else $time=number_format($time,2,',','.').' s';
}
  $time2=nicetime3(0,'');

  if($t<1) $time3=number_format($db_querys_time*1000,1,'.',',').' ms';
  else $time3=number_format($db_querys_time,2,',','.').' s';
  echo '
<div id="generation-time">TT: '.$time.', QT: '.$time3.', Qs: '.$db_querys.'</div>
<div id="server-time">'.$time2.'</div>
';

  basicfooter();
}

?>