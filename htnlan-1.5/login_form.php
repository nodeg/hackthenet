<html>
<head>
<link rel="stylesheet" type="text/css" href="styles/crystal/style.css" />
</head>
<body style="margin:0; padding:0;">
<?php
include "config.php";

  $dbcon=@mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($database_prefix);

function db_query($q)
{
  global $db_querys, $db_querys_time, $db_querys_sql;
  $db_querys_sql[]=$q;
  $start=microtime();
  $r = mysql_query($q);
  $end=microtime();
  if(mysql_error() != '')
  {
    die('<tt>'.$q.'</tt><br />caused an error:<br />'.mysql_error());
  }
  return $r;
}
   function file_get($filename) { //----------- File Get -----------------
       global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR;
       $file = @fopen($filename, 'r');
       if ($file) {
           if ($fsize = @filesize($filename)) {
               $fdata = fread($file, $fsize);
           } else {
               while (!feof($file)) $fdata .= fread($file, 1024);
           }
           fclose($file);
       }
       if($_SERVER[HTTP_HOST]=='localhost') return str_replace("\r",'',$fdata); else return $fdata;
   }

$ix=mt_rand(1,1000);
$r=db_query('SELECT `key` FROM `verify_imgs` WHERE `id`=\''.$ix.'\' LIMIT 1');
$key=mysql_result($r,0,'key');
?>

<div id="public-login" style="margin:0; padding:0;"><h3 style="margin:0; padding:0; padding-left:50px; padding-top:10px;">Log In für HTN.LAN</h3>
<?
if (file_exists("./data/newround.txt"))
{
	$new_round_time = file_get("./data/newround.txt");
	if ($new_round_time > time()) 
	{
		echo "<p style='margin:0; padding:0;' ><b>Der Server ist zurzeit gesperrt, ab dem ". date("d.m.Y",$new_round_time)." um ". date("H:i",$new_round_time)." (Serverzeit) ist er freigegeben, du kannst dich allerdings schon registrieren!</b></p>";
	}
}
if (file_exists("./data/roundend.txt"))
{
	$serverstop_time = file_get("./data/roundend.txt");
	echo "<p style='margin:0; padding:0;'><b>Serverarbeiten! Leider sind die Probleme noch nicht ganz behoben, wir sind aber daran sie zu lösen</b></p>";
}
function GetOnlineUserCnt($server) {
$cnt=0;
$h=opendir('data/login');
while($fn=readdir($h)) {
if(is_file('data/login/'.$fn) && substr($fn,0,1)==$server && substr_count($fn,'lock')==0) $cnt++;
}
closedir($h);
return $cnt;
}
$gcnt=GetOnlineUserCnt(1);
?>

<form action="login.php?a=login" method="post" target="_blank">
<table style="margin:0; padding:0;">
<tr><th>Spieler Online:</th><td><?=$gcnt?></td></tr>
<tr><th>Nickname:</th><td><input name="nick" maxlength="20" <?=$usrname?>/></td></tr>
<tr><th>Passwort:</th><td><input type="password" name="pwd" <?=$pwd?>/></td></tr>
<tr><th><img src="verifyimgs/<?=$key?>.png" width="100" height="50" /></th>
<td><input type="text" maxlength="3" size="10" name="chars" /><input type="hidden" name="key" value="<?=$key?>"/></td></tr>
<tr><td colspan="2"><input type="hidden" name="server" value="1" /><input type="submit" value="Enter" /></form></td></tr>
<tr><td colspan="2"><form action="http://htnlan.rom021.server4free.de/htnlan_neu/pub.php?d=newpwd" method="post" target="_blank"><input type="submit" value="Passwort vergessen" /></form></td></tr>
<tr><td colspan="2"><form action="http://htnlan.rom021.server4free.de/htnlan_neu/pub.php?a=register" method="post" target="_blank"><input type="submit" value="Registrieren" /></form></td></tr>
</table>
</div>
</body>
</html>