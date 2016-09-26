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
case 'movepc': // ----------------------------------- MOVE PC --------------------------------

$pc=getpc($_REQUEST['pcid']);
if($pc===false)
  $pc=getpc($_REQUEST['pcip'],'ip');
if(!$pc) die("bla1");

$from_usr=getuser($pc['owner']);

$to_usr=getuser($_REQUEST['to']);
if(!$to_usr) die('bla3');

mysql_query("UPDATE pcs SET owner='$to_usr[id]' WHERE id='$pc[id]';");
echo mysql_error();
echo write_pc_list($from_usr['id']);
echo write_pc_list($to_usr['id']);

die('FERTIG!');

break;


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
    if($bez=='id') {
      echo (string)$val;
    }
    elseif(strlen($val)>50 || substr_count($val,"\n")>0) {
      echo '<textarea rows=5 cols=50 name="'.$bez.'">'.$val.'</textarea>';
    }
    elseif($val=='yes' || $val=='no') {
      $no=($val=='yes'?'':' selected');
      $yes=($no==true?'':' selected');
      echo '<input type="radio" name="'.$bez.'" value="yes" id="'.$bez.'_yes" ';
      if($yes) echo ' checked="checked" />'; else echo '/>';
      echo '<label for="'.$bez.'_yes">yes</label>&nbsp;&nbsp;';
      echo '<input type="radio" name="'.$bez.'" value="no" id="'.$bez.'_no" ';
      if($no) echo ' checked="checked" />'; else echo '/>';
      echo '<label for="'.$bez.'_no">no</label>';
      #echo '<select name="'.$bez.'"><option'.$yes.'>yes</option><option'.$no.'>no</option></select>';
    }
    else {
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
switch($_REQUEST[type])
{
case 'pc':
  $pc = getpc((int)$_REQUEST['id']);
  $sql = '';
  foreach($pc as $k=>$v)
  {
    if($_POST[$k] !== $pc[$k] && $k!='id')
    {
      $sql.=$k."='".mysql_escape_string($_POST[$k])."',";
    }
  }
  $sql = trim($sql,',');
  if($sql != '')
  {
    db_query('UPDATE pcs SET '.$sql.' WHERE id='.$pc['id'].' LIMIT 1;');
    db_query('INSERT INTO logs SET type=\'adminedit\', usr_id='.$usr['id'].', payload=\'PC '.$pc['id'].': '.mysql_escape_string($sql).'\';');
  }
  break;
  
case 'user':
  $user = getuser((int)$_REQUEST['id']);
  $sql = '';
  foreach($user as $k=>$v)
  {
    if($_POST[$k] !== $user[$k] && $k!='id')
    {
      $sql.=$k."='".mysql_escape_string($_POST[$k])."',";
    }
  }
  $sql = trim($sql,',');
  if($sql != '')
  {
    db_query('UPDATE users SET '.$sql.' WHERE id='.$user['id'].' LIMIT 1;');
    db_query('INSERT INTO logs SET type=\'adminedit\', usr_id='.$usr['id'].', payload=\'USER '.$user['id'].': '.mysql_escape_string($sql).'\';');
  }
  break;
  
case 'cluster':
  $cluster = getcluster((int)$_REQUEST['id']);
  $sql = '';
  foreach($cluster as $k=>$v)
  {
    if($_POST[$k] !== $cluster[$k] && $k!='id')
    {
      $sql.=$k."='".mysql_escape_string($_POST[$k])."',";
    }
  }
  $sql = trim($sql,',');
  if($sql != '')
  {
    db_query('UPDATE clusters SET '.$sql.' WHERE id='.$cluster['id'].' LIMIT 1;');
    db_query('INSERT INTO logs SET type=\'adminedit\', usr_id='.$usr['id'].', payload=\'CLUSTER '.$cluster['id'].': '.mysql_escape_string($sql).'\';');
  }
  break;
}
header('Location: secret.php?sid='.$sid.'&mode=file&type='.$_REQUEST['type'].'&id='.$_REQUEST['id'].'&ok=GESPEICHERT');
break;


case 'login_ips': // ----------------------------------- LOGIN IPS --------------------------------
if($usr['stat']!=1000) simple_message('Das darf nur der KING!');

$uid=(int)$_REQUEST['user'];
$uuser=getuser($uid);
if($uuser===false) die('gibts nicht');

$result=array();
$done_ips=array();
$r1=mysql_query('SELECT ip FROM logins WHERE usr_id='.$uid.';');
while($x=mysql_fetch_assoc($r1))
{
  if($done_ips[$x['ip']] == 1) continue;
  
  $done_ips[$x['ip']]=1;
  $r2=mysql_query('SELECT usr_id FROM logins WHERE ip=\''.$x['ip'].'\' AND ip!=\'195.93.60.108\';');
  $xid=@mysql_result($r2,0,'usr_id');
  if($xid != $uid)
  {
    $result[$xid]+=mysql_num_rows($r2);
  }
  #while($x2=mysql_fetch_assoc($r2))
  #{
  #  $result[$x2['usr_id']]+=1;
  #}
}

arsort($result);

createlayout_top('IPs');

echo '<div class="content"><h2>IPs untersuchen</h2><div id="secret-login_ips"><h3>User '.$uuser['name'].'</h3>
<p><strong>User die sich von der gleichen IP wie '.$uuser['name'].' eingeloggt haben:</strong></p>
<table><tr><th>Name</th><th>Wie oft</th></tr>';
foreach($result as $k=>$v)
{
  $r=db_query('SELECT name FROM users WHERE id=\''.$k.'\' LIMIT 1;');
  if(mysql_num_rows($r) == 1) $name='<a href="user.php?page=info&sid='.$sid.'&user='.$k.'">'.@mysql_result($r,0,'name').'</a>'; else $name=$k;
  echo '<tr><td>'.$name.'</td><td>'.$v.'</td></tr>'.LF;
}
echo '</table></div></div>';
createlayout_bottom();

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

break;
}
echo '</div>';
createlayout_bottom();
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
  simple_message('user deleted');
