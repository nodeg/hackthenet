<div class="content" id="public"><h2>Willkommen bei HackTheNet</h2>

<?php
$s1="checked=\"checked\" ";
if(substr_count(($_COOKIE['htnLoginData4']),"|")==2) {	list($server,$usrname,$pwd)=explode("|",$_COOKIE['htnLoginData4']);	eval("\$s$server='checked=\"checked\" ';");
	$usrname="value=\"$usrname\" ";	$pwd="value=\"[xpwd]\" ";	$sv="checked=\"checked\" ";}
echo $notif;
?>

<div id="public-login"><h3>Log In</h3>
<form action="login.htn?a=login" method="post">
<table>
<tr><th>Nickname:</th><td><input name="nick" maxlength="20" <?=$usrname?>/></td></tr>
<tr><th>Passwort:</th><td><input type="password" name="pwd" <?=$pwd?>/></td></tr>
<tr><td colspan="2"><a href="#public-login-extended-options">Erweiterte Optionen anzeigen &raquo;</a></td></tr>
<tr id="public-login-extended-options">
<th>Erweiterte Optionen:</th><td><input type="checkbox" name="save" value="yes" <?=$sv?>/> Login speichern<br />
<input type="checkbox" name="noipcheck" value="yes" /> IP-Überprüfung deaktivieren (nicht empfohlen)</td></tr>
<tr><td colspan="2"><input type="hidden" name="server" value="1" /><input type="submit" value="Enter" /></td></tr>
</table>
</form>
</div>

<div id="public-statistic"><h3>Statistik</h3><p>
<?php
$gcnt=GetOnlineUserCnt(1);
echo "Spieler online: $gcnt<br />\n";
?></p><p><a href="pub.htn?d=stats">Ausführliche Statistik</a></p></div>

<div class="info"><h3>Aktuelle News</h3>
<p>Besuchen Sie auch das Original dieses Spiels auf <a href="http://www.hackthenet.org/">www.hackthenet.org</a>.</p>
<p>Auf unseren Seiten sehen wir den Internet Explorer nicht so gerne, wir empfehlen statt dessen den <a href="http://www.mozilla.org/products/firefox/">Firefox</a>.</p>
<p>Der offizielle HTN-IRC-Channel ist im Freakarea-Netzwerk zu finden! Server ist <em>irc.freakarea.net</em>, Channel <em>#hackthenet</em>.
</div>