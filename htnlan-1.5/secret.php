<?php

define('IN_HTN',1);
$FILE_REQUIRES_PC=FALSE;
include('ingame.php');

if((int)$usr['stat']<10) { header('HTTP/1.0 404 Not Found'); exit; }

$action=$_REQUEST['page'];
if($action=='') $action=$_REQUEST['mode'];
if($action=='') $action=$_REQUEST['action'];
if($action=='') $action=$_REQUEST['a'];
if($action=='') $action=$_REQUEST['m'];

switch($action) {
case 'file':  // ----------------------------------- FILE --------------------------------

switch($_REQUEST['type']) {
case 'pc': $dat=getpc($_REQUEST['id']); break;
case 'user': $dat=getuser($_REQUEST['id']); break;
case 'cluster': $dat=getcluster($_REQUEST['id']); break;
}

$title='htn_server'.$server.'.'.$_REQUEST['type'].'s.'.$dat['id'];

if($usr['stat']==1000) $hw='Du bist Administrator und darfst alles ansehen und &auml;ndern!'; else $hw='Du darfst dir alles angucken, aber nichts &auml;ndern!';

$javascript='<script type="text/javascript">
function calc_ts() {
// dirty little hack
var s=parseInt(document.tsf.ts.value);
if(!isNaN(s)) {
var d=new Date();
d.setTime(s*1000);
document.tsf.zeit.value=d.toLocaleString();
} else {
document.tsf.zeit.value=\'\';
alert(\'Bitte Zahl eingeben!\');
}
}
</script>';

createlayout_top();

echo '<div class="content" id="secret">
<h2>Administrator-Bereich</h2>
<div id="secret-file">
'.$notif.'
<h3>'.$title.'</h3>
<p>'.$hw.'</p><br />
<form action="secret.php?a=save&amp;sid='.$sid.'&amp;type='.$_REQUEST['type'].'&amp;id='.$_REQUEST['id'].'" method="post">
<table>
<tr><th>ID</th><th>Wert</th><th>Erkl&auml;rung</th></tr>';

while(list($bez,$val)=each($dat)) {
  echo '<tr>';
  $val=htmlspecialchars($val);
  echo '<th>'.$bez.'</th>';
  if($usr['stat']==1000) {
    echo '<td>';
    if(strlen($val)>50 || substr_count($val,"\n")>0) {
      echo '<textarea rows=5 cols=50 name="'.$bez.'">'.$val.'</textarea>';
    } elseif($val=='yes' || $val=='no') {
      $no=($val=='yes'?'':' selected');
      $yes=($no==true?'':' selected');
      echo '<select name="'.$bez.'"><option'.$yes.'>yes</option><option'.$no.'>no</option></select>';
    } else {
      if($bez=='password') $t='type=password'; else $t='type=text';
      echo '<input '.$t.' size=30 name="'.$bez.'" value="'.$val.'">';
      if(substr($val,0,3)==substr(time(),0,3)) echo '<br />'.nicetime3($val);
    }
    echo '</td>';
  } elseif($bez!='password') {
    if(substr($val,0,3)==substr(time(),0,3)) $val=nicetime3($val);
    echo '<td><tt>'.nl2br($val).'</tt></td>';
  } else echo '<td>[hidden]</td>';
  echo '<td><i>'.idtotext($bez).'</i></td>';
  echo '</tr>';
}

echo '</table><br /><br />';

if($usr['stat']==1000) {
echo '<p><input type=submit value="     PUMP UP DEN SHIT !     "></p>
</form>';
} else echo '</form>';

echo '<form name="tsf">
<h3>Timestamp in Datum umrechnen</h3>
<table>
<tr><th>Timestamp: <input name="ts" size=20>&nbsp;<input type=button value="Rechnen" onclick="calc_ts()"></th></tr>
<tr><th>Ergebnis: <input name="zeit" size=40 readonly></th></tr>
</table>
</form>';

echo '</div></div>';
createlayout_bottom();
break;

case 'save':  // ----------------------------------- SAVE --------------------------------
if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
switch($_REQUEST[type]) {
case 'pc': savepc($_REQUEST['id'],$_POST); break;
case 'user': saveuser($_REQUEST['id'],$_POST); break;
case 'cluster': savecluster($_REQUEST['id'],$_POST); break;
}
header('Location: secret.php?sid='.$sid.'&mode=file&type='.$_REQUEST['type'].'&id='.$_REQUEST['id'].'&ok=GESPEICHERT');
break;


case 'cboard': // ----------------------------------- CBOARD --------------------------------
$cix=$_REQUEST['id'];
if($cix==18) { simple_message('Nix gibts!'); exit; }
$cluster=getcluster($cix);
if($cluster==false) exit;

createlayout_top('HackTheNet - FREMDES Board');
echo '<div id="cluster-board" class="content">';
echo '<h2>FREMDES Board</h2>';

switch($_REQUEST['board']) {
case 'showthread':

$id=(double)$_REQUEST['threadid'];

function show_beitrag($data) {
  global $STYLESHEET,$DATADIR,$sid;
  #$sender=getuser($data['user']);
  if($data['relative']==-1) echo '<h3>Beitrag: '.$data['subject'].'</h3><br />';
  $cstat=cscodetostring($data['user_cstat']);
  $time=nicetime($data['time']);
echo '<table>
<tr class=board2><td><span class=boardtitle>'.$data['subject'].'</span>&nbsp;
<span style="font-size:9pt;">von <a href="user.php?a=info&user='.$data['user'].'&sid='.$sid.'">'.$data['user_name'].'</a>
('.$cstat.') am '.$time.'</span></td></tr>
<tr valign=top><td class=board1>'.$data['content'].'</td></tr>
</table><br />';
}

$result=db_query('SELECT * FROM cboards WHERE thread LIKE \''.mysql_escape_string($id).'\'');
if(mysql_num_rows($result)>0) {
  settype($a,'array');
  show_beitrag(mysql_fetch_assoc($result));
  $result=db_query('SELECT * FROM cboards WHERE relative LIKE \''.mysql_escape_string($id).'\' ORDER BY time ASC;');
  while($data=mysql_fetch_assoc($result)):
    show_beitrag($data);
  endwhile;
  foreach($a as $item) show_beitrag($item);
} else exit;

break;

default:

echo '<h3>Cluster-Board von '.$cluster['code'].'</h3><br />';

function listboard($boxid) {
global $sid,$cluster,$cix;
echo '<table cellpadding=3 style="font-size:10pt;">';
echo '<tr class=head><td><b>Titel</b></td><td><b>Autor</b></td><td><b>Datum</b></td><td><b>Ge&auml;ndert</b></td><td><b>Antw.</b></td>';
echo '</tr>';

$output='';
$result=db_query('SELECT * FROM cboards WHERE cluster LIKE \''.mysql_escape_string($cix).'\' AND box LIKE \''.mysql_escape_string($boxid).'\' AND relative LIKE \'-1\' ORDER BY time ASC;');
$cnt=mysql_num_rows($result);
while($data=mysql_fetch_assoc($result)):
  $tmp='';
  $r=db_query('SELECT * FROM cboards WHERE cluster LIKE \''.mysql_escape_string($cix).'\' AND relative LIKE \''.mysql_escape_string($data['thread']).'\' ORDER BY time ASC;');
  $aws=mysql_num_rows($r);
  if($aws>0) {
    $lastm_usr['id']=mysql_result($r,$aws-1,'user');
    $lastm_usr['name']=mysql_result($r,$aws-1,'user_name'); #=getuser(mysql_result($r,$aws-1,'user'),'id');
    $lastm_time=mysql_result($r,$aws-1,'time');
  } else $lastm_time=$data['time'];

  $tmp.='<tr class=greytr style="font-size:8pt;">';
  #$sender=getuser($data['user'],'id');

  $tmp.='<td style="font-size:10pt;"><a href="secret.php?sid='.$sid.'&mode=cboard&board=showthread&threadid='.$data['thread'].'&id='.$cix.'">'.$data['subject'].'</a></td>';
  $tmp.='<td><a href="user.php?a=info&sid='.$sid.'&user='.$data['user'].'" class=il>'.$data['user_name'].'</a></td><td>'.nicetime2($data['time'],true).'</td>';
  if($lastm_time==$data['time']) $slm='nein'; else $slm=nicetime2($lastm_time,true).' von <a href="user.php?a=info&sid='.$sid.'&user='.$lastm_usr['id'].'" class=il>'.$lastm_usr['name'].'</a>';
  $tmp.='<td>'.$slm.'</td>';
  if($aws<1) $aws='keine';
  $tmp.='<td>'.$aws.'</td>';
  $tmp.='</tr>';
  $output=$tmp.$output;
endwhile;
echo $output;
echo '</table>';
}

echo '<br /><b>Ordner 1 ('.$cluster['box1'].'):</b>';
listboard(0);
echo '<br /><b>Ordner 2 ('.$cluster['box2'].'):</b>';
listboard(1);
echo '<br /><b>Ordner 3 ('.$cluster['box3'].'):</b>';
listboard(2);
echo '<br /><b>Ordner 4 ('.$cluster['box4'].'):</b>';
listboard(3);

break;
}
echo '</div>';
createlayout_bottom();
break;

case 'news':  // ----------------------------------- News --------------------------------
if($usr['stat']<100) { simple_message('Das darf nur der KING!'); }
else {
  createlayout_top('Admin');
  if ($_GET['b']=="add") {
    if ($_POST['submit']=="") {
      echo "<form action=\"secret.php?a=news&b=add&sid=".$sid."\" method=\"post\"><h3>News ändern</h3>
            <table>
            <tr><th>Username:</th><td><a href=\"user.php?a=info&amp;user=".$usrid."&amp;sid=".$sid."\">".$usr['name']."</a></td></tr>
            <tr><th>Titel:</th><td><input type=\"text\" name=\"titel\" maxlength=\"40\" size=\"40\"></td></tr>
            <tr><th>Group:</th><td><input type=\"text\" name=\"kategorie\" maxlength=\"40\" value=\"HTN.Lan\" size=\"40\"></td></tr>
            <tr><th>Link1:</th><td><input type=\"text\" name=\"url1\" maxlength=\"40\"  size=\"40\"></td></tr>
            <tr><th>Link1 Text:</th><td><input type=\"text\" name=\"link1\" maxlength=\"40\"  size=\"40\"></td></tr>
            <tr><th>Link2:</th><td><input type=\"text\" name=\"url2\" maxlength=\"40\"  size=\"40\"></td></tr>
            <tr><th>Link2 Text:</th><td><input type=\"text\" name=\"link2\" maxlength=\"40\"  size=\"40\"></td></tr>
            <tr><th>Link 3:</th><td><input type=\"text\" name=\"url3\" maxlength=\"40\"  size=\"40\"></td></tr>
            <tr><th>Link 3 Text:</th><td><input type=\"text\" name=\"link3\" maxlength=\"40\" size=\"40\"></td></tr>
            <tr><th>Text:</th><td><textarea name=\"text\" cols=70 rows=6></textarea></td></tr>
            <tr><th>&nbsp;</th><td><input type=\"submit\" name=\"submit\" value=\" Weiter \"></td>
            </table></form>";
    } else {
      if ($_POST['url1']=="" && $_POST['link1']!="") { $_POST['url1']==""; $_POST['link1']==""; }
      if ($_POST['url1']!="" && $_POST['link1']=="") { $_POST['url1']==""; $_POST['link1']=="".$_POST['url1'].""; }
      if ($_POST['url2']=="" && $_POST['link2']!="") { $_POST['url2']==""; $_POST['link2']==""; }
      if ($_POST['url2']!="" && $_POST['link2']=="") { $_POST['url2']==""; $_POST['link2']=="".$_POST['url2'].""; }
      if ($_POST['url3']=="" && $_POST['link3']!="") { $_POST['url3']==""; $_POST['link3']==""; }
      if ($_POST['url3']!="" && $_POST['link3']=="") { $_POST['url3']==""; $_POST['link3']=="".$_POST['url3'].""; }
      mysql_query('INSERT INTO news(`id`, `time`, `autor`, `autor_id`, `title`, `kategorie`, `url1`, `link1`, `url2`, `link2`, `url3`, `link3`, `text`) VALUES("", "'.time().'", "'.$usr['name'].'", "'.$usrid.'", "'.mysql_escape_string($_POST['titel']).'", "'.mysql_escape_string($_POST['kategorie']).'", "'.mysql_escape_string($_POST['url1']).'", "'.mysql_escape_string($_POST['link1']).'", "'.mysql_escape_string($_POST['url2']).'", "'.mysql_escape_string($_POST['link2']).'", "'.mysql_escape_string($_POST['url3']).'", "'.mysql_escape_string($_POST['link3']).'", "'.mysql_escape_string($_POST['text']).'")');
      echo '<div class=content><h2>Admin</h2><br>News wurde hinzugefügt.</div>';
    }
  }
  if ($_GET['b']=="del" || $_GET['b']=="edit") {
    $newsid=$_GET['id'];
    if ($newsid=="") { $newsid=1; }
    if (!is_numeric($newsid)) { echo 'hacking attempt.'; exit(); }
    $news=getNews($newsid);
    if ($news!=false) {
      if ($_GET['b']=="del") {
        mysql_query('DELETE FROM news WHERE id='.$news['id']); mysql_query('DELETE FROM news_comment WHERE news_id='.$news['id']);
        echo '<div class=content><h2>Admin</h2><br>News wurde gelöscht.</div>';
      }
      if ($_GET['b']=="edit") {
        if ($_POST['submit']=="") {
          echo "<form action=\"secret.php?a=news&b=edit&id=".$newsid."&sid=".$sid."\" method=\"post\"><h3>News ändern</h3>
                <table>
                <tr><th>Username:</th><td><a href=\"user.php?a=info&amp;user=".$usrid."&amp;sid=".$sid."\">".$news['autor']."</a></td></tr>
                <tr><th>Titel:</th><td><input type=\"text\" name=\"titel\" maxlength=\"40\" value=\"".$news['title']."\" size=\"40\"></td></tr>
                <tr><th>Group:</th><td><input type=\"text\" name=\"kategorie\" maxlength=\"40\" value=\"".$news['kategorie']."\" size=\"40\"></td></tr>
                <tr><th>Link 1:</th><td><input type=\"text\" name=\"url1\" maxlength=\"40\" value=\"".$news['url1']."\" size=\"40\"></td></tr>
                <tr><th>Link 1 Text:</th><td><input type=\"text\" name=\"link1\" maxlength=\"40\" value=\"".$news['link1']."\" size=\"40\"></td></tr>
                <tr><th>Link 2:</th><td><input type=\"text\" name=\"url2\" maxlength=\"40\" value=\"".$news['url2']."\" size=\"40\"></td></tr>
                <tr><th>Link 2 Text:</th><td><input type=\"text\" name=\"link2\" maxlength=\"40\" value=\"".$news['link2']."\" size=\"40\"></td></tr>
                <tr><th>Link 3:</th><td><input type=\"text\" name=\"url3\" maxlength=\"40\" value=\"".$news['url3']."\" size=\"40\"></td></tr>
                <tr><th>Link 3 Text:</th><td><input type=\"text\" name=\"link3\" maxlength=\"40\" value=\"".$news['link3']."\" size=\"40\"></td></tr>
                <tr><th>Text:</th><td><textarea name=\"text\" cols=70 rows=6>".$news['text']."</textarea></td></tr>
                <tr><th>&nbsp;</th><td><input type=\"submit\" name=\"submit\" value=\" Weiter \"></td>
                </table></form>";
        } else {
          if ($_POST['url1']=="" && $_POST['link1']!="") { $_POST['url1']==""; $_POST['link1']==""; }
          if ($_POST['url1']!="" && $_POST['link1']=="") { $_POST['url1']==""; $_POST['link1']==$_POST['url1']; }
          if ($_POST['url2']=="" && $_POST['link2']!="") { $_POST['url2']==""; $_POST['link2']==""; }
          if ($_POST['url2']!="" && $_POST['link2']=="") { $_POST['url2']==""; $_POST['link2']==$_POST['url2']; }
          if ($_POST['url3']=="" && $_POST['link3']!="") { $_POST['url3']==""; $_POST['link3']==""; }
          if ($_POST['url3']!="" && $_POST['link3']=="") { $_POST['url3']==""; $_POST['link3']==$_POST['url3']; }

          mysql_query('UPDATE news SET title="'.$_POST['titel'].'", kategorie="'.$_POST['kategorie'].'", url1="'.$_POST['url1'].'", link1="'.$_POST['link1'].'", url2="'.$_POST['url2'].'", link2="'.$_POST['link2'].'", url3="'.$_POST['url3'].'", link3="'.$_POST['link3'].'", text="'.$_POST['text'].'" WHERE id='.$_GET['id']);
          echo '<div class=content><h2>Admin</h2><br>News wurde geändert.</div>';
        }
      }
    } else { echo '<div class=content><h2>Admin</h2><br>News nicht vorhanden.</div>'; }
  }
  echo '</div>';
  createlayout_bottom();
}

break;

case 'newsc':  // ----------------------------------- News Kommentar --------------------------------
if($usr['stat']<100) { simple_message('Das darf nur der KING!'); }
else {
  createlayout_top('Admin');
  $cid=$_GET['id'];
  if (!is_numeric($cid)) { echo 'hacking attempt.'; exit(); }
  $news=mysql_query('SELECT id FROM news_comment WHERE id='.$cid);
  if (mysql_num_rows($news)>0) {
    if ($_GET['b']=="del") {
      mysql_query('DELETE FROM news_comment WHERE id='.$cid);
      echo '<div class=content><h2>Admin</h2><br>Kommentar wurde gelöscht.</div>';
    }
    if ($_GET['b']=="edit") {
      if ($_POST['submit']) {
        $s='';
        while(list($bez, $val)=each($_POST)) {
          if ($bez!="submit") { $s.=mysql_escape_string($bez).'=\''.mysql_escape_string($val).'\','; }
        }
        $s=trim($s,',');
        if($s != '') mysql_query('UPDATE news_comment SET '.$s.' WHERE id=\''.mysql_escape_string($cid).'\'');
        echo '<div class=content><h2>Admin</h2><br>Kommentar wurde geändert.</div>';
      } else {
        $comment=mysql_fetch_assoc(mysql_query('SELECT * FROM news_comment WHERE id='.$cid));
        echo "<form action=\"secret.php?a=newsc&b=edit&id=".$cid."&sid=".$sid."\" method=\"post\"><h3>Kommentare ändern</h3>
              <table width=\"80%\">
              <tr><th>Username:</th><td><a href=\"user.php?a=info&amp;user=".$comment['autor_id']."&amp;sid=".$sid."\">".$comment['autor']."</a></td></tr>
              <tr><th>Titel:</th><td><input type=\"text\" name=\"titel\" maxlength=\"40\" value=\"".$comment['titel']."\" size=\"40\"></td></tr>
              <tr><th>Text:</th><td><textarea name=\"text\" cols=70 rows=6>".$comment['text']."</textarea></td></tr>
              <tr><th>&nbsp;</th><td><input type=\"submit\" name=\"submit\" value=\" Weiter \"></td>
              </table></form><br>";
      }
    }
  } else { echo '<div class=content><h2>Admin</h2><br>Kommentar nicht vorhanden.</div>'; }
  echo '</div>';
  createlayout_bottom();
}

break;

case 'delacc1':  // ----------------------------------- DEL ACC 1 --------------------------------
if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
createlayout_top('Admin');
echo '<div class=content>
<h2>Admin</h2>
<h3>User l&ouml;schen</h3>
<b>Wenn du wirklich den Account l&ouml;schen willst, dann klick auf den Button!</b>
<br /><form action="secret.php?a=delacc2&amp;sid='.$sid.'"  method="post">
<input type=hidden name="user" value="'.$_REQUEST['user'].'">
<input type=submit value=" ACCOUNT L&Ouml;SCHEN ">
</form>
</div>';
createlayout_bottom();
break;

case 'delacc2':  // ----------------------------------- DEL ACC 2 --------------------------------
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
  $u=delete_account($_POST['user']);
  db_query('INSERT INTO logs SET type=\'deluser\', usr_id=\''.mysql_escape_string($u['id']).'\', payload=\''.mysql_escape_string($u['name'].' '.$u['email'].' deleted by '.$usr['name']).'\';');
  simple_message('deleted');
  mysql_query('INSERT INTO logs(`id`, `type`, `usr_id`, `payload`) VALUES("", "delete", "'.$usrid.'", "deleted user '.$uid.'")');
break;

case 'delockacc':  // ----------------------------------- LOCK ACC --------------------------------
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
  $uid=(int)$_REQUEST['user'];
  setuserval('locked','',$uid);
  simple_message('delocked');
  mysql_query('INSERT INTO logs(`id`, `type`, `usr_id`, `payload`) VALUES("", "delock", "'.$usrid.'", "delocked user '.$uid.'")');
break;

case 'lockacc2':  // ----------------------------------- LOCK ACC --------------------------------
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
  $uid=(int)$_REQUEST['user'];
  db_query('INSERT INTO logs SET type=\'lockuser\', usr_id=\''.mysql_escape_string($uid).'\', payload=\'locked by '.mysql_escape_string($usr[name]).'\';');
  setuserval('locked',$_POST['grund'],$uid);
  simple_message('locked');
  mysql_query('INSERT INTO logs(`id`, `type`, `usr_id`, `payload`) VALUES("", "lock", "'.$usrid.'", "locked user '.$uid.'")');
break;

case 'lockacc': // ------------------------------------ LOCK ACC --------------------------------
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
  createlayout_top('Admin');
  echo '<table><tr><th>Grund:</th></tr>';
  echo '<form action="secret.php?a=lockacc2&amp;sid='.$sid.'&amp;user='.$_GET['user'].'" method="post">';
  echo '<tr><td><input type="text" name="grund" size="20" maxlength="100"></td></tr>';
  echo '<tr><td><input type=submit value="Lock"></td></tr></table></form></div>';
  createlayout_bottom();
break;

case 'warn': // ------------------------------------ VERWARNUNG --------------------------------
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
  createlayout_top('Admin');
  echo '<table><tr><th>Grund:</th></tr>';
  echo '<form action="secret.php?a=warn2&amp;sid='.$sid.'&amp;user='.$_GET['user'].'" method="post">';
  echo '<tr><td><input type="text" name="grund" size="20" maxlength="100"></td></tr>';
  echo '<tr><td><input type=submit value="Warn"></td></tr></table></form></div>';
  createlayout_bottom();
break;

case 'warn2': // ------------------------------------ VERWARNUNG --------------------------------
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
  $uid=(int)$_REQUEST['user'];
  $verwarnungenresult=mysql_query('SELECT verwarnung FROM `users` WHERE id=\''.$uid.'\'');
  while($verwarnungen=mysql_fetch_array($verwarnungenresult)) {
    $verwarnung=$verwarnungen['verwarnung'];
    $verwarnung++;
  }
  db_query('UPDATE users SET verwarnung=\''.mysql_escape_string($verwarnung).'\' WHERE id=\''.$uid.'\'');
  if ($verwarnung == "3") { db_query('UPDATE users SET locked=\'zuviele Verwarnungen\' WHERE id=\''.$uid.'\''); simple_message('warned and locked'); }
  else {
    simple_message('warned'); 
    $newmails=mysql_fetch_assoc(db_query('SELECT newmail FROM users WHERE id='.$uid.' LIMIT 1'));
    $newmail=$newmails['newmail']; $newmail++;
    db_query('UPDATE users SET newmail='.$newmail.' WHERE id='.$uid); 
    db_query('INSERT INTO sysmsgs(`msg`, `user`, `time`, `text`, `xread`) VALUES("", '.$uid.', '.time().', "Du hast soeben eine Verwarnung erhalten. Grund dafür: '.$_POST['grund'].'", "no")');
  }
  db_query("INSERT INTO logs(`id`, `type`, `usr_id`, `payload`) VALUES(\"\", \"warn\", ".$usrid.", \"warned user ".$uid."\")");
break;

case 'dewarn': // ------------------------------------ VERWARNUNG --------------------------------
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
  $uid=(int)$_REQUEST['user'];
  $verwarnungenresult=mysql_query('SELECT verwarnung FROM `users` WHERE id=\''.$uid.'\'');
  while($verwarnungen=mysql_fetch_array($verwarnungenresult)) {
    $verwarnung=$verwarnungen['verwarnung'];
    $verwarnung--;
  }
  db_query('UPDATE users SET verwarnung=\''.mysql_escape_string($verwarnung).'\' WHERE id=\''.$uid.'\'');
  if ($verwarnung == "2") { db_query('UPDATE users SET locked=\'\' WHERE id=\''.$uid.'\''); simple_message('dewarned and delocked'); }
  else { 
    simple_message('dewarned');
    $newmails=mysql_fetch_array(mysql_query('SELECT newmail FROM users WHERE id='.$uid));
    $newmail=$newmails['newmail']; $newmail++;
    mysql_query('UPDATE users SET newmail='.$newmail.' WHERE id='.$uid); 
    mysql_query('INSERT INTO sysmsgs(`msg`, `user`, `time`, `text`, `xread`) VALUES("", '.$uid.', '.time().', "Dir wurde soeben eine Verwarnung gelöscht.", "no")'); 
  }
  mysql_query('INSERT INTO logs(`id`, `type`, `usr_id`, `payload`) VALUES("", "dewarn", "'.$usrid.'", "dewarned user '.$uid.'")');
break;

case 'inactives':

createlayout_top();

$z=0;
$ts=mktime(0,0,0,7,13,2004);
echo 'seit '.nicetime($ts).' haben sich <strong>';
$r=db_query('SELECT * FROM users WHERE login_time < \''.mysql_escape_string($ts).'\';');
#$r=db_query('SELECT * FROM users WHERE locked=\'yes\';');
echo mysql_num_rows($r).'</strong> User nicht mehr eingeloggt!<br>';

EXIT;

set_time_limit(600);
ignore_user_abort(0);


while($data=mysql_fetch_assoc($r)):
  /*mail($data['email'], 'Dein HackTheNet-Account wird bald gelöscht!',
  'Hallo '.$data[name].'!'.LF.'Du hast dich in deinen Account des browserbasierten Online-Spiels '.
  'HackTheNet ( http://www.hackthenet.org/ ) seit '.nicetime($data[login_time]).' nicht mehr '.
  'eingeloggt.'.LF.'Wenn du dich nicht bis zum 3. Juli, 24 Uhr mit den folgenden Daten einloggst, '.
  'wird dein Account automatisch gelöscht!'.LF."\n".'Nickname: '.$data['name'].LF.'Passwort: '.$data['password'].LF.'Server: 1'.LF."\n".
  'MfG'.LF.'Das HackTheNet-Team',
  'From: HackTheNet <robot@hackthenet.org>');*/
  #delete_account($data['id']);
endwhile;

echo 'FERTIG';

createlayout_bottom();

break;

case 'investigate':
echo '<pre>';
$r=db_query('SELECT * FROM `transfers` WHERE `from_id`=483 AND `from_type` LIKE \'cluster\';');
while($data=mysql_fetch_assoc($r)):
  echo "\n".nicetime($data['time'])."\t";
  echo $data['credits'].' Credits nach ';
  if($data['to_type']=='user') {
    $p=getpc($data['to_id']);
    echo 'PC 10.47.'.$p['ip'].' von '.$p['owner_name']."\t";
  } else {
    $c=getcluster($data['to_id']);
    echo 'Cluster '.$c['code'].' ('.$c['name'].')'."\t";
  }
endwhile;

echo '</pre>';

break;

case 'rempcs':

createlayout_top();

include('data/static/country_data.inc.php');
reset($countrys);
$gcnt=0;
while(list($bez,$item)=each($countrys)):
  $r=db_query('SELECT pcs.id AS pcs_id, pcs.ip AS pcs_ip, pcs.name AS pcs_name, pcs.points AS pcs_points, users.id AS users_id, users.name AS users_name, users.points AS users_points, clusters.id AS clusters_id, clusters.name AS clusters_name FROM (clusters RIGHT JOIN users ON clusters.id = users.cluster) RIGHT JOIN pcs ON users.id = pcs.owner WHERE pcs.country LIKE \''.mysql_escape_string($item['id']).'\' AND pcs.points<\'50\' ORDER BY pcs.id ASC;');
    #$r=db_query('SELECT id FROM pcs WHERE country=\''.mysql_escape_string($item['id']).'\' AND owner_name=\'\';');
    $r2=db_query('SELECT id FROM pcs WHERE country=\''.mysql_escape_string($item['id']).'\';');
    echo 'ohneowner: '.mysql_num_rows($r).' gesamt: '.mysql_num_rows($r2).'<br>\n';

    #$r=db_query('SELECT users.name FROM pcs RIGHT JOIN users ON pcs.owner=users.id WHERE pcs.country=\''.mysql_escape_string($item['id']).'\';');
/*
    $cnt=0;
    $sql='DELETE FROM pcs WHERE id IN(';
    while($data=mysql_fetch_assoc($r)) {
      if($data['users_name']===NULL) { $cnt++; $sql.=$data['pcs_id'].","; }
    }
    if($cnt<26) continue;
    $sql.='0);';
    echo $item[id].' :: '.$cnt.'<br>';
    $gcnt+=$cnt;
    #echo $sql; continue;
    db_query($sql);
    
    $i=0;
    $r=db_query('SELECT id FROM pcs WHERE country=\''.mysql_escape_string($item['id']).'\' ORDER BY id ASC;');
    while($data=mysql_fetch_assoc($r)):
      $i++;
      db_query('UPDATE pcs SET ip=\''.mysql_escape_string($item['subnet'].'.'.$i).'\' WHERE id='.mysql_escape_string($data['id']).';');
      echo '$i ';
    endwhile;
*/
endwhile;

createlayout_bottom();

break;

}

function idtotext($id) {  // --------------------- ID TO TEXT ------------------
$items=array('id'=>'interne Kennnummer','name'=>'Name', 'ads'=>'Werbung', 'dialer'=>'0900-Dialer',
  'auctions'=>'Auktionsbetrug', 'bankhack'=>'Online-Banking', 'credits'=>'Credits',
  'owner'=>'Besitzer-Nummer', 'ip'=>'IP-Adresse', 'lmupd'=>'Zeit des letzten Geld-Updates',
  'country'=>'Land', 'points'=>'Punkte', 'code'=>'Code',
  'money'=>'Verm&ouml;gen', 'tax'=>'Mitgliedsbeitrag', 'events'=>'Ereignisse',
  'infotext'=>'Beschreibung', 'logofile'=>'Logo-Datei', 'hp'=>'Homepage',
  'email'=>'Email', 'gender'=>'Geschlecht', 'birthday'=>'Geburtstag', 'clusterstat'=>'Cluster-Status',
  'cluster'=>'Cluster-ID','newmail'=>'Neue Messages','liu'=>'Letztes Post-Update', 'pcs'=>'Computer',
  'stat'=>'Status (1000=king)','cm'=>'Cluster-Geld-&Uuml;berweisung', 'homepage'=>'Homepage',
  'sid'=>'Session-ID', 'pacts'=>'Vertr&auml;ge', 'wohnort'=>'Wohnort',
  'di'=>'Deaktiviertes Item', 'dt'=>'Deaktiviert bis', 'la'=>'letzter Angriff',
  'blocked'=>'PC blockiert bis', 'lpu'=>'letztes Punkte-Update', 'locked'=>'Account gesperrt',
  'lastmail'=>'letzte Mail', 'bigacc'=>'Extended Account?',  'sig_mails'=>'Mail-Signatur',
  'sig_board'=>'Board-Signatur', 'ref_pc'=>'PC f&uuml;r Geld aus Ref-Prog.',
  'ref_mode'=>'Ref-Prog.-Modus (0=normal,1=credits)', 'ref_users'=>'geworbene User',
  'login_time'=>'letztes Login','lrh'=>'letztes REMOTE HIJACK', 'password'=>'Passwort',
  'lic'=>'letzte Pr&uuml;fung des Posteingangs', 'ref_by'=>'Geworben von', 'stylesheet'=>'Style',
  'inbox_full'=>'Posteingang voll-Nachricht','avatar'=>'Userlogo-Datei','rank'=>'Platz in der Rangliste',
  'box1'=>'Ordner 1 (Board)', 'box2'=>'Ordner 2 (Board)','box3'=>'Ordner 3 (Board)',
  'acceptnew'=>'Neue Mitglieder?','da_avail'=>'Distributed Attack?');
$s=idtoname($id);
if($s=='') $s=$items[$id];
return $s;
}

?>