break;

case 'delcluster':  // ----------------------------------- DEL CLUSTER --------------------------------
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
  deletecluster((int)$_REQUEST['id']);
  simple_message('cluster deleted');
break;

case 'lockacc':  // ----------------------------------- LOCK ACC --------------------------------
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
  $uid=(int)$_REQUEST['user'];
  db_query('INSERT INTO logs SET type=\'lockuser\', usr_id=\''.$uid.'\', payload=\'locked by '.mysql_escape_string($usr['name']).'\';');
  mysql_query('UPDATE users SET locked=\'yes\',locked_till=0,locked_by=\''.$usr['name'].'\',locked_reason=\'kein Grund angegeben\' WHERE id=\''.$uid.'\' LIMIT 1;');
  #setuserval('locked','yes',$uid);
  header('Location: '.$_SERVER['HTTP_REFERER']);
break;

case 'lockaccex':  // ----------------------------------- LOCK ACC EX --------------------------------
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
  $uid=(int)$_REQUEST['user'];
  $u=getuser($uid);
  if($u===false) { die('user inexistent!'); }
  createlayout_top();
  echo '<div class="content">
  <h2>Acc Sperren</h2>
  <div id="lockacc-ex">
  <form action="secret.php?a=lockaccex2&sid='.$sid.'&user='.$uid.'" method="post">
  <table>
  <tr><th>Acc:</th><td>'.$u['name'].' ('.$u['id'].')</td></tr>
  <tr><th>Sperren bis:</th><td><input type="text" name="till" size="30" value="'.strftime('%x %X',time()+24*60*60).'"><br />0 eingeben für unbegrenzt</td></tr>
  <tr><th>Grund:</th><td><input type="text" name="reason" size="30" value="'.$u['locked_reason'].' ('.$u['locked_by'].')"></td></tr>
  <tr><th colspan="2" align="right"><input type="submit" value=" Sperren! "></tr>
  </table>
  </form>
  </div>
  </div>';
  createlayout_bottom();
break;

case 'lockaccex2':  // ----------------------------------- LOCK ACC EX 2 --------------------------------
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
  $uid=(int)$_REQUEST['user'];
  db_query('INSERT INTO logs SET type=\'lockuser\', usr_id=\''.$uid.'\', payload=\'locked ex by '.mysql_escape_string($usr['name']).'\';');
  if($_POST['till'] == '0')
  {
    $ts=0;
  }
  else
  {
    $a=explode(' ',$_POST['till']);
    $a[0]=explode('.', $a[0]);
    $a[1]=explode(':', $a[1]);
    // int mktime ( [int Stunde [, int Minute [, int Sekunde [, int Monat [, int Tag [, int Jahr [, int is_dst]]]]]]])
    $ts=mktime($a[1][0], $a[1][1], $a[1][2], $a[0][1], $a[0][0], $a[0][2]);
  }
  mysql_query('UPDATE users SET locked=\'yes\',locked_till='.$ts.',locked_by=\''.$usr['name'].'\',locked_reason=\''.mysql_escape_string($_POST['reason']).'\' WHERE id=\''.$uid.'\' LIMIT 1;');
  header('Location: user.php?sid='.$sid.'&user='.$uid.'&page=info');
