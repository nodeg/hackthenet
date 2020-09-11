<?php

if ( !defined('IN_HTN') )
{
  die('Hacking attempt');
}

$javascript = '';
$bodytag = '';

function menu_entry($url,$text,$help='',$em='',$emclass='',$selftags=false,$inatag='')
{
  echo "\n".'<li><a href="'.$url.'"'.$inatag.'><strong>'.$text.'</strong>';
  if($em!='')
  {
    if(!$selftags) echo '<br /><em class="'.$emclass.'">'.$em.'</em>'; else echo $em;
  }
  echo '</a>';
  if($help!='') echo '<div class="help">'.$help.'</div>';
  echo '</li>';
}

function basicheader($title) 
{
  global $usr,$javascript,$STYLESHEET,$bodytag,$stylesheets;
  global $STYLESHEET_BASEDIR,$standard_stylesheet;
  $stylesheet="anti-ie"; ///$STYLESHEET;
  if($stylesheets[$stylesheet]['id']=='' || $stylesheet=='') $stylesheet=$standard_stylesheet;
  
  $ts=time()+1;
  echo '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n";
  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
  <html>
  <head>
  <title>'.$title.'</title>
  <link rel="stylesheet" type="text/css" href="../'.$STYLESHEET_BASEDIR.$stylesheet.'/style.css" />
  <script type="text/javascript">var stm=new Date(); stm.setTime('.$ts.'000); var sltm=new Date();</script>
  <script type="text/javascript" src="../global.js"></script>
  <link rel="SHORTCUT ICON" href="favicon.ico" />
  '.$javascript.'
  </head>
  ';
}

function basicfooter() 
{
  echo '</body>
  </html>
  ';
}

function createlayout_top($title='HackTheNet', $nomenu=false)
{
  global $usr, $javascript, $STYLESHEET, $bodytag, $localhost, $FILE_REQUIRES_PC, $pc, $pcid;
  global $server, $transfer_ts, $t_limit_server;
  if ($usr['sid']!='') $sid='&amp;sid='.$usr['sid'];
  
  $stylesheet=$STYLESHEET;
  if(@is_dir('../styles/'.$stylesheet)==false || $stylesheet=='') $stylesheet='crystal';
  
  basicheader($title);
    
  echo '<body'.$bodytag.'>
  <div class="header">
  <h1>HTN - Reloaded v2.2</h1>
  ';
  if ($nomenu==false) 
  {
    echo '<ul class="navigation">
    ';
    
    if ($_SESSION['level'] >= 5)
    {
      // INGAME ITEMS
            
      if ($usr['newmail'] > 0)
      {
        $hw = ($usr['newmail'] == 1 ? '' : 's');
        $hw = 'Du hast '.$usr['newmail'].' neue Message'.$hw;
      }
     
      menu_entry('../game.php?m=start'.$sid, 'Game Menü', 'Zurück zum Game Menü.');
      menu_entry('index.php?m=news'.$sid, 'News', 'Hier könnt Ihr News eintragen und bearbeiten.');
      menu_entry('index.php?m=wart'.$sid, 'Wartungs Arbeiten','Spiel in den Wartungs Modus Schalten und Begründung festlegen.');     
      menu_entry('index.php?m=lock'.$sid, 'User Sperren', 'Hier kannst du einen Spieler auf Zeit Sperren oder dauerhaft Sperren.');
      menu_entry('index.php?m=opt'.$sid, 'Optionen');
      menu_entry('index.php?m=fill'.$sid, 'Subnetze', 'Hier kannst du Subnetze füllen.');
      
      
    }
    
    // COMMON ITEMS
    #menu_entry('http://forum.hackthenet.org/', 'Forum');
    
    if ($sid!='')
    {
      // INGAME ITEMS 2
      menu_entry('../login.php?a=logout'.$sid, 'Log Out', 'Hier kannst du dich abmelden.', '<br />Account: <em>'.$usr['name'].'</em>', '', true);
    
    }
    
  }
  
  echo '</ul>
  </div>
  
  <!-- NAVI ENDE -->
  ';
}

function createlayout_bottom()
{ $time="";
  global $starttime, $_query_cnt;
  if($starttime > 0)
  {
    $time = (float)calc_time($starttime, 0, 10);
    if($time < 1)
    {
      $time = number_format($time * 1000, 1, '.', ',') . ' ms';
    }
    else
    {
      $time = number_format($time, 2, ',', '.') . ' s';
    }
  }
  echo '
  <div id="generation-time">' . $time . ' / ' . (int)$_query_cnt . '</div>
  <div id="server-time">' . nicetime3(0, '') . '</div>
  ';
  
  basicfooter();
}

?>