<div class="content" id="public"><h2>Willkommen bei HackTheNet</h2>                                                                                                                                                                                     <!--<script type="text/javascript" src="http://www.adprovider.de/spss2/script.php?s=388"></script>//-->

<?php
$s1='checked="checked" '; $s2='';
if(substr_count(($_COOKIE['htnLoginData4']),'|')==2)
{
  list($server,$usrname,$pwd) = explode('|',$_COOKIE['htnLoginData4']);
  $server = (int)$server;
  $usrname = htmlentities($usrname);
  $pwd = htmlentities($pwd);
  eval("\$s$server='checked=\"checked\" ';");
	$usrname="value=\"$usrname\" ";	$pwd='value="[xpwd]" ';
	$sv='checked="checked" ';
}
echo $notif;


?>

<div id="public-login"><h3>Log In</h3>
<form action="login.php?a=login" method="post">
<input type="hidden" name="server" value="1" />
<table>
<tr><th>Nickname:</th><td><input name="nick" maxlength="20" <?=$usrname?>/></td></tr>
<tr><th>Passwort:</th><td><input type="password" name="pwd" <?=$pwd?>/></td></tr>
<th>Erweitert:</th><td><input type="checkbox" name="save" value="yes" <?=$sv?>/> Login speichern<br />
<input type="checkbox" name="noipcheck" value="yes" /> keine IP-Überprüfung<br />(nicht empfohlen)</td></tr>
<tr><th colspan="2" style="text-align:right;"><input type="submit" value="  Enter  " /></th></tr>
</table>
</form>
</div>

<div id="public-statistic"><h3>Statistik</h3><p>
<?php
$cnt1=GetOnlineUserCnt(1);
echo 'Spieler online (Server 1): '.$cnt1.'<br />'.LF;
?></p><p><a href="pub.php?d=stats">Ausführliche Statistik</a></p></div>

<div class="info"><h3>Aktuelle News</h3>
<p>Auf unseren Seiten sehen wir den Internet Explorer nicht so gerne, wir empfehlen statt dessen den <a href="http://www.mozilla-europe.org/de/">Firefox</a>.</p>
</div>

<div id="team-kings">
<h3>Wichtig!</h3>
<p>Der Quellcode dieses Spiels stammt im Original von <a href="http://www.hackthenet.org/">www.hackthenet.org</a>.
Diesen Hinweis bitte nicht entfernen.</p>
</div>