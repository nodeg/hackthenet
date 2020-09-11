<?php
include('config.php');
mysql_select_db($database_prefix);
$ix=mt_rand(1,1000);
$r=db_query('SELECT `key` FROM `verify_imgs` WHERE `id`=\''.$ix.'\' LIMIT 1');
$key=mysql_result($r,0,'key');
?>

<div id="public-login"><h3>Log In für HTN.LAN</h3>
<?
if (file_exists("./data/newround.txt"))
{
	$new_round_time = file_get("./data/newround.txt");
	if ($new_round_time > time()) 
	{
		echo "<p><b>Der Server ist zurzeit gesperrt, ab dem ". date("d.m.Y",$new_round_time)." um ". date("H:i",$new_round_time)." (Serverzeit) ist er freigegeben, du kannst dich allerdings schon registrieren!</b></p>";
	}
}
if (file_exists("./data/roundend.txt"))
{
	$serverstop_time = file_get("./data/roundend.txt");
	echo "<p><b>Der Server ist ab dem ". date("d.m.Y",$serverstop_time)." um ". date("H:i",$serverstop_time)." gesperrt! Danach beginnt die neue Runde!</b></p>";
}
?>
<form action="login.php?a=login" method="post">
<table>
<tr><th>Nickname:</th><td><input name="nick" maxlength="20" <?=$usrname?>/></td></tr>
<tr><th>Passwort:</th><td><input type="password" name="pwd" <?=$pwd?>/></td></tr>
<tr><th><img src="verifyimgs/<?=$key?>.png" width="100" height="50" /></th>
<td>Einmal abtippen!<br />
<input type="text" maxlength="3" size="10" name="chars" /><input type="hidden" name="key" value="<?=$key?>"/></td></tr>
<tr><td colspan="2"><a href="#public-login-extended-options">Erweiterte Optionen anzeigen &raquo;</a></td></tr>
<tr id="public-login-extended-options">
<th>Erweiterte Optionen:</th><td><input type="checkbox" name="save" value="yes" <?=$sv?>/> Login speichern<br />
<input type="checkbox" name="noipcheck" value="yes" /> IP-Überprüfung deaktivieren (nicht empfohlen)</td></tr>
<tr><td colspan="2"><input type="hidden" name="server" value="1" /><input type="submit" value="Enter" /></td></tr>
</table>
</form>
</div>