break;

case 'unlockacc':  // ----------------------------------- UNLOCK ACC --------------------------------
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
  $uid=(int)$_REQUEST['user'];
  mysql_query('UPDATE users SET locked=\'no\',locked_till=0 WHERE id=\''.$uid.'\' LIMIT 1;');
  header('Location: '.$_SERVER['HTTP_REFERER']);
break;


case 'inactives':
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
createlayout_top();

$z=0;
$ts=mktime(0,0,0,11,10,2004);
echo 'seit '.nicetime($ts).' haben sich <strong>';
$r=db_query('SELECT * FROM users WHERE login_time<='.$ts.' OR (locked=\'yes\' AND locked_till=0);');
#$r2=db_query('SELECT * FROM users WHERE locked=\'yes\';');
echo mysql_num_rows($r).'</strong> User nicht mehr eingeloggt oder sind gesperrt!<br>';
#echo 'außerdem sind '. mysql_num_rows($r2) . ' User gesperrt!<br>';

#exit;

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
  delete_account($data['id']);
endwhile;

echo 'FERTIG';

createlayout_bottom();

break;

case 'rempcs':
  if($usr['stat']!=1000) simple_message('Das darf nur der KING!');
createlayout_top();

include('data/static/country_data.inc.php');
reset($countrys);
$gcnt=0;
while(list($bez,$item)=each($countrys)):
  $r=db_query('SELECT pcs.id AS pcs_id, pcs.ip AS pcs_ip, pcs.name AS pcs_name, pcs.points AS pcs_points, users.id AS users_id, users.name AS users_name, users.points AS users_points, clusters.id AS clusters_id, clusters.name AS clusters_name FROM (clusters RIGHT JOIN users ON clusters.id = users.cluster) RIGHT JOIN pcs ON users.id = pcs.owner WHERE pcs.country LIKE \''.mysql_escape_string($item['id']).'\' AND pcs.points<\'50\' ORDER BY pcs.id ASC;');
    #$r=db_query('SELECT id FROM pcs WHERE country=\''.mysql_escape_string($item['id']).'\' AND owner_name=\'\';');
    #$r2=db_query('SELECT id FROM pcs WHERE country=\''.mysql_escape_string($item['id']).'\';');
    #echo 'ohneowner: '.mysql_num_rows($r).' gesamt: '.mysql_num_rows($r2).'<br>\n';

#$r=db_query('SELECT users.name FROM pcs RIGHT JOIN users ON pcs.owner=users.id WHERE pcs.country=\''.mysql_escape_string($item['id']).'\';');

    $cnt=0;
    $sql='DELETE FROM pcs WHERE id IN(';
    while($data=mysql_fetch_assoc($r)) {
      if($data['users_name']===NULL) { $cnt++; $sql.=$data['pcs_id'].','; }
    }
    if($cnt<20) continue;
    $sql.='0);';
    echo $item[id].' :: '.$cnt.'<br>';
    $gcnt+=$cnt;
    #echo $sql; continue;
    db_query($sql);
    
    $i=0;
    $r=db_query('SELECT id FROM pcs WHERE country=\''.mysql_escape_string($item['id']).'\' ORDER BY id ASC;');
    while($data=mysql_fetch_assoc($r)):
      $i++;
      db_query('UPDATE pcs SET ip=\''.$item['subnet'].'.'.$i.'\' WHERE id='.$data['id'].' LIMIT 1;');
      echo $i.' ';
    endwhile;

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
  'acceptnew'=>'Neue Mitglieder?','da_avail'=>'Distributed Attack?','sid_ip'=>'IP-Adresse zur SID',
  'locked_till'=>'gesperrt bis','locked_by'=>'gesperrt durch','locked_reason'=>'Grund der Sperrung');
$s=idtoname($id);
if($s=='') $s=$items[$id];
return $s;
}

?>
