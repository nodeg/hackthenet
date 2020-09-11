<div class="content" id="public"><h2>Willkommen HTN.LAN</h2>

<?php
include('config.php');
$s1="checked=\"checked\" ";
if(substr_count(($_COOKIE['htnLoginData4']),"|")==2) {	list($server,$usrname,$pwd)=explode("|",$_COOKIE['htnLoginData4']);	eval("\$s$server='checked=\"checked\" ';");
	$usrname="value=\"$usrname\" ";	$pwd="value=\"[xpwd]\" ";	$sv="checked=\"checked\" ";}
echo $notif;
?>
<script Language="JavaScript">
document.write ('<scr' + 'ipt Language="JavaScript" src="http://www.euros4click.de/showme.php?id=796"></scr' + 'ipt>');
</script><div style="float:left">
<p><a href="http://www.browsergames24.de/modules.php?name=Web_Links&l_op=ratelink&lid=744" target="_blank"><img src="http://www.browsergames24.de/votebg.gif" alt="Vote for us @ BG24"></a>&nbsp;&nbsp;
</p></div>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<p><input type="image" src="https://www.paypal.com/de_DE/i/btn/x-click-but04.gif" border="0" name="submit" alt="Zahlen Sie mit PayPal - schnell, kostenlos und sicher!"></p>
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
</form>

<?php

$news_pro_page=1;
  echo '<div class="info"><h3>News</h3></div>';
  $page=$_GET['p'];
  $maxpages=ceil(mysql_num_rows(mysql_query('SELECT id FROM news'))/$news_pro_page);
  if ($page=="") { $page=1; }
  if (!is_numeric($page)) { echo 'hacking attempt.'; exit(); }
  if ($page<1) { $page=1; }
  if ($page>$maxpages) { $page=1; }
  $result=mysql_query('SELECT * FROM news ORDER BY id DESC LIMIT '.($page -1) * $news_pro_page.','.$news_pro_page);
  if ($usr['stat']>=100) { echo "<dl><a href=\"secret.php?a=news&b=add&sid=".$sid."\">News-Eintrag hinzufügen</a></dl><br>"; }
  if (mysql_num_rows($result)>0) {
    while($news=mysql_fetch_array($result)) {
      $newstext=explode(" ", nl2br($news['text'])); $newstext2='';
      for ($i=0; $i<80; $i++) {
        $newstext2.=$newstext[$i].' ';
      }
      $relatedlinks='';
      if ($news['url1']!="" && $news['link1']!="") { $relatedlinks.="<a href=\"derefer.php?u=".$news['url1']."\" target=\"_blank\">".$news['link1']."</a><br>"; }
      if ($news['url2']!="" && $news['link2']!="") { $relatedlinks.="<a href=\"derefer.php?u=".$news['url2']."\" target=\"_blank\">".$news['link2']."</a><br>"; }
      if ($news['url3']!="" && $news['link3']!="") { $relatedlinks.="<a href=\"derefer.php?u=".$news['url3']."\" target=\"_blank\">".$news['link3']."</a><br>"; }
      if ($relatedlinks=="") { $relatedlinks="<i>nicht vorhanden</i>"; }
      if ($usr['stat']>=100) { $adminoption="<a href=\"secret.php?a=news&b=edit&id=".$news['id']."&sid=".$sid."\">edit</a> | <a href=\"secret.php?a=news&b=del&id=".$news['id']."&sid=".$sid."\">del</a>"; }
      else { $adminoption=""; }
      echo "
      <table width=\"80%\">
      <tr><th>".$news['kategorie']." :: ".$news['title']." ".$adminoption."</th><th width=\"20%\">Related Links:</th></tr>
      <tr><td>".text_replace($newstext2)."...<br><a href=\"?action=comment&id=".$news['id']."&sid=".$sid."\">Read more ...</a></td><td valign=\"top\">".$relatedlinks."</td></tr>
      <tr><th>erstellt am: ".date("d.m.Y", $news['time']).", ".date("H:i", $news['time'])." Uhr von ".$news['autor']."</th><th>&nbsp;</th></tr>
      </table>";
    }
    if ($maxpages>1) {
      $pagem = $page - 1;
      $pagep = $page + 1;
      $pagepp = $page + 2;
      echo "<dl><a href=\"?m=start&p=1&sid=".$sid."\">«</a> ";
      if (!($page == "1")) {
        echo "<a href=\"?m=start&p=".$pagem."&sid=".$sid."\"><</a> ";
      }
      else {
        echo "<a href=\"#\"><</a> ";
      }
      if ($page == "1") {
        echo "1 ";
        if ($maxpages >= "2") { echo "<a href=\"?m=start&p=".$pagep."&sid=".$sid."\">2</a> "; }
        if ($maxpages >= "3") { echo "<a href=\"?m=start&p=".$pagepp."&sid=".$sid."\">3</a> "; }
      }
      else {
        if ($maxpages >= $pagem) { echo "<a href=\"?m=start&p=".$pagem."&sid=".$sid."\">".$pagem."</a> "; }
        if ($maxpages >= $page) { echo $page." "; }
        if ($maxpages >= $pagep) { echo "<a href=\"?m=start&p=".$pagep."&sid=".$sid."\">".$pagep."</a> "; }
      }
      if ($pagep > $maxpages) {
        echo "<a href=\"#\">></a> ";
      }
      else {
        echo "<a href=\"?m=start&p=".$pagep."&sid=".$sid."\">></a> ";
      }
      echo "<a href=\"?m=start&p=".$maxpages."&sid=".$sid."\">»</a></dl> ";
    }
  } else { echo '<dl>Es wurden noch keine News geschrieben</dl>'; }

?>
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
	if ($serverstop_time < time()) 
	{	
		echo "<p><b>Die Runde ist beendet!</b></p>";
	} else {
		echo "<p><b>Diese Runde dauert bis:". date("d.m.Y",$serverstop_time)." um ". date("H:i",$serverstop_time)."</b></p>";	
	}
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
<div id="public-statistic"><h3>Statistik</h3><p>
<?php
$gcnt=GetOnlineUserCnt(1);
echo "Spieler online: $gcnt<br />\n";
?></p><p><a href="pub.php?d=stats">Ausführliche Statistik</a></p>
</div>

<div class="info"><h3>Copyright</h3>
<p>Dies ist die Veröffentlichte Version vom originalen <a href="http://www.htn-lan.com" target="_blank">HTN.LAN by Schnitzel</a></p>
<p>Für weitere Informationen zu HTN.LAN besuche das <a href="http://www.htn-lan.com/forum" target="_blank">Forum</a></p>
<p>Diese Version ist ein veränderte Version von <a href='http://www.hackthenet.org' target='_blank'>HackTheNet</a><br>
Herzlichen Dank an das Team von HackTheNet für den HTNV2 Source.</p>
</div>