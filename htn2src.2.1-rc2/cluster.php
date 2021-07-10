<?php

define('IN_HTN',1);
$FILE_REQUIRES_PC=FALSE;
include('ingame.php');


$action=$_REQUEST['page'];
# Die folgenden Variablen sollten nicht mehr verwendet werden
if($action=='') $action=$_REQUEST['mode'];
if($action=='') $action=$_REQUEST['action'];
if($action=='') $action=$_REQUEST['a'];
if($action=='') $action=$_REQUEST['m'];

# Konstanten f�r Cluster-Vertr�ge:
define('CV_WAR',1,false);
define('CV_BEISTAND',2,false);
define('CV_PEACE',3,false);
define('CV_NAP',5,false);
define('CV_WING',6,false);

# Cluster-Daten lesen:
$clusterid=$usr['cluster'];
$good_actions='start join found info listmembers request1 request2';
$cluster=getcluster($clusterid);
if($cluster==false && eregi($action,$good_actions)===false) { no_(); exit; }

function savemycluster()
{ # Eigenen Cluster speichern
global $clusterid,$cluster;
$s='';
while(list($bez,$val)=each($cluster))  $s.=$bez.'=\''.mysql_escape_string($val).'\',';
$s=trim($s,',');
db_query('UPDATE clusters SET '.$s.' WHERE id=\''.$clusterid.'\'');
}

switch($action) {
case 'start': //------------------------- START -------------------------------

if($usr['da_avail']=='yes') $pc=getpc($pcid);

createlayout_top('HackTheNet - Cluster');
echo '<div class="content" id="cluster">'."\n";
echo '<h2>Cluster</h2>'."\n";


function nocluster() {
# ich bin keinem (existierenden) Cluster
global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $sid,$usrid,$pcid;
echo '<div id="cluster-found">
<h3>Cluster gr&uuml;nden</h3>
<form action="cluster.php?page=found&amp;sid='.$sid.'" method="post">
<table>
<tr>
<th>Name:</th>
<td><input type="text" name="name" maxlength="48" /></td>
</tr>
<tr>
<th>Code:</th>
<td><input type="text" name="code" maxlength="12" /></td>
</tr>
<tr><td colspan="2"><input type="submit" value="Gr&uuml;nden" /></td>
</tr>
</table>
</form>
</div>
<div class="important"><h3>Hinweis</h3>
<p>Um einem existierenden Cluster beizutreten, rufe die Info-Seite eines Clusters auf.
Dort findest du einen "Mitgliedsantrag stellen"-Link.</p></div>
</div>';
}

#  kein Cluster
if($cluster===false) { nocluster(); createlayout_bottom(); exit; }

if(eregi('http://.*/.*',$cluster['logofile'])) {
  #if($usr['sid_ip']!='noip') {
    $img=$cluster['logofile'];
    #$img=dereferurl($img);
    $img='<tr>'.LF.'<td colspan="2"><img src="'.$img.'" alt="Cluster-Logo" /></td>'.LF.'</tr>'."\n";
  #}
  #$img='<tr>'.LF.'<td colspan="2">Das Clusterlogo kann im Moment wegen einer noch nicht geschlossenen Sicherheitsl&uuml;cke nicht angezeigt werden.</td>'.LF.'</tr>'."\n";
} else $img='';

$a=explode("\n",$cluster['events']);
if(count($a)>21) {
  $cluster['events']=joinex(array_slice($a,0,20),"\n");
  $mod=true;
}
$list=str_replace("\n",'<br />',$cluster['events']);
gFormatText($list);

if($mod==true) { savemycluster(); }

$reqs=@mysql_num_rows(db_query('SELECT user FROM cl_reqs WHERE cluster='.$clusterid.' AND dealed=\'no\''));
$funcs='';
$stat=(int)$usr['clusterstat'];
$settings='<a href="cluster.php?page=config&amp;sid='.$sid.'">Einstellungen</a><br />';
$members='<a href="cluster.php?page=members&amp;sid='.$sid.'">Mitglieder-Verwaltung</a><br />';
$finances='<a href="cluster.php?page=finances&amp;sid='.$sid.'">Cluster-Kasse</a><br />';
$battles='<a href="cluster.php?page=battles&amp;sid='.$sid.'">Angriffs&uuml;bersicht</a><br />';
$konvents='<a href="cluster.php?page=convents&amp;sid='.$sid.'">Vertr&auml;ge</a><br />';
$req_verw='<a href="cluster.php?page=req_verw&amp;sid='.$sid.'">Mitgliedsantr&auml;ge</a> ('.$reqs.')<br />';
if($stat==CS_ADMIN) { $funcs=$settings.$members.$finances.$battles.$konvents.$req_verw; $jobs='Den Cluster verwalten. Du kannst alles machen!'; }
if($stat==CS_COADMIN) { $funcs=$settings.$finances.$battles.$konvents.$req_verw; $jobs='Den Cluster verwalten. Du kannst alles machen au&szlig;er den Status von Mitgliedern &auml;ndern.'; }
if($stat==CS_WAECHTER) { $funcs=$battles; $jobs='Schlachten im Auge behalten.'; }
if($stat==CS_WARLORD) { $funcs=$battles.$konvents.$finances; $jobs='Wie ein General den Cluster durch Kriege f&uuml;hren!'; }
if($stat==CS_KONVENTIONIST) { $funcs=$konvents.$finances; $jobs='Durch Verhandlungen, Zahlungen und Vertr&auml;ge den politischen Status des Clusters bestimmen.'; }
if($stat==CS_SUPPORTER) { $funcs=$finances; $jobs='Schwache Cluster-Mitglieder unterst&uuml;tzen.'; }
if($stat==CS_MITGLIEDERMINISTER) { $funcs=$req_verw; $jobs='Aufname-Antr&auml;ge pr&uuml;fen.'; }

if($stat>CS_MEMBER) $jobs='<tr>'.LF.'<th>Aufgaben:</th>'.LF.'<td>'.$jobs.'</td>'.LF.'</tr>'."\n";

if($funcs!="") $funcs='<tr>'.LF.'<th>Funktionen:</th>'.LF.'<td>'.$funcs.'</td>'.LF.'</tr>'."\n";

$members=mysql_num_rows(db_query('SELECT id FROM users WHERE cluster=\''.$clusterid.'\''));

if($members>0 && $cluster['points']>0) $av=round($cluster['points']/$members,2); else $av=0;

$money=number_format((int)$cluster['money'],0,',','.');

$clusterstat=cscodetostring($usr['clusterstat']);

while(list($bez,$val)=each($cluster)) {
$cluster[$bez]=safeentities($val);
}

echo '<div id="cluster-overview">
<h3>'.$cluster['name'].'</h3>
<table width="90%">
'.$img.'<tr id="cluster-overview-board1">
<td colspan="2"><a href="cboard.php?page=board&amp;sid='.$sid.'">Zum Cluster-Board</a></td>
</tr>
<tr>
<th>Name:</th>
<td>'.$cluster['name'].'</td>
</tr>
<tr>
<th>Code:</th>
<td>'.$cluster['code'].'</td>
</tr>
<tr>
<th>Mitglieder (<a href="cluster.php?page=listmembers&amp;cluster='.$usr['cluster'].'&amp;sid='.$sid.'">anzeigen</a>):</th>
<td>'.$members.'
(<a href="cluster.php?page=leave&amp;sid='.$sid.'">Austreten</a>)</td>
</tr>
<tr>
<th>Punkte</th>
<td>'.number_format($cluster['points'],0,',',',').'</td>
</tr>
<tr>
<th>Durchschnitt:</th>
<td>'.$av.' Punkte pro User</td>
</tr>
<tr>
<th>Dein Status:</th>
<td>'.$clusterstat.'</td>
</tr>
'.$jobs.$funcs.'<tr>
<th>Verm&ouml;gen:</th>
<td>'.$money.' Credits</td>
</tr>
<tr>
<th>Mitgliedsbeitrag:</th>
<td>'.$cluster['tax'].' Credits pro Tag pro User</td>
</tr>
<tr id="cluster-overview-events">
<th>Ereignisse:</th>
<td><div>'.$list.'</div></td>
</tr>
<tr id="cluster-overview-board2">
<td colspan="2"><a href="cboard.php?page=board&amp;sid='.$sid.'">Zum Cluster-Board</a></td>
</tr>
</table>
</div>';

if($usr['clusterstat']==CS_ADMIN || $usr['clusterstat']==CS_COADMIN):
$cluster['notice']=html_entity_decode($cluster['notice']);
echo '<div id="cluster-notice-create">
<h3>Aktuelle Notiz</h3>
<form action="cluster.php?sid='.$sid.'&amp;page=savenotice" method="post">
<table>
<tr><th>Text:</th><td><textarea name="notice" rows="4" cols="30">'.$cluster['notice'].'</textarea></td></tr>
<tr><th>Aktionen:</th><td><input type="submit" value="Speichern" />
<input type="button" onclick="this.form.notice.value=\'\';this.form.submit();" value="L&ouml;schen" />
</td></tr>
</table>
</form>
</div>';
endif;

echo '<div id="cluster-distributed-attacks">
<h3>Distributed Attacks</h3><br />';
if($usr['da_avail']=='yes')
{
$pc=getpc($pcid);
if(isavailh('da',$pc)==true) echo '<p><a href="distrattack.php?sid='.$sid.'&amp;page=create">Neue Distributed Attack erstellen</a></p>'."\n";
else echo '<p>Von diesem PC aus kannst du keine DA erstellen!</p>'."\n";
}
echo '<p><a href="distrattack.php?sid='.$sid.'&amp;page=list">Vorhandene Distributed Attacks anzeigen</a></p>'."\n";

echo '</div>'.LF;

echo '<div id="cluster-overview-infotext"><h3>Aktuelle Clusterbeschreibung</h3><p>'.nl2br(safeentities($cluster['infotext'])).'</p></div>'."\n";

ext_conventlist();

echo '</div>';
createlayout_bottom();
break;

case 'delconvent': //----------------- DELETE CONVENT -------------------------
if($usr['clusterstat']!=CS_ADMIN && $usr['clusterstat']!=CS_WARLORD &&
  $usr['clusterstat']!=CS_KONVENTIONIST && $usr['clusterstat']!=CS_COADMIN)
{
  simple_message('Du hast dazu keine Rechte!');
}


$c=explode('-',$_REQUEST['convent']);
$c[0]=(int)$c[0];
$c[1]=(int)$c[1];

$sql='FROM cl_pacts WHERE cluster='.$clusterid.' AND partner='.mysql_escape_string($c[1]).' AND convent='.mysql_escape_string($c[0]).' LIMIT 1';
$r=db_query('SELECT * '.$sql.';');
if(@mysql_num_rows($r)==1) {
  db_query('DELETE '.$sql.';');

  $convent=cvcodetostring($c[0]);
  $dat=getcluster($c[1]);

  $dat[events]=nicetime4().' Der Cluster [cluster='.$clusterid.']'.$cluster['code'].'[/cluster] hat <i>'.$convent.'</i> mit euch annulliert!'.LF.$dat['events'];
  db_query('UPDATE clusters SET events=\''.mysql_escape_string($dat['events']).'\' WHERE id='.mysql_escape_string($dat['id']));

  $cluster['events']=nicetime4().' [usr='.$usrid.']'.$usr['name'].'[/usr] annulliert <i>'.$convent.'</i> mit dem Cluster [cluster='.$dat['id'].']'.$dat['code'].'[/cluster]!'.LF.$cluster['events'];

  $x=explode("\n",$cluster['events']);
  if(count($x)>21) $cluster['events']=joinex(array_slice($x,0,20),"\n");

  db_query('UPDATE clusters SET events=\''.mysql_escape_string($cluster['events']).'\' WHERE id='.$clusterid);
}

header('Location: cluster.php?sid='.$sid.'&page=convents&ok='.urlencode('Der Vertrag wurde annulliert.'));

break;


case 'convents': //----------------- CONVENTS -------------------------
if($usr['clusterstat']==CS_ADMIN || $usr['clusterstat']==CS_WARLORD ||
  $usr['clusterstat']==CS_KONVENTIONIST || $usr['clusterstat']==CS_COADMIN) {

#simple_message('Die Vertr&auml;ge-Verwaltung ist heute morgen nicht verf&uuml;gbar. Probier es heute nachmittag nochmal.');
#exit;

createlayout_top('HackTheNet - Cluster - Vertr&auml;ge');
echo '<div class="content" id="cluster">
<h2>Cluster</h2>
'.$notif.'<div id="cluster-create-convent">
<h3>Vertrag erstellen</h3>
'.$xxx.'
<form action="cluster.php?page=saveconvents&amp;sid='.$sid.'" method="post">
<table>
<tr>
<th>Vertrags-Partner (Code):</th>
<td><input type="text" name="partner" maxlength="12" /></td>
</tr>
<tr>
<th>Vertrags-Art:</th>
<td><select name="type">
<option value="1">Kriegserkl&auml;rung</option>
<option value="2">Beistandsvertrag</option>
<option value="3">Friedensvertrag</option>
<option value="5">Nicht-Angriffs-Pakt</option>
<option value="6">Wing-Treaty</option>
</select></td>
</tr>
<tr id="cluster-create-convent-confirm">
<td colspan="2"><input type="submit" value="Erstellen" /></td>
</tr>
</table>
</form>
</div>';

ext_conventlist();

echo '</div>'."\n";
createlayout_bottom();

} else no_();
break;

case 'saveconvents': //------------------------- SAVE CONVENTS -------------------------------
if($usr['clusterstat']==CS_ADMIN || $usr['clusterstat']==CS_WARLORD ||
  $usr['clusterstat']==CS_KONVENTIONIST || $usr['clusterstat']==CS_COADMIN) {

$dat=getcluster($_POST['partner'],'code');
if($dat==false) {
  $error='Ein Cluster mit dem Code '.$_POST['partner'].' existiert nicht!';
} elseif($dat['id']==$clusterid) {
  $error='Du kannst keinen Vertrag mit dem eigenen Cluster abschlie&szlig;en!';
} else {
  $type=(int)$_POST['type'];
  if($type<1 OR $type>6) { no_(); exit; }
  $convent=cvCodeToString($type);
  $cname=htmlspecialchars($dat['code']);
  $dat['events']=nicetime4().' Der Cluster [cluster='.$clusterid.']'.$cluster['code'].'[/cluster] hat <i>'.$convent.'</i> mit euch eingetragen.'.LF.$dat['events'];
  db_query('UPDATE clusters SET events=\''.mysql_escape_string($dat['events']).'\' WHERE id='.mysql_escape_string($dat['id']));
  db_query('INSERT INTO cl_pacts VALUES ('.$clusterid.', '.mysql_escape_string($type).', '.mysql_escape_string($dat['id']).');');
  $cluster[events]=nicetime4().' [usr='.$usrid.']'.$usr['name'].'[/usr] tr&auml;gt <i>'.$convent.'</i> mit dem Cluster [cluster='.$dat['id'].']'.$cname.'[/cluster] ein.'.LF.$cluster['events'];
  db_query('UPDATE clusters SET events=\''.mysql_escape_string($cluster['events']).'\' WHERE id='.$clusterid);
  $ok='Der Vertrag wurde abgeschlossen.';
}
header('Location: cluster.php?page=convents&sid='.$sid.'&'.($ok!='' ? 'ok='.urlencode($ok) : 'error='.urlencode($error)));

} else no_();
break;

case 'savefincances': //------------------------- SAVE FINANCES -------------------------------
if($usr['clusterstat']==CS_ADMIN || $usr['clusterstat']==CS_COADMIN) {
$tax=(int)$_REQUEST['tax'];
if(time() < $transfer_ts && $server == $t_limit_server && $tax>100)
{
  $tax=100;
}
if($tax >= 0)
{
  $cluster['events']=nicetime4().' [usr='.$usrid.']'.$usr['name'].'[/usr] setzt Mitgliedsbeitrag auf '.mysql_escape_string($tax).' Credits pro Tag'.LF.$cluster['events'];
  db_query('UPDATE clusters SET events=\''.mysql_escape_string($cluster['events']).'\',tax='.mysql_escape_string($tax).' WHERE id='.$clusterid);
  header('Location: cluster.php?page=finances&sid='.$sid.'&ok='.urlencode('Die &Auml;nderungen wurden &uuml;bernommen.'));
} else {
  header('Location: cluster.php?page=finances&sid='.$sid.'&error='.urlencode('Bitte eine Zahl eingeben.'));
}
} else no_();
break;

case 'finances': //------------------------- FINANCES -------------------------------
if($usr['clusterstat']==CS_ADMIN || $usr['clusterstat']==CS_WARLORD ||
  $usr['clusterstat']==CS_KONVENTIONIST || $usr['clusterstat']==CS_SUPPORTER || $usr['clusterstat']==CS_COADMIN) {

$cluster['money']=(int)$cluster['money'];
$cluster['tax']=(int)$cluster['tax'];

$javascript='<script type="text/javascript">'."\n";
if($usr['bigacc']=='yes') {
$javascript.='function fill(s) { document.frm.pcip.value=s; }';
}
$javascript.='
function autosel(obj) { var i = (obj.name==\'pcip\' ? 1 : 0);
  document.frm.reciptype[i].checked=true; }
</script>';

createlayout_top('HackTheNet - Cluster - Finanzen');
echo '<div class="content" id="cluster">
<h2>Cluster</h2>
'.$notif;
if($usr['clusterstat']==CS_ADMIN || $usr['clusterstat']==CS_COADMIN) {
$fm=number_format($cluster[money],0,',','.');
echo '<div id="cluster-money">
<h3>Verm�gen</h3>
<p>Aktuelles Verm&ouml;gen des Clusters: '.$fm.' Credits.</p>
</div>
<div id="cluster-tax">
<h3>Mitgliedsbeitrag</h3>
<p>Mitgliedsbeitrag in Credits pro User pro Tag festlegen:</p>
<form action="cluster.php?page=savefincances&amp;sid='.$sid.'" method="post">
<table>
<tr>
<th>Cluster-Mitgliedsbeitrag:</th>
<td><input type="text" name="tax" maxlength="5" value="'.$cluster['tax'].'" /></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Speichern" /></td>
</tr>
</table>
</form>
</div>
';
}

if($usr['bigacc']=='yes') $bigacc='&nbsp;<a href="javascript:show_abook(\'pc\')">Adressbuch</a>';
echo '
<div id="cluster-transfers">
<h3>�berweisungen</h3>
<form action="cluster.php?page=transfer&amp;sid='.$sid.'" method="post" name="frm">
<table>
<tr>
<th>Empf&auml;nger:</th>
<td><input type="radio" checked="checked" name="reciptype" value="cluster" /> Cluster &ndash; Code: <input type="text" name="clustercode" onchange="autosel(this)" maxlength="12" /><br />
<input type="radio" name="reciptype" value="user" /> Benutzer &ndash; IP: 10.47.<input type="text" name="pcip" onchange="autosel(this)" maxlength="7" />'.$bigacc.'</td>
</tr>
<tr>
<th>Betrag:</th>
<td><input type="text" name="credits" maxlength="5" value="0" /> Credits</td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Ausf&uuml;hren" /></td>
</tr>
</table>
</form>
</div>
<div id="cluster-tax-paid">
<h3>Wer hat bezahlt?</h3>
<table>
<tr>
<th>Name</th>
<th>letzte Bezahlung</th>
</tr>
';


# Wer hat wann bezahlt...?
$r=db_query('SELECT id,name,cm FROM users WHERE cluster=\''.$clusterid.'\' ORDER BY name ASC');
while($user=mysql_fetch_assoc($r))
{
  if($user['cm']==strftime('%d.%m.')) $user['cm']='heute'; elseif($user['cm']==strftime('%d.%m.',time()-86400)) $user['cm']='gestern';
  echo '<tr>'.LF.'<td><a href="user.php?page=info&amp;user='.$user['id'].'&amp;sid='.$sid.'">'.$user['name'].'</a></td><td>'.$user['cm'].'</td></tr>';
}
echo '</table>'.LF.'</div>'.LF.'</div>'.LF;

createlayout_bottom();

} else no_();
break;

case 'members': //------------------------- MEMBERS -------------------------------
if($usr['clusterstat'] == CS_ADMIN)
{

createlayout_top('HackTheNet - Cluster');
echo '<div class="content" id="cluster">
<h2>Cluster</h2>
'.$notif.'<div id="cluster-member-administration">
<h3>Mitglieder-Verwaltung</h3>
<form action="cluster.php?page=savemembers&amp;sid='.$sid.'" method="post">
<table>
<tr>
<th>Name</th>
<th>Punkte</th>
<th>Status</th>
<th>Letztes Log In</th>
<th>Ausschlie&szlig;en?</th>
</tr>
';

function stat_list_item($id,$c) {
echo '<option value="'.$id.'"'.($c==$id?' selected="selected">':'>').cscodetostring($id).'</option>';
}

$r=db_query('SELECT * FROM users WHERE cluster=\''.$clusterid.'\' ORDER BY name ASC');

while($udat=mysql_fetch_assoc($r))
{
  $uix=$udat['id'];
  if($uix==$usrid) continue;
  echo '<tr>'.LF.'<td><a href="user.php?page=info&amp;user='.$uix.'&amp;sid='.$sid.'">'.$udat['name'].'</a></td>'.LF.'<td>'.number_format($udat['points'],0,',','.').'</td>'.LF.'<td>';
  echo '<select name="stat'.$uix.'">';
  stat_list_item(CS_MEMBER,$udat['clusterstat']);
  stat_list_item(CS_ADMIN,$udat['clusterstat']);
  stat_list_item(CS_COADMIN,$udat['clusterstat']);
  stat_list_item(CS_WAECHTER,$udat['clusterstat']);
  stat_list_item(CS_JACKASS,$udat['clusterstat']);
  stat_list_item(CS_WARLORD,$udat['clusterstat']);
  stat_list_item(CS_KONVENTIONIST,$udat['clusterstat']);
  stat_list_item(CS_SUPPORTER,$udat['clusterstat']);
  stat_list_item(CS_MITGLIEDERMINISTER,$udat['clusterstat']);
  echo '</select></td>'.LF.'<td>'.nicetime3($udat['login_time']).'</td>'.LF.'<td><input type="checkbox" value="yes" name="kick'.$uix.'" /></td></tr>';
}

echo '<tr>
<td colspan="5"><input type="submit" value="Speichern" /></td>
</tr>
</table>
</form>
</div>
</div>
';
createlayout_bottom();

} else no_();
break;

case 'savemembers': //-------------------- SAVE MEMBERS ------------------
if($usr['clusterstat']==CS_ADMIN)
{
  
  $r=db_query('SELECT id,name,clusterstat FROM users WHERE cluster=\''.$clusterid.'\' ORDER BY name ASC');
  
  while($udat=mysql_fetch_assoc($r))
  {
    $uix=$udat['id'];
    if($uix==$usrid) continue;
    if($_POST['kick'.$uix]=='yes') { # User aus dem Cluster schmei&szlig;en?
      db_query('UPDATE users SET cluster=\'\',cm=\'\',clusterstat=0 WHERE id='.mysql_escape_string($uix));
      $cluster['events']=nicetime4().' [usr='.$udat['id'].']'.$udat['name'].'[/usr] wird durch [usr='.$usrid.']'.$usr['name'].'[/usr] aus dem Cluster ausgeschlossen.'.LF.$cluster['events'];
      addsysmsg($udat['id'],'Du wurdest durch [usr='.$usrid.']{'.$usr['name'].'[/usr] aus dem Cluster [cluster='.$clusterid.']'.$cluster['code'].'[/cluster] ausgeschlossen!');
    } else {
      $stat=(int)$_REQUEST['stat'.$uix];
      if($udat['clusterstat']!=$stat) {
        db_query('UPDATE users SET clusterstat=\''.mysql_escape_string($stat).'\' WHERE id='.mysql_escape_string($uix));
        $cluster['events']=nicetime4().' [usr='.$udat['id'].']'.$udat['name'].'[/usr] erh&auml;lt durch [usr='.$usrid.']'.$usr['name'].'[/usr] den Status '.cscodetostring($stat).'.'.LF.$cluster['events'];
      }
    }
  }
  
  $x=explode("\n",$cluster['events']);
  if(count($x)>21) $cluster['events']=joinex(array_slice($x,0,20),"\n");
  db_query('UPDATE clusters SET events=\''.mysql_escape_string($cluster['events']).'\' WHERE id='.$clusterid);
  
  header('Location: cluster.php?page=members&sid='.$sid.'&ok='.urlencode('Die &Auml;nderungen wurden &uuml;bernommen!'));
} else no_();
break;

case 'config': //------------------------- CONFIG -------------------------------
if($usr['clusterstat'] == CS_ADMIN || $usr['clusterstat'] == CS_COADMIN)
{
  
  while(list($bez,$val)=each($cluster))
  {
    $cluster[$bez]=safeentities(html_entity_decode($val));
  }
  
  $anch=($cluster['acceptnew']=='yes' ? ' checked="checked"' : '');
  
  createlayout_top('HackTheNet - Cluster');
  echo '<div class="content" id="cluster">
  <h2>Cluster</h2>
  '.$notif.'<div id="cluster-settings">
  <h3>Cluster-Einstellungen</h3>
  <form action="cluster.php?page=savecfg&amp;sid='.$sid.'" method="post">
  <table>
  <tr>
  <th>Cluster-Name:</th>
  <td><input type="text" name="name" maxlength="48" value="'.$cluster['name'].'" /></td>
  </tr>
  <tr>
  <th>Cluster-Code:</th>
  <td><input type="text" name="code" maxlength="12" value="'.$cluster['code'].'" /></td>
  </tr>
  <tr>
  <th>Neue Mitglieder?</th>
  <td><input name="acceptnew" value="yes" type="checkbox"'.$anch.' /> Sollen Spieler Mitgliedsantr&auml;ge stellen d&uuml;rfen, um dem Cluster beizutreten?</td>
  </tr>
  <tr>
  <th>Beschreibung:</th>
  <td><textarea rows="10" cols="50" name="about">'.$cluster['infotext'].'</textarea></td>
  </tr>
  <tr>
  <th>Namen der Ordner im Cluster-Board:</th>
  <td>Ordner 1:<br />
  <input type="text" name="box0" value="'.$cluster['box1'].'" maxlength="30" /><br />
  Ordner 2:<br />
  <input type="text" name="box1" value="'.$cluster['box2'].'" maxlength="30" /><br />
  Ordner 3:<br />
  <input type="text" name="box2" value="'.$cluster['box3'].'" maxlength="30" /></td>
  </tr>
  <tr>
  <th>Logo-Datei:</th>
  <td><input type="text" name="logofile" value="'.$cluster['logofile'].'" /><br />Eine Internet-Adresse mit http:// eingeben.</td>
  </tr>
  <tr>
  <th>Homepage:</th>
  <td><input type="text" name="homepage" value="'.$cluster['homepage'].'" /><br />Eine Internet-Adresse mit http:// eingeben.</td>
  </tr>
  <tr>
  <th>Cluster l&ouml;schen:</th>
  <td><input name="delete" value="yes" type="checkbox" /></td>
  </tr>
  <tr>
  <td colspan="2"><input type="submit" value="Speichern" /></td>
  </tr>
  </table>
  </form>
  </div>
  </div>
  ';
  createlayout_bottom();

} else no_();
break;

case 'delcluster':
if($usr['clusterstat'] != CS_ADMIN)
{
  no_();
  exit;
}
if($_POST['delete']=='yes')
{
  $r=db_query('SELECT id FROM users WHERE cluster=\''.$clusterid.'\';');
  while($data=mysql_fetch_assoc($r))
  {
    addsysmsg($data['id'], 'Dein Cluster '.$cluster['code'].' wurde gel&ouml;scht! Das passierte durch [usr='.$usrid.']'.$usr['name'].'[/usr] ('.cscodetostring($usr['clusterstat']).')');
  }
  deletecluster($usr['cluster']);
  db_query('INSERT INTO logs SET type=\'delcluster\', usr_id=\''.$usrid.'\', payload=\''.mysql_escape_string($usr['name']).' deletes '.mysql_escape_string($cluster['code']).'\';');
}
break;

case 'savecfg': //------------------------- SAVE CONFIG -------------------------------
if($usr['clusterstat']!=CS_ADMIN && $usr['clusterstat']!=CS_COADMIN)
{
  no_();
}

if($_POST['delete']=='yes')
{
  
  if($usr['clusterstat'] != CS_ADMIN)
  {
    simple_message('Nur Clusteradmins k�nnen Cluster l�schen!');
    exit;
  }
  
  createlayout_top();
  echo '<div class="content" id="cluster">
  <h2>Cluster l&ouml;schen</h2>
  <h3>Bitte best&auml;tigen!</h3>
  <form action="cluster.php?page=delcluster&amp;sid='.$sid.'" method="post">
  <p><strong>Setz den Haken und klick auf "Weiter" um den Cluster endg&uuml;ltig zu l&ouml;schen!</strong></p>
  <p><input type="checkbox" value="yes" name="delete" /></p>
  <p><input type="submit" value=" Weiter " /></p>
  </form>
  </div>';
  createlayout_bottom();

}
else
{
  
  $name=$_POST['name']; $code=$_POST['code'];
  $text=$_POST['about']; $logo=str_replace('\\','/',$_POST['logofile']);
  $hp=str_replace('\\','/',$_POST['homepage']);
  $acceptnew=($_POST['acceptnew']=='yes'?'yes':'no');
  
  $msg=''; $e=false;
  if(trim($code)=='') { $e=true; $msg.='Das Feld Code muss ein K&uuml;rzel f&uuml;r den Cluster enthalten!<br />'; }
  if(trim($name)=='') { $e=true; $msg.='Das Feld Name muss einen Namen f&uuml;r den Cluster enthalten!<br />'; }
  if(preg_match('/[;<>"]/',$name)!=false) { $e=true; $msg.='Der Name darf nicht die Zeichen ; &lt; &gt; &quot; enthalten!<br />'; }
  if(preg_match('/[;<>"]/',$code)!=false) { $e=true; $msg.='Der Code darf nicht die Zeichen ; &lt; &gt; &quot; enthalten!<br />'; }
  if(eregi('http://.*/.*',$logo)==false) { $logo=''; }
  if(eregi('http://.*',$hp)==false) { $hp=''; }
  if($code!=$cluster['code']) {
    $c=getcluster($code,'code');
    if($c!=false && $c['id']!=$cluster['id']) {
      $e=true;
      $msg='Ein Cluster mit diesem Code existiert bereits! Bitte einen anderen w�hlen!';
    }
  }
  
  if($e==true) {
    
    header('Location: cluster.php?page=config&error='.urlencode($msg).'&sid='.$sid);
  
  } else {
    while(list($bez,$val)=each($_POST)) $_POST[$bez]=html_entity_decode($val);
    $cluster['box1']=safeentities($_POST['box0']);
    $cluster['box2']=safeentities($_POST['box1']);
    $cluster['box3']=safeentities($_POST['box2']);
    $cluster['name']=$name;
    $cluster['code']=$code;
    $cluster['acceptnew']=$acceptnew;
    $cluster['infotext']=safeentities($text);
    $cluster['logofile']=safeentities($logo);
    $cluster['homepage']=safeentities($hp);
    savemycluster();
    header('Location: cluster.php?page=config&ok='.urlencode('Die ge&auml;nderten Einstellungen wurden &uuml;bernommen!').'&sid='.$sid);
  }

}
break;

case 'found': //------------------------- FOUND -------------------------------
$code=trim($_POST['code']);
$name=trim($_POST['name']);

$msg=''; $e=false;
if(trim($code)=='') { $e=true; $msg.='Das Feld Code muss ein K&uuml;rzel f&uuml;r den Cluster enthalten!<br />'; }
if(trim($name)=='') { $e=true; $msg.='Das Feld Name muss einen Namen f&uuml;r den Cluster enthalten!<br />'; }
if(eregi('(;|\<|\>|\\")',$name)!=false) { $e=true; $msg.='Der Name darf nicht die Zeichen ; &lt; &gt; &quot; enthalten!<br />'; }
if(eregi('(;|\<|\>|\\")',$code)!=false) { $e=true; $msg.='Der Code darf nicht die Zeichen ; &lt; &gt; &quot; enthalten!<br />'; }


if(! (strlen($code)<=12 and strlen($name)<=48 and strlen($pwd)<=16)) { $e=true; $msg.='Bitte alle drei Felder ausf&uuml;llen!<br />'; }

if($e==false) {

$x=getcluster($code,'code');
if($x===false) {

$events=nicetime2().' Der Cluster wird durch '.$usr['name'].' gegr&uuml;ndet!';
$r=db_query('INSERT INTO clusters(id, name, code, events)  VALUES(0, \''.mysql_escape_string($name).'\', \''.mysql_escape_string($code).'\', \''.mysql_escape_string($events).'\');');
$id=mysql_insert_id();

setuserval('cluster',$id);
setuserval('clusterstat',CS_ADMIN);

$pcs=count(explode(',',$usr['pcs']));
db_query('INSERT INTO rank_clusters VALUES(0,'.mysql_escape_string($id).',1,'.$usr['points'].','.$usr['points'].','.mysql_escape_string($pcs).','.mysql_escape_string($pcs).',0)');

header('Location: cluster.php?page=start&sid='.$sid);
} else simple_message('Ein Cluster mit diesem K&uuml;rzel existiert bereits!');

} else { createlayout_top(); echo '<div class="error"><h3>Fehler</h3><p>'.$msg.'</p></div>'; createlayout_bottom(); }

break;

case 'join': //------------------------- JOIN -------------------------------

$x=GetCluster((int)$_REQUEST['cluster']);

if($x!==false)
{

$r=db_query('SELECT * FROM cl_reqs WHERE cluster='.mysql_escape_string($x['id']).' AND dealed=\'yes\' AND user='.$usrid);
if(@mysql_num_rows($r)<1) { simple_message('Der Antrag ist abgelaufen!'); exit; }
db_query('DELETE FROM cl_reqs WHERE cluster='.mysql_escape_string($x['id']).' AND dealed=\'yes\' AND user='.$usrid);


$members = mysql_num_rows(db_query('SELECT id FROM users WHERE cluster=\''.mysql_escape_string($x['id']).'\''));
if($members < MAX_CLUSTER_MEMBERS)
{
  
  $oldcluster=getcluster($usr['cluster']);
  if($oldcluster!==false) {
    $oldcluster['events']=nicetime4().' [usr='.$usrid.']'.$usr['name'].'[/usr] verl&auml;sst den Cluster und wechselt zu [cluster='.$x['id'].']'.$x['code'].'[/cluster].'.LF.$oldcluster['events'];
    db_query('UPDATE clusters SET events=\''.mysql_escape_string($oldcluster['events']).'\' WHERE id='.$oldcluster['id'].';');
  }
  
  $x['events']=nicetime4().' [usr='.$usrid.']'.$usr['name'].'[/usr] tritt dem Cluster bei.'.LF.$x['events'];
  db_query('UPDATE clusters SET events=\''.mysql_escape_string($x['events']).'\' WHERE id='.mysql_escape_string($x['id']).';');
  
  setuserval('cm','');
  setuserval('cluster',$x['id']);
  setuserval('clusterstat',CS_MEMBER);
  
  header('Location: cluster.php?page=start&sid='.$sid);

} else simple_message('Dieser Cluster hat die maximale Mitgliedszahl von '.MAX_CLUSTER_MEMBERS.' Benutzern schon erreicht!');

}
break;

case 'leave': //------------------------- LEAVE -------------------------------
createlayout_top('HackTheNet - Cluster');
#$r=db_query('SELECT id FROM users WHERE cluster='.$clusterid.';');
#$members=mysql_num_rows($r);
$r=db_query('SELECT id FROM users WHERE cluster='.$clusterid.' AND clusterstat='.(CS_ADMIN).';');
$admins=mysql_num_rows($r);
if($usr['clusterstat']==CS_ADMIN && $admins<2) {
echo '<h3>Cluster verlassen</h3>
<p><div class="error"><h3>Verweigert</h3><p>Du kannst den Cluster nicht verlassen, da du der letzte Admin bist!<br />Du musst den Cluster in den Cluster-Einstellungen aufl&ouml;sen!</p></div>';
} else {
echo '<div class="content" id="cluster">
<h2>Cluster</h2>
'.$notif.'<div id="cluster-leave">
<h3>Cluster verlassen</h3>
<p><strong>Wenn du wirklich den Cluster verlassen willst, dann klick auf den Button!</strong></p>
<form action="cluster.php?page=do_leave&amp;sid='.$sid.'" method="post">
<p><input type="submit" value="Austreten" name="subm" /></p>
</form>
';
}
createlayout_bottom();
break;

case 'do_leave': //------------------------- DO LEAVE -------------------------------

$r=db_query('SELECT id FROM users WHERE cluster='.$clusterid.' AND clusterstat='.(CS_ADMIN).';');
$admins=mysql_num_rows($r);
if($usr['clusterstat']==CS_ADMIN && $admins<2) exit;

$cluster['events']=nicetime4().' [usr='.$usrid.']'.$usr['name'].'[/usr] verl&auml;sst den Cluster!'.LF.$cluster['events'];
setuserval('cluster','');
setuserval('cm','');
setuserval('clusterstat',CS_MEMBER);

db_query('UPDATE clusters SET events=\''.mysql_escape_string($cluster['events']).'\' WHERE id='.$clusterid);

header('Location: cluster.php?page=start&sid='.$sid);

break;

case 'listmembers': //------------------------- LIST MEMBERS -------------------------------
$c=$_REQUEST['cluster'];
$st=$_REQUEST['sortby'];
$sel=' selected="selected"';
switch($st) {
case 'points': $st='points DESC'; $ch2=$sel; break;
case 'stat': $st='clusterstat DESC'; $ch3=$sel;  break;
case 'lastlogin': $st='login_time DESC'; $ch4=$sel; break;
default: $ch1=$sel; $st='name ASC';
}
$c=getcluster($c);
if($c!==false) {

createlayout_top('HackTheNet - Cluster - Mitglieder');

$members='';
$r=db_query('SELECT * FROM users WHERE cluster=\''.mysql_escape_string($c['id']).'\' ORDER BY '.mysql_escape_string($st).';');
while($member=mysql_fetch_assoc($r))
{
  if($member!==false && (is_noranKINGuser($member['id'])==false || $c['id']==$no_ranking_cluster)) {
    $lli=$member['login_time'];
    if($lli >= (time()-24*60*60)) $clr='darkgreen';
    elseif($lli >= (time()-72*60*60)) $clr='darkorange';
    else $clr='darkred';
    $lli='<span style="color:'.$clr.';">'.nicetime3($lli).'</span>';
    if($member['sid_lastcall'] > time() - SID_ONLINE_TIMEOUT) $online='<span style="color:green;">Online</span>';
      else $online='<span style="color:red;">Offline</span>';
    $members.='<tr>'.LF.'<td><a href="user.php?page=info&amp;user='.$member['id'].'&amp;sid='.$sid.'">'.$member['name'].'</a></td>'.LF.'<td>'.cscodetostring($member['clusterstat']).'</td>'.LF.'<td>'.number_format($member['points'],0,',','.').'</td>'.LF.'<td>'.$online.'</td>'.LF.'<td>'.$lli.'</td>'.LF.'</tr>'.LF;
  }
  $lli='';
}

$short=htmlspecialchars($c['code']);
echo '<div class="content" id="cluster">
<h2>Cluster</h2>
<div id="cluster-members">
<h3>Mitglieder von '.$short.'</h3>
<form action="cluster.php?sid='.$sid.'&amp;page=listmembers&amp;cluster='.$c['id'].'" method="post">
<p><strong>Ordnen nach:</strong>&nbsp;<select name="sortby" onchange="this.form.submit()">
  <option value="name"'.$ch1.'>Name</option>
  <option value="points"'.$ch2.'>Punkte</option>
  <option value="stat"'.$ch3.'>Rang</option>
  <option value="lastlogin"'.$ch4.'>Letztes LogIn</option>
</select></p>
</form>
<table>
<tr>
<th>Name</th>
<th>Rang</th>
<th>Punkte</th>
<th>Status</th>
<th>Letztes Log In</th>
</tr>
'.$members.'</table>
</div>
</div>
';
createlayout_bottom();
} else simple_message('Diesen Cluster gibt es nicht!');
break;

case 'battles': //------------------------- BATTLES -------------------------------


if($usr['clusterstat']==CS_ADMIN || $usr['clusterstat']==CS_WARLORD ||
  $usr['clusterstat']==CS_WAECHTER || $usr['clusterstat']==CS_COADMIN) {

function xpcinfo($item,$ix_usrid,$ix_pcid) {
global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $cluster,$clusterid,$sid;
static $usr_cache,$cluster_cache,$pc_cache;

    $tmp=$ix_pcid;
    if(isset($pc_cache[$tmp])==false) {
      $p=getpc($tmp);
      $pc_cache[$tmp]=$p;
    } else $p=$pc_cache[$tmp];
    echo '<td><strong>10.47.'.$p['ip'].'</strong>';

    $tmp=$ix_usrid;
    if(isset($usr_cache[$tmp])==false) {
      $u=getuser($ix_usrid);
      $usr_cache[$tmp]=$u;
    } else $u=$usr_cache[$tmp];

    if($u!==false) {
      echo ' von <a href="user.php?page=info&amp;sid='.$sid.'&amp;user='.$u['id'].'">'.$u['name'].'</a>';
      if($u['cluster']!=$clusterid) {

        $tmp=(int)$u['cluster'];
        if(isset($cluster_cache[$tmp])==false) {
          $c=getcluster($u['cluster']);
          $cluster_cache[$tmp]=$c;
        } else $c=$cluster_cache[$tmp];

        if($c!==false) {
          echo ' (<a href="cluster.php?page=info&amp;sid='.$sid.'&amp;cluster='.$u['cluster'].'">'.$c['code'].'</a>)</td>'."\n";
        } else echo '</td>'."\n";
      } else {
        echo '</td>'."\n";
      }
    } else echo '</td>'."\n";
}

function battle_table($dir) {
global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $cluster,$clusterid;

echo '<table>
<tr>
<th>Zeit</th>
<th>Angreifer</th>
<th>Opfer</th>
<th>Waffe</th>
<th>Erfolg</th>
</tr>
';

$ts=time()-24*60*60;
$r=db_query('SELECT * FROM attacks WHERE '.($dir=='in'?'to_cluster':'from_cluster').'='.$clusterid.' AND time>='.$ts.' ORDER BY time DESC;');

while($data=mysql_fetch_assoc($r))
{
  echo '<tr>'."\n";

  echo '<td>'.nicetime2($data['time']).'</td>'."\n";

  if($dir=='out' || $data['noticed']==1) {
    xpcinfo($data,$data['from_usr'],$data['from_pc']);
  } else echo '<td>?</td>'."\n";

  #if($dir=='in') {
    xpcinfo($data,$data['to_usr'],$data['to_pc']);
  #} else echo '<td>?</td><td>?</td><td>?</td>';

  $ia=array('scan'=>'Remote Scan', 'trojan'=>'Trojaner', 'smash'=>'Remote Smash',
    'block'=>'Remote Block', 'hijack'=>'Remote Hijack');

  $data['opt']=strtoupper($data['opt']);
  switch($data['type']) {
    case 'trojan':
      $s.=' (<tt>'.$data['opt'].'</tt>).';
    break;
    case 'smash':
      $s.=' mit der Option <tt>'.$data['opt'].'</tt>.';
    break;
  }

  $s=$ia[$data['type']];
  echo '<td>'.$s.'</td>';

  if($data['success']==1) {
    if($dir=='in') $c='red'; else $c='green';
    $s='<span style="color:$c;font-weight:bold;">Ja</span>';
  } else {
    if($dir=='out') $c='red'; else $c='green';
    $s='<span style="color:$c;font-weight:bold;">Nein</span>';
  }

  echo '<td>'.$s.'</td>';

  echo '</tr>';
}

echo '</table>';
}

createlayout_top('HackTheNet - Cluster');
echo '<div class="content" id="cluster">'."\n";
echo '<h2>Cluster</h2>'."\n";
echo '<div id="cluster-battles">'."\n";
echo '<h3>Angriffs&uuml;bersicht</h3>'."\n\n";
echo '<p>Es werden alle Angriffe der letzten 24 Stunden angezeigt</p>'."\n";
echo '<p><strong>Angriffe <em>durch</em> Mitglieder des Clusters</strong></p>'."\n";
battle_table('out');
echo '<br /><p><strong>Angriffe <em>auf</em> Mitglieder des Clusters</strong></p>'."\n";
battle_table('in');

createlayout_bottom();
} else no_();
break;

case 'info': //------------------------- INFO -------------------------------

$c=$_REQUEST['cluster'];
$cluster=getcluster($c,'id');
if($cluster!==false) {
createlayout_top('HackTheNet - Cluster-Profil');
echo '<div class="content" id="cluster-profile">
<h2>Cluster-Profil</h2>
<div id="cluster-profile-profile">
<h3 id="cluster-profile-code">'.$cluster['code'].'</h3>
';
if(eregi('http://.*/.*',$cluster['logofile'])) {
  if($usr['sid_ip']!='noip') {
    $img=$cluster['logofile'];
    $img='<tr>'.LF.'<td colspan="2" align="center"><img src="'.$img.'" alt="Logo" /></td>'.LF.'</tr>'."\n";
  }
}
if(eregi('http://.*',$cluster['homepage'])) {
  $hp=dereferurl($cluster['homepage']);
  $hp='<tr>'.LF.'<th>Homepage:</th>'.LF.'<td><a href="'.$hp.'">'.$cluster['homepage'].'</a></td>'.LF.'</tr>'."\n";
}

$members=mysql_num_rows(db_query('SELECT id FROM users WHERE cluster=\''.mysql_escape_string($cluster['id']).'\''));

if($members>0 && $cluster['points']>0) $av=round($cluster['points']/$members,2); else $av=0;

$text=nl2br($cluster['infotext']);

if($usr['stat']>10) {
$text.='</td></tr><tr class="greytr2"><td>SONDER-FUNKTIONEN</td><td><a href="secret.php?sid='.$sid.'&page=file&type=cluster&id='.$c.'">bearbeiten</a> | <a href="secret.php?sid='.$sid.'&page=cboard&id='.$c.'">Cluster-Board</a>
| <a href="secret.php?sid='.$sid.'&page=delcluster&id='.$c.'">Cluster l�schen!!</a>';
}

if($cluster['id']!=$usr['cluster']) {
if($cluster['acceptnew']=='yes') {
  if($members<MAX_CLUSTER_MEMBERS) { $col='green'; $aufnahme='M&ouml;glich (<a href="cluster.php?page=request1&amp;sid='.$sid.'&amp;cluster='.$cluster['id'].'">Aufnahmeantrag stellen</a>)'; }
  else { $col='red'; $aufnahme='Der Cluster hat die max. Mitgliederzahl von '.MAX_CLUSTER_MEMBERS.' schon erreicht!'; }
} else { $col='red'; $aufnahme='Der Cluster akzeptiert keine neuen Mitglieder mehr!'; }
$aufnahme='<tr>'.LF.'<th>Aufnahme:</th>'.LF.'<td><span style="color:'.$col.';">'.$aufnahme.'</span></td>'.LF.'</tr>'."\n";
}

echo '
<div class="submenu"><p><a href="ranking.php?page=ranking&amp;sid='.$sid.'&amp;type=cluster&amp;id='.$c.'">Cluster in Rangliste</a></p></div>
<table>
'.$img.'<tr>
<th>Code:</th>
<td>'.$cluster['code'].'</td>
</tr>
<tr>
<th>Name:</th>
<td>'.$cluster['name'].'</td>
</tr>
<tr><th>Punkte:</th>
<td>'.$cluster['points'].'</td>
</tr>
<tr>
<th>Durchschnitt:</th>
<td>'.$av.' Punkte pro User</td>
</tr>
'.$hp.'
<tr>
<th>Mitglieder (<a href="cluster.php?page=listmembers&amp;cluster='.$c.'&amp;sid='.$sid.'">anzeigen</a>):</th>
<td>'.$members.'</td>
</tr>
<tr>
<th>Beschreibung:</th>
<td>'.$text.'</td>
</tr>
'.$aufnahme.'
</table>
</div>
';
echo conventlist($c);
echo '</div>'."\n";
createlayout_bottom();
} else simple_message('Diesen Cluster gibt es nicht!');

break;

case 'transfer': // ------------------------- TRANSFER ------------------------

if(time() < $transfer_ts && $server == $t_limit_server)
{
  simple_message('�berweisungen sind erst ab '.nicetime($transfer_ts).' erlaubt!');
  exit;
}

if($usr['clusterstat']==CS_ADMIN || $usr['clusterstat']==CS_WARLORD ||
  $usr['clusterstat']==CS_KONVENTIONIST || $usr['clusterstat']==CS_SUPPORTER
  || $usr['clusterstat']==CS_COADMIN) {

$type=$_POST['reciptype'];
$credits=human2int(trim($_POST['credits']));

$e='';
if($credits>$cluster['money']) $e='Nicht gen&uuml;gend Credits f&uuml;r &Uuml;berweisung vorhanden!';
switch($type) {
case 'user':
  $recip=GetPC($_POST['pcip'],'ip');
  if($recip===false) $e='Ein Computer mit dieser IP existiert nicht!';
  if($recip['owner']==$usrid) $e='Du kannst dir selber kein Geld &uuml;berweisen!';
break;
case 'cluster':
  $recip=$_POST['clustercode'];
  $recip=GetCluster($recip,'code');
  if($recip===false) $e='Ein Cluster mit diesem Code existiert nicht!';
  if($recip['id']==$usr['cluster']) $e='Du kannst kein Geld an deinen eigenen Cluster &uuml;berweisen!';
break;
default:
  $e='Ung&uuml;ltiger Empf&auml;nger-Typ!';
break;
}

if($credits<100) $e='Der Mindestbetrag f&uuml;r eine &Uuml;berweisung sind 100 Credits!';

if($e=='') {
$tcode=random_string(16);
$fin=0;
createlayout_top('HackTheNet - Cluster - &Uuml;berweisen');
echo '<div class="content" id="cluster">
<h2>Cluster</h2>
<div id="cluster-transfer1">
<h3>&Uuml;berweisung</h3>

<form action="cluster.php?page=transfer2&amp;sid='.$sid.'"  method="post">
<input type="hidden" name="tcode" value="'.$tcode.'">';
switch($type) {
case 'user':
  $recip_usr=getuser($recip['owner']);
  $text='<p><strong>Hiermit werden '.$credits.' Credits an den Rechner 10.47.'.$recip['ip'].', der <a href="user.php?page=info&user='.$recip['owner'].'&sid='.$sid.'">'.$recip_usr['name'].'</a> geh&ouml;rt, &uuml;berwiesen.</strong></p><br />';

  $c=GetCountry('id',$recip['country']);
  $country2=$c['name']; $in=$c['in'];
  $rest=$credits-$in;
  if($rest>0) {
    $fin=$rest;
    $text.='<p>Von diesem Betrag werden noch '.$in.' Credits Geb&uuml;hren als Einfuhr nach '.$country2.', dem Standort von 10.47.'.$recip['ip'].' abgezogen. '.$recip_usr['name'].' erh&auml;lt also noch <b>'.$rest.' Credits.</p>';
  } else {
    $text.='<p>Da der Betrag sehr gering ist, werden keine Geb&uuml;hren erhoben. '.$recip_usr['name'].' erh&auml;lt <b>'.$credits.' Credits.</p>';
    $fin=$credits;
  }

  $max=getmaxbb($recip);
  if($recip['credits']+$fin>$max) {
    $rest=$max-$recip['credits'];
    $fin=$rest;
    $credits=$rest;
    $text.='<br /><p>Da '.$recip_usr['name'].' seinen BucksBunker nicht weit genug ausgebaut hat, um das Geld zu Empfangen, werden nur <b>'.$rest.' Credits</b> (inklusive Geb&uuml;hren) &uuml;berwiesen!</p>';
  }
if($rest<1) {
  echo '<div class="error"><h3>BucksBunker voll</h3><p>Der BucksBunker von '.$recip_usr['name'].' ist voll! &Uuml;berweisung wird abgebrochen!</p></div>';
  createlayout_bottom();
  exit;
}
echo $text;

break;
case 'cluster':
  echo '<p><strong>Hiermit werden '.$credits.' Credits an den Cluster '.htmlspecialchars($recip['code']).' ('.$recip['name'].') &uuml;berwiesen.</strong></p><br />';
  $fin=$credits;
break;
}
echo '<br /><p><input type="submit" value=" Ausf&uuml;hren "></p></form>';
echo '</div></div>';
createlayout_bottom();
file_put($DATADIR.'/tmp/transfer_'.$tcode.'.txt',$type.'|'.$recip['id'].'|'.$credits.'|'.$fin);
db_query('UPDATE users SET tcode=\''.mysql_escape_string($tcode).'\' WHERE id=\''.$usrid.'\' LIMIT 1;');

} else header('Location: cluster.php?sid='.$sid.'&page=finances&error='.urlencode($e));
} else no_();
break;

case 'transfer2':  // ------------------------- TRANSFER 2 ------------------------

if($usr['clusterstat']==CS_ADMIN || $usr['clusterstat']==CS_WARLORD ||
  $usr['clusterstat']==CS_KONVENTIONIST || $usr['clusterstat']==CS_SUPPORTER || $usr['clusterstat']==CS_COADMIN) {

$code=$_REQUEST['tcode'];
$fn=$DATADIR.'/tmp/transfer_'.$code.'.txt';
if($usr['tcode']!=$code || file_exists($fn)!=true) { simple_message('&Uuml;berweisung ung&uuml;ltig! Bitte neu erstellen!'); break; }
$dat=explode('|',file_get($fn));
@unlink($fn);

if(@count($dat)==4) {
$cluster['money']-=$dat[2];
if($dat[0]=='user') {
  $recip=getpc($dat[1]);
  $recip['credits']+=$dat[3];
  db_query('UPDATE pcs SET credits=\''.mysql_escape_string($recip['credits']).'\' WHERE id='.mysql_escape_string($dat[1]));
  $s='Der Cluster [cluster='.$clusterid.']'.$cluster['code'].'[/cluster] hat dir '.$dat[2].' Credits auf deinen PC 10.47.'.$recip['ip'].' ('.$recip['name'].') &uuml;berwiesen.';
  if($dat[2]!=$dat[3]) $s.=' Abz&uuml;glich der Geb&uuml;hren hast du '.$dat[3].' Credits erhalten!';
  addsysmsg($recip['owner'],$s);
  $recip_usr=getUser($recip['owner']);
  $cluster['events']=nicetime4().' [usr='.$usrid.']'.$usr['name'].'[/usr] hat '.$dat[2].' Credits an [usr='.$recip_usr['id'].']'.$recip_usr['name'].'[/usr] �berwiesen.'.LF.$cluster['events'];
  db_query('UPDATE clusters SET money=\''.mysql_escape_string($cluster['money']).'\',events=\''.mysql_escape_string($cluster['events']).'\' WHERE id='.mysql_escape_string($cluster['id']));
  $msg='&Uuml;berweisung an 10.47.'.$recip['ip'].' ('.$recip['name'].') ausgef&uuml;hrt!';
} elseif($dat[0]=='cluster') {
  $c=getcluster($dat[1]);
  $c['money']+=$dat[3];
  $cluster['events']=nicetime4().' [usr='.$usrid.']'.$usr['name'].'[/usr] �berweist '.$dat[3].' Credits an den Cluster [cluster='.$c['id'].']'.$c['code'].'[/cluster]'.LF.$cluster['events'];
  $c[events]=nicetime4().' Der Cluster [cluster='.$clusterid.']'.$cluster['code'].'[/cluster] �berweist dem Cluster '.$dat[3].' Credits.'.LF.$c['events'];
  db_query('UPDATE clusters SET money=\''.mysql_escape_string($c['money']).'\',events=\''.mysql_escape_string($c['events']).'\' WHERE id='.mysql_escape_string($dat[1]));
  db_query('UPDATE clusters SET money=\''.mysql_escape_string($cluster['money']).'\',events=\''.mysql_escape_string($cluster['events']).'\' WHERE id='.mysql_escape_string($cluster['id']));
  $msg='Dem Cluster '.$c['code'].' wurden '.$dat[2].' Credits &uuml;berwiesen!';
}
db_query('INSERT INTO transfers VALUES(\''.$clusterid.'\', \'cluster\', \'' . $usrid . '\', \''.mysql_escape_string($dat[1]).'\', \''.mysql_escape_string($dat[0]).'\', \''.mysql_escape_string($recip['owner']).'\', \''.mysql_escape_string($dat[3]).'\', \''.time().'\');');
header('Location: cluster.php?page=finances&sid='.$sid.'&ok='.urlencode($msg));
}
} else no_();
break;

case 'request1': // ------------------------- REQUEST 1 -----------------------
$c=getcluster((int)$_REQUEST['cluster']);
$members=@mysql_num_rows(db_query('SELECT * FROM users WHERE cluster=\''.$c['id'].'\''));
if($c===false || $c['acceptnew']!='yes' || $members>=MAX_CLUSTER_MEMBERS) exit;
createlayout_top('HackTheNet - Cluster - Mitgliedsantrag');
echo '<div class="content" id="cluster">
<h2>Cluster</h2>
<div id="cluster-request-new1">
<h3>Aufnahmeantrag stellen</h3>
<p><b>Antrag auf Aufnahme in den Cluster <a href="cluster.php?sid='.$sid.'&cluster='.$c['id'].'&page=info">'.$c['code'].'</a> stellen:</b></p>
<form action="cluster.php?page=request2&sid='.$sid.'" method="post">
<input type="hidden" name="cluster" value="'.$c['id'].'">
<p>
<textarea name="comment" rows=8 cols=50>Hallo!
Ich bin '.$usr['name'].' und w&uuml;rde gerne eurem Cluster beitreten.
W&auml;re sch&ouml;n, wenn das ginge.

Also bis dann
'.$usr['name'].'</textarea><br /><br />
Du wirst dann per System-Nachricht informiert, ob du aufgenommen wurdest oder nicht.
<br /><br /><input type="submit" value=" Abschicken ">
</p>
</form>
</div></div>';
createlayout_bottom();
break;

case 'request2': // ------------------------- REQUEST 2 -----------------------
$c=getcluster((int)$_REQUEST['cluster']);
$members=@mysql_num_rows(db_query('SELECT id FROM users WHERE cluster=\''.mysql_escape_string($c['id']).'\''));
if($c===false || $c['acceptnew']!='yes' || $members>=MAX_CLUSTER_MEMBERS) exit;

db_query('INSERT INTO cl_reqs VALUES(\''.$usrid.'\', \''.mysql_escape_string($c['id']).'\', \''.mysql_escape_string(nl2br(safeentities($_POST['comment']))).'\', \'no\');');
// sql-injenction-bug fixed (8.11.2004)

createlayout_top('HackTheNet - Cluster - Mitgliedsantrag');
echo '<div class="content" id="cluster">
<h2>Cluster</h2>
<div id="cluster-request-new2">
<h3>Aufnahmeantrag stellen</h3>
<p><b>Der Antrag auf Aufnahme in den Cluster <a href="cluster.php?sid='.$sid.'&cluster='.$c['id'].'&page=info">'.$c['code'].'</a> wurde abgesandt.
Wenn ein Admin oder ein Mitgliederminister des Clusters &uuml;ber deine Aufnahme entschieden
hat, wirst du per System-Nachricht informiert.</b></p>
</div></div>';
createlayout_bottom();
break;

case 'req_verw': // ------------------------- REQUEST VERWALTUNG -----------------------
if($usr['clusterstat']==CS_ADMIN || $usr['clusterstat']==CS_MITGLIEDERMINISTER || $usr['clusterstat']==CS_COADMIN):

createlayout_top('HackTheNet - Cluster - Mitgliedsantr&auml;ge verwalten');
echo '<div class="content" id="cluster">
<h2>Cluster</h2>
<div id="cluster-request-administration">
<h3>Aufnahmeantr&auml;ge</h3>
'.$notif.'
<form action="cluster.php?page=savereqverw&sid='.$sid.'" method="post">
<table cellpadding="3" cellspacing="2">
<tr><th>Spieler</th><th>Punkte</th><th>Kommentar</th><th>Aufnehmen</th><th>Ablehnen</th><th>Nicht &auml;ndern</th></tr>';

$r=db_query('SELECT * FROM cl_reqs WHERE cluster='.$clusterid.' AND dealed=\'no\'');
while($data=mysql_fetch_assoc($r))
{
  $u=getuser($data['user']);
  if($u===false) { db_query('DELETE FROM cl_reqs WHERE user='.mysql_escape_string($data['user']).';'); continue; }
  echo '<tr><th><a href="user.php?page=info&sid='.$sid.'&user='.$u['id'].'" class="il">'.$u['name'].'</a></th>';
  echo '<td>'.$u['points'].'</td><td><tt>'.$data['comment'].'</tt></td>';
  echo '<td><input type="radio" name="u'.$u['id'].'" value="yes" /></td>';
  echo '<td><input type="radio" name="u'.$u['id'].'" value="no" /></td>';
  echo '<td><input type="radio" name="u'.$u['id'].'" value="ignore" checked="checked" /></td>';
  echo '</tr>';
}

echo '<tr><th colspan="6" align="right"><input type="submit" value=" &Uuml;bernehmen "></th></tr>
</table></form>
</div></div>';
createlayout_bottom();

endif;
break;


case 'savereqverw': // ------------------------- SAVE REQUEST VERWALTUNG -----------------------
if($usr['clusterstat']==CS_ADMIN || $usr['clusterstat']==CS_MITGLIEDERMINISTER || $usr['clusterstat']==CS_COADMIN):

$r=db_query('SELECT * FROM cl_reqs WHERE cluster='.$clusterid.' AND dealed=\'no\'');
$delstr=''; $acstr='';
while($data=mysql_fetch_assoc($r))
{
  $u=getuser($data[user]);
  if($u===false) continue;
  $chs=$_POST['u'.$u['id']];
  if($chs=='yes') {
    addsysmsg($u['id'],'Dein Aufnahmeantrag in den Cluster [cluster='.$clusterid.']'.$cluster['code'].'[/cluster] wurde angenommen!<br />Klicke <a href="cluster.php?sid=%sid%&page=join&cluster='.$clusterid.'">hier</a> um deinen jetzigen Cluster zu verlassen und '.$cluster['code'].' beizutreten.');
    $acstr.='user='.mysql_escape_string($u['id']).' OR ';
  } elseif($chs=='no') {
    addsysmsg($u['id'],'Dein Aufnahmeantrag in den Cluster [cluster='.$clusterid.']'.$cluster['code'].'[/cluster] wurde abgelehnt!');
    $delstr.='user='.mysql_escape_string($u['id']).' OR ';
  }
}

if($delstr!='') {
  $delstr=substr($delstr,0,strlen($delstr)-4);
  db_query('DELETE FROM cl_reqs WHERE ('.$delstr.') AND cluster='.$clusterid);
}
if($acstr!='') {
  $acstr=substr($acstr,0,strlen($acstr)-4);
  db_query('UPDATE cl_reqs SET dealed=\'yes\' WHERE ('.$acstr.') AND cluster='.$clusterid);
}

header('Location: cluster.php?sid='.$sid.'&page=req_verw&ok='.urlencode('Die Aufnahmeantr&auml;ge wurden bearbeitet!'));

endif;
break;

case 'savenotice': // ------------------------- SAVE NOTICE -----------------------

$n=safeentities($_POST['notice']);

db_query('UPDATE clusters SET notice=\''.mysql_escape_string($n).'\' WHERE id='.$clusterid.';');

createlayout_top('HackTheNet - Cluster-Notiz');
echo '<div class="content" id="cluster-notice-saved">'."\n";
echo '<h2>Cluster-Notiz</h2>'."\n";
echo '<div class="ok">'.LF.'<h3>Aktion ausgef�hrt</h3>'.LF.'<p>Notiz gespeichert!</p></div>';
echo '</div>';
createlayout_bottom();

db_query('INSERT INTO logs SET type=\'chclinfo\', usr_id=\'0\', payload=\''.mysql_escape_string($usr['name']).' changes notice of '.mysql_escape_string($cluster['code']).'\';');

break;

}


function cvCodeToString($code) { // -------- CV CODE TO STRING ------
switch($code) {
case CV_WAR: $s='Kriegserkl&auml;rung'; break;
case CV_BEISTAND: $s='Beistandsvertrag'; break;
case CV_PEACE: $s='Friedensvertrag'; break;
case CV_NAP: $s='Nicht-Angriffs-Pakt'; break;
case CV_WING: $s='Wing-Treaty'; break;
}
return $s;
}

function conventlist($cid) { // ----------- CONVENTLIST -----------
global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $clusterid,$cluster,$sid;

$r=db_query('SELECT cl_pacts.convent,clusters.code,clusters.id FROM (cl_pacts RIGHT JOIN clusters ON cl_pacts.partner=clusters.id) WHERE cl_pacts.cluster='.mysql_escape_string($cid).' ORDER BY clusters.code ASC;');
#echo mysql_error();
if (mysql_num_rows($r)>0) {
$s='<table>'.LF.'<tr>'.LF.'<th>Cluster</th>'.LF.'<th>Vertrag</th>'.LF.'</tr>'."\n";
while($pact=mysql_fetch_assoc($r))
{
  #$partner=getcluster($pact[partner]);
  $temp=cvcodetostring($pact['convent']);
  $s.='<tr>'.LF.'<td><a href="cluster.php?page=info&amp;sid='.$sid.'&amp;cluster='.$pact['id'].'">'.$pact['code'].'</a></td>'.LF.'<td>'.$temp.'</td>'.LF.'</tr>'."\n";
}
$s.='</table>';
}
return $s;
}

function ext_conventlist() 
{
  global $clusterid, $usr, $sid;
  
  $r=db_query('SELECT cl_pacts.convent,clusters.code,clusters.id,cl_pacts.partner FROM (cl_pacts RIGHT JOIN clusters ON cl_pacts.partner=clusters.id) WHERE cl_pacts.cluster='.$clusterid.' ORDER BY clusters.code ASC;');
  if(mysql_num_rows($r) > 0) {
  echo '<div id="cluster-convents">
  <h3>Eigene bestehende Vertr&auml;ge</h3>
  <table>
  <tr>
  <th>Cluster</th>
  <th>Vertrag</th>';
  if($usr['clusterstat'] > 20)
  {
    echo '<th>L�schen?</th>';
  }
  echo '</tr>
  ';
  while($pact=mysql_fetch_assoc($r))
  {
    $temp=cvcodetostring($pact['convent']);
    echo '<tr>
  <td><a href="cluster.php?page=info&amp;sid='.$sid.'&amp;cluster='.$pact['id'].'">'.$pact['code'].'</a></td>
  <td>'.$temp.'</td>';
  if($usr['clusterstat'] > 20)
  {
    echo '<td><a href="cluster.php?page=delconvent&amp;sid='.$sid.'&amp;convent='.$pact['convent'].'-'.$pact['partner'].'">L&ouml;schen</a></td>';
  }
  echo '</tr>
  ';
  }
  echo '</table>
  </div>
  ';
  }
  
  $r=db_query('SELECT cl_pacts.convent,clusters.code,clusters.id FROM (cl_pacts RIGHT JOIN clusters ON cl_pacts.cluster=clusters.id) WHERE cl_pacts.partner='.$clusterid.' ORDER BY clusters.code ASC;');
  if(mysql_num_rows($r) > 0) {
  echo '<div id="cluster-convents">
  <h3>Bestehende Vertr&auml;ge anderer Cluster mit uns</h3>
  <table>
  <tr>
  <th>Cluster</th>
  <th>Vertrag</th>
  </tr>
  ';
  while($pact=mysql_fetch_assoc($r))
  {
    $temp=cvcodetostring($pact['convent']);
    echo '<tr>
  <td><a href="cluster.php?page=info&amp;sid='.$sid.'&amp;cluster='.$pact['id'].'">'.$pact['code'].'</a></td>
  <td>'.$temp.'</td>
  </tr>
  ';
  }
  echo '</table>
  </div>
  ';
  }
}

?>