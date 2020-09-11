<?php

define('IN_HTN',1);
include 'gres.php';

if(isset($_GET['sid'])) 
{
  $sid=$_GET['sid'];
}
else 
{
  $sid=$_POST['sid'];
}

if(preg_match("/^[a-z0-9]+$/i",$sid) == false || $sid == '') exit;

$server = (int)$sid{0};

db_select(dbname($server));
$DATADIR = 'data/_server'.$server;

$usr = db_query('SELECT id,verifyimg FROM users WHERE sid=\''.mysql_escape_string($sid).'\' LIMIT 1');
if( mysql_num_rows($usr) != 1 )
{
  die('Session-ID unbekannt!');
}
$usr = mysql_fetch_assoc($usr);

if($_GET['a'] == 'submit')
{
  $r = db_query('SELECT `chars` FROM `verifyimgs` WHERE `id`=\'' . $usr['verifyimg'] . '\' LIMIT 1');
  $chars = mysql_result($r, 0, 'chars');
  
  if(strtoupper($chars) != strtoupper(trim($_POST['chars'])))
  {
    simple_message('Sicherungscode falsch abgetippt! Bitte nochmal probieren!');
    exit;
  }
  
  db_query('UPDATE users SET last_verified='.time().', verifyimg=0 WHERE id='.$usr['id'].' LIMIT 1;');
  
  $ref=$_SERVER['HTTP_REFERER'];
  if($ref=='') $ref='game.php?m=start&sid='.$sid;
  header('Location: '.$ref);
}
elseif($_GET['a'] == 'image')
{
  header('Content-Type: image/png');
  readfile($DATADIR . '/verifyimgs/' . $usr['verifyimg'] . '.png');
}

?>