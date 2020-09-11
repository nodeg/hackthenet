<?php
$server = 1;
// HTN.LAN by Schnitzel
// Changelog pub.php
// 1. Bei Passwortänderung, keine Fehler Anzeigen, da kein Mail verschickt werden kann

#if(substr_count($_SERVER['HTTP_HOST'],'.')>1) { header('Location: http://htnsrv.org/pub.php'); exit; }

define('IN_HTN',1);
$starttime=microtime();
include('config.php');
if (!$htnlan_installed) 
{
	header("Location: install.php");
}
include('gres.php');
include('layout.php');

define('REG_CODE_LEN',24,false);

$action=$_REQUEST['page'];
if($action=='') $action=$_REQUEST['mode'];
if($action=='') $action=$_REQUEST['action'];
if($action=='') $action=$_REQUEST['a'];
if($action=='') $action=$_REQUEST['m'];
if($action=='') $action=$_REQUEST['d'];

function showdoc($te='',$fn) {
if($te!='') $x=' - '.$te;
createlayout_top('HackTheNet'.$x);
$x='data/pubtxt/'.$fn;
if(file_exists($x.'.txt'))
readfile($x.'.txt');
else @include($x.'.php');
createlayout_bottom();
}

function showdoc_single($te='',$fn) {
if($te!='') $x=' - '.$te;
createlayout_top();
$x='data/pubtxt/'.$fn;
if(file_exists($x.'.txt'))
readfile($x.'.txt');
else @include($x.'.php');
createlayout_bottom();
}

