<?php


/**
 * Alle Angriffe werden hier drin abgehandelt
 **/

define('IN_HTN', 1);
$FILE_REQUIRES_PC = TRUE;
include('ingame.php');

$action = $_REQUEST['page'];
if($action == '') $action = $_REQUEST['mode'];
if($action == '') $action = $_REQUEST['action'];
if($action == '') $action = $_REQUEST['a'];
if($action == '') $action = $_REQUEST['m'];

if($pc['mk']<1) { no_(); exit; }
if($pc['blocked']>time()) { exit; }

$bucks=number_format($pc['credits'], 0, ',', '.');
$payload='';

switch($action) {

case 'opc': // --------------- Operation Center -----------------------

$javascript='<style>tt { color:darkred; }</style>';
if($usr['bigacc']=='yes') {
$javascript.='
<script language="JavaScript" type="text/javascript">
function fill(s) {
  document.frm.code.value+=\'10.47.\'+s;
}
</script>';

$bigacc='<br /><a href="#codebox" onclick="show_abook(\'pc\')">Adressbuch</a>';
}
createlayout_top('HackTheNet - Operation Center');
echo '<div class="content" id="attacks">';
echo '<h2>Operation Center</h2>';

if(!isattackallowed($next,$last)) {
#echo 'next='.$next.', last='.$last.'<br />';
$total=($next-$last);
#echo 'total='.$total.'<br />';
$gone=time()-$last;
#echo 'gone='.$gone.'<br />';
$percent=round($gone*100/$total,0);
echo '<div class="error"><h3>Hinweis</h3><p>Du kannst erst wieder '.nicetime2($next,true).' angreifen!</p></div>';
echo '<br /><p><img src="images/pbar.gif"> <strong>Reloading... '.$percent.' %</strong></p>';
echo '</div></div>'; 
createlayout_bottom(); 
exit;
}

echo '<div id="attacks-attack1">';
echo '<h3>Angriff erstellen</h3>';

$v=$pc['mk']; $weapons='';
  if(isavailh('scan',$pc)==true) { $weapons.='<tr class="greytr2"><td nowrap="nowrap">REMOTE SCAN</td><td><tt>REMOTE SCAN</tt></td><td>Spioniert fremde Rechner aus</td><td>Waffe</td></tr>'; }
  if(isavailh('trojan',$pc)==true) {
    $tEx='<table style="font-size:8pt;" class="nomargin">';
    if(tisavail('defacement')==true) $tEx.='<tr><td nowrap="nowrap"><tt>TROJAN DEFACEMENT</tt></td><td>Du kannst die Beschreibung des gegnerischen Users &auml;ndern</td></tr>';
    if(tisavail('transfer')==true) $tEx.='<tr><td nowrap="nowrap"><tt>TROJAN TRANSFER</tt></td><td>Klaut Geld</td></tr>';
    if(tisavail('deactivate')==true) $tEx.='<tr><td nowrap="nowrap"><tt>TROJAN DEACTIVATE FIREWALL</tt></td><td>Deaktiviert die Firewall</td></tr>
      <tr><td nowrap="nowrap"><tt>TROJAN DEACTIVATE AV</tt></td><td>Deaktiviert das Antivirus-Program</td></tr>
      <tr><td nowrap="nowrap"><tt>TROJAN DEACTIVATE IDS</tt></td><td>Deaktiviert das IDS</td></tr>';
    $tEx.='</table>';
    $weapons.='<tr class="greytr2"><td nowrap="nowrap">TROJAN [TYP]</td><td colspan=2>'.$tEx.'</td><td>Waffe</td></tr>';
  }
  if(isavailh('smash',$pc)==true) { $weapons.='<tr class="greytr2"><td nowrap="nowrap">REMOTE SMASH [ITEM]</td><td nowrap><tt>REMOTE SMASH CPU</tt><br /> oder
    <tt>REMOTE SMASH FIREWALL</tt><br /> oder
    <tt>REMOTE SMASH SDK</tt>
    </td><td>Zerst&ouml;rt Prozessor (CPU), Firewall (FIREWALL) oder SDK (SDK) von fremden Rechnern</td><td>Waffe</td></tr>'; }
  if(isavailh('block',$pc)==true) { $weapons.='<tr class="greytr2"><td nowrap="nowrap">REMOTE BLOCK</td><td><tt>REMOTE BLOCK</tt></td><td>Blockiert Computer f&uuml;r dessen Besitzer</td><td>Waffe</td></tr>'; }

  if(isavailh('rh',$pc)==true) { $weapons.='<tr class="greytr2"><td nowrap="nowrap">REMOTE HIJACK</td><td><tt>REMOTE HIJACK</tt></td><td>Versucht, den Computer des Gegners zu &uuml;bernehmen</td><td>Waffe</td></tr>'; }

#  <tr><td colspan="4"><sup>1)</sup> Diese Waffe kann entweder alleine oder zusammen mit dem Scan eingesetzt werden.
#Eine Kombination zwischen dieser und einer anderen ist jedoch nicht m&ouml;glich.</td></tr>

echo '<p><b>Geld: '.$bucks.' Credits</b></p><br />
<form action="battle.php?sid='.$sid.'&action=opc_submit"  method="post" name="frm">
<table>
<tr><th>Befehle</th></tr>
<tr><td><ul><li>Jeder Befehl steht in einer Zeile.<li>Leerzeilen zwischen den Befehlen sind erlaubt!
<li>Gro&szlig;/Kleinschreibung ist egal!
<li>Reihenfolge der Befehle ist egal!
</ul>
</td></tr><tr><td>
<table style="font-size:8pt;" class="nomargin">
<tr class="greytr2"><td><b>Syntax</b></td><td><b>Beispiel</b></td><td><b>Beschreibung</b></td><td><b>Hinweis</b></td></tr>
<tr class="greytr2"><td nowrap="nowrap">TARGET [IP-Adresse]</td><td><tt>TARGET 10.47.0.0</tt></td><td>Legt das Ziel des Angriffs fest</td><td>Pflicht</td></tr>
<tr class="greytr2"><td nowrap="nowrap">MSG { [NACHRICHT] }</td><td><tt>MSG { Ich mach dich platt! }</tt></td><td>Sendet einen Kommentar an das Opfer</td><td>optional</td></tr>
'.$weapons.'
</table>
</td></tr>
</table><br /><br />
<a name="codebox"></a>
<table>
<tr><th>Angriffs-Script erstellen</th></tr>
<tr><td>
<textarea name="code" cols=70 rows=6>'.$_POST['code'].'</textarea>'.$bigacc.'
</td></tr>
<tr><th><input type="hidden" name="pcid" value="'.$pcid.'" />
<input type="reset" value="  Reset  " /> <input type=submit value=" Weiter " /></th></tr>
</table>
</form>
</div></div>';

createlayout_bottom();
break;

case 'opc_submit': // --------------- OPC Submit -----------------------

$pc=getpc((int)$_POST['pcid']);
if($pc===false) exit;

if(!isattackallowed($b,$d)) exit;

createlayout_top('HackTheNet - Operation Center');
echo '<div class="content" id="attacks">';
echo '<h2>Operation Center</h2>';
echo '<div id="attacks-attack2">';

$emsg=''; $opt=''; $e='';
$target=''; $scan=0; $trojan=0; $smash=0; $block=0; $hijack=0; $king=0;

$code=str_replace(chr(9),'',$_POST['code']);
while(strpos($code,'  ')!=false) {
  $code=str_replace('  ',' ',$code);
}
$lines=explode("\n",$code);
for($i=0;$i<count($lines);$i++) {
  if(trim($lines[$i])!='') {
    $lines[$i]=explode(' ',trim($lines[$i]," \n\r\x0b\0\t"));
    switch(strtoupper($lines[$i][0])) {

      case 'TARGET': $target=$lines[$i][1]; break;

      case 'REMOTE':
        switch(strtoupper(trim($lines[$i][1])))
        {
        case 'SCAN': $scan=1; break;
        case 'SMASH': $smash=1; $opt=trim($lines[$i][2]); break;
        case 'BLOCK': $block=1; break;
        case 'HIJACK': $hijack=1; break;
        default:
          $emsg.='Unbekannter Befehl: <i>'.join($lines[$i],' ').'</i><br />';
        break;
        }
      break;

      case 'TROJAN':
        $trojan=1;
        $opt=trim($lines[$i][1]).' '.trim($lines[$i][2]);
      break;

      case 'KING':
        $king=1;
      break;

      case 'MSG':
      $s=trim($lines[$i][1]);
      if(substr($s,0,1)=='{') {
        $lines[$i]=join(' ',$lines[$i])."\n";
        $msg=substr($lines[$i],1);;
        $msg=substr($msg,1).' ';

        for($x=$i+1;$x<count($lines);$x++) {
          $aa=$lines[$x];
          if(substr_count($aa,'}')>0) {
            $i=$x;
            $msg.=$aa;
            break;
          } else $msg.=$aa;
        }
        if(msg!='') {
          $msg=substr($msg,3);
          $msg=substr($msg,0,strpos($msg,'}'));
        }
      }
      break;

      default:
        $emsg.='Unbekannter Befehl: <i>'.join($lines[$i],' ').'</i><br />';
      break;
    }
  }
}

$opt=strtoupper(trim($opt));
$msg=htmlentities(trim($msg));
$msg=str_replace('|',' ',$msg);

if(strlen($msg)>512) $msg=substr($msg,0,512);
$text='';
$target=trim($target," \n\r\x0b\0\t");
if(eregi('^10\\.47\\.([0-9]{2,3})\\.([0-9]{1,3})$',$target)==true) {
  $target=getpc(substr($target,6),'ip');
  if($target===false) $e.='Ung&uuml;ltige Ziel-Adresse: Der PC existiert nicht!<br />';
} else $e.='Ung&uuml;ltige Ziel-Adresse: IP-Adresse muss in der Form 10.47.x.x vorliegen!<br />';

#if(is_noranKINGuser($target['owner']) && $localhost==false) { $e.='Du kannst keinen Administrator des Spiels angreifen!<br />'; }
if($localhost==false) { if($target[owner]==$usrid) $e.='Du kannst dich nicht selber angreifen!<br />'; }

if($scan & isavailh('scan',$pc)==false) $e.='Der Scan ist noch nicht verf&uuml;gbar!<br />';
if($trojan & isavailh('trojan',$pc)==false) $e.='Der Trojaner ist noch nicht verf&uuml;gbar!<br />';
if($block & isavailh('block',$pc)==false) $e.='<tt>REMOTE BLOCK</tt> ist noch nicht verf&uuml;gbar!<br />';
if($smash & isavailh('smash',$pc)==false) $e.='<tt>REMOTE SMASH</tt> ist noch nicht verf&uuml;gbar!<br />';
if($hijack & isavailh('rh',$pc)==false) $e.='<tt>REMOTE HIJACK</tt> ist noch nicht verf&uuml;gbar!<br />';

if($hijack & ($trojan || $scan || $smash || $block)) { $e.='Der Hijack kann nur alleine eingesetzt werden!<br />'; }
if($trojan & ($smash || $block || $scan)) { $e.='Der Trojaner kann nur alleine eingesetzt werden!<br />'; }
if($smash & ($trojan || $block || $scan)) { $e.='<tt>REMOTE SMASH</tt> kann nur alleine eingesetzt werden!<br />'; }
if($block & ($smash || $trojan || $scan)) { $e.='<tt>REMOTE BLOCK</tt> kann nur alleine eingesetzt werden!<br />'; }
if($smash & $opt=='') { $e.='<tt>REMOTE SMASH</tt> erwartet einen Parameter!<br />'; }
if($trojan & $opt=='') { $e.='Der Trojaner erwartet einen Parameter!<br />'; }
if(!($hijack || $trojan || $scan || $smash || $block)) $e.='Du musst mindestens eine Waffe angeben!<br />';

if($GAME_MODE == '2.1')
{
  if($hijack && count(explode(',', $usr['pcs'])) >= MAX_PCS_PER_USER) $e.='Du hast bereits die maximale Anzahl von PCs erreicht!<br />';
}

if($hijack && ($pc['lrh']+REMOTE_HIJACK_DELAY>time() && $localhost==false)) $e.='Du kannst immer nur einen <tt>REMOTE HIJACK</tt>-Angriff innerhalb von zwei Tagen ausf&uuml;hren!<br />';

if($smash)
{
  switch($opt)
  {
    case 'CPU': break;
    case 'FIREWALL': break;
    case 'SDK': break;
    default:
    $e.='Unbekannte Option f&uuml;r <tt>REMOTE SMASH</tt>: <i>'.$opt.'</i>!<br />';
  }
}

if($trojan)
{
  switch($opt)
  {
    case 'DEFACEMENT':
      if(tisavail('defacement')==false) $e.='Defacement ist noch nicht verf&uuml;gbar!<br />';
      break;
    case 'TRANSFER':
      if(tisavail('transfer')==false) $e.='Geld klauen ist noch nicht verf&uuml;gbar!<br />';
      break;
    case 'DEACTIVATE FIREWALL':
      if(tisavail('deactivate')==false) $e.='Deaktivierung ist noch nicht verf&uuml;gbar!<br />';
      break;
    case 'DEACTIVATE AV':
      if(tisavail('deactivate')==false) $e.='Deaktivierung ist noch nicht verf&uuml;gbar!<br />';
      break;
    case 'DEACTIVATE IDS':
      if(tisavail('deactivate')==false) $e.='Deaktivierung ist noch nicht verf&uuml;gbar!<br />';
      break;
    default:
    $e.='Unbekannte Option f&uuml;r den Trojaner: <i>'.$opt.'</i>!<br />';
  }
}

# Durch Trojaner deaktivierte Items beachten!!
if($target['di']!='' && $target['dt']>time() && $trojan==1 && substr($opt,0,10)=='DEACTIVATE')
{
  $e.='Auf dem Zielrechner ist bereits ein Item deaktiviert! Es k&ouml;nnen nicht gleichzeitig mehrere Items auf einem PC deaktiviert sein!<br />';
}

$owner=@getuser($target['owner']);
#if($owner['cluster'] == $no_ranking_cluster) $e.='Mitglieder dieses Clusters k�nnen nicht angegriffen werden!';

if($owner!=false && $owner['login_time']+MIN_INACTIVE_TIME > time() && substr_count($owner['pcs'],',')<2)
{
    /*
    if($hijack==1) {
        $my_points=$pc['points'];
        $enemy_points=$target['points'];
        #echo 'my_points=$usr['points'], enemy_points=$owner['points'], pc_points=$target['points']';
        
        if( $enemy_points <= ($my_points * (10/100)) ) {
          $r=db_query('SELECT * FROM `attacks` WHERE `from_usr`=\''.mysql_escape_string($owner['id']).'\' AND `to_usr`=\''.$usrid.'\' AND `type`<>\'scan\';');
          
          if(mysql_num_rows($r)==0) {
            $e.='Von diesem PC aus ('.$my_points.' Punkte) kannst du diesen PC ('.$enemy_points.' Punkte) nicht hijacken!<br />';
          }
        }
    }
    */
    if($scan==0) {
      if(!is_pc_attackable($target))
      {
        $e.='Du kannst diesen armen, schwachen PC nicht angreifen!<br />';
      }
    }
}



$c=GetCountry('id',$pc['country']);
$country=$c['name']; $out=$c['out']*9;
$c=GetCountry('id',$target['country']);
$country2=$c['name']; $in=$c['in']*9;
$cost=$in+$out;
if($country==$country2) $cost=0;

if($pc['credits']-$cost<0 && $country!=$country2) $e.='Nicht gen&uuml;gend Credits! Dieser Angriff w&uuml;rde '.$cost.' Credits kosten, du hast aber nur '.$pc['credits'].' Credits!<br />';

#if(($e=='' && $scan==0) & ($owner['points']<MIN_ATTACK_POINTS && $owner['login_time']+MIN_INACTIVE_TIME>time())) $e.='Du kannst keinen User mit weniger als 100 Punkten angreifen, der sich in den letzten 3 Tagen eingeloggt hat!<br />';

if($e=='')
{
  $text='<h3>Angriff best&auml;tigen</h3><table>
<tr><th>Angriffsdetails</th></tr>
<tr><td>
<ul><li><b>Der Angriff richtet sich gegen 10.47.'.$target['ip'].' ('.$target['name'].'). Dieser Rechner geh&ouml;rt ';
if($owner!==false) $text.='<a href="user.php?a=info&amp;user='.$target['owner'].'&amp;sid='.$sid.'" target="_blank">'.$owner['name'].'</a>';
else $text.='niemandem';
  $text.='.</b></li>';

  if($country!=$country2) {
    $text.='<li>Es fallen '.$cost.' Credits Geb&uuml;hren f&uuml;r den Angriff an. '.$out.' Credits Ausfuhr aus '.$country.' und '.$in.' Credits Einfuhr nach '.$country2.'.</li>';
  } else $cost=0;

  if($msg!='') $text.='<li><b>Kommentar:</b><br /><tt>'.nl2br($msg).'</tt></li>';
  if($scan) $text.='<li>Es wird <tt>REMOTE SCAN</tt> eingesetzt, um den Rechner auszuspionieren!</li>';
  if($trojan) {
    switch($opt) {
      case 'DEFACEMENT': $s='die Beschreibung des Gegners zu &auml;ndern'; break;
      case 'TRANSFER': $s='Geld zu klauen'; break;
      case 'DEACTIVATE FIREWALL': $s='die Firewall zu deaktivieren'; break;
      case 'DEACTIVATE AV': $s='das Anti-Virus-Programm zu deaktivieren'; break;
      case 'DEACTIVATE IDS': $s='das IDS zu deaktivieren'; break;
    }
    $text.='<li>Es wird ein Trojaner eingesetzt, um '.$s.'.</li>';
  }
  if($smash) {
    switch($opt) {
      case 'CPU': $modul='den Prozessor'; break;
      case 'FIREWALL': $modul='die Firewall'; break;
      case 'SDK': $modul='das SDK'; break;
    }
    $text.='<li>Es wird <tt>REMOTE SMASH</tt> eingesetzt, um <b>'.$modul.'</b> zu schw&auml;chen!</li>';
  }
  if($block) $text.='<li>Es wird <tt>REMOTE BLOCK</tt> eingesetzt, um den feindlichen Rechner zu blockieren.</li>';
  if($hijack) $text.='<li>Es wird <tt>REMOTE HIJACK</tt> eingesetzt.</li>';
  if($king) $text.='<li><br /><br />Der KING ist der KING!</li>';
  $acode=random_string(16);
  file_put($DATADIR.'/tmp/attack_'.$acode.'.txt', implode('|', array($target[id], $scan, $trojan, $smash, $block, $hijack, $opt, $msg, $cost, $pc['id'])));
  db_query('UPDATE users SET acode=\''.mysql_escape_string($acode).'\' WHERE id=\''.$usrid.'\' LIMIT 1;');
  $text.='<input type="hidden" name="acode" value="'.$acode.'">';

  $text.='</ul></td></tr>
<tr><th>
<input type="button" value="Abbrechen" onclick="location.replace(\'battle.php?sid='.$sid.'&a=opc#codebox\');" />
<input type="submit" value="  Angriff !!  " /></th></tr>
</table>';
  $a="pre_execute";
  #if($localhost) $a='execute';
} else {
  $text='<div class="error"><h3>Fehler</h3><p>'.$e.'</p></div><br /><p><input type="submit" value=" Zur&uuml;ck " /></p>';
  $a='opc#codebox';
}
#$emsg.='<br />';

#echo $emsg;
#if($emsg!='') $emsg='<div class="error"><h3>Fehler</h3><p>'.$emsg.'</p></div>';
echo '<form action="battle.php?sid='.$sid.'&a='.$a.'" method="post">
'.$text.'
</form>
</div></div>';

/* echo '<br /><br /><tt>DEBUG-VARIABLEN<br />';
echo 'Ziel: \''.$target.'\'<br /> Scan: '.$scan.'<br />';
echo 'Trojan: '.$trojan.'<br /> Smash: '.$smash.'<br />';
echo 'Block: '.$block.'<br /> Hijack: '.$hijack.'<br />';
echo 'Opt: '.$opt.'<br /> Kommentar: '.nl2br($msg).'<br />';
echo '</tt>'; */

createlayout_bottom();
break;

case 'pre_execute': // ---------------------- PRE_EXECUTE -----------------------

$code=$_POST[acode];

if(file_exists($DATADIR.'/tmp/attack_'.$code.'.txt')==false || $code!=$usr['acode']) { simple_message('Angriff ung&uuml;ltig. Bitte neu erstellen!'); exit; }

list($target,$scan,$trojan,$smash,$block,$hijack,$opt,$msg,$cost,$pcidnew)=explode('|',file_get($DATADIR.'/tmp/attack_'.$code.'.txt'));

if($pcidnew!=$pcid) { $pc=getpc($pcidnew); $pcid=$pcidnew; }

if(!isattackallowed($b,$d)) exit;

if($cost>0):
  $pc[credits]-=$cost;
  db_query('UPDATE pcs SET credits='.$pc['credits'].' WHERE id='.$pcid.';');
endif;

$delay=110-((int)$pc[lan]*10);
$javascript='
<script language="JavaScript" type="text/javascript">
function anim(z) {
  if(z<=296) {
    var lay=getLay(\'pbar\');
    lay.width=z;
    setTimeout(\'anim(\'+(z+1)+\')\','.$delay.');
  } else {
    document.forms[0].submit();
  }
}
function abortattack() {
location.replace(\'battle.php?m=opc&sid='.$sid.'\');
}
</script>';

$bodytag=' onload="anim(1)"';
createlayout_top('HackTheNet - Operation Center', false, false);
echo '<div class="content" id="attacks">';
echo '<h2>Operation Center</h2>';
echo '<div id="attacks-attack3">';
echo '<h3>Angriff l&auml;uft...</h3>';


echo '<form action="battle.php?a=execute&amp;sid='.$sid.'" method="post"><input type="hidden" name="acode" value="'.$code.'"></form>
<p><div style="display:block;width:300px;border:1px solid black;padding:1px;background-color:white;margin-left:100px;"><img src="images/bluepix.gif" id="pbar" border=0 style="position:relative;top:0px;left:0px;margin:0px;" height=30></div>
</p>
<p style="color:red;font-weight:bold;">WARNUNG: Diese Seite nicht verlassen! Sonst wird der Angriff abgebrochen!</p>
<p><form><input type=button onclick="abortattack()" value=" Abbrechen "></form></p>
</div>
</div>';
createlayout_bottom();

break;

case 'execute': // --------------------------------- EXECUTE ---------------------------------

$code=$_REQUEST['acode'];

$fn=$DATADIR.'/tmp/attack_'.$code.'.txt'; if(!file_exists($fn) || $code!=$usr['acode']) { no_('be1a'); exit; }
list($target,$scan,$trojan,$smash,$block,$hijack,$opt,$msg,$cost,$pcidnew)=explode('|',file_get($fn));

if($pcidnew!=$pcid) { $pc=getpc($pcidnew); $pcid=$pcidnew; }

if(!isattackallowed($b,$d)) { simple_message('Ne ne ne!!','error'); exit; }

if($localhost!=true || $usrid!=30 ) @unlink($fn);

if(trim($msg)!='') $msg='<br />Der Angreifer hat folgende Nachricht f&uuml;r dich:<br /><tt>'.nl2br($msg).'</tt>';

$ts=time();
db_query('UPDATE pcs SET la=\''.mysql_escape_string($ts).'\' WHERE id='.$pcid);
db_query('UPDATE users SET la=\''.mysql_escape_string($ts).'\' WHERE id='.$usrid);
$pc['la']=$ts;
$usr['la']=$ts;

$remote=getpc($target);
$local=$pc;
$remote2=$remote;
$local2=$local;

if($remote['di']!='' && $remote['dt']>time() && $trojan==1 && substr($opt,0,10)=='DEACTIVATE')
{
  simple_message('Auf dem Zielrechner ist bereits ein Item deaktiviert! Es k&ouml;nnen nicht gleichzeitig mehrere Items auf einem PC deaktiviert sein!<br />');
  exit;
}

# Durch Trojaner deaktivierte Items beachten!!
$s=$remote['di']; if($s!='' && (int)$remote['dt']>time()) $remote[$s]=0;
$s=$local['di']; if($s!='' && (int)$local['dt']>time()) $local[$s]=0;


function getDefend($s) {
  global $remote,$local,$pc,$usr,$usrid;
  $x = eval('return '.$s.';');
  #echo "<pre>defend=$x " . eval('return "'. $s . '";') . "</pre>";
  #$x = eval('return ' . eval('return "'. $s . '";') . ';');
  #echo "<pre>defend=$x " . eval('return "'. $s . '";') . "</pre>";
  $x += mt_rand(-5, 10);
  if($remote['blocked']>time()) $x=$x*0.8;
  if(is_norankinguser($usrid)) echo " $x ";
  return $x;
}
function getAttack($s) {
  global $remote,$local,$pc,$usr,$usrid;
  $x = eval('return '.$s.';');
  $x += mt_rand(-10, 10);
  #echo "<pre>attack=$x " . eval('return "'. $s . '";') . "</pre>";
  if(is_norankinguser($usrid)) echo " $x ";
  return $x;
}

/*
function getsuccess($attack,$defend,$margin=50) {
  $noticed=0;
  $success=0;
  mt_srand();
  if($attack<$defend) { # Angriff schw�cher
    if($attack+$margin<$defend) { # Angriff viel schw�cher
      $success=0;  $noticed=1;
    } else { # kein gro�er Unterschied
      $success=mt_rand(0,1);  $noticed=1;
    }
  } else { # Angriff st�rker
    if($attack-$margin>$defend) { # Angriff viel st�rker
      $success=1;  $noticed=0;
    } else { # kein gro�er Unterschied
      $success=1;  $noticed=1;
    }
  }

  if(mt_rand(0,100)==50) { $success=1; $noticed=0; }
  elseif(mt_rand(100,200)==150) { $success=0; $noticed=1; }

  $a[success]=$success;
  $a[noticed]=$noticed;
  return $a;
}*/

if($scan==1) { // ------------ SCAN ------------

  $defend = getDefend('($remote[ids] + $remote[fw]) * 10');
  $attack = getAttack('$local[ips] * 20 + $local[mk]');
  
  $success = (int)(bool)($attack > $defend);
  $noticed = (int)(!(bool)($defend + 50 < $attack));
  
  #echo 'Attack = '.$attack.' (IPS='.$local['ips'].' / MK='.$local['mk'].')<br />Defend =  '.$defend.' (IDS='.$remote['ids'].' / FW='.$remote['fw'].')<br /><br />Success = '.$success.'<br />Noticed =  '.$noticed;
  if($success != 0) $usr['newmail']+=1;
  createlayout_top('HackTheNet - Operation Center');
  if($success != 0) $usr['newmail']-=1;
  echo '<div class="content" id="attacks">';
  echo '<h2>Operation Center</h2>';
  echo '<div id="attacks-attack4">';
  echo '<h3>Remote Scan</h3>';
  echo '<p>';
  if($success==0) {
    echo '<span style="color:red;"><b>Der Angriff wurde abgewehrt!</b></span><br />';
    if($noticed==0) { echo 'Du hattest aber Gl&uuml;ck und wurdest nicht erkannt!';
    addsysmsg($remote['owner'],'Auf deinen Rechner 10.47.'.$remote['ip'].' ('.$remote['name'].') wurde ein Spionage-Angriff ver&uuml;bt!<br />Er wurde abgeblockt!<br />Der Feind konnte nicht identifiziert werden!'.$msg); 
  } else { 
    echo 'Du konntest nicht anonym bleiben!';
    addsysmsg($remote['owner'],'Auf deinen Rechner 10.47.'.$remote['ip'].' ('.$remote['name'].') wurde ein Spionage-Angriff ver&uuml;bt!<br />Er konnte aber abgeblockt werden!<br />Der Feind wurde als [usr='.$usrid.']'.$usr['name'].'[/usr] identifiziert.'.$msg); }
  } else {
    echo '<span style="color:green;"><b>Der Angriff war erfolgreich!</b></span><br />';
    if($noticed==0) echo 'Du wurdest au&szlig;erdem nicht bemerkt!';
    else { echo 'Du konntest aber nicht anonym bleiben!';
    addsysmsg($remote['owner'],'Auf deinen Rechner 10.47.'.$remote['ip'].' ('.$remote['name'].') wurde ein Spionage-Angriff ver&uuml;bt!<br />Er war erfolgreich!<br />Der Feind konnte als [usr='.$usrid.']'.$usr['name'].'[/usr] identifiziert werden!'.$msg); }
    echo '<br /><br />Ein Bericht wurde dir als System-Nachricht zugestellt.';
    $owner=getuser($remote[owner]);
    addsysmsg($usrid,'<b>Spionage-Bericht von 10.47.'.$remote['ip'].'</b> Besitzer: [usr='.$owner['id'].']'.$owner['name'].'[/usr]<br />
    Prozessor = '.$cpu_levels[$remote['cpu']].' Mhz,<br />Arbeitsspeicher = '.$ram_levels[$remote['ram']].' MB RAM,<br /> MoneyMarket = v'.$remote['mm'].',<br /> BucksBunker = v'.$remote['bb'].',<br />
    Firewall = v'.$remote['fw'].',<br /> Anti-Virus-Programm = v'.$remote['av'].',<br /> IDS = v'.$remote['ids'].',<br />
    IPS = v'.$remote['ips'].',<br /> Malware Kit = v'.$remote['mk'].',<br /> Trojaner = v'.$remote['trojan'].',<br /> SDK = v'.$remote['sdk'].',<br />
    Remote Hijack = v'.$remote['rh'].',<br /> Distributed Attack = '.(int)isavailh('da',$remote)).'<br />
    Geld: '.$remote['credits'].' Credits';
  }
  echo '</p></div></div>';
  createlayout_bottom();
} elseif($trojan==1) {  // ------------ TROJAN ------------

  function deactivate($key)
  {
    global $remote,$local,$target,$local2,$remote2;
    global $msg,$success,$noticed,$usr,$usrid;
    $defend = getDefend('($remote[av] + $remote[fw]) * 15 + $remote[ids] * 5');
    $attack = getAttack('($local[mk] + $local[sdk]) * 15 + $local[ips] * 5');
    
    $noticed = (int)(bool)($remote['ids'] * 1.1 >= $local['ips'] * 0.9);
    $success = (int)(bool)($attack > $defend);
    
    if($success == 1 && $noticed == 0) $t = 90;
    elseif($success == 1 && $noticed == 1) $t = 45;

    createlayout_top('HackTheNet - Operation Center');
    echo '<div class="content" id="attacks">';
    echo '<h2>Operation Center</h2>';
    echo '<div id="attacks-attack4">';
    echo '<h3>Deactivate</h3>';
    echo '<p>';
    if($success==1)
    {
      $t=time()+$t*60;
      echo '<span style="color:green;font-weight:bold;">Angriff erfolgreich! '.idtoname($key).' blockiert bis '.nicetime($t).'!</span><br />';
      $s='Auf deinen PC 10.47.'.$remote['ip'].' ('.$remote['name'].') wurde ein erfolgreicher Angriff ver&uuml;bt: '.idtoname($key).' ist blockiert bis '.nicetime($t).'!<br />';
      #$remote2[di]=$key; $remote2[dt]=$t;
      db_query('UPDATE pcs SET di=\''.mysql_escape_string($key).'\', dt=\''.mysql_escape_string($t).'\' WHERE id='.mysql_escape_string($remote['id']).';');
    }
    else
    {
      echo '<span style="color:red;font-weight:bold;">Angriff abgewehrt!</span><br />';
      $s='Es wurde versucht '.idtoname($key).' auf deinem PC 10.47.'.$remote['ip'].' ('.$remote['name'].') zu blockieren. Der Angriff konnte aber abgeblockt werden!<br />';
    }

    if($noticed == 1)
    {
      echo '<br />Du wurdest erkannt!';
      $s.='Der Angreifer konnte als [usr='.$usrid.']'.$usr['name'].'[/usr] identifiziert werden!';
    } else {
      echo '<br />Du konntest anonym bleiben!';
      $s.='Der Angreifer konnte leider nicht identifiziert werden!';
    }
    addsysmsg($remote['owner'],$s.$msg);
    echo '</p></div></div>';
    createlayout_bottom();

  }
  switch($opt) {
    case 'DEFACEMENT': // ------------ DEFACEMENT ------------
        $defend = getDefend('($remote[fw] + $remote[av]) * 12');
        $attack = getAttack('$local[ips] * 12 + $local[sdk] * 18');
        
        $noticed = (int)(bool)($remote['ids'] >= $local['ips']);
        $success = (int)(bool)($attack > $defend);

    createlayout_top('HackTheNet - Operation Center');
    echo '<div class="content" id="attacks">';
    echo '<h2>Operation Center</h2>';
    echo '<div id="attacks-attack4">';
    echo '<h3>Defacement</h3>';
    echo '<p>';
        if($success == 1)
        {
          $code = random_string(10);
          file_put($DATADIR.'/tmp/defacement_'.$code.'.txt',$remote['owner']);
          $u=getuser($remote['owner']);
          $text=htmlspecialchars($u['text']);
          echo '<span style="color:green;"><b>Angriff erfolgreich!</b></span>
<b>Du kannst jetzt die Beschreibung &auml;ndern:</b>
<form action="battle.php?sid='.$sid.'&a=chtext&code='.$code.'"  method="post">
<textarea rows=6 cols=70 name="text">'.$text.'</textarea>
<br /><input type=submit value="Speichern">
</form>';
          if($noticed==1) { echo 'Du wurdest bemerkt und erkannt!';
          addsysmsg($remote['owner'],'Auf dich wurde ein erfolgreicher Defacement-Angriff ver&uuml;bt!<br />Der Feind konnte als [usr='.$usrid.']'.$usr['name'].'[/usr] identifiziert werden!'.$msg);
          } else {
            echo 'Du wurdest bemerkt, aber nicht erkannt!';
            addsysmsg($remote['owner'],'Auf dich wurde ein erfolgreicher Defacement-Angriff ver&uuml;bt!<br />Der Feind konnte nicht identifiziert werden!'.$msg);
          }
        } else {
          echo '<span style="color:red;font-weight:bold;"><b>Der Angriff war NICHT erfolgreich!</b></span><br />';
          if($noticed==1) {
            addsysmsg($remote['owner'],'Es wurde erfolglos versucht, dich mit einem Defacement-Angriff zu schm&auml;hen!<br />Der Feind konnte als [usr='.$usrid.']'.$usr['name'].'[/usr] identifiziert werden!'.$msg);
            echo 'Du wurdest bemerkt und erkannt!';
          } else {
            echo 'Du wurdest nicht bemerkt!';
          }
        }

        echo '</p></div></div>';
        createlayout_bottom();

        break;
    case 'TRANSFER': // ------------ TRANSFER ------------
      $defend = getDefend('($remote[av] + $remote[fw]) * 19 + $remote[ids] * 3');
      #$defend = getDefend('($av + $fw) * 30 + $ids * 5');
      $attack = getAttack('($local[mk] + $local[sdk]) * 22 + $local[ram] * 5');
      
      $noticed = (int)(bool)($remote['ids'] * 1.2 >= $local['ips'] * 0.8);
      $success = (int)(bool)($attack > $defend + 5);
      
      echo "<pre>$defend $attack $noticed $success</pre>";

      # Geld updaten:
      if($remote2['lmupd']+60<=time()) {
        $plus=(int)round(get_gdph($remote2)*((time()-$remote2['lmupd'])/3600),0);
        $remote2['credits']+=$plus;
        $remote2['lmupd']=time();
        $max=getmaxbb($remote2);
        if($remote2['credits']>$max) $remote2['credits']=$max;
        db_query('UPDATE pcs SET lmupd=\''.mysql_escape_string($remote2[lmupd]).'\', credits=\''.mysql_escape_string($remote2['credits']).'\' WHERE id='.mysql_escape_string($remote2['id']).';');
        $remote['credits']=$remote2['credits'];
      }

    createlayout_top('HackTheNet - Operation Center');
    echo '<div class="content" id="attacks">';
    echo '<h2>Operation Center</h2>';
    echo '<div id="attacks-attack4">';
    echo '<h3>Trojan Transfer</h3>';
    echo '<p>';
      if($success==1) {
        if($noticed==1) $quo=6; else $quo=4;
        $credits=floor($remote['credits']/$quo);
        $payload = $credits;
        if(getmaxbb($local)<$local['credits']+$credits) {
          $credits=getmaxbb($local)-$local['credits'];
          if($credits==0) { echo 'Dein BucksBunker ist voll! Es kann nichts geklaut werden!';
            echo '</p></div></div>'; createlayout_bottom(); exit; }
        }
        $remote2['credits']-=$credits;
        $local['credits']+=$credits;
        if($local['credits']>0 && $remote2['credits']>0) {
          db_query('UPDATE pcs SET credits=\''.mysql_escape_string($remote2['credits']).'\' WHERE id='.mysql_escape_string($remote2['id']).';');
          db_query('UPDATE pcs SET credits=\''.mysql_escape_string($local['credits']).'\' WHERE id='.mysql_escape_string($local['id']).';');
        }
        echo '<span style="color:green;"><b>Der Angriff war erfolgreich! Du hast '.$credits.' Credits geklaut!</b></span><br /><br />';
        $s='Von deinem Rechner 10.47.'.$remote['ip'].' ('.$remote['name'].') wurden '.$credits.' Credits geklaut!<br />';
      } else {
        echo '<span style="color:red;"><b>Der Angriff war NICHT erfolgreich!</b></span><br /><br />';
        $s='Es wurde erfolglos versucht, Geld von deinem Rechner 10.47.'.mysql_escape_string($remote['ip']).' ('.mysql_escape_string($remote['name']).') zu klauen!<br />';
      }

      if($noticed == 1)
      {
        echo 'Du wurdest erkannt!';
        $s .= 'Der Angreifer konnte als [usr='.$usrid.']'.$usr['name'].'[/usr] identifiziert werden!';
      }
      else
      {
        echo 'Du konntest anonym bleiben!';
        $s .= 'Der Angreifer konnte leider nicht identifiziert werden!';
      }
      addsysmsg($remote[owner],$s.$msg);
      echo '</p></div></div>';
      createlayout_bottom();
      break;
    case 'DEACTIVATE FIREWALL': // ------------ DEACTIVATE FIREWALL ------------
      deactivate('fw'); break;
    case 'DEACTIVATE AV': // ------------ DEACTIVATE AV ------------
      deactivate('av'); break;
    case 'DEACTIVATE IDS': // ------------ DEACTIVATE IDS ------------
      deactivate('ids'); break;
    default:
    no_('bet1');
  }
} elseif($smash==1)
{
  function smash($key)
  {
    global $remote,$local,$target,$local2,$remote2;
    global $cpu_levels,$ram_levels,$msg,$success,$noticed,$usr,$usrid;
    
    $defend = getDefend('($remote[av] + $remote[fw]) * 20');
    $attack = getAttack('($local[sdk] * 2 + $local[mk]) * 19');
  
    $noticed = (int)(bool)($remote['ids'] * 1.4 >= $local['ips'] * 0.7);
    $success = (int)(bool)($attack > $defend + mt_rand(20, 40));
    
    createlayout_top('HackTheNet - Operation Center');
    echo '<div class="content" id="attacks">';
    echo '<h2>Operation Center</h2>';
    echo '<div id="attacks-attack4">';
    echo '<h3>Remote Smash</h3>';
    echo '<p>';
    #echo '<tt>defend = '.$defend.' :: attack = '.$attack.'<br></tt>';
    $s='Auf deinen PC 10.47.'.mysql_escape_string($remote['ip']).' ('.mysql_escape_string($remote['name']).') wurde ein SMASH-Angriff ver&uuml;bt! ';
    if($success == 1)
    {
      $min=0;
      if($remote[$key]<1)
        $min = 1;
      else
      {
        if($key=='cpu') {
          if($cpu_levels[$remote[cpu]]>$cpu_levels[0]) $newval=getlastcpulevel($remote['cpu']);
          else $min=1;
        } else {
          if($remote[$key]>1) $newval=($remote[$key]-0.5);
          else $min=1;
        }
      }
      echo '<span style="color:green;font-weight:bold;">Angriff erfolgreich! ';
      if($min==1) {
        $xs=idtoname($key).' konnte aber nicht weiter zerst&ouml;rt werden, da das niedriegste Level schon erreicht wurde!';
        echo $xs.'</span><br />';
        $s.=$xs.'<br />';
      } else {
        if($newval != '')
        {
          $remote2[$key]=$newval;
        }
        savepc($target,$remote2);
        
        if($key=='cpu') $newval=$cpu_levels[$newval]; // nur f�r Meldung!
        $s.=idtoname($key).' wurde zerst&ouml;rt auf '.$newval.'!<br />';
        echo idtoname($key).' zerst&ouml;rt auf '.$newval.'!</span><br />';
      }

    } else {
      $s.='Er konnte aber abgewehrt werden!';
      echo '<span style="color:red;font-weight:bold;">Angriff abgeblockt!</span><br />';
    }

    if($noticed==1) {
      echo '<br />Du wurdest erkannt!';
      $s.='Der Angreifer konnte als [usr='.$usrid.']'.$usr['name'].'[/usr] identifiziert werden!';
    } else {
      echo '<br />Du konntest anonym bleiben!';
      $s.='Der Angreifer konnte leider nicht identifiziert werden!';
    }
    addsysmsg($remote['owner'],$s.$msg);
    echo '</p></div></div>';
    createlayout_bottom();
  }
  switch($opt) {
    case 'CPU': smash('cpu'); break; // ------------ SMASH CPU ------------
    case 'FIREWALL': smash('fw'); break;  // ------------ SMASH FIREWALL ------------
    case 'SDK': smash('sdk'); break;  // ------------ SMASH SDK ------------
    default:
    no_('bes1');
  }
} elseif($block==1) {  // ------------ REMOTE BLOCK ------------

  $defend = getDefend('$remote[ids] * 5 + $remote[av] * 10');
  $attack = getAttack('$local[sdk] * 11 + $local[mk] * 11');
  
  $noticed = (int)(bool)($remote['ids'] * 1.6 >= $local['ips'] * 0.5);
  $success = (int)(bool)($attack > $defend);
  
  if($local['cpu'] <= $remote['cpu']) { $success = 0; $noticed = 1; }
  if($success == 1 && $noticed == 0)  $t = 180;
  elseif($success == 1 && $noticed == 1) $t = 60;

    createlayout_top('HackTheNet - Operation Center');
    echo '<div class="content" id="attacks">';
    echo '<h2>Operation Center</h2>';
    echo '<div id="attacks-attack4">';
    echo '<h3>Remote Block</h3>';
    echo '<p>';
  if($success==1)
  {
    $ts=time()+$t*60;
    $s='Dein Computer 10.47.'.$remote['ip'].' ('.$remote['name'].') wurde bis '.nicetime($ts).' durch einen feindlichen Angriff blockiert!<br />';
    echo '<span style="color:green;font-weight:bold;">Angriff erfolgreich!</span><br />Der feindliche PC ist blockiert bis '.nicetime($ts).'<br /><br />';
    $remote2['blocked']=$ts;
    savepc($target,$remote2);
  }
  else
  {
    $s='Es wurde versucht, deinen Computer 10.47.'.$remote['ip'].' ('.$remote['name'].') zu blockieren!<br />';
    echo '<span style="color:red;font-weight:bold;">Angriff NICHT erfolgreich!</span><br /><br />';
  }
  if($noticed==1)
  {
    $s.='Der Angreifer konnte als [usr='.$usrid.']'.$usr['name'].'[/usr] identifiziert werden!';
    echo 'Du konntest nicht anonym bleiben!<br />';
  }
  else
  {
    $s.='Der Angreifer konnte leider nicht identifiziert werden!';
    echo 'Du konntest anonym bleiben!<br />';
  }
  addsysmsg($remote['owner'],$s.$msg);
  echo '</p></div></div>';
  createlayout_bottom();

} elseif($hijack == 1) {  // ------------ REMOTE HIJACK ------------
  $ts=time();
  $pc['lrh']=$ts;
  $local['lrh']=$ts;
  $local2['lrh']=$ts;
  db_query('UPDATE pcs SET lrh=\''.$ts.'\' WHERE id='.$pc['id'].';');
  
  
  $defend = getDefend('$remote[av] * 2 + $remote[fw] * 2 + $remote[ids] + $remote[cpu] + $remote[ram]');
  $attack = getAttack('$local[rh] * 2 + $local[sdk] * 1.5 + $local[mk] * 1.5 + $local[ips] + $local[cpu] / 1.8 + $local[ram] / 1.8');
  
  $noticed = (int)(bool)(($remote['ids'] * 1.2 >= $local['ips'] * 0.8) && ($local['rh'] * 1.2 >= $remote['fw'] * 0.8));
  $success = (int)(bool)($attack > $defend + 10);
  
  $outof = ($usr['rank'] < 10 ? 25 : 40);
  if(mt_rand(0, $outof) < 6) { $success = 0; $noticed = 1; }
  #echo 'defend = '.$defend.'<br>attack = '.$attack;
  #exit;
    createlayout_top('HackTheNet - Operation Center');
    echo '<div class="content" id="attacks">';
    echo '<h2>Operation Center</h2>';
    echo '<div id="attacks-attack4">';
    echo '<h3>Remote Hijack</h3>';
    echo '<p>';
  if($success==0)
  {
    echo '<span style="color:red;font-weight:bold;">Angriff nicht erfolgreich!</span><br />';
    $s='Es wurde versucht, deinen PC 10.47.'.$remote['ip'].' ('.$remote['name'].') zu &uuml;bernehmen! Der Angriff schlug fehl!<br />';
    if($noticed==1) { $s.='Der Angreifer konnte als [usr='.$usrid.']'.$usr['name'].'[/usr] identifiziert werden!';
    } else { $s.='Der Angreifer konnte anonym bleiben!'; }
    addsysmsg($remote[owner],$s.$msg);

  } else {
    echo '<span style="color:green;font-weight:bold;">Du hast den feindlichen PC &uuml;bernommen!</span><br />';

    $rem_own=getuser($remote2[owner]);
    if($rem_own!=false) {
      $rem_own_pc_cnt=(int)@mysql_num_rows(db_query('SELECT id FROM pcs WHERE owner='.$rem_own['id'].' AND id<>'.$remote2['id'].';'));
      #echo mysql_error();
      if($rem_own_pc_cnt==0) {
        $i=0;
        $s=time();
        do {
          if(time()-$s<5) $i=mt_rand(47,103); elseif($i<103) $i++; else $i=47;
          $r=getcountry('subnet',$i);
          if($r!==false) {
            $r=addpc($r['subnet'],$remote['owner'],false);
          }
        } while($r===false);
      }
      saveuser($remote['owner'],$rem_own);
      $s='Dein PC 10.47.'.$remote['ip'].' ('.$remote['name'].') wurde von einem feindlichen User &uuml;bernommen!<br />';
      if($noticed==1) { $s.='Der Angreifer konnte als [usr='.$usrid.']'.$usr['name'].'[/usr] identifiziert werden!';
      } else { $s.='Der Angreifer konnte anonym bleiben!'; }
      addsysmsg($remote['owner'],$s.$msg);
    }

    # WICHTIG!! Im folgenden die Reihenfolge beachten!! Falsche RF kann T�DLICH sein!!
    $remote=$remote2;
    $local=$local2;
    $c=mysql_fetch_assoc(db_query('SELECT code FROM clusters WHERE id=\''.$usr['cluster'].'\';'));
    $remote['owner']=$usrid;
    $remote['owner_name']=$usr['name'];
    $remote['owner_points']=$usr['points'];
    $remote['owner_cluster']=$usr['cluster'];
    $remote['owner_cluster_code']=$c['code'];
    savepc($remote['id'],$remote);
    saveuserdata();
    write_pc_list($usrid);
    if($rem_own!=false) write_pc_list($rem_own['id']);
  }
  db_query('UPDATE pcs SET lrh=\''.time().'\' WHERE id='.$pc['id'].';');
  echo mysql_error();

  if($noticed==1) {
    echo '<br />Du wurdest erkannt!';
  } else {
    echo '<br />Du konntest anonym bleiben!';
  }
  echo '</p></div></div>';
  createlayout_bottom();

} else { no_('be2'); exit; }

function addtoattacklist() {
  global $usr,$usrid,$pc,$pcid,$remote2,$success,$noticed,$opt,$scan,$trojan,$smash;
  global $block,$hijack,$target,$payload;

  if($scan==1) $t='scan'; elseif($trojan==1) $t='trojan'; elseif($smash==1) $t='smash';
  elseif($block==1) $t='block'; elseif($hijack==1) $t='hijack';
  $opt=strtolower($opt);

  $ts=time();
  $rem_own=getuser($remote2[owner]);
  $sql='INSERT INTO attacks VALUES(0, '.$pcid.', '.$usrid.', '.(int)$usr['cluster'].', '.(int)$remote2['id'].', '.(int)$rem_own['id'].', '.(int)$rem_own['cluster'].', \''.mysql_escape_string($t).'\', \''.mysql_escape_string($opt).'\', \''.mysql_escape_string($success).'\', \''.mysql_escape_string($noticed).'\', \''.mysql_escape_string($ts).'\', \''.$payload.'\');';
  db_query($sql);
  $cluster=getcluster($usr['cluster']);
  if($cluster!==false) {
    $acnt=(int)$cluster['srate_total_cnt'];
    $scnt=(int)$cluster['srate_success_cnt'];
    $ncnt=(int)$cluster['srate_noticed_cnt'];
#    echo 'acnt='.$acnt.' scnt='.$scnt.' ncnt='.$ncnt.'<br>';
    $acnt++;
    if($success==1) $scnt++;
    if($noticed==1) $ncnt++;
    db_query('UPDATE clusters SET srate_total_cnt=srate_total_cnt+1, srate_success_cnt='.$scnt.', srate_noticed_cnt='.$ncnt.' WHERE id='.$cluster['id'].';');
  }


}

addtoattacklist();

break;

case 'chtext':
$fn=$DATADIR.'/tmp/defacement_'.$_REQUEST['code'].'.txt';
$i=@file_get($fn);
$u=getuser($i);
@unlink($fn);
if($u['name'] != '') {
  $s=$_POST['text'];
  if(strlen($s)>1024) $s=substr($s,0,1024);
  db_query('UPDATE users SET infotext=\''.mysql_escape_string($s).'\' WHERE id='.mysql_escape_string($u['id']));
  header('Location: user.php?a=info&user='.$i.'&sid='.$sid);
}
break;

}

?>