switch($action) {

case 'faq': showdoc('FAQ','faq'); break;
case 'credits': showdoc('Team','credits'); break;
case 'newpwd': showdoc_single('Neues Passwort anfordern','newpwd'); break;
case 'rules': showdoc('Regeln','rules'); break;
case 'chat': showdoc('Chat','chat'); break;
case 'impressum': showdoc('Impressum','impressum'); break;
case 'refinfo': showdoc('Werben von neuen Benutzern','refinfo'); break;
case 'regelverstoss': showdoc('Regelversto&szlig; melden','regelverstoss'); break;
break;

case 'register':

createlayout_top('HackTheNet - Account anlegen');
echo '<div class="content" id="register" style="margin:0; padding:0;">
';
if ($notif=='') {
echo '<div class="important" style="margin:0; padding:0;">'."\n";
echo '<h3 style="margin:0; padding:0; padding-left:50px; padding-top:10px;">Wichtig!</h3>'."\n";
echo '<p>Jeder User verpflichtet sich, die <a href="pub.php?d=rules">Regeln</a> einzuhalten.<br />'.LF.'Verst&ouml;&szlig;e gegen die Regeln f&uuml;hren zum Ausschluss vom Spiel!</p>'."\n";
echo '</div>'."\n";
}
echo $notif.'<div id="register-step1">
<h3 style="margin:0; padding:0; padding-left:50px; padding-top:10px;">Schritt 1: Zugangsdaten und Server</h3>
<form action="pub.php?a=regsubmit" method="post">
<table style="margin:0; padding:0;">
<tr>
<th>Gew&uuml;nschter NickName:</th>
<td><input name="nick" id="_nick" maxlength="20" /></td>
</tr>
<tr>
<th>Deine Email-Adresse:</th>
<td><input name="email" id="_email" maxlength="50" /><br />
Nur wenn eine korrekte Email-Adresse angegeben wurde, kann der Account aktiviert werden!</td>
</tr>
<tr>
<td colspan="2"><input type="hidden" name="server" value="1" />
<input type="submit" value="Abschicken" /></td>
</tr>
</table>
</form>
</div>
</div>
';
createlayout_bottom();

break;

case 'baduser':
$nick1=$_POST['nick1'];
$nick2=trim($_POST['nick2']);
$text=trim($_POST['text']);
createlayout_top('HackTheNet - Regelversto&szlig;');
echo '<div class="content" id="rules">
<h2>Regelversto&szlig; gemeldet</h2>
';
if($nick2!='' & $text!='') {
@mail('info@htn-lan.com',
  'Regelversto&szlig; gemeldet von '.$nick1,
  $nick2.' hat angeblich folgendes getan:'.LF."\n".$text,
  'From: HackTheNet <robot@hackthenet.org>');
echo '<div class="ok"><h3>Gemeldet.</h3><p>Danke f&uuml;r deine Hilfe!</p></div>';
} else echo '<div class="error"><h3>Fehler</h3><p>Du musst schon den User angeben, der gegen die Regeln versto&szlig;en hat!<br />Auch was er getan hat, ist wichtig!</p></div>';
echo '</div>'; createlayout_bottom();

break;

  case 'comment':
  $newsid=$_REQUEST['id'];
  if (!is_numeric($newsid)) { echo 'hacking attempt.'; exit(); }
  $news=getnews($newsid);
  if($news!=false) {
    createlayout_top('HackTheNet - News - '.$news['kategorie'].' :: '.$news['title'].'');
    echo '<div class="content" id="overview">
    <h2>'.$news['kategorie'].' :: '.$news['title'].'</h2>';

    echo '<div class="info"><h3>Erstellt am: '.date("d.m.Y", $news['time']).', '.date("H:i", $news['time']).' Uhr</h3></div>';
    if ($_GET['b']=="") {
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
      <tr><td>".text_replace(nl2br($news['text']))."</td><td valign=\"top\">".$relatedlinks."</td></tr>
      <tr><th>erstellt am: ".date("d.m.Y", $news['time']).", ".date("H:i", $news['time'])." Uhr von ".$news['autor']."</th><th>&nbsp;</th></tr>
      </table><br>";
      $comments_pro_page=5;
      $page=$_GET['p'];
      $maxpages=ceil(mysql_num_rows(mysql_query('SELECT id FROM news_comment WHERE news_id='.$newsid))/$comments_pro_page);
      if ($page=="") { $page=1; }
      if (!is_numeric($page)) { echo 'hacking attempt.'; exit(); }
      if ($page<1) { $page=1; }
      if ($page>$maxpages) { $page=1; }
      $sql=mysql_query('SELECT * FROM news_comment WHERE news_id='.mysql_escape_string($newsid).' ORDER BY id ASC LIMIT '.($page -1) * $comments_pro_page.','.$comments_pro_page);
      if (mysql_num_rows($sql)>0) {
        echo "<h3>Kommentare</h3>";
        echo "<table id=\"comments\" width=\"80%\">";
        while($comments=mysql_fetch_array($sql)) {
          if ($usr['stat']>=100) { $adminoption="<a href=\"secret.php?a=newsc&b=edit&id=".$comments['id']."&sid=".$sid."\">edit</a> | <a href=\"secret.php?a=newsc&b=del&id=".$comments['id']."&sid=".$sid."\">del</a>"; }
          else { $adminoption=""; }
          echo "<tr><th>".$comments['titel']." von ".$comments['autor']." am: ".date("d.m.Y", $comments['time']).", um ".date("H:i", $comments['time'])." Uhr ".$adminoption."</th></tr>
          <tr><td>".text_replace(nl2br($comments['text']))."</td></tr>";
        }
        echo "</table><br>";

        if ($maxpages>1) {
          $pagem = $page - 1;
          $pagep = $page + 1;
          $pagepp = $page + 2;
          echo "<dl><a href=\"?m=comment&id=".$newsid."&p=1&sid=".$sid."\">«</a> ";
          if (!($page == "1")) {
            echo "<a href=\"?m=comment&id=".$newsid."&p=".$pagem."&sid=".$sid."\"><</a> ";
          }
          else {
            echo "<a href=\"#\"><</a> ";
          }
          if ($page == "1") {
            echo "1 ";
            if ($maxpages >= "2") { echo "<a href=\"?m=comment&id=".$newsid."&p=".$pagep."&sid=".$sid."\">2</a> "; }
            if ($maxpages >= "3") { echo "<a href=\"?m=comment&id=".$newsid."&p=".$pagepp."&sid=".$sid."\">3</a> "; }
          }
          else {
            if ($maxpages >= $pagem) { echo "<a href=\"?m=comment&id=".$newsid."&p=".$pagem."&sid=".$sid."\">".$pagem."</a> "; }
            if ($maxpages >= $page) { echo $page." "; }
            if ($maxpages >= $pagep) { echo "<a href=\"?m=comment&id=".$newsid."&p=".$pagep."&sid=".$sid."\">".$pagep."</a> "; }
          }
          if ($pagep > $maxpages) {
            echo "<a href=\"#\">></a> ";
          }
          else {
            echo "<a href=\"?m=comment&id=".$newsid."&p=".$pagep."&sid=".$sid."\">></a> ";
          }
          echo "<a href=\"?m=comment&id=".$newsid."&p=".$maxpages."&sid=".$sid."\">»</a></dl>";
        }

      } else { echo '<h3>Kommentare</h3>'."\n"; echo'<table><tr><th>Es wurden noch keine Kommentare geschrieben</th></tr></table>'."\n"; }
      if ($usr['name']!="") {
        echo "<form action=\"?action=comment&b=add&id=".$newsid."&sid=".$sid."\" method=\"post\"><h3>Kommentare hinzufügen</h3>
        <table width=\"80%\">
        <tr><th>Username:</th><td>".$usr['name']."</td></tr>
        <tr><th>Titel:</th><td><input type=\"text\" name=\"titel\" maxlength=\"40\" value=\"Re: ".$news['title']."\" size=\"40\"></td></tr>
        <tr><th>Text:</th><td><textarea name=\"text\" cols=70 rows=6></textarea></td></tr>
        <tr><th>&nbsp;</th><td><input type=\"submit\" name=\"submit\" accesskey=\"s\" value=\" Weiter \"></td>
        </table></form><br>";
      }
    }
    if ($_GET['b']=="add" && $usr['name']!="") {
      if (strlen($_POST['text'])>1000) { echo 'Übertreib es nicht. Text maximal 1000 Zeichen'; exit(); }
      elseif (strlen($_POST['titel'])>40) { echo 'Übertreib es nicht. Titel maximal 40 Zeichen'; exit(); }
      elseif ($_POST['titel']=="") { echo 'Titel wurde nicht angegeben.'; }
      elseif ($_POST['text']=="") { echo 'Text wurde nicht angegeben.'; }
      else {
        mysql_query('INSERT INTO news_comment(`id`, `autor`, `autor_id`, `ip`, `time`, `news_id`, `text`, `titel`) VALUES("", "'.$usr['name'].'", "'.$usrid.'", "'.$usr['sid_ip'].'", "'.time().'", "'.$newsid.'", "'.text_replace(mysql_escape_string(htmlspecialchars($_POST['text']))).'", "'.text_replace(mysql_escape_string(htmlspecialchars($_POST['titel']))).'")');
        echo '<br><dl>Kommentar wurde hinzugefügt.</dl>';
      }
    }
  } else simple_message('Diesen News-Eintrag gibt es nicht!');
  createlayout_bottom();

  break;


case 'regsubmit': // ----------------------- RegSubmit --------------------------

$email=trim($_POST['email']);
$nick=trim($_POST['nick']);
$server=(int)$_POST['server'];
if($server<1 || $server>2) $server=2;
mysql_select_db(dbname($server));
$info=gettableinfo('users',dbname($server));  if($info['Rows']>=MAX_USERS_PER_SERVER) exit;
$e=false;

$badwords='rio|schnitzel|unknown|xxxunknownxxx|admin|4dmin|adm1n|@dmin|fuck|fick|sex|porn|penis|vagina|arsch|hitler|himmler|goebbels|göbbels|hure|nutte|fotze|bitch|schlampe';
# nein, king ist kein böses, sondern ein reserviertes wort ^^
$nickzeichen='abcdefghijklmnopqrstuvwxyzäüöABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜß0123456789_-:@.!=?\$%/&';
function checknick($nick) {
global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $nickzeichen;
$b=true;
$len=strlen($nick);
for($i=0;$i<$len;$i++) {
  $zz=substr($nick,$i,1);
  if(strstr($nickzeichen,(string)$zz)==false) {
    $b=false; break;
  }
}
$x=eregi_replace('[-_:@.!=?$%&/0-9]','',$nick);
if(trim($x)=='') $b=false;
return $b;
}

$result = mysql_query('SELECT * FROM blacklist WHERE name="'.$nick.'" || email = "'.$email.'"');

if($nick!='') {
  if(mysql_num_rows($result)>0) { $e=true; $msg.='Du stehst auf der HTN.Lan Blacklist!<br />'; }
  if(getuser($email,'email')!==false) { $e=true; $msg.='Ein Benutzer mit dieser Emailadresse existiert bereits!<br />'; }
  if(getuser($nick,'name')!==false) { $e=true; $msg.='Ein Benutzer mit diesem Nicknamen existiert bereits!<br />'; }
  if(checknick($nick)==false) {
    $e=true; $msg.='Der Nickname darf NUR die Zeichen <i>'.$nickzeichen.'</i> enthalten. Au&szlig;erdem darf er nicht nur aus Sonderzeichen bestehen.<br />'; }
  if(strlen($nick)<3 | strlen($nick)>20) { $e=true; $msg.='Der Nickname muss zwischen 3 und 20 Zeichen lang sein.<br />'; }
  $x=eregi_replace('[-_:@.!=?$%&/0-9]','',$nick);
  if(eregi('('.$badwords.')',$x)!=false) { $e=true; $msg.='Der Nickname darf bestimmte W&ouml;rter nicht enthalten.<br />'; }
} else { $e=true; $msg.='Bitte Nickname eingeben.<br />'; }
if(!check_email($email)) { $e=true; $msg.='Bitte eine g&uuml;ltige Email-Adresse im Format x@y.z angeben.<br />'; }

$javascript=file_get('data/pubtxt/selcountry_head.txt');

if($e==false) {

createlayout_top('HackTheNet - Account anlegen');

$pwd=generateMnemonicPassword();
$tmpfnx=randomx(REG_CODE_LEN);
$tmpfn='data/regtmp/'.$tmpfnx.'.txt';
file_put($tmpfn,$nick.'|'.$email.'|'.$pwd.'|'.$server);

$selcode=str_replace('%path%','images/maps',file_get('data/pubtxt/selcountry_body.txt'));
echo '<div class="content" id="register" style="margin:0; padding:0;">
<div id="register-step2" style="margin:0; padding:0;">
<h3 style="margin:0; padding:0; padding-left:50px; padding-top:10px;">Schritt 2: Land auswählen</h3>
<p style="margin:0; padding:0;">Bitte w&auml;hle jetzt, in welchem Land der Erde dein Computer stehen soll. Nat&uuml;rlich nur im Spiel und nicht in echt...</p>
<form action="pub.php?a=regsubmit2" method="post" name="coolform">
<input type="hidden" name="code" value="'.$tmpfnx.'" />
<input type="hidden" name="country" value="" />
'.$selcode.'
</form>
</div>
</div>
';
createlayout_bottom();
} else {
  header('Location:pub.php?a=register&error='.urlencode($msg));
}
break;

case 'regsubmit2':  // ----------------------- RegSubmit 2 --------------------------

$tmpfnx=$_POST['code'];
$fn='data/regtmp/'.$tmpfnx.'.txt';
list($nick,$email,$pwd,$server)=explode('|',file_get($fn));
mysql_select_db(dbname($server));

createlayout_top('HackTheNet - Account anlegen');
echo '<div class="content" id="register" style="margin:0; padding:0;">
<div id="public-login" style="margin:0; padding:0;">';
$country=$_POST['country'];

# IST DAS LAND VOLL ? START
$c=GetCountry('id',$country);
$subnet=$c[subnet];

$r=db_query('SELECT `id` FROM `pcs` WHERE `ip` LIKE \''.mysql_escape_string($subnet).'.%\';');
$cnt=mysql_num_rows($r);
$xip=$cnt+1;

if($xip > 254) {
  @unlink('data/regtmp/'.$tmpfnx.'.txt');
  echo '  <div class="error" style="margin:0; padding:0;"><h3 style="margin:0; padding:0; padding-left:50px; padding-top:10px;">Sorry</h3>
  <p style="margin:0; padding:0;">Das gew&auml;hlte Land ist schon "voll"! Bitte such dir ein anderes Land aus!</p></div>
  <form action="pub.php?a=regsubmit" method="post">
  <input type=hidden name="server" value="'.$server.'">
  <input type=hidden name="nick" value="'.$nick.'">
  <input type=hidden name="email" value="'.$email.'">
  <p style="margin:0; padding:0;"><input type=submit value=" Zur&uuml;ck "></p>
  </form>';
  echo '</div>'.LF.'</div>'; createlayout_bottom();
  exit;
}

# IST DAS LAND VOLL ? X_END

file_put($fn,$nick.'|'.$email.'|'.$pwd.'|'.$country.'|'.$server);
if($nick=='' || $email=='' || $pwd=='' || $country=='' || $server=='') { simple_message_single('FEHLER AUFGETRETEN!','error'); exit; }

$body='Hallo '.$nick.'!'.LF."\n".'Du hast dich bei HTN.LAN angemeldet!';
$body.=' Hier sind deine Zugangsdaten!'.LF."\n".'Nickname: '.$nick."\n".'Passwort: '.$pwd."\n"."\n".'Bevor du deinen';
$body.=' neuen Account nutzen kannst, musst du ihn aktivieren! Rufe dazu die folgende URL in deinem Browser auf:'.LF."\n";
/*if($localhost) $body.='<a href="';*/
$body.='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?a=regactivate&code='.$tmpfnx;
/*if($localhost) $body.='"><b>aktivieren</b></a></p>';*/
$body.="\n";


if(@mail($email,'Dein HTN.LAN Account',$body,'From: HTN.LAN <nor@htn-lan.com>')) {
 readfile('data/pubtxt/regok.txt');
} else {
echo nl2br($body);
}

echo '</div>'.LF.'</div>'; createlayout_bottom();
break;

case 'regactivate': // ----------------------- RegActivate --------------------------

if(strlen($_GET[code])<>REG_CODE_LEN) { simple_message('Keine Hackversuche bitte!'); exit; }

$fn='data/regtmp/'.$_GET['code'].'.txt';
if(file_exists($fn)==false) {
  simple_message('Ung&uuml;ltiger Registrierungscode!');
} else {

$a=explode('|',file_get($fn));
list($nick,$email,$pwd,$country,$server)=explode('|',file_get($fn));
unlink($fn);

mysql_select_db(dbname($server));

if(getuser($nick,'name')!==false) { simple_message('Ein Benutzer mit diesem Nicknamen existiert bereits!'); }

$tableinfo=GetTableInfo('users',dbname($server));
$autoindex=$tableinfo['Auto_increment'];
$r=addpc($country,$autoindex);
if($r!=false) {

  $ts=time();
  if ($only_big_accounts)
  { 
  	db_query('INSERT INTO users(name, email,   password, pcs, liu, lic,  clusterstat, login_time, bigacc)'.'VALUES(\''.mysql_escape_string($nick).'\',\''.mysql_escape_string($email).'\',\''.md5($pwd).'\', \''.mysql_escape_string($r).'\', \''.mysql_escape_string($ts).'\', \''.mysql_escape_string($ts).'\', 0,        \''.mysql_escape_string($ts).'\',\'yes\');');
  } else {
	db_query('INSERT INTO users(name, email,   password, pcs, liu, lic,  clusterstat, login_time, bigacc)'.'VALUES(\''.mysql_escape_string($nick).'\',\''.mysql_escape_string($email).'\',\''.md5($pwd).'\', \''.mysql_escape_string($r).'\', \''.mysql_escape_string($ts).'\', \''.mysql_escape_string($ts).'\', 0,        \''.mysql_escape_string($ts).'\',\'no\');');
  }

  $ownerid=mysql_insert_id();
  db_query('UPDATE pcs SET owner=\''.mysql_escape_string($ownerid).'\', owner_name=\''.mysql_escape_string($nick).'\', owner_points=0, owner_cluster=0, owner_cluster_code=\'\' WHERE id='.mysql_escape_string($r));

  db_query('INSERT INTO rank_users VALUES(0, '.mysql_escape_string($ownerid).', \''.mysql_escape_string($nick).'\', 0, 0);');
  $rank=mysql_insert_id();
  db_query('UPDATE users SET rank='.mysql_escape_string($rank).' WHERE id='.mysql_escape_string($ownerid).';');

  /*setcookie('ref_user');
  setcookie('regc1','yes',time()+24*60*60);
  $dummy=reloadsperre_CheckIP(true); # IP speichern
  */
createlayout_top('HackTheNet - Account aktivieren');
echo '<div class="content" id="register">
<h2>Account aktivieren</h2>
<div id="register-activate">
';
  echo '<div class="ok"><h3>Account aktiviert!</h3>';
  echo '<p>Herzlichen Gl&uuml;ckwunsch!<br />Dein Account wurde aktiviert!<br />Du kannst dich jetzt auf der <a href="./">Startseite</a> einloggen!</p></div>';

} else {
createlayout_top();
echo '<div class="content" id="register">
<h2>Account aktivieren</h2>
<div id="register-activate">
';
echo '<div class="error"><h3>Sorry</h3>

<p>Das gew&auml;hlte Land ist schon "voll"! Bitte such dir ein anderes Land aus!</p></div>
<form action="pub.php?a=regsubmit" method="post">
<input type=hidden name="server" value="'.$server.'">
<input type=hidden name="nick" value="'.$nick.'">
<input type=hidden name="email" value="'.$email.'">
<p><input type=submit value=" Weiter "></p>
</form>';
}

echo '</div>'.LF.'</div>'; createlayout_bottom();

}
break;

case 'newpwdsubmit': // ----------------------- NEW PWD SUBMIT --------------------------

$usrname=strtolower(trim($_REQUEST['nick']));
$email=strtolower(trim($_REQUEST['email']));
$server=(int)$_POST['server'];
if($server<1 || $server>2) $server=1;

mysql_select_db(dbname($server));

if(check_email($email)===true) {

$usr=getuser($usrname,'name');

if($usr!==false) {
if($email==strtolower($usr[email])) {
  $pwd=generateMnemonicPassword();

  db_query('UPDATE users SET password=\''.md5($pwd).'\' WHERE id=\''.mysql_escape_string($usr['id']).'\';');

  if(@mail($email,'Zugangsdaten für HTN.LAN',"\n".'http://www.htn-lan.com/'."\n".'Benutzername: '.$usr['name'].LF.'Passwort: '.$pwd."\n",'From: HTN.LAN <noreply@htn-lan.com>')) {
  db_query('UPDATE users SET sid=\'\' WHERE id=\''.mysql_escape_string($usr['id']).'\' LIMIT 1;');
  unset($usr);
  simple_message_single('Das neue Passwort wurde an Deine Email-Adresse geschickt!');
  } else {
    # simple_message('Beim Verschicken der Email trat ein Fehler auf!'); // 1. Alte Version
    simple_message_single('Neues Passwort: '.$pwd); // 1. Neue Version
    /*if($_SERVER[HTTP_HOST]==localhost) echo '<br />Neues Passwort: '.$pwd;*/ // 1. Wird nicht mehr gebraucht
  }

} else { unset($usr); simple_message_single('Falsche Email-Adresse!'); }
} else { unset($usr); simple_message_single('Benutzername unbekannt!'); }
} else { unset($usr); simple_message_single('Email-Adresse ung&uuml;ltig!'); }

break;

case 'stats_old': // ----------------------- STATS --------------------------
createlayout_top('HackTheNet - Statistik');
echo '<div class="content" id="server-statistic">'."\n";
echo '<h2 >Statistik</h2>'."\n";
if(mysql_select_db($database_prefix)) {

$uinfo=gettableinfo('users',$database_prefix);
$pcinfo=gettableinfo('pcs',$database_prefix);

$cnt1=$uinfo['Rows'];
$cnt2=$pcinfo['Rows'];
$cnt=$cnt2-$cnt1;
$cnt3=(int)@file_get('data/_server1/logins_'.strftime('%x').'.txt');

$cnt4=GetOnlineUserCnt($server);

echo '<h3>Allgemein</h3>
<table>
<tr>
<th>Registrierte User:</th>
<td>'.$cnt1.'</td>
</tr>
<tr>
<th>Computer:</th>
<td>'.$cnt2.'</td>
</tr>
<tr>
<th>Spieler online:</th>
<td>'.$cnt4.'</td>
</tr>
<tr>
<th>Logins heute:</th>
<td>'.$cnt3.'</td>
</tr>
';

$fn='data/_server1/logins_'.strftime('%Y%m%d',time()-86400).'.txt';
if(file_exists($fn)) {
$cnt=(int)file_get($fn);
echo '<tr>'.LF.'<th>Logins gestern:</th>'.LF.'<td>'.$cnt.'</td>'.LF.'</tr>'."\n";
}
echo '</table>'."\n";
}




echo "<h3>Erweitert</h3>";
include("statistik/index.php");
echo "\n".'</div>';

createlayout_bottom();
break;

case 'stats': // ----------------------- STATS --------------------------
createlayout_top();
if(mysql_select_db($database_prefix)) {

$uinfo=gettableinfo('users',$database_prefix);
$pcinfo=gettableinfo('pcs',$database_prefix);

$cnt1=$uinfo['Rows'];
$cnt2=$pcinfo['Rows'];
$cnt=$cnt2-$cnt1;
$cnt3=(int)file_get('data/_server1/logins_'.strftime('%Y%m%d').'.txt');

$cnt4=GetOnlineUserCnt($server);

echo '<div class="content" id="server-statistic"><h3 style="margin:0; padding:0; padding-left:50px; padding-top:10px;">Allgemein</h3>
<table  style="margin:0; padding:0;">
<tr>
<th>Registrierte User:</th>
<td>'.$cnt1.'</td>
</tr>
<tr>
<th>Computer:</th>
<td>'.$cnt2.'</td>
</tr>
<tr>
<th>Spieler online:</th>
<td>'.$cnt4.'</td>
</tr>
<tr>
<th>Logins heute:</th>
<td>'.$cnt3.'</td>
</tr>
';

$fn='data/_server1/logins_'.strftime('%Y%m%d',time()-86400).'.txt';
if(file_exists($fn)) {
$cnt=(int)file_get($fn);
echo '<tr>'.LF.'<th>Logins gestern:</th>'.LF.'<td>'.$cnt.'</td>'.LF.'</tr>'."\n";
}
echo '</table>'."\n";
}




echo "<h3 style='margin:0; padding:0; padding-left:50px; padding-top:10px;'>Erweitert</h3>";
include("statistik/index.php");
echo "\n".'</div>';
createlayout_bottom();
break;


case 'deleteaccount':  // ----------------------- DELETE ACCOUNT --------------------------
$code=$_GET['code'];
$x=@file_get('data/regtmp/del_account_'.$code.'.txt');
if($x) {
$x=explode('|',$x);

mysql_select_db(dbname($x[1]));
if($usr=@delete_account($x[0])) {

db_query('INSERT INTO logs SET type=\'deluser\', usr_id=\''.mysql_escape_string($usr['id']).'\', payload=\''.mysql_escape_string($usr['name']).' '.mysql_escape_string($usr['email']).' self-deleted\';');

simple_message('Account '.$usr['name'].' ('.$usrid.') gel&ouml;scht!');

} else {
simple_message('Account '.$usr['name'].' existiert nicht!');
}

} else simple_message('Ung&uuml;ltiger Account-L&ouml;sch-Code!');
break;


default: // ----------------------- STARTSEITE --------------------------

if (!$htnlan_installed) 
{
	header("Location: install.php");
} else {
	createlayout_top('HTN.LAN');
	include('data/pubtxt/startseite.php');
	createlayout_bottom();
}
}

/*
function listnews($file) {
$f = fopen($file,'r');
$blub = fread($f,65535);
fclose($f);

$p = xml_parser_create();
xml_parse_into_struct($p,$blub,$values,$index);
xml_parser_free($p);

$pointer = 0;

for($i=0;$i<=sizeof($values);$i++) {
  if($values[$i]['tag']=='TITLE') {
  $linktitle[$pointer] = $values[$i]['value'];
}

if($values[$i]['tag']=='LINK') {
  $linkurl[$pointer] = $values[$i]['value'];
  $pointer++;
}
}

echo '<table>';
for($i=1;$i<=sizeof($linktitle);$i++) {
  if($linkurl[$i]!='' && $linktitle[$i]!='')
    echo '<tr><td><a href="'.$linkurl[$i].'">'.$linktitle[$i].'</a></td></tr>';
}
echo "</table>";

}*/

function generateMnemonicPassword() {

$charset[0] = array('a', 'e', 'i', 'o', 'u');
$charset[1] = array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'z');
$specials=array('!', '$', '%', '&', '/', '=', '?', '+', '-', '.', ':', ',', ';', '*', '#','_');

$password = '';

for ($i = 1; $i <= 2; $i++) {
$password .= $charset[$i % 2][array_rand($charset[$i % 2])];
}
$password.=$specials[mt_rand(0,count($specials)-1)];
for ($i = 1; $i <= 2; $i++) {
$password .= $charset[$i % 2][array_rand($charset[$i % 2])];
}

for ($i = 1; $i <= 2; $i++) {
$password .= rand(0, 9);
}
$password.=$charset[1][mt_rand(0,count($charset[1])-1)];

return $password;
}

?>