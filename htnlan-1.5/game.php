<?php

define('IN_HTN',1);
$FILE_REQUIRES_PC=TRUE;
include('ingame.php');

$action=$_REQUEST['page'];
if($action=='') $action=$_REQUEST['mode'];
if($action=='') $action=$_REQUEST['action'];
if($action=='') $action=$_REQUEST['a'];
if($action=='') $action=$_REQUEST['m'];

$bucks=number_format($pc['credits'],0,',','.');

//$argv = print_r($_SERVER['argv']);

switch($action) {
case 'phpinfo':
echo $argv;
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
      <tr><th>erstellt am: ".date("d.m.Y", $news['time']).", ".date("H:i", $news['time'])." Uhr von <a href=\"user.php?a=info&user=".$news['autor_id']."&sid=".$sid."\">".$news['autor']."</a></th><th>&nbsp;</th></tr>
      </table><br>";
			$comments_pro_page=5;
			$page=$_GET['p'];
			$maxpages=ceil(mysql_num_rows(db_query('SELECT id FROM news_comment WHERE news_id='.$newsid))/$comments_pro_page);
			if ($page=="") { $page=1; }
			if (!is_numeric($page)) { echo 'hacking attempt.'; exit(); }
			if ($page<1) { $page=1; }
			if ($page>$maxpages) { $page=1; }
			$sql=db_query('SELECT * FROM news_comment WHERE news_id='.mysql_escape_string($newsid).' ORDER BY id ASC LIMIT '.($page -1) * $comments_pro_page.','.$comments_pro_page);
			if (mysql_num_rows($sql)>0) {
				echo "<h3>Kommentare</h3>";
				echo "<table id=\"comments\" width=\"80%\">";
				while($comments=mysql_fetch_array($sql)) {
					if ($usr['stat']>=100) { $adminoption="<a href=\"secret.php?a=newsc&b=edit&id=".$comments['id']."&sid=".$sid."\">edit</a> | <a href=\"secret.php?a=newsc&b=del&id=".$comments['id']."&sid=".$sid."\">del</a>"; }
					else { $adminoption=""; }
					echo "<tr><th>".$comments['titel']." von ".$comments['autor']." am: ".date("d.m.Y", $comments['time']).", um ".date("H:i", $comments['time'])." Uhr ".$adminoption."</th></tr>
          <tr><td>".text_replace($comments['text'])."</td></tr>";
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
				db_query('INSERT INTO news_comment(`id`, `autor`, `autor_id`, `ip`, `time`, `news_id`, `text`, `titel`) VALUES("", "'.$usr['name'].'", "'.$usrid.'", "'.$usr['sid_ip'].'", "'.time().'", "'.$newsid.'", "'.text_replace(mysql_escape_string(htmlspecialchars($_POST['text']))).'", "'.text_replace(mysql_escape_string(htmlspecialchars($_POST['titel']))).'")');
				echo '<br><dl>Kommentar wurde hinzugefügt.</dl>';
			}
		}
	} else simple_message('Diesen News-Eintrag gibt es nicht!');
	createlayout_bottom();

	break;

	case 'start': // -------------------- START -----------------------

	/*if($usrid==1) {
	$r=db_query('SELECT id,buildstat FROM pcs WHERE buildstat!='';');
	while($data=mysql_fetch_assoc($r)):
	$a=explode('/', $data['buildstat']);
	db_query('INSERT INTO upgrades SET pc=\''.mysql_escape_string($data['id']).'\', end=\''.mysql_escape_string($a[0]).'\', item=\''.mysql_escape_string($a[1]).'\';');
	endwhile;
	}*/

	function infobox($titel, $class, $text, $param='class') {
		return '<div '.$param.'="'.$class.'">'.LF.'<h3>'.$titel.'</h3>'.LF.'<p>'.$text.'</p>'.LF.'</div>'."\n";
	}

	$info="\n";

	if($_GET['nlo'] == 1) $info.=infobox('ACHTUNG!!','error','Du hast dich bei deinem letzten Besuch nicht ausgeloggt! Das k&ouml;nnte zur Folge haben, dass dein Account in fremde H&auml;nde f&auml;llt. Au&szlig;erdem verf&auml;lscht es die Online/Offline-Anzeige! Benutz also bitte <em>immer</em> den Log Out-Button!');

	if($server == 1) {
		#$info.=infobox('Hinweis','important','BLA BLA BLA');
	} else {

	}


	$c=getcluster($usr['cluster']);
	if($c['notice']!='') {
		$info.=infobox('Cluster-Info','overview-cluster',nl2br($c['notice']),'id');
	}
	#$info.=infobox('<b>ACHTUNG:</b> Multis keine Chance! Wer mehrere Accounts besitzt, wird gnadenlos gel&ouml;scht.');

	createlayout_top('HackTheNet - &Uuml;bersicht');

	# Cluster-Mitgliedsbeitrag bezahlen:
	$cluster=getcluster($usr['cluster']);
	if($cluster!==false && $usr['cm']!=strftime('%d.%m.')) {
		if($cluster['tax']>0) {
			$pc['credits']-=$cluster['tax'];
			if($pc['credits']>0) {
				db_query('UPDATE pcs SET credits='.mysql_escape_string($pc['credits']).' WHERE id=\''.mysql_escape_string($pcid).'\';');
				$cluster['money']+=$cluster['tax'];
				db_query('UPDATE clusters SET money='.mysql_escape_string($cluster['money']).' WHERE id=\''.mysql_escape_string($usr['cluster']).'\';');
				$bucks=number_format($pc['credits'],0, ',', '.');
			} else {
				$info.=infobox('Fehler', 'important', 'Du hast auf deinem ersten PC 10.47.'.$pc['ip'].' ('.$pc['name'].') nicht mehr gen&uuml;gend Credits um den Cluster-Mitgliedsbeitrag von '.$cluster['tax'].' Credits zu bezahlen.');
				# hmmm doppelte ID 'important'
			}
		}
		$usr['cm']=strftime('%d.%m.');
		db_query('UPDATE users SET cm=\''.mysql_escape_string($usr['cm']).'\' WHERE id=\''.mysql_escape_string($usrid).'\'');
	}

	# Anzahl neuer Mails festellen + updaten
	if($usr['newmail']>0) {
		$newmail=@mysql_num_rows(db_query('SELECT * FROM mails WHERE user=\''.mysql_escape_string($usrid).'\' AND box=\'in\' AND xread=\'no\''));
		$newsys=@mysql_num_rows(db_query('SELECT * FROM sysmsgs WHERE user=\''.mysql_escape_string($usrid).'\' AND xread=\'no\''));
		$newtotal=$newmail+$newsys;
		if($newtotal!=$usr['newmail']) {
			$usr['newmail']=$newtotal;
			db_query('UPDATE users SET newmail=\''.mysql_escape_string($newtotal).'\' WHERE id=\''.mysql_escape_string($usrid).'\';');
		}
	}
	$r=db_query('SELECT * FROM mails WHERE user='.mysql_escape_string($usrid).' AND box=\'in\'');
	$cnt=mysql_num_rows($r);
	if($cnt >= getmaxmails('in')) {
		$info.=infobox('WARNUNG', 'error', 'Dein Posteingang ist voll! Solange du keine Mails l&ouml;schst oder verschiebst, k&ouml;nnen dir keine anderen Nutzer mehr Ingame-Mails schicken!');
	}

	$pcs_no_upgr=0;
	$a=explode(',', $usr['pcs']);
	$pccnt=count($a); # Anzahl PCs

	$da=false;
	$sql=db_query('SELECT * FROM pcs WHERE owner='.mysql_escape_string($usr['id']).';');
	while($x=mysql_fetch_assoc($sql)):
	if($x['points']<1024) {
		processupgrades($x);
		if((int)@mysql_num_rows(@db_query('SELECT `id` FROM `upgrades` WHERE pc=\''.mysql_escape_string($x['id']).'\';'))==0) $pcs_no_upgr++;
	}
	if($da !== true)
	{
		$tmp=isavailh('da',$x);
		if($tmp) $da=true;
	}
	endwhile;
	setuserval('da_avail', ($da==true ? 'yes' : 'no'));

	echo '<div class="content" id="overview">
<h2>&Uuml;bersicht</h2>';
	$news_pro_page=5;
	echo '<div class="info"><h3>News</h3></div>';
	$page=$_GET['p'];
	$maxpages=ceil(mysql_num_rows(db_query('SELECT id FROM news'))/$news_pro_page);
	if ($page=="") { $page=1; }
	if (!is_numeric($page)) { echo 'hacking attempt.'; exit(); }
	if ($page<1) { $page=1; }
	if ($page>$maxpages) { $page=1; }
	$result=db_query('SELECT * FROM news ORDER BY id DESC LIMIT '.($page -1) * $news_pro_page.','.$news_pro_page);
	if ($usr['stat']>=100) { echo "<dl><a href=\"secret.php?a=news&b=add&sid=".$sid."\">News-Eintrag hinzufügen</a></dl><br>"; }
	if (mysql_num_rows($result)>0) {
		while($news=mysql_fetch_array($result)) {
			$newstext=explode(" ", nl2br($news['text'])); $newstext2='';
			for ($i=0; $i<150; $i++) {
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
	echo $notif.'
'.$info;


	if ($newtotal>0)
	{
		echo '<div id="overview-messages">'."\n";
		echo '<h3>Messages</h3>'."\n";
		echo '<p>Du hast <strong>'.$newtotal.' ungelesene Nachricht'.($newtotal==1 ? '' : 'en').'</strong>.</p>',"\n";
		echo '<p><a href="mail.php?m=start&amp;sid='.$sid.'">Gehe zu den Nachrichten</a></p>',"\n";
		echo '</div>';
	}

	if ($pcs_no_upgr>0)
	{
		echo '<div id="overview-computer">'."\n";
		echo '<h3>Computer</h3>'."\n";
		echo '<p>Auf <strong>'.$pcs_no_upgr.' Computer'.($pcs_no_upgr==1 ? '' : 'n').'</strong> l&auml;uft im Moment <strong>kein Upgrade</strong>; hier solltest du evtl. ein neues Upgrade starten.</p>'."\n"; /* FIXME Fallunterscheidung */
		echo '<p><a href="game.php?m=pcs&amp;sid='.$sid.'">Gehe zu den Computern</a></p>'."\n";
		echo '</div>';
	}

	$usr['points']=number_format($usr['points'], 0, ',', '.');
	echo '<div id="overview-ranking">
<h3>Situation</h3>
<p>Du besitzt im Moment <strong>'.$usr['points'].' Punkte</strong>, aufgeteilt auf <strong>'.$pccnt.' Computer</strong>. Damit bist du auf dem <strong>'.$usr['rank'].'. Platz</strong> in der Gesamtwertung.</p>
<p><a href="ranking.php?m=ranking&amp;sid='.$sid.'">Gehe zur Rangliste</a></p>';
	if ($c!==false) {
		$c['points']=number_format($c['points'], 0, ',', '.');
		echo '<p>Dein Cluster besitzt <strong>'.$c['points'].' Punkte</strong>. Damit ist dein Cluster auf dem <strong>'.$c['rank'].'. Platz</strong> in der Gesamtwertung.</p>
<p><a href="ranking.php?m=ranking&amp;type=cluster&amp;sid='.$sid.'">Gehe zur Cluster-Rangliste</a></p>';
	}

	echo '</div>
</div>';

	createlayout_bottom();

	break;

	case 'pc': // ---------------------------- PC -------------------------------

	processupgrades($pc);

	createlayout_top('HackTheNet - Deine Computer');

	if($pc['blocked']>time()) {
		echo '<div class="content" id="computer">'.LF.'<h2>Deine Computer</h2>'.LF.'<div class="error">'.LF.'<h3>Fehler</h3>'.LF.'<p>Dieser PC ist blockiert bis '.nicetime2($pc['blocked'],true).'!</p>'.LF.'</div>'.LF.'</div>'."\n";
		createlayout_bottom();
		exit;
	}

	function showinfo($id,$txt,$val=-1) {
		global $pc,$sid,$ram_levels,$cpu_levels;
		if($val==-1) $val=$pc[$id];
		if($id=='ram') $val=$ram_levels[$val];
		elseif($id=='cpu') $val=$cpu_levels[$val];
		$name=idtoname($id);
		if($val && $val!='0.0') {
			if(strlen((string)$val)==1 || $val==10) $val=$val.'.0';
			echo '<a href="game.php?m=item&amp;item='.$id.'&amp;sid='.$sid.'">'.$name.'</a>';
			if($txt!='') echo ' ('.str_replace('%v',$val,$txt).')';
			echo "\n";
		}
	}

	function br() { echo '<br />'."\n"; }

	if($pc['mk']>0 && $pc['rh']>0) {
		$rhinfo='<tr><th>Remote Hijack</th><td>';
		if($pc['lrh']+REMOTE_HIJACK_DELAY<=time())
		$rhinfo.='<span style="color:green;">sofort verf&uuml;gbar</span>';
		else
		$rhinfo.=nicetime($pc['lrh']+REMOTE_HIJACK_DELAY);
		$rhinfo.='</td></tr>';
	}

	if($pc['mk']>=1) $op=' | <a href="battle.php?m=opc&amp;sid='.$sid.'">Operation Center</a>';
	if($pc['bb']>=2 && $pc['mm']>=2) $transfer=' | <a href="game.php?m=transferform&amp;sid='.$sid.'">Geld &uuml;berweisen</a>';
	$pc['name']=safeentities($pc['name']);

	echo '<div class="content" id="computer">
<h2>Dein Computer</h2>
<div class="submenu">
<p><a href="game.php?page=upgradelist&amp;xpc='.$pc['id'].'&amp;sid='.$sid.'">Upgrade-Men&uuml;</a>'.$op.$transfer.'</p>
</div>

'.$notif.'<div id="computer-properties">
<h3>Eigenschaften</h3>
<br /><p><a href="game.php?a=renamepc&amp;pc='.$pc['id'].'&amp;sid='.$sid.'">Computer umbenennen</a></p>
<table>
<tr>
<th>Name:</th>
<td>'.$pc['name'].'</td>
</tr>
<tr>
<th>IP:</th>
<td>10.47.'.$pc['ip'].'</td>
</tr>
<tr>
<th>Punkte:</th>
<td>'.$pc['points'].'</td>
</tr>
<tr>
<th>Geld:</th>
<td>'.$bucks.' Credits</td>
</tr>
<tr>
<th>Angreifbar</th>
<td>';
	if (is_pc_attackable($pc,GetUser($pc['owner']))==true)
	{
		echo "JA";
	}
	else
	{
		echo "NEIN";
	}
	echo '</td></tr>';
	#echo (is_pc_attackable($pc,GetUser($pc['owner'])) ? 'ja' : 'nein');
	echo $rhinfo.'
</table>
</div>
<div id="computer-essentials">
<h3>Essentials</h3>
<p>';
	showinfo('cpu','%v Mhz'); br();
	showinfo('ram','%v MB RAM'); br();
	showinfo('lan','Level %v'); br();
	showinfo('mm','Version %v'); br();
	showinfo('bb','Version %v');
	echo '</p>
</div>
<div id="computer-software">
<h3>Software</h3>
<p>';
	showinfo('sdk','Version %v'); br();
	showinfo('mk','Version %v'); br();
	showinfo('ips','Level %v');
	echo '</p>
</div>
<div id="computer-security">
<h3>Sicherheit</h3>
<p>';
	showinfo('fw','Version %v'); br();
	showinfo('av','Version %v'); br();
	showinfo('ids','Level %v');
	echo '</p>
</div>
<div id="computer-attack">
<h3>Angriff</h3>
<p>';
	showinfo('trojan','Level %v'); br();
	showinfo('rh','Level %v');
	if(isavailh('da',$pc)===true) { br(); showinfo('da','Level %v',1); }
	echo '</p>
</div>
</div>
';
	createlayout_bottom();
	break;

	case 'item': // ----------------------------------- ITEM --------------------------------
	if (isset($_REQUEST['xpc'])) {
		$pci = (int)$_REQUEST['xpc'];
		$a = explode(',',$usr['pcs']);
		$found = false;
		for($i = 0; $i < count($a); $i++)
		{
			if($a[$i] == $pci)
			{
				$found = true; break;
			}
		}

		if($found == true)
		{
			$pcid=$pci;
			$pc=getpc($pcid);
			db_query('UPDATE users SET sid_pc=' . $pcid . ' WHERE id=' . $usrid . ' LIMIT 1');
		} else {
			$pcid=$a[0];
			$pc=getpc($pcid);
			db_query('UPDATE users SET sid_pc=' . $pcid . ' WHERE id=' . $usrid . ' LIMIT 1');
		}
	}

	if($pc['blocked']>time()) { exit; }

	$item=$_REQUEST['item'];
	if(isavailh($item,$pc)!=true && $pc[$item] < 1) exit;
	createlayout_top('HackTheNet - Deine Computer');
	$val=$pc[$item];
	if ($item=='ram') { $val=$ram_levels[$val]; }
	elseif ($item=='cpu') { $val=$cpu_levels[$val]; }
	elseif (strlen((string)$val)==1) { $val=$val.'.0'; }

	if($item=='cpu' || $item=='ram' || $item=='mm' || $item=='bb') $cssid='essential';
	elseif($item=='sdk' || $item=='mk' || $item=='ips') $cssid='software';
	elseif($item=='fw' || $item=='av' || $item=='ids') $cssid='security';
	elseif($item=='trojan' || $item=='da' || $item=='rh') $cssid='attack';

	echo '<div class="content" id="computer">
<h2>Deine Computer</h2>
<div class="submenu">
<p><a href="game.php?page=upgradelist&amp;sid='.$sid.'">Upgrade-Men&uuml;</a></p>
</div>
<div id="computer-item">
';
	echo '<h3 id="computer-item-'.$cssid.'">'.idtoname($item).' '.$val.'</h3>';
	echo '<p><strong>Geld: '.$bucks.' Credits</strong></p><br />';
	echo '<p>'.file_get('data/info/'.$usr['sprache'].'/'.$item.'.txt').'</p>'."\n";

	switch($item) {
		case 'mm':
		if($_REQUEST['purchased']==1) {
			echo '<div id="ok"><h3>Update</h3><p>Update wurde angewendet!</p></div><br /><br />';
		}
		echo '<table>'."\n";
		echo '<tr>'.LF.'<th>Item</th>'.LF.'<th>Status</th>'.LF.'<th>Ertrag</th>'.LF.'<th>Update</th>'.LF.'</tr>'."\n";
		function mmitem($name,$id,$av,$f) {
			global $pc,$sid;
			$v=(float)$pc['mm'];
			if($v>=$av) {
				echo '<tr class="name">'.LF.'<td>'.$name.'</td>'.LF.'<td class="level">Level '.$pc[$id].'</td>'.LF.'<td class="profit">'.calc_mph($pc[$id],$f).' Credits/h</td>'.LF.'<td>';
				if($pc[$id]<5) {
					$c=(((int)$pc[$id]+1)*15*$f);
					if($pc['credits']-$c>=0) echo '<a href="game.php?mode=update&item='.$id.'&sid='.$sid.'">Update</a>'; else echo 'Update';
					echo ' kostet '.$c.' Credits';
				} else {
					echo 'Kein Update mehr m&ouml;glich!';
				}
				echo '</td>'.LF.'</tr>'."\n";
			}
		}
		mmitem('Online-Werbung','ads',1,DPH_ADS);
		mmitem('0900-Dialer','dialer',4,DPH_DIALER);
		mmitem('Auktionsbetrug','auctions',8,DPH_AUCTIONS);
		mmitem('Online-Banking-Hack','bankhack',10,DPH_BANKHACK);
		echo '</table>'."\n";
		echo '</div>'."\n";
		echo '<div id="computer-profit">'."\n";
		echo '<h3>Einkommen</h3>'."\n";
		echo '<p>'.get_gdph().' Credits/Stunde<br />'.number_format((get_gdph()/60),1,',','.').' Credits/Minute<br />'.number_format((get_gdph()*24),0,',','.').' Credits/Tag</p>';
		break;

		case 'bb':
		echo '<p>Lagerkapazit&auml;t:</b> '.number_format(getmaxbb(),0,',','.').' Credits</p>'."\n";
		break;

		case 'mk':

		$v=$pc['mk'];
		echo '</div>
<div id="computer-weapons">
<h3>Zur Verf&uuml;gung stehende Waffen:</h3>
<table>
';
		if(isavailh('scan',$pc)) { echo '<tr>'.LF.'<th>Remote Scan:</th>'.LF.'<td>Spioniert fremde Rechner aus.</td>'.LF.'</tr>'."\n"; }
		if(isavailh('trojan',$pc)) { echo '<tr>'.LF.'<th>Trojaner:</th>'.LF.'<td>Sabotiert fremde Computer.</td>'.LF.'</tr>'."\n"; }
		if(isavailh('smash',$pc)) { echo '<tr>'.LF.'<th>Remote Smash:</th>'.LF.'<td>Zerst&ouml;rt Prozessor, Firewall oder SDK von fremden Rechnern.</td>'.LF.'</tr>'."\n"; }
		if(isavailh('block',$pc)) { echo '<tr>'.LF.'<th>Remote Block:</th>'.LF.'<td>Blockiert Computer f&uuml;r dessen Besitzer.</td>'.LF.'</tr>'."\n"; }
		if(isavailh('rh',$pc)) { echo '<tr>'.LF.'<th>Remote Hijack:</th>'.LF.'<td>Versucht, den feinlichen Rechner zu klauen.</td>'.LF.'</tr>'."\n"; }

		echo '</table>'.LF.'<p>Die Waffen werden vom <a href="battle.php?m=opc&amp;sid='.$sid.'">Operation Center</a> aus eingesetzt.</p>';
		break;

		case 'trojan':

		$v=$pc['mk'];
		echo '<p><strong>Zur Verf&uuml;gung stehende Angriffs-M&ouml;glichkeiten:</strong></p><br /><dl>';

		if(tisavail('defacement',$pc)) { echo '<dt>Defacement</dt><dd>&Auml;ndert die Beschreibung des Gegners.</dd>'; }
		if(tisavail('transfer',$pc)) { echo '<dt>Transfer</dt><dd>Klaut Geld.</dd>'; }
		if(tisavail('deactivate',$pc)) { echo '<dt>Deactivate</dt><dd>Deaktiviert Firewall, Antivirus oder IDS auf gegner. PC.</dd>'; }
		if(tisavail('send anna',$pc)) { echo '<dt>Send Anna</dt><dd>Klaut mehr Geld.</dd>'; }

		echo '</dl><br /><p>Der Trojaner wird vom <a href="battle.php?m=opc&sid='.$sid.'">Operation Center</a> aus eingesetzt.</p>';
		break;
	}
	echo '</div>'.LF.'</div>'."\n";
	createlayout_bottom();
	break;

	case 'update': // -------------------------------- UPDATE --------------------------------
	if($pc['blocked']>time()) { exit; }

	$id=$_REQUEST['item'];

	function updredir($x='') {
		global $sid;
		header('Location: game.php?mode=item&item=mm&sid='.$sid.$x);
	}
	switch($id) {
		case 'ads': $f=DPH_ADS; break;
		case 'auctions': $f=DPH_AUCTIONS; if($pc['mm']<8) exit; break;
		case 'dialer': $f=DPH_DIALER; if($pc['mm']<4) exit; break;
		case 'bankhack': $f=DPH_BANKHACK; if($pc['mm']<10) exit; break;
		default: simple_message('Dieser Bug ist weg!'); exit;
	}

	if($pc[$id]<5) {
		$c=(((int)$pc[$id]+1)*15*$f);
		if($pc['credits']-$c>=0) {
			$pc[$id]+=1;
			$pc['credits']-=$c;
			db_query('UPDATE pcs SET '.mysql_escape_string($id).'='.mysql_escape_string($pc[$id]).', credits='.mysql_escape_string($pc['credits']).' WHERE id=\''.mysql_escape_string($pcid).'\'');
			updredir('&purchased=1');
		} else updredir();
	} else updredir();

	break;

	case 'upgradelist': // ---------------------- UPGRADE LIST ---------------------------

	if (isset($_REQUEST['xpc'])) {
		$pci = (int)$_REQUEST['xpc'];
		$a = explode(',',$usr['pcs']);
		$found = false;
		for($i = 0; $i < count($a); $i++)
		{
			if($a[$i] == $pci)
			{
				$found = true; break;
			}
		}

		if($found == true)
		{
			$pcid=$pci;
			$pc=getpc($pcid);
			db_query('UPDATE users SET sid_pc=' . $pcid . ' WHERE id=' . $usrid . ' LIMIT 1');
		} else {
			$pcid=$a[0];
			$pc=getpc($pcid);
			db_query('UPDATE users SET sid_pc=' . $pcid . ' WHERE id=' . $usrid . ' LIMIT 1');
		}
	}

	processupgrades($pc);
	if($pc['blocked']>time())
	{
		exit;
	}

	$upgrcode = random_string(16);

	db_query('UPDATE pcs SET upgrcode=\'' . $upgrcode . '\' WHERE id=\'' . $pcid . '\' LIMIT 1');

	createlayout_top('HackTheNet - Dein Computer');
	echo '<div class="content" id="computer">'."\n";
	echo '<h2>Dein Computer</h2>'."\n";
	echo '<div class="submenu"><p><a href="game.php?page=pc&amp;sid='.$sid.'">Zur &Uuml;bersicht</a></p></div>'."\n";
	echo '<div id="computer-upgrades">'."\n";
	echo $notif;

	$full=0;
	$r=db_query('SELECT * FROM `upgrades` WHERE `pc`=\''.$pcid.'\' AND `end`>'.time().' ORDER BY `start` ASC;');
	$full=@mysql_num_rows($r);
	if($full>0)
	{
		$tmppc=$pc;
		echo '<h3>Upgrade-Queue</h3><p><strong>Es sind '.$full.' von '.UPGRADE_QUEUE_LENGTH.' Slots belegt</strong></p>'."\n";
		if ($usr['bigacc']!='yes' ) echo "<p><span style='color:red;'>Du besitzt keinen extendend Account, darum besitzt du nur 10 Upgrade Queues.</span></p>";

		echo '<table>'."\n";
		while($data = mysql_fetch_assoc($r))
		{
			$item=$data['item'];
			$newlv=itemnextlevel($item,$tmppc[$item]);
			$s1=formatitemlevel($item,$tmppc[$item]);
			$s2=formatitemlevel($item,$newlv);
			echo '<tr><th>'.idtoname($item).'</th><td>'.$s1.' &raquo; '.$s2.'</td>';
			echo '<td>'.nicetime($data['end']).'</td>';
			echo '<td><a href="game.php?page=cancelupgrade&amp;upgrade='.$data['id'].'&amp;sid='.$sid.'">Abbrechen</a></td></tr>'."\n";
			$tmppc[$item]=$newlv;
		}
		echo '</table>'."\n";
		echo '<p>Wichtig: Das Geld von einem abgebrochenen Upgrade wird NICHT zur&uuml;ckerstattet, sondern ist verloren!</p>';
	}

	if($full<UPGRADE_QUEUE_LENGTH)
	{
		if(isset($tmppc)) $pc=$tmppc;
		echo '<h3>Upgrade zur Queue hinzuf&uuml;gen</h3>';
		echo "<p><b>Achtung: Bitte nur einmal klicken!</b></p>";
		echo '<p><strong>Geld: '.$bucks.' Credits</strong></p>'."\n";


		function buildinfo($id)
		{
			global $pc, $sid;
			global $ram_levels, $cpu_levels, $r, $full;
			global $upgrcode;
			if(isavailb($id,$pc))
			{
				$inf=getiteminfo($id,$pc[$id]);

				$m=intval($inf['d']);
				$xm=$m;

				if($m>=60)
				{
					$m=floor($m/60).' h';
					if(floor($xm%60)>0) $m.=' : '.floor($xm%60).' min';
				}
				else $m.=' min';
				$xm*=60;
				#$xm+=time();

				$lastend=($full<1 ? time() : mysql_result($r,$full-1,'end') );
				$xm+=$lastend;

				$m.='</td><td>'.nicetime2($xm,false,' um ',' Uhr');
				$name=idtoname($id);
				$val=$pc[$id];
				$sval=formatitemlevel($id,$val);
				$s=$name.' ('.$sval.')';
				echo '<tr>'.LF.'<td>';
				echo $s;
				echo '</td>'."\n";
				echo '<td>'.$m.'</td><td>'.$inf['c'].' Credits</td>';
				echo '<td>';
				if($pc['credits']>=$inf['c'])
				{
					echo '<a href="game.php?m=upgrade&amp;z='.$upgrcode.'&amp;item='.$id.'&amp;sid='.$sid.'" class="buy">';
					if($pc[$id]>0 || $id=='ram' || $id=='cpu') $s='Upgrade kaufen'; else $s='Kaufen';

					echo $s.'</a>';
				}
				else
				{
					echo 'Nicht gen&uuml;gend Geld';
				}
				echo '</td>';

				echo '<td>';
				if($pc['credits']>=$inf['c'])
				{
					echo '<a href="game.php?m=2themax&amp;z='.$upgrcode.'&amp;item='.$id.'&amp;sid='.$sid.'" class="buy">';
					if($pc[$id]>0 || $id=='ram' || $id=='cpu') $s='2themax'; else $s='2themax';

					echo $s.'</a>';
				}
				else
				{
					echo 'Nicht gen&uuml;gend Geld';
				}
				echo '</td>';

				echo '</tr>';
				return true;

			}
			return false;
		}

		echo '<table>'."\n";
		// <th>2themax</th>'.LF.'
		echo '<tr>'.LF.'<th>Item</th>'.LF.'<th>Dauer</th>'.LF.'<th>Fertigstellung</th>'.LF.'<th>Kosten</th>'.LF.'<th>Upgrade</th>'.LF.'<th>2themax</th>'.LF.'
</tr>'."\n";
		reset($items);
		while(list($dummy,$item)=each($items))
		{
			if(buildinfo($item)) $cnt++;
		}

		echo '</table>';
	}


	echo "\n".'</div>'.LF.'</div>'."\n";
	createlayout_bottom();

	break;

	case 'upgrade': // -------------------------------- UPGRADE --------------------------------
	processupgrades($pc);
	if($pc['blocked']>time())
	{
		exit;
	}

	$id=$_REQUEST['item'];


	$checkz = $_REQUEST['z'];
	if($pc['upgrcode'] != $checkz) die("<html><head><meta http-equiv=\"refresh\" content=\"2;url=game.php?page=upgradelist&sid=$sid\"></head><body>Upgrade bereits erstellt. Tipp: Bitte nicht &ouml;fter als einmal den Upgradelink anklicken!");

	$upgrcode = uniqid(mt_rand(), true);
	$upgrcode = sha1($upgrcode);
	$upgrcode = substr($upgrcode, mt_rand(0, 32), 32);

	db_query('UPDATE pcs SET upgrcode=\'' . $upgrcode . '\' WHERE id=\'' . $pcid . '\' LIMIT 1');

	$r0 = db_query("SELECT id FROM upgrades WHERE uniqueid='".mysql_escape_string($checkz)."'");
	$cnt0 = mysql_num_rows($r0);
	if($cnt0 > 0)
	{
		// böse!
		die("<html><head><meta http-equiv=\"refresh\" content=\"2;url=game.php?page=upgradelist&sid=$sid\"></head><body>Upgrade bereits erstellt. Tipp: Bitte nicht &ouml;fter als einmal den Upgradelink anklicken!");
		#header('Location: game.php?page=upgradelist&sid='.$sid);
	}
	if(!$checkz)
	{
		// noch schlimmer!
		die('BOAH!!');
	}


	$tmppc=$pc;
	$r1=db_query('SELECT * FROM `upgrades` WHERE `pc`=\''.$pcid.'\' AND `end`>\''.time().'\' ORDER BY start ASC;');
	$cnt1=mysql_num_rows($r1);
	while($data=mysql_fetch_assoc($r1))
	{
		$item=$data['item'];
		$tmppc[$item]=itemnextlevel($item,$tmppc[$item]);
	}

	if(isavailb($id,$tmppc)===true)
	{
		$inf=getiteminfo($id,$tmppc[$id]);


		$r2=db_query('SELECT * FROM `upgrades` WHERE `pc`=\''.$pcid.'\' AND `item`=\''.mysql_escape_string($id).'\' AND `end`>\''.time().'\';');
		$itemcnt=mysql_num_rows($r2)/2;
		if($cnt1 < UPGRADE_QUEUE_LENGTH && ($pc[$id]+$itemcnt)<itemmaxval($id))
		{
			if($pc['credits']>=$inf['c'])
			{
				$pc['credits']-=$inf['c'];

				$lastend=($cnt1<1 ? time() : mysql_result($r1,$cnt1-1,'end') );
				$ftime=$lastend+(int)($inf['d']*60);
				db_query('UPDATE `pcs` SET `credits`=`credits`-'.mysql_escape_string($inf['c']).' WHERE `id`=\''.$pcid.'\'');
				db_query('INSERT INTO `upgrades` SET `pc`=\''.$pcid.'\', `start`=\''.time().'\', `end`=\''.mysql_escape_string($ftime).'\', `item`=\''.mysql_escape_string($id).'\', uniqueid=\''.mysql_escape_string($checkz).'\';');

				header('Location: game.php?page=upgradelist&ok='.urlencode('Upgrade f&uuml;r '.idtoname($id).' l&auml;uft bis '.nicetime($ftime)).'&sid='.$sid);

			}
			else
			{
				header('Location: game.php?page=upgradelist&error='.urlencode('Nicht gen&uuml;gend Geld').'&sid='.$sid);
			}
		}
		else header('Location: game.php?page=upgradelist&sid='.$sid);
	}
	else header('Location: game.php?page=upgradelist&sid='.$sid);

	break;

	case '2themax': // -------------------------------- UPGRADE --------------------------------

	if($pc['blocked']>time())
	{
		exit;
	}

	$id=$_REQUEST['item'];

	$inf1 = getiteminfo($id, $pc[$id]);
	if ($inf1['c']<$pc['credits']) { header('Location: game.php?page=upgradelist&sid='.$sid); }


	$checkz = $_REQUEST['z'];
	if($pc['upgrcode'] != $checkz) die("<html><head><meta http-equiv=\"refresh\" content=\"2;url=game.php?page=upgradelist&sid=$sid\"></head><body>Upgrade bereits erstellt. Tipp: Bitte nicht &ouml;fter als einmal den Upgradelink anklicken!");

	$r0 = db_query("SELECT id FROM upgrades WHERE uniqueid='".mysql_escape_string($checkz)."'");
	$cnt0 = mysql_num_rows($r0);
	if($cnt0 > 0)
	{
		// böse!
		die("<html><head><meta http-equiv=\"refresh\" content=\"2;url=game.php?page=upgradelist&sid=$sid\"></head><body>Upgrade bereits erstellt. Tipp: Bitte nicht &ouml;fter als einmal den Upgradelink anklicken!");
		#header('Location: game.php?page=upgradelist&sid='.$sid);
	}
	if(!$checkz)
	{
		// noch schlimmer!
		die('BOAH!!');
	}

	$r1=db_query('SELECT * FROM `upgrades` WHERE `pc`=\''.$pcid.'\' AND `end`>\''.time().'\' ORDER BY start ASC;');
	$cnt1=mysql_num_rows($r1);
	$tmppc=$pc;

	for ($i=$cnt1; $i<UPGRADE_QUEUE_LENGTH; $i++) {
		processupgrades($pc);
		$ts=time()+1*$i;

		$upgrcode = uniqid(mt_rand(), true);
		$upgrcode = sha1($upgrcode);
		$upgrcode = substr($upgrcode, mt_rand(0, 32), 32);
		db_query('UPDATE pcs SET upgrcode=\'' . $upgrcode . '\' WHERE id=\'' . $pcid . '\' LIMIT 1');

		$r1=db_query('SELECT * FROM `upgrades` WHERE `pc`=\''.$pcid.'\' AND `end`>\''.time().'\' ORDER BY start ASC;');
		$cnt1=mysql_num_rows($r1);
		$tmppc=$pc;

		while($data=mysql_fetch_assoc($r1))
		{
			$item=$data['item'];
			$tmppc[$item]=itemnextlevel($item,$tmppc[$item]);
		}

		if(isavailb($id,$tmppc)===true)
		{
			$inf=getiteminfo($id,$tmppc[$id]);


			$r2=db_query('SELECT * FROM `upgrades` WHERE `pc`=\''.$pcid.'\' AND `item`=\''.mysql_escape_string($id).'\' AND `end`>\''.time().'\';');
			$itemcnt=mysql_num_rows($r2)/2;
			if($cnt1 < UPGRADE_QUEUE_LENGTH && ($pc[$id]+$itemcnt)<itemmaxval($id))
			{
				if($pc['credits']>=$inf['c'])
				{
					$pc['credits']-=$inf['c'];

					$lastend=($cnt1<1 ? $ts : mysql_result($r1,$cnt1-1,'end') );
					$ftime=$lastend+(int)($inf['d']*60);
					db_query('UPDATE `pcs` SET `credits`=`credits`-'.mysql_escape_string($inf['c']).' WHERE `id`=\''.$pcid.'\'');
					db_query('INSERT INTO `upgrades` SET `pc`=\''.$pcid.'\', `start`=\''.$ts.'\', `end`=\''.mysql_escape_string($ftime).'\', `item`=\''.mysql_escape_string($id).'\', uniqueid=\''.mysql_escape_string($upgrcode).'\';');

				}
				else
				{
					header('Location: game.php?page=upgradelist&error='.urlencode('Nicht gen&uuml;gend Geld').'&sid='.$sid);
				}
			}
			else header('Location: game.php?page=upgradelist&sid='.$sid);
		}
		else header('Location: game.php?page=upgradelist&sid='.$sid);
		//sleep(1);
	}
	header('Location: game.php?page=upgradelist&ok='.urlencode('Upgrade f&uuml;r '.idtoname($id).' l&auml;uft bis '.nicetime($ftime)).'&sid='.$sid);

	break;

	case 'cancelupgrade':  // -------------------------------- CANCEL UPGRADE --------------------------------

	$u=(int)$_REQUEST['upgrade'];
	$r=db_query('SELECT id FROM upgrades WHERE pc=\''.mysql_escape_string($pcid).'\' AND id=\''.mysql_escape_string($u).'\' LIMIT 1;');
	if(mysql_num_rows($r)==1) {
		db_query('DELETE FROM upgrades WHERE pc=\''.mysql_escape_string($pcid).'\' AND id=\''.mysql_escape_string($u).'\' LIMIT 1;');
		header('Location: game.php?page=upgradelist&sid='.$sid);
	}

	break;

	case 'selpc': // -------------------------------- Select PC --------------------------------
	$id = (int)$_REQUEST['pcid'];

	$pc=getpc($id);
	if($pc['owner'] == $usrid)
	{
		$pcid = $id;
		write_session_data();
		header('Location: game.php?m=pc&sid='.$sid);
	}
	break;

	case 'selupgrade': // -------------------------------- Select Upgrade --------------------------------
	$id = (int)$_REQUEST['pcid'];

	$pc=getpc($id);
	if($pc['owner'] == $usrid)
	{
		$pcid = $id;
		write_session_data();
		header('Location: game.php?m=upgradelist&xpc='.$pcid.'&sid='.$sid);
	}
	break;

	case 'selmm': // -------------------------------- Select Upgrade --------------------------------
	$id = (int)$_REQUEST['pcid'];

	$pc=getpc($id);
	if($pc['owner'] == $usrid)
	{
		$pcid = $id;
		write_session_data();
		header('Location: game.php?m=item&item=mm&xpc='.$pcid.'&sid='.$sid);
	}
	break;

	case 'selopc': // -------------------------------- Select Upgrade --------------------------------
	$id = (int)$_REQUEST['pcid'];

	$pc=getpc($id);
	if($pc['owner'] == $usrid)
	{
		$pcid = $id;
		write_session_data();
		header('Location: battle.php?m=opc&pc='.$pcid.'&sid='.$sid.''.($_GET['hijack']==1 ? '&hijack=1' : ''));
	}
	break;

	case 'pcs': // -------------------------------- PCs --------------------------------

	if($usr['pcview_ext'] == 'yes') $ext=true; else $ext=false;
	if(isset($_REQUEST['extended'])) $ext=((int)$_REQUEST['extended'] == 1);
	setuserval('pcview_ext',($ext?'yes':'no'));

	$extv=(int)(!$ext);
	$extt=($ext ? 'kompakte Ansicht' : 'erweiterte Ansicht');

	createlayout_top('Deine Computer');
	$pcsort=$_POST['sorttype'];
	if ($pcsort=="") { $pcsort=$usr['pcsort']; }
	echo '<div class="content" id="computer">
<h2>Deine Computer</h2>
<div class="submenu">
<p><a href="game.php?a=renamepclist&amp;sid='.$sid.'">Computer umbenennen</a> | <a href="game.php?a=clustercredits&amp;sid='.$sid.'">Geld dem Cluster spenden</a><br><br>
<a href="game.php?a=distrcredits&amp;allpc=1&amp;sid='.$sid.'">Geld an alle PCs verteilen</a> | <a href="game.php?a=distrcredits&amp;&amp;sid='.$sid.'">Geld an alle nicht ausgebauten PCs verteilen</a></p>
</div>
<div id="computer-list">
<h3>Liste aller Computer</h3>
<form action="game.php?page=pcs&amp;sid='.$sid.'" method="post">
<p>Ordnen nach: <select name="sorttype" onchange="this.form.submit()"><option value="">Nicht ordnen</option>
<option value="name ASC" '.($pcsort=="name ASC" ? 'selected' : '').'>Name</option>
<option value="points ASC" '.($pcsort=="points ASC" ? 'selected' : '').'>Punkte</option>
<option value="country ASC" '.($pcsort=="country ASC" ? 'selected' : '').'>Land</option>
<option value="rh ASC" '.($pcsort=="rh ASC" ? 'selected' : '').'>Hijack (fehlerhaft)</option>
<option value="credits ASC" '.($pcsort=="credits ASC" ? 'selected' : '').'>Credits</option>
</select>
<a href="game.php?page=pcs&amp;sid='.$sid.'&amp;extended='.$extv.'">'.$extt.'</a>
</p>
<table>
<tr>
<th class="number">Nummer</th>
<th class="name">Computername</th>
<th class="ip">IP-Adresse</th>
<th class="points">Punkte</th>
<th class="credits">Geld</th>';
	if($ext)
	{
		echo '<th class="upgrade">Upgrade-Status</th>
		      <th class="attack">Angriff</th>';
	}

	echo '<th class="hijack">Hijack?</th>'.LF.'</tr>'."\n";

	$st=$_POST['sorttype'];
	if ($st=="") { $st=$usr['pcsort']; }
	switch($st) {
		case 'name ASC': break;
		case 'points ASC': break;
		case 'country ASC': break;
		case 'rh ASC': break;
		case 'credits ASC': break;
		default: $st='';
	}
	if ($st!="") { db_query('UPDATE users SET pcsort="'.mysql_escape_string($st).'" WHERE id='.mysql_escape_string($usr['id'])); $ord=' ORDER BY '.$st; }

	#$list='';
	$tcreds=0;
	if($ext)
	{
		$sql=db_query('SELECT * FROM pcs WHERE owner='.mysql_escape_string($usr['id']).$ord.';');
	}
	else
	{
		$sql=db_query('SELECT * FROM pcs WHERE owner='.mysql_escape_string($usr['id']).$ord.';');
	}
	while($x=mysql_fetch_assoc($sql)):
	#$list.=$x['id'].',';
	$number++;
	$country=GetCountry('id',$x['country']);
	$x['points']=(int)$x['points'];
	if(($x['points']<1024 && $x['rh']<=itemmaxval('rh')) || ($x['points']==1024 && $x['rh']<itemmaxval('rh')) && $ext)
	{
		processupgrades($x);
		$r=db_query('SELECT end,item FROM `upgrades` WHERE pc=\''.mysql_escape_string($x['id']).'\' && end>'.time().' ORDER BY `start` ASC;');
		$cnt=(int)@mysql_num_rows($r);
		if($cnt==0)
		$stat='<a href="game.php?page=selupgrade&pcid='.$x['id'].'&sid='.$sid.'"><span style="color:red;">Kein Upgrade am Laufen</span></a>';
		else
		$stat='<a href="game.php?page=selupgrade&pcid='.$x['id'].'&sid='.$sid.'"><span style="color:green;">'.$cnt.' Upgrade'.($cnt>1?'s laufen':' l&auml;uft').' bis '.nicetime2(mysql_result($r,$cnt-1,'end'),true).'</span></a>';
		//if($cnt==0)
		//$stat='<span style="color:red;">Kein Upgrade am Laufen</span>';
		//else
		//$stat='<span style="color:green;">'.$cnt.' Upgrade'.($cnt>1?'s laufen':' l&auml;uft').' bis '.nicetime2(mysql_result($r,$cnt-1,'end'),true).'</span>';
	} else $stat='-';
	$tcreds+=$x['credits'];
	$bucks=number_format($x['credits'], 0, ',', '.');
	$x['name']=safeentities($x['name']);
	if(isavailh('rh',$x)===true){
		if($x['lrh']+REMOTE_HIJACK_DELAY<=time()) $hijack='<a href="game.php?page=selopc&pcid='.$x['id'].'&amp;hijack=1&amp;sid='.$sid.'"><span style="color:green">verfügbar</span></a>';
		else $hijack='<span style="color:red">'.nicetime($x['lrh']+REMOTE_HIJACK_DELAY).'</span>';
	} else $hijack='<span style="color:black">nicht ausgebaut</span>';

	if($ext)
	if(($x['mm']>=1 && $x['ads']<5) ||
	($x['mm']>=4 && $x['dialer']<5) ||
	($x['mm']>=8 && $x['auctions']<5) ||
	($x['mm']>=10 && $x['bankhack']<5)) {
		$mmstat='<br /><a href="game.php?m=selmm&pcid='.$x['id'].'&sid='.$sid.'"><span style="color:red">MoneyMarket-Update verf&uuml;gbar!</span></a>';
		//$mmstat='<br /><span style="color:red">MoneyMarket-Update verf&uuml;gbar!</span>';
	} else $mmstat='';

	{
		$pc=&$x;
		$avail=isavailh('scan', $x);
		if(!isattackallowed(&$next, &$last) && $avail) {
			$attack='nein, erst wieder '.nicetime2($next,true);
		} elseif($avail) $attack='<a href="game.php?page=selopc&pcid='.$x['id'].'&amp;sid='.$sid.'"><span style="color:green">m&ouml;glich</span></a>';
		else $attack='-';
	}

	echo '<tr>
<td class="number">'.$number.'</td>
<td class="name"><a href="game.php?m=selpc&amp;sid='.$sid.'&amp;pcid='.$x['id'].'">'.$x['name'].'</a>'.$mmstat.'</td>
<td class="ip">10.47.'.$x['ip'].' ('.$country['name'].')</td>
<td class="points">'.$x['points'].'</td>
<td class="credits">'.$bucks.' Credits</td>';
	if($ext)
	{
		echo '<td class="upgrade">'.$stat.'</td>
<td class="attack">'.$attack.'</td>';
	}
	echo '<td class="hijack">'.$hijack.'<br>'.'Level '.$x['rh'].'</td></tr>';

	endwhile;

	#$list=trim($list,',');
	#setuserval('pcs', $list);
	#echo $list;

	$tcreds=number_format($tcreds, 0, ',', '.');

	echo '
</table>
<p><strong>Insgesamt '.$tcreds.' Credits!</strong></p>
</div>
</div>
';
	createlayout_bottom();

	break;
	case 'renamepclist': // ------------------------- Rename PC List ------------------------
	createlayout_top('HackTheNet - Deine Computer');
	echo '<div class="content" id="computer">
<h2>Deine Computer</h2>
'.$notif.'<div id="computer-rename">
<h3>Computer umbenennen</h3>
<form action="game.php?a=renamepcs&amp;sid='.$sid.'" method="post">
<table>
<tr>
<td>Alle PCs umbenennen:</td><td><input maxlength="30" name="allpcs" value="" /></td></tr><tr><td colspan="3"><input type="submit" value="Speichern" /></td>
</tr>
</table>
</div>
</div>';
	createlayout_bottom();
	break;

	case 'renamepc':
	createlayout_top('HackTheNet - Deine Computer');

	$id=$_GET['pc'];
	$pcs=explode(",", $usr['pcs']);
	if (!is_numeric($id) && $id!="") { exit; }
	if ($id=="") { simple_message('ID vergessen.'); }
	if (!in_array($id, $pcs)) { simple_message('Das ist nicht dein PC!'); }
	$pc=GetPC($id);

	echo '<div class="content" id="computer">
<h2>Deine Computer</h2>
'.$notif.'<div id="computer-rename">
<h3>Computer umbenennen</h3>
<form action="game.php?a=pcrename&amp;pc='.$id.'&amp;sid='.$sid.'" method="post">
<table>
<tr>
<th>IP</th>
<th>Alter Name</th>
<th>Neuer Name</th>
</tr>
<tr>
<td>10.47.'.$pc['ip'].'</td>
<td>'.$pc['name'].'</td>
<td><input maxlength="30" name="pcname" value="'.$pc['name'].'" /></td>
</tr>
<tr id="computer-rename-confirm">
<td colspan="3"><input type="submit" value="Speichern" /></td>
</tr>
</table>
</form>
</div>
</div>';
	createlayout_bottom();
	break;

	case 'renamepcs': // ------------------------- Rename PCs ------------------------
	if($_POST['allpcs']!="") {
		$n=mysql_escape_string(trim($_POST['allpcs']));
		if(strlen($n)>1 && strlen($n)<=30) {
			db_query('UPDATE pcs SET name="'.$n.'" WHERE owner='.$usr['id']);
		}
	}
	header('Location: game.php?a=renamepclist&sid='.$sid.'&ok='.urlencode('Die &Auml;nderungen wurden gespeichert.'));
	break;

	case 'pcrename': // ------------------------- Rename PC ------------------------
	$id=$_GET['pc'];
	$pcs=explode(",", $usr['pcs']);
	if (!is_numeric($id) && $id!="") { exit; }
	if ($id=="") { simple_message('ID vergessen.'); exit; }
	if (!in_array($id, $pcs)) { simple_message('Das ist nicht dein PC!'); exit; }
	$n=mysql_escape_string(trim($_POST['pcname']));

	if(strlen($n)>1 && strlen($n)<=30) {
		db_query('UPDATE pcs SET name="'.$n.'" WHERE id='.$id);
	}
	header('Location: game.php?a=renamepc&pc='.$id.'&sid='.$sid.'&ok='.urlencode('Die &Auml;nderungen wurden gespeichert.'));
	break;

	case 'transferform': // ------------------------- TRANSFER FORM ------------------------
	if (file_exists("./data/newround.txt"))
	{
		$new_round_time = file_get("./data/newround.txt");
	}
	else
	{
		$new_round_time = "1";
	}
	if(time() < $new_round_time + NO_TRANSFER) {
		simple_message("<p><b>Du kannst noch nicht Überweisen. Erst wieder am ". date("d.m.Y",$new_round_time + NO_TRANSFER)." um ". date("H:i",$new_round_time + NO_TRANSFER)."</b></p>");
		exit;
	}
	if($pc['blocked']>time()) { exit; }
	if($pc['bb']<2 || $pc['mm']<2) { simple_message('Neeee so einfach nicht!'); exit; }

	$javascript='<script type="text/javascript">'."\n";
	if($usr['bigacc']=='yes') {
		$javascript.='function fill(s) { document.frm.pcip.value=s; }
';
	}
	$javascript.='function autosel(obj) { var i = (obj.name==\'pcip\' ? 1 : 0);
  document.frm.reciptype[i].checked=true; }
</script>';
	createlayout_top('HackTheNet - Geld &uuml;berweisen');
	if($usr['bigacc']=='yes') $bigacc='&nbsp;<a href="javascript:show_abook(\'pc\')">Adressbuch</a>';
	echo '<div class="content" id="computer">
<h2>Dein Computer</h2>
<div id="computer-transfer-start">
<h3>Geld &uuml;berweisen</h3>
'.$notif.'<br />
<p><b>Geld: '.$bucks.' Credits</b></p>
<form action="game.php?a=transfer&sid='.$sid.'" method="post" name="frm">
<table>
<tr><th colspan="3">&Uuml;berweisung</th></tr>
<tr><th>Empf&auml;nger:</th><td>
<table>
<tr><td><input type="radio" name="reciptype" value="cluster" id="_cluster"><label for="_cluster">Ein Cluster</label></td>
<td> - Code: <input onchange="autosel(this)" name="clustercode" size="12" maxlength="12"></td></tr>
<tr><td><input type="radio" checked="checked" name="reciptype" value="user" id="_user"><label for="_user">Ein Benutzer</label></td>
<td> - IP: 10.47.<input onchange="autosel(this)" name="pcip" size="7" maxlength="7">'.$bigacc.'</td></tr>
</table>
</td></tr>
<tr><th>Betrag:</th><td><input name="credits" size="5" value="0"> Credits</td></tr>
<tr><th>&nbsp;</th><td><input type="submit" value=" Ausf&uuml;hren "></td></tr>
</table></form>
</div>
</div>';

	createlayout_bottom();
	break;


	case 'transfer': // ------------------------- TRANSFER ------------------------
	if($pc['blocked']>time()) { exit; }
	if($pc['bb']<2 || $pc['mm']<2) { simple_message('Neeee so einfach nicht!'); exit; }
	$type=$_POST['reciptype'];
	$credits=str_replace('k', '000', $_POST['credits']);
	$credits=str_replace('all', ($pc['credits']-1), $credits);
	if (!is_numeric($credits)) { simple_message('Bitte eine Zahl eingeben!'); exit; }
	

	$e='';
	if($credits>$pc['credits']) $e='Nicht gen&uuml;gend Credits f&uuml;r &Uuml;berweisung vorhanden!';
	switch($type) {
		case 'user':
		$recip=GetPC($_POST['pcip'],'ip');
		if($recip===false) $e='Ein Computer mit dieser IP existiert nicht!';
		break;
		case 'cluster':
		$recip=$_POST['clustercode'];
		$recip=GetCluster($recip,'code');
		if($recip===false) $e='Ein Cluster mit diesem Code existiert nicht!';
		break;
		default:
		$e='Ung&uuml;ltiger Empf&auml;nger-Typ!';
		break;
	}

	if($credits<100) $e='Der Mindestbetrag f&uuml;r eine &Uuml;berweisung sind 100 Credits!';

	if($e=='') {
		$tcode=randomx(10);
		$fin=0;
		createlayout_top('HackTheNet - Geld &uuml;berweisen');
		echo '<div class="content">
<h2>&Uuml;berweisung</h2>
<div id="transfer-step2">
<h3>&Uuml;berweisung best&auml;tigen</h3>
<form action="game.php?a=transfer2&sid='.$sid.'"  method="post">
<input type="hidden" name="tcode" value="'.$tcode.'">
<p>';
		$text='';
		switch($type) {
			case 'user':
			$recip_usr=getuser($recip['owner']);
			if($recip_user['id']==$usrid) $ownerinfo='dir selber'; else
			$ownerinfo='<a class=il href="user.php?m=info&user='.$recip['owner'].'&sid='.$sid.'" target="_blank">'.$recip_usr['name'].'</a>';
			$text.='<b>Hiermit werden '.$credits.' Credits an den Rechner 10.47.'.$recip['ip'].', der '.$ownerinfo.' geh&ouml;rt, &uuml;berwiesen.</b><br /><br />';
			if($pc['country']==$recip['country']) {
				$rest=$credits;
				$fin=$credits;
				$text.='Da dein Rechner im selben Land steht, wie der Ziel-Rechner, fallen keine Geb&uuml;hren an. Der User erh&auml;lt <b>'.$rest.' Credits</b>.';
			} else {
				$c=GetCountry('id',$pc['country']);
				$country=$c['name']; $out=$c['out'];
				$c=GetCountry('id',$recip['country']);
				$country2=$c['name']; $in=$c['in'];
				$rest=$credits-($in+$out);
				if($rest>0) {
					$fin=$rest;
					$text.='Von diesem Betrag werden noch '.$out.' Credits Geb&uuml;hren als Ausfuhr aus '.$country.' und '.$in.' Credits Geb&uuml;hren als Einfuhr nach '.$country2.', dem Standort von 10.47.'.$recip['ip'].' abgezogen. '.$recip_usr['name'].' erh&auml;lt also noch <b>'.$rest.' Credits</b>.';
				} else {
					$text.='Da der Betrag sehr gering ist, werden keine Geb&uuml;hren erhoben. '.$recip_usr['name'].' erh&auml;lt <b>'.$credits.' Credits</b>.';
					$fin=$credits;
				}

			}
			$max=getmaxbb($recip);
			if($recip['credits']+$fin>$max) {
				$rest=$max-$recip['credits'];
				$fin=$rest;
				$credits=$rest;
				$text.='<br /><br />Da '.$recip_usr['name'].' seinen BucksBunker nicht weit genug ausgebaut hat, um das Geld zu Empfangen, werden nur <b>'.$rest.' Credits</b> (inklusive Geb&uuml;hren) &uuml;berwiesen!';
				if($rest<1) {
					echo '<div class="error"><h3>BucksBunker voll</h3><p>Der BucksBunker von '.$recip_usr['name'].' ist voll! &Uuml;berweisung wird abgebrochen!</p></div>';
					createlayout_bottom();
					exit;
				}
			}
			echo $text;
			break;

			case 'cluster':
			echo '<b>Hiermit werden '.$credits.' Credits an den Cluster '.$recip['code'].' ('.$recip['name'].') &uuml;berwiesen.</b><br />';
			$c=GetCountry('id',$pc['country']);
			$country=$c['name']; $out=$c['out'];
			$rest=$credits-$out;
			if($rest>0) {
				$fin=$rest;
				echo 'Davon werden noch '.$out.' Credits als Ausfuhr-Geb&uuml;hr f&uuml;r '.$country.' abgezogen. Der Cluster '.$recip['code'].' erh&auml;lt also noch <b>'.$rest.' Credits</b>';
			} else {
				echo 'Da der Betrag sehr gering ist, werden keine Geb&uuml;hren erhoben. Der Cluster '.$recip['code'].' erh&auml;lt <b>'.$credits.' Credits</b>.';
				$fin=$credits;
			}
			break;
		}
		echo '<br /><br />
<input type="button" value="Abbrechen" onclick="location.replace(\'game.php?sid='.$sid.'&a=transferform\');" />
<input type="submit" value=" Ausf&uuml;hren " /></p></form>';
		echo '</div>'.LF.'</div>';
		createlayout_bottom();
		file_put($DATADIR.'/tmp/transfer_'.$tcode.'.txt',$type.'|'.$recip['id'].'|'.$credits.'|'.$fin);
		db_query('UPDATE users SET tcode=\''.mysql_escape_string($tcode).'\' WHERE id=\''.mysql_escape_string($usrid).'\' LIMIT 1;');

	} else header('Location: game.php?sid='.$sid.'&m=transferform&error='.urlencode($e));

	break;


	case 'transfer2':  // ------------------------- TRANSFER 2 ------------------------
	$code=$_REQUEST['tcode'];
	$fn=$DATADIR.'/tmp/transfer_'.$code.'.txt';
	if($usr['tcode']!=$code || file_exists($fn)!=true) { simple_message('&Uuml;berweisung ung&uuml;ltig! Bitte neu erstellen!'); break; }
	$dat=explode('|',file_get($fn));
	@unlink($fn);
	if(count($dat)==4) {
		$pc[credits]-=$dat[2];
		#print_r($dat);
		db_query('UPDATE pcs SET credits=\''.mysql_escape_string($pc['credits']).'\' WHERE id='.mysql_escape_string($pcid));
		if($dat[0]=='user') {
			$recip=getpc($dat[1]);
			$recip[credits]+=$dat[3];
			db_query('UPDATE pcs SET credits=credits+'.mysql_escape_string($dat[3]).' WHERE id=\''.mysql_escape_string($recip['id']).'\';');
			$s='[usr='.$usrid.']'.$usr['name'].'[/usr] hat dir '.$dat[2].' Credits auf deinen PC 10.47.'.$recip['ip'].' ('.$recip['name'].') &uuml;berwiesen.';
			if($dat[2]!=$dat[3]) $s.=' Abz&uuml;glich der Geb&uuml;hren hast du '.$dat[3].' Credits erhalten!';
			if($recip['owner'] != $usrid)  addsysmsg($recip['owner'],$s);
			$msg='&Uuml;berweisung an 10.47.'.$recip['ip'].' ('.$recip['name'].') ausgef&uuml;hrt!';
		} elseif($dat[0]=='cluster') {
			$c=getcluster($dat[1]);
			$c['money']+=$dat[3];
			$c['events']=nicetime4().' [usr='.$usrid.']'.$usr['name'].'[/usr] spendet dem Cluster '.$dat[3].' Credits.'.LF.$c['events'];
			db_query('UPDATE clusters SET money=\''.mysql_escape_string($c['money']).'\',events=\''.mysql_escape_string($c['events']).'\' WHERE id='.mysql_escape_string($c['id']));
			$msg='Dem Cluster '.$c['code'].' wurden '.$dat['2'].' Credits &uuml;berwiesen!';
		}
		db_query('INSERT INTO transfers VALUES(\''.mysql_escape_string($pcid).'\', \'user\', \''.mysql_escape_string($usrid).'\', \''.mysql_escape_string($dat[1]).'\', \''.mysql_escape_string($dat[0]).'\', \''.mysql_escape_string($recip['owner']).'\', \''.mysql_escape_string($dat[3]).'\', \''.time().'\');');
		header('Location: game.php?m=transferform&sid='.$sid.'&ok='.urlencode($msg));
	}
	break;


	case 'subnet': // ------------------------- SUBNET ------------------------

	$subnet=$_REQUEST['subnet'];
	if($subnet=='') $subnet=subnetfromip($pc['ip']);
	if((int)$subnet==0) {
		$tmp=getcountry('id',$subnet);
		$subnet=$tmp[subnet];
	}

	if($subnet=='') { no_('gs_1'); exit; }
	$c=GetCountry('subnet',$subnet);
	$info='<div id="subnet-properties">'."\n";
	$info.='<h3>Aktuelles Subnet</h3>'."\n";
	$info.='<form action="game.php?mode=subnet&amp;sid='.$sid.'" method="post">';
	$info.='<table>'."\n";
	$info.='<tr>'."\n";
	$info.='<th>Subnet:</th>'."\n";
	$info.='<td>10.47.'.$subnet.'.0/254</td>'."\n";
	$info.='</tr>'."\n";
	$info.='<tr>'."\n";
	$info.='<th>Land:</th>'."\n";
	$info.='<td>'.$c['name'].'</td>'."\n";
	$info.='</tr>'."\n";
	$info.='<tr>'."\n";
	$info.='<th>Einfuhr-Geb&uuml;hr:</th>'."\n";
	$info.='<td>'.$c['in'].'</td>'."\n";
	$info.='</tr>'."\n";
	$info.='<tr>'."\n";
	$info.='<th>Ausfuhr-Geb&uuml;hr:</th>'."\n";
	$info.='<td>'.$c['out'].'</td>'."\n";
	$info.='</tr>'."\n";

	include('data/static/country_data.inc.php');
	$options='';
	foreach($countrys as $ctry)
	{
		$options.='<option value="'.$ctry['subnet'].'">10.47.'.$ctry['subnet'].'.x - '.$ctry['name'].'</option>';
	}

	$listpage=$_REQUEST[listpage];
	if($listpage<1 || $listpage>4) $listpage=1;
	$r=db_query('SELECT pcs.ip AS pcs_ip, pcs.name AS pcs_name, pcs.points AS pcs_points, users.id AS users_id, users.name AS users_name, users.points AS users_points, clusters.id AS clusters_id, clusters.name AS clusters_name FROM (clusters RIGHT JOIN users ON clusters.id = users.cluster) RIGHT JOIN pcs ON users.id = pcs.owner WHERE pcs.ip LIKE \''.mysql_escape_string($subnet).'%\' ORDER BY pcs.id ASC;');
	$anz=mysql_num_rows($r);
	$pages=ceil($anz*(4/256)); $plist='';
	for($i=1;$i<=$pages;$i++) {
		if($listpage!=$i)
		$plist.='<a href="game.php?a=subnet&amp;sid='.$sid.'&amp;subnet='.$subnet.'&amp;listpage='.$i.'#subnet-content">'.$i.'</a> | ';
		else
		$plist.=$i.' | ';
	}
	$plist='<tr>'.LF.'<td class="navigation" colspan="5">Seite: | '.$plist.'</td>'.LF.'</tr>'."\n";

	$javascript='<script type="text/javascript">
function showcountrysel() {
var newwin;
newwin=window.open(\'static/selcountry.php\',\'selcountry\',\'width=650,height=450,toolbar=0,menubar=0,location=0,status=1,resizable=1,scrollbars=1\');
}
function subnetgo(s) {
location.href=\'../game.php?mode=subnet&sid='.$sid.'&subnet=\'+s;
}
</script>
';

	createlayout_top('HackTheNet - Subnet');
	echo '<div class="content" id="subnet">
<h2>Subnet</h2>
'.$info.'
<tr>
<td class="options" colspan="2">Anderes Subnet: <select name="subnet">
<option selected="selected" value="">[Eigenes Subnet]</option>';
	#readfile('data/static/subnets.txt');
	echo $options;
	echo '</select> <input type="submit" value="OK" /></td>
</tr>
<tr>
<!--<td class="map" colspan="2"><a href="javascript:showcountrysel()">Von Karte ausw&auml;hlen...</a></td>//-->
</tr>
</table>
</form>
</div>
<div id="subnet-content">
<h3>Subnet-Liste</h3>
<table>
'.$plist.'
<tr>
<th class="ip">IP</th>
<th class="name">Name</th>
<th class="points">Punkte</th>
<th class="owner">Besitzer</th>
<th class="cluster">Cluster</th>
</tr>';

	switch($_REQUEST['listpage']) {
		case 2: $start=65; break;
		case 3: $start=129; break;
		case 4: $start=193; break;
		default: $start=0;
	}

	if($start>0) mysql_data_seek($r,$start-1);
	$i=$start;
	while($data=mysql_fetch_assoc($r)):
	$i++;
	if($i>$start+64) break;
	$ix=$data['pcs_id'];
	$data['pcs_name']=safeentities($data['pcs_name']);
	if(is_noranKINGuser($data['users_id'])===true && is_noranKINGuser($usrid)===false) continue;

	if($data['users_name']!='') {
		$userinfo='<a href="user.php?a=info&amp;user='.$data['users_id'].'&amp;sid='.$sid.'">'.$data['users_name'].'</a> ('.$data['users_points'].' P)';
		$userclass='owner';
	} else {
		$userclass='no-owner'; $userinfo='';
	}

	if($data['clusters_name']!='') {
		$clusterclass='cluster';
		$clusterinfo='<a href="cluster.php?a=info&amp;cluster='.$data['clusters_id'].'&amp;sid='.$sid.'">'.$data['clusters_name'].'</a>';
	} else {
		$clusterclass='no-cluster'; $clusterinfo='';
	}

	echo '<tr>
<td class="ip">10.47.'.$data['pcs_ip'].'</td>
<td class="name">'.$data['pcs_name'].'</td>
<td class="points">'.$data['pcs_points'].'</td>
<td class="'.$userclass.'">'.$userinfo.'</td>
<td class="'.$clusterclass.'">'.$clusterinfo.'</td>
</tr>
';

	endwhile;

	echo $plist."\n".'</table>'.LF.'</div>'.LF.'</div>'."\n";
	createlayout_bottom();

	break;

	case 'kb':
	createlayout_top('HackTheNet - Hilfe');
	readfile('data/static/kb.html');
	createlayout_bottom();
	break;

	case 'tools': // -------------------- Tools -----------------------

	createlayout_top('HackTheNet - Tools');
	echo '<div class="content" id="subnet">';
	echo '<h2>Tools</h2>';
        	if ($usr['bigacc']!='yes' ) echo "<p><span style='color:red;'>Du besitzt keinen extendend Account, darum werden dir einige Tools nicht angezeigt.</span></p>";
        echo '<table>';
        if($usr['bigacc']=='yes') { 
           echo '<tr>
                  <th><a href="game.php?m=notiz&sid='.$sid.'">Notizblock</a></th><td>Hinterlasst Notizen, speichert IPs oder schreibt eure Taktik auf.</td>
                </tr>';
        }
           echo '<tr>
                  <th><a href="game.php?m=pc_suche&sid='.$sid.'">PC Suche</a></th><td>Sucht nach Computern durch bestimmte Kriterien.</td>
                </tr>
                <tr>
                  <th><a href="game.php?m=win&sid='.$sid.'">Gewinnspiel</a></th><td>Setzt auf eine Zahl von 1-100 und gewinnt einen riesen Jackpot voll Geld.</td>
                </tr>
              </table>';

	echo "</div>";
	createlayout_bottom();
	break;

	case 'team': // -------------------- Team -----------------------

	createlayout_top('HackTheNet - Team');
	echo '<div class="content" id="subnet">';
	echo '<h2>Team</h2>';

	echo '<table><tr><th>Name</th><th>Aufgaben</th><th>Profil</th></tr>';

	echo '<tr><td width="150">Schnitzel</td><td width="300">Organisator, Gameadmin, Forenadmin</td><td><a href="user.php?a=info&user=4&sid='.$sid.'">klick</a></td></tr>';
	echo '<tr><td>Rio</td><td>Coder, Gameadmin, Forenadmin</td><td><a href="user.php?a=info&user=2&sid='.$sid.'">klick</a></td></tr>';
	echo '<tr><td>xXxUnKnownxXx</td><td>Designer, Gameadmin, Forenadmin</td><td><a href="user.php?a=info&user=3&sid='.$sid.'">klick</a></td></tr>';

	echo "</table></div>";
	createlayout_bottom();
	break;

	case 'notiz': // -------------------- NOTIZ -----------------------
        if($usr['bigacc']!='yes') { simple_message('Nur f&uuml;r User mit Extended Account!'); exit; }
	createlayout_top('HackTheNet - Notizblock');
	echo '<div class="content" id="subnet">';
	echo '<h2>Notizblock</h2>';
	echo '<div id="subnet-content">';
	echo '<h3>Notiz schreiben</h3>';

	$notizquery = db_query("SELECT notiz FROM users WHERE id=$usrid");

	while ($notizrow = mysql_fetch_row($notizquery)) {
		$notizz = $notizrow[0];
		echo '<form action="game.php?sid='.$sid.'&page=notiz2"  method="post" name="frm">
<a name="codebox"></a>
<table>
<tr><th>Notiz erstellen/bearbeiten</th></tr>
<tr><td>
<textarea name="code" cols=70 rows=6>'.$notizz.'</textarea>
</td></tr>
<tr><th><input type="reset" value="  Reset  " /> <input type=submit value=" Weiter " /></th></tr>
</table>
</form>';
	}

	echo "</div></div>";

	createlayout_bottom();
	break;

	case 'notiz2': // -------------------- NOTIZ 2 -----------------------
        if($usr['bigacc']!='yes') { simple_message('Nur f&uuml;r User mit Extended Account!'); exit; }
	createlayout_top('HackTheNet - Notizblock');
	echo '<div class="content" id="subnet">';
	echo '<h2>Notizblock</h2>';
	echo '<div id="subnet-content">';
	$notiz = $_POST['code'];

	$notiz_save = db_query('UPDATE users SET notiz="'.mysql_escape_string($notiz).'" WHERE id="'.$usrid.'"');

	echo '<div class="important"><h3>Notiz</h3><p>Notiz wurde gespeichert.</p></div>';

	echo "</div></div>";

	createlayout_bottom();
	break;

	case 'pc_suche':  // ----------------------- PC SUCHE Start --------------------------
	createlayout_top('HackTheNet - PC Search');
	echo '<div class="content" id="subnet"><h2>PC Search</h2>';

	echo '<div id="subnet-content"><h3>Suche erstellen</h3>';

	echo '<form action="game.php?sid='.$sid.'&m=pc_suche_execute"  method="post" name="frm">
<table>
<tr><th>Befehle</th></tr>
<tr><td><ul><li>Jeder Befehl steht in einer Zeile.<li>Leerzeilen zwischen den Befehlen sind erlaubt!
<li>Gro&szlig;/Kleinschreibung ist egal!
<li>Reihenfolge der Befehle ist egal!
</ul>
</td></tr><tr><td>
<table style="font-size:8pt;" class="nomargin">
<tr class="greytr2"><td><b>Syntax</b></td><td><b>Beispiel</b></td><td><b>Beschreibung</b></td><td><b>Hinweis</b></td></tr>
<tr class="greytr2"><td nowrap="nowrap">NOTUSER [Username]</td><td><tt>NOTUSER Administrator</tt></td><td>Legt fest ob User Administrator angezeigt wird. 0 für herrenlose PCs.</td><td>optional</td></tr>
<tr class="greytr2"><td nowrap="nowrap">NOTCLUSTER [Clustercode]</td><td><tt>NOTCLUSTER ::root::</tt></td><td>Legt fest ob Cluster ::root:: angezeigt wird. 0 für kein Cluster.</td><td>optional</td></tr>
<tr class="greytr2"><td nowrap="nowrap">USER [Username]</td><td><tt>USER Administrator</tt></td><td>Legt fest ob nur User Administrator angezeigt wird. 0 für herrenlose PCs.</td><td>optional</td></tr>
<tr class="greytr2"><td nowrap="nowrap">CLUSTER [Clustercode]</td><td><tt>CLUSTER ::root::</tt></td><td>Legt fest ob nur Cluster ::root:: angezeigt wird. 0 für kein Cluster.</td><td>optional</td></tr>
<tr class="greytr2"><td nowrap="nowrap">POINTS [>= / <= / &lt; / &gt; / = ] [points]</td><td><tt>POINTS >= 500</tt></td><td>Legt fest ob nach einer bestimmten Punktzahl gesucht wird.<br><li>&gt;= - größer oder gleich<br><li>&lt;= - kleiner oder gleich<br><li>&gt; - größer<br><li>&lt; - kleiner<br><li>= - gleich</td><td>optional</td></tr>
</table>
</td></tr>
</table><br /><br />
<a name="codebox"></a>
<table>
<tr><th>Such-Script erstellen</th></tr>
<tr><td>
<textarea name="code" cols=70 rows=6></textarea>
</td></tr>
<tr><th><input type="reset" value="  Reset  " /> <input type=submit value=" Weiter " /></th></tr>
</table>
</form></div></div>';

	createlayout_bottom();
	break;

	case 'pc_suche_execute':  // ----------------------- PC SUCHE Execute --------------------------
	//if (!is_norankinguser($usr['id'])) { simple_message('working...'); exit; }
	createlayout_top('HackTheNet - PC Search');
	echo '<div class="content" id="subnet">';
	echo '<h2>Liste</h2>';
	echo '<div id="subnet-content">';

	$notuser = array(); $notcluster = array();
	$suser = array(); $scluster = array();
	$points=''; $pzeichen='';


	$code=str_replace(chr(9),'',$_POST['code']);
	while(strpos($code,'  ')!=false) {
		$code=str_replace('  ',' ',$code);
	}
	$lines=explode("\n",$code);
	for($i=0;$i<count($lines);$i++) {
		if(trim($lines[$i])!='') {
			$lines[$i]=explode(' ',trim($lines[$i]," \n\r\x0b\0\t"));
			switch(strtoupper($lines[$i][0])) {

				case 'NOTUSER': $notuser[]=$lines[$i][1]; break;
				case 'NOTCLUSTER': $notcluster[]=$lines[$i][1]; break;

				case 'USER': $suser[]=$lines[$i][1]; break;
				case 'CLUSTER': $scluster[]=$lines[$i][1]; break;

				case 'POINTS':
				switch(strtoupper(trim($lines[$i][1])))
				{
					case '>=': $pzeichen=strtoupper($lines[$i][1]); $points=trim($lines[$i][2]); break;
					case '<=': $pzeichen=strtoupper($lines[$i][1]); $points=trim($lines[$i][2]); break;
					case '>': $pzeichen=strtoupper($lines[$i][1]); $points=trim($lines[$i][2]); break;
					case '<': $pzeichen=strtoupper($lines[$i][1]); $points=trim($lines[$i][2]); break;
					case '=': $pzeichen=strtoupper($lines[$i][1]); $points=trim($lines[$i][2]); break;
					default:
					$emsg.='Unbekannter Befehl: <i>'.join($lines[$i],' ').'</i><br />';
					break;
				}
				break;

				default:
				$emsg.='Unbekannter Befehl: <i>'.join($lines[$i],' ').'</i><br />';
				break;
			}
		}
	}

	if (!is_numeric($points) && $points!="") { echo 'blub'; exit; }
	if ($pzeichen != ">=" && $pzeichen != "<=" && $pzeichen != "=" && $pzeichen != ">" && $pzeichen != "<" && $pzeichen!="") { echo 'blub'; exit; }

	$searchvars = "";
	$x=0;

	for($i=0; $i<count($notuser); $i++) {
		$searchvars.=($x==0 ? '' : '&& ').'owner'.($j!="0" ? '_name' : '').'!='.($j!="0" ? '"'.mysql_escape_string($j).'"' : '0').' ';
		if ($x==0) { $x=1; }
	}

	for($i=0; $i<count($notcluster); $i++) {
		$searchvars.=($x==0 ? '' : '&& ').'owner_cluster'.($j!="0" ? '_code' : '').'!='.($j!="0" ? '"'.mysql_escape_string($j).'"' : '0').' ';
		if ($x==0) { $x=1; }
	}

	for($i=0; $i<count($suser); $i++) {
		$searchvars.=($x==0 ? '' : '&& ').'owner'.($suser[$i]!="0" ? '_name' : '').'='.($suser[$i]!="0" ? '"'.mysql_escape_string($suser[$i]).'"' : '0').' ';
		if ($x==0) { $x=1; }
	}

	for($i=0; $i<count($scluster); $i++) {
		$searchvars.=($x==0 ? '' : '&& ').'owner_cluster'.($j!="0" ? '_code' : '').'='.($j!="0" ? '"'.mysql_escape_string($j).'"' : '0').' ';
		if ($x==0) { $x=1; }
	}

	if ($points!="") {
		$searchvars.=($x==0 ? '' : '&& ').'points'.$pzeichen.$points.' ';
	}

	//var_dump($suser);
	//if (is_norankinguser($usrid)) { echo $searchvars."<br>"; var_dump($suser); echo "<br>".$suser[0]; echo "<br>".($suser[0]==0 ? '0' : '1'); }
	if ($searchvars == "") { echo "<div class=\"important\"><h3>Suche</h3><p>Keine Suchkriterie angegeben!</p></div>"; exit; }
	$searchquery = db_query("select owner,owner_name,owner_cluster_code,ip,name,points from pcs WHERE ".$searchvars." ORDER by points DESC ".($usr['bigacc']!='yes' ? 'LIMIT 20' : ''));
	echo "<div class=\"important\"><h3>Suche</h3><p>".mysql_num_rows($searchquery)." PCs gefunden.";
	if ($usr['bigacc']!='yes' ) echo "<br /><span style='color:red;'>Du besitzt keinen extendend Account, darum werden dir nur max 20 Ergebnisse angezeigt!</span>";
	echo "</p></div>";
	echo "<table id=\"search-table\">";
	echo "<tr>";
	echo "<th class=\"name\">Username</th><th class=\"cluster\">Cluster</th><th class=\"ip\">IP-Adresse</th><th class=\"pcname\">PC Name</th><th class=\"pcpoints\">Points</th>";
	echo "</tr>";

	while ($row = mysql_fetch_array($searchquery)) {
		/**
		if ($row['owner'] != "0") {
			$result_search_cluster = db_query('SELECT cluster FROM users WHERE id="'.$search_owner.'"');
			$search_cluster = mysql_fetch_array($result_search_cluster);

			$result_search_cluster_code = db_query("SELECT code FROM clusters WHERE id=\"".$search_cluster['cluster']."\"");
			$search_cluster_code = mysql_fetch_array($result_search_cluster_code);
		}
		**/

		echo "<tr>";
		echo "<td class=\"name\">".($row['owner']!=0 ? $row['owner_name'] : 'NoName')."</td><td class=\"cluster\">".$row['owner_cluster_code']."</td><td class=\"ip\">10.47.".$row['ip']."</td><td class=\"pcname\">".$row['name']."</td><td class=\"pcpoints\">".$row['points']."</td>";
		echo "</tr>";
	}
	echo "</table></div></div>";
	createlayout_bottom();

	break;

	case 'changelog':  // ----------------------- Changelog --------------------------

	createlayout_top('HackTheNet - Changelog');
	$javascript='<script type="text/javascript">
function confirm_abort() {
return window.confirm(\'Willst du den Log wirklich löschen?\');
}
</script>';
	echo '<div class="content" id="subnet"><h2>Changelog</h2>';
	$changelog_pro_page=100;
	$result=db_query('SELECT * FROM changelog ORDER BY time DESC LIMIT 0,'.$changelog_pro_page);
	if ($usr['stat']==1000) { echo "<a href=\"game.php?m=admincl&cl=add&sid=".$sid."\">Beitrag hinzufügen</a><br><br>"; }
	if (mysql_num_rows($result)>0) {
		echo "<table id=\"changelog\">";
		echo "<tr>
      <th class=\"time\">Zeit</th><th class=\"change\">Änderung</th>";
		if ($usr['stat']==1000) { echo "<th class=\"admin\">Admin Funktionen</th>"; }
		echo "</tr>";
		while($changelog=mysql_fetch_array($result)) {
			echo "<tr><td class=\"time\" valign=\"top\">".date("d.m.Y H:i" ,$changelog['time'])."</td><td class=\"change\">".text_replace($changelog['text'])."</td>";
			if ($usr['stat']==1000) { echo "<td><a href=\"game.php?m=admincl&cl=edit&id=".$changelog['id']."&sid=".$sid."\">editieren</a> | <a href=\"game.php?m=admincl&cl=del&id=".$changelog['id']."&sid=".$sid."\" onclick=\"return confirm_abort();\">löschen</a></td></tr>"; }
			else { echo "</tr>"; }
		}
	} else { echo 'Keine Einträge gefunden'; }
	echo "</table></div></div>";
	createlayout_bottom();

	break;

	case 'admincl':

	if ($usr['stat']>=100 && is_norankinguser($usrid)) {
		if ($_GET['cl']=="add") {
			if (!$_POST['add']) {
				createlayout_top('HackTheNet - Changelog');
				echo '<div class="content" id="subnet"><h2>Changelog</h2>';
				echo '<form action="game.php?sid='.$sid.'&m=admincl&cl=add"  method="post" name="addcl">
          <textarea name="code" cols=70 rows=6></textarea><br>
          <input type="reset" value="  Reset  " /> <input type=submit name="add" value=" Weiter " /></form>';
				echo "</div></div>";
				createlayout_bottom();
			} else {
				createlayout_top('HackTheNet - Changelog');
				echo '<div class="content" id="subnet"><h2>Changelog</h2>';
				db_query('INSERT INTO changelog(`id`, `text`, `time`) VALUES("", "'.$_POST['code'].'", '.time().')');
				echo 'Neuer Eintrag hinzugefügt.';
				echo "</div></div>";
				createlayout_bottom();
			}
		}

		if ($_GET['cl']=="edit") {
			if (!$_POST['edit']) {
				createlayout_top('HackTheNet - Changelog');
				echo '<div class="content" id="subnet"><h2>Changelog</h2>';
				$changelog=mysql_fetch_array(db_query('SELECT text FROM changelog WHERE id='.$_GET['id']));
				echo '<form action="game.php?sid='.$sid.'&m=admincl&cl=edit&id='.$_GET['id'].'"  method="post" name="editcl">
          <textarea name="code" cols=70 rows=6>'.$changelog['text'].'</textarea><br>
          <input type="reset" value="  Reset  " /> <input type=submit name="edit" value=" Weiter " /></form>';
				echo "</div></div>";
				createlayout_bottom();
			} else {
				createlayout_top('HackTheNet - Changelog');
				echo '<div class="content" id="subnet"><h2>Changelog</h2>';
				db_query('UPDATE changelog SET text="'.$_POST['code'].'" WHERE id='.$_GET['id']);
				echo 'Eintrag geändert.';
				echo "</div></div>";
				createlayout_bottom();
			}
		}

		if ($_GET['cl']=="del") {
			createlayout_top('HackTheNet - Changelog');
			echo '<div class="content" id="subnet"><h2>Changelog</h2>';
			db_query('DELETE FROM changelog WHERE id='.$_GET['id']);
			echo 'Eintrag geändert.';
			echo "</div></div>";
			createlayout_bottom();
		}
	}

	break;

	case 'hof': // ----------------------- HALL OF FAME --------------------------
	createlayout_top('HackTheNet - Hall of Fame');
	$javascript='<script type="text/javascript">
function confirm_abort() {
return window.confirm(\'Willst du diesen Eintrag wirklich aus der Hall of Famelöschen?\');
}
</script>';
	echo '<div class="content" id="user"><h2>Hall of Fame</h2>';
	$halloffame_pro_page=50;
	$result=db_query('SELECT * FROM hof ORDER BY id LIMIT 0,'.$halloffame_pro_page);
	if ($usr['stat']==1000) { echo "<a href=\"game.php?m=adminhof&hof=add&sid=".$sid."\">Beitrag hinzufügen</a><br><br>"; }
	if (mysql_num_rows($result)>0) {
		echo "<table id=\"hof\">";
		echo "<tr>
      <th class=\"round\">Runde</th><th class=\"name\">Name</th><th class=\"cluster\">Cluster</th><th class=\"why\">Grund</th>";
		if ($usr['stat']==1000) { echo "<th class=\"admin\">Admin Funktionen</th>"; }
		echo "</tr>";
		while($halloffame=mysql_fetch_array($result)) {
			echo "<tr><td class=\"round\" valign=\"top\">".$halloffame['round']."</td><td class=\"name\" valign=\"top\">".$halloffame['user']."</td><td class=\"round\" valign=\"top\">".$halloffame['cluster']."</td><td class=\"why\">".nl2br($halloffame['why'])."</td>";
			if ($usr['stat']==1000) { echo "<td><a href=\"game.php?m=adminhof&hof=edit&id=".$halloffame['id']."&sid=".$sid."\">editieren</a> | <a href=\"game.php?m=adminhof&hof=del&id=".$halloffame['id']."&sid=".$sid."\" onclick=\"return confirm_abort();\">löschen</a></td></tr>"; }
			else { echo "</tr>"; }
		}
	} else { echo 'Keine Einträge gefunden'; }
	echo "</table></div></div>";
	createlayout_bottom();

	break;

	case 'adminhof': // ----------------------- HALL OF FAME Admin --------------------------

	if ($usr['stat']>=100 && is_norankinguser($usrid)) {
		if ($_GET['hof']=="add") {
			if (!$_POST['add']) {
				createlayout_top('HackTheNet - Hall of Fame');
				echo '<div class="content" id="user"><h2>Hall of Fame</h2>';
				echo '<form action="game.php?sid='.$sid.'&m=adminhof&hof=add"  method="post" name="addhof">
          <table><tr>
          <th class=\"round\">Runde</th><th class=\"name\">Name</th><th class=\"cluster\">Cluster</th>
          </tr>
          <tr>
          <td><input name="round" type="text"></td>
          <td><input name="name" type="text"></td>
          <td><input name="cluster" type="text"></td>
          </tr>
          <tr><th class=\"round\" colspan=\"4\">Grund</th></tr>
          <tr>
          <td colspan=\"4\"><textarea name="why" cols=70 rows=6></textarea></td>
          </tr></table><br><br>
          <input type="reset" value="  Reset  " /> <input type=submit name="add" value=" Weiter " /></form>';
				echo "</div>";
				createlayout_bottom();
			} else {
				createlayout_top('HackTheNet - Hall of Fame');
				echo '<div class="content" id="user"><h2>Hall of Fame</h2>';
				db_query('INSERT INTO hof(`id`, `user`, `cluster`, `why`, `round`) VALUES("", "'.$_POST['name'].'", "'.$_POST['cluster'].'", "'.$_POST['why'].'", "'.$_POST['round'].'")');
				echo 'Neuer Eintrag hinzugefügt.';
				echo "</div>";
				createlayout_bottom();
			}
		}

		if ($_GET['hof']=="edit") {
			if (!$_POST['edit']) {
				createlayout_top('HackTheNet - Hall of Fame');
				echo '<div class="content" id="user"><h2>Hall of Fame</h2>';
				$halloffame=mysql_fetch_array(db_query('SELECT round, user, cluster, why FROM hof WHERE id='.$_GET['id']));
				echo '<form action="game.php?sid='.$sid.'&m=adminhof&hof=edit&id='.$_GET['id'].'"  method="post" name="edithof">
          <table><tr>
          <th class=\"round\">Runde</th><th class=\"name\">Name</th><th class=\"cluster\">Cluster</th>
          </tr><tr>
          <td><input name="round" type="text" value="'.$halloffame['round'].'" onfocus="this.value=\'\'"></td>
          <td><input name="name"  type="text" value="'.$halloffame['user'].'" onfocus="this.value=\'\'"></td>
          <td><input name="cluster" type="text" value="'.$halloffame['cluster'].'" onfocus="this.value=\'\'"></td>
          </tr><tr>
          <tr><th colspan="3" class=\"old\">Zur Zeit eingetragen</th></tr>
          <td>'.$halloffame['round'].'</td>
          <td>'.$halloffame['user'].'</td>
          <td>'.$halloffame['cluster'].'</td>
          </tr>
          <tr><th class=\"round\" colspan=\"4\">Grund</th></tr>
          <tr>
          <td colspan=\"4\"><textarea name="why" cols=70 rows=6>'.$halloffame['why'].'</textarea></td>
          </tr></table><br><br>
          <input type="reset" value="  Reset  " /> <input type=submit name="edit" value=" Weiter " /></form>';
				echo "</div>";
				createlayout_bottom();
			} else {
				createlayout_top('HackTheNet - Hall of Fame');
				echo '<div class="content" id="user"><h2>Hall of Fame</h2>';
				db_query('UPDATE hof SET round="'.$_POST['round'].'", user="'.$_POST['name'].'", cluster="'.$_POST['cluster'].'", why="'.$_POST['why'].'" WHERE id='.$_GET['id']);
				echo 'Eintrag geändert.';
				echo "</div>";
				createlayout_bottom();
			}
		}

		if ($_GET['hof']=="del") {
			createlayout_top('HackTheNet - Hall of Fame');
			echo '<div class="content" id="user"><h2>Hall of Fame</h2>';
			db_query('DELETE FROM hof WHERE id='.$_GET['id']);
			echo 'Eintrag gelöscht.';
			echo "</div>";
			createlayout_bottom();
		}
	}
	break;

	case 'todo':  // ----------------------- ToDo --------------------------

	createlayout_top('HackTheNet - ToDo');
	$javascript='<script type="text/javascript">
function confirm_abort() {
return window.confirm(\'Willst du den Eintrag wirklich löschen?\');
}
</script>';
	echo '<div class="content" id="subnet"><h2>ToDo</h2>';
	$todo_pro_page=10;
	$result=db_query('SELECT * FROM todo ORDER BY time DESC LIMIT 0,'.$todo_pro_page);
	if ($usr['stat']==1000) { echo "<a href=\"game.php?m=admintodo&todo=add&sid=".$sid."\">Beitrag hinzufügen</a><br><br>"; }
	if (mysql_num_rows($result)>0) {
		echo "<table id=\"todo\">";
		echo "<tr>
      <th class=\"time\">Zeit</th><th class=\"change\">Änderung</th>";
		if ($usr['stat']==1000) { echo "<th class=\"admin\">Admin Funktionen</th>"; }
		echo "</tr>";
		while($todo=mysql_fetch_array($result)) {
			echo "<tr><td class=\"time\" valign=\"top\">".date("d.m.Y H:i" ,$todo['time'])."</td><td class=\"change\">".text_replace($todo['text'])."</td>";
			if ($usr['stat']==1000) { echo "<td><a href=\"game.php?m=admintodo&todo=edit&id=".$todo['id']."&sid=".$sid."\">editieren</a> | <a href=\"game.php?m=admintodo&todo=del&id=".$todo['id']."&sid=".$sid."\" onclick=\"return confirm_abort();\">löschen</a></td></tr>"; }
			else { echo "</tr>"; }
		}
	} else { echo 'Keine Einträge gefunden'; }
	echo "</table></div></div>";
	createlayout_bottom();

	break;

	case 'admintodo':

	if ($usr['stat']>=100 && is_norankinguser($usrid)) {
		if ($_GET['todo']=="add") {
			if (!$_POST['add']) {
				createlayout_top('HackTheNet - ToDo');
				echo '<div class="content" id="subnet"><h2>ToDo</h2>';
				echo '<form action="game.php?sid='.$sid.'&m=admintodo&todo=add"  method="post" name="addcl">
          <textarea name="code" cols=70 rows=6></textarea><br>
          <input type="reset" value="  Reset  " /> <input type=submit name="add" value=" Weiter " /></form>';
				echo "</div></div>";
				createlayout_bottom();
			} else {
				createlayout_top('HackTheNet - ToDo');
				echo '<div class="content" id="subnet"><h2>ToDo</h2>';
				db_query('INSERT INTO todo(`id`, `text`, `time`) VALUES("", "'.$_POST['code'].'", '.time().')');
				echo 'Neuer Eintrag hinzugefügt.';
				echo "</div></div>";
				createlayout_bottom();
			}
		}

		if ($_GET['todo']=="edit") {
			if (!$_POST['edit']) {
				createlayout_top('HackTheNet - ToDo');
				echo '<div class="content" id="subnet"><h2>ToDo</h2>';
				$todo=mysql_fetch_array(db_query('SELECT text FROM todo WHERE id='.$_GET['id']));
				echo '<form action="game.php?sid='.$sid.'&m=admintodo&todo=edit&id='.$_GET['id'].'"  method="post" name="editcl">
          <textarea name="code" cols=70 rows=6>'.$todo['text'].'</textarea><br>
          <input type="reset" value="  Reset  " /> <input type=submit name="edit" value=" Weiter " /></form>';
				echo "</div></div>";
				createlayout_bottom();
			} else {
				createlayout_top('HackTheNet - ToDo');
				echo '<div class="content" id="subnet"><h2>ToDo</h2>';
				db_query('UPDATE todo SET text="'.$_POST['code'].'" WHERE id='.$_GET['id']);
				echo 'Eintrag geändert.';
				echo "</div></div>";
				createlayout_bottom();
			}
		}

		if ($_GET['todo']=="del") {
			createlayout_top('HackTheNet - ToDo');
			echo '<div class="content" id="subnet"><h2>ToDo</h2>';
			db_query('DELETE FROM todo WHERE id='.$_GET['id']);
			echo 'Eintrag geändert.';
			echo "</div></div>";
			createlayout_bottom();
		}
	}

	break;

	case 'win':  // ----------------------- Gewinnspiel --------------------------
	createlayout_top('HackTheNet - Gewinnspiel');
	if ($_GET['win']=="") {
		$jackpotresult=db_query('SELECT jackpot,wintipp FROM win WHERE id=1'); $jackpot=mysql_fetch_assoc($jackpotresult);
		echo '<div class="content" id="subnet"><h2>Gewinnspiel</h2>
            <form action="game.php?sid='.$sid.'&m=win&win=add"  method="post"><table><tr>
            <td>Willkommen zum HTN.Lan Gewinnspiel. Es ist ganz einfach hier mitzumachen. Du wählst einfach eine Zahl
                zwischen 0 und 100 und hast jeden Abend um 20:00 Uhr die Chance den Jackpot zu gewinnen. Sollten mehrere gewinnen so wird
                der Checkpot einfach durch die User geteilt. Logisch, oder?<br>Jeder Tipp kostet dich 
                nur 1000 Credits. Du kannst in einer Runde 5 Tipps abgeben. Es muss auch gesagt sein, dass abgegebene Tipps nicht mehr rückgängig gemacht werden können.<br>
                Der Jackpot setzt sich so zusammen: Du gibst für deinen Tipp 1000 Credits. Die Credits werden als Jackpot verwendet<br>
                inklusive 1000 Credits dazu vom System. Das heisst je mehr mitmachen desto höher der Jackpot.<br>
                Jackpot=Tipps*2000<br>
                Wenn jedoch letzte Runde keiner gewonnen hat, so wird einfach der Jackpot beibehalten und der Gewinn steigert sich immer höher.</td>
            </tr></table><br>
            <table><tr><td>Letztes Mal war diese Zahl die richtige: '.$jackpot['wintipp'].'</td></tr></table><br>
            <table>
            <tr><td>Der Jackpot liegt bei '.(mysql_num_rows($jackpotresult)==0 ? 0 : $jackpot['jackpot']).' Credits</td></tr>
            </table><table><tr><td>Ihr Tipp:&nbsp;</td><td><input type="text" name="tipp" maxlength="3"></td>
            <td><input type=submit name="add" value=" Weiter "></td></tr>
            </table>';
		$winresult=db_query('SELECT * FROM win WHERE userid="'.$usrid.'"'); $i=1; $tipps='';
		echo '<table><tr><td><b>'.(mysql_num_rows($winresult)>0 ? 'Deine bisherigen Tipps' : 'Du hast noch keine Tipps abgegeben.').'</b>:&nbsp;</td></tr>';
		if (mysql_num_rows($winresult)>0) { echo '<tr><td>'; }
		while($win=mysql_fetch_array($winresult)) {
			if ($i==mysql_num_rows($winresult)) { $tipps.=$win['tipp']; }
			else { $tipps.=$win['tipp'].", "; }
			$i++;
		}
		echo $tipps;
		echo '</td></tr></table>';
	}
	if ($_GET['win']=="add") {
		if (strlen($_POST['tipp'])==0 || $_POST['tipp']<0 || $_POST['tipp']>100 || $_POST['tipp']==0 || !is_numeric($_POST['tipp'])) { echo '<div class="content" id="subnet"><h2>Gewinnspiel</h2><br>Bitte Tipp richtig ausfüllen.'; exit(); }
		elseif (mysql_num_rows(db_query('SELECT id FROM win WHERE userid='.$usrid))>=5) { echo '<div class="content" id="subnet"><h2>Gewinnspiel</h2><br>Du hast schon 5 Tipps abgegeben.'; exit(); }
		else {
			if (mysql_num_rows(db_query('SELECT tipp FROM win WHERE tipp="'.$_POST['tipp'].'" && userid="'.$usrid.'"'))>0) { echo '<div class="content" id="subnet"><h2>Gewinnspiel</h2><br>Du darfst nicht zwei gleiche Tipps abgeben.'; exit(); }
			else {
				$pcidresult=db_query('SELECT id,credits FROM pcs WHERE owner="'.$usrid.'" && credits>="1000" ORDER BY credits DESC LIMIT 1');
				if (mysql_num_rows($pcidresult)==0) { echo '<div class="content" id="subnet"><h2>Gewinnspiel</h2><br>Einer deiner PCs muss min. 1000 Credits haben.'; exit(); }
				$pcid=mysql_fetch_assoc($pcidresult);
				$pcredits=$pcid['credits'] -1000;
				db_query('UPDATE pcs SET credits='.$pcredits.' WHERE id='.$pcid['id']);
				$jackpot=mysql_fetch_assoc(db_query('SELECT jackpot FROM win LIMIT 1'));
				$newjackpot=$jackpot['jackpot'] +2000;
				db_query('INSERT INTO win(`id`, `userid`, `tipp`, `time`) VALUES("", "'.$usrid.'", "'.$_POST['tipp'].'", "'.time().'")');
				db_query('UPDATE win SET jackpot='.$newjackpot.' WHERE id=1');
				echo '<div class="content" id="subnet"><h2>Gewinnspiel</h2><br>Tipp wurde eingetragen. Viel Glück!';
			}
		}
	}
	createlayout_bottom();
	break;

	case 'clustercredits': // ----------------------- Cluster Credits spenden --------------------------
	$new_round_time = file_get("./data/newround.txt");
	if(time() < $new_round_time + NO_TRANSFER) {
		simple_message("<p><b>Du kannst noch nicht Überweisen. Erst wieder am ". date("d.m.Y",$new_round_time + NO_TRANSFER)." um ". date("H:i",$new_round_time + NO_TRANSFER)."</b></p>");
		exit;
	}
	if ($usr['cluster']==0 || $usr['cluster']=="") { simple_message('Du besitzt kein Cluster.'); exit; }
	$dbresult=db_query('SELECT credits FROM pcs WHERE owner='.$usrid);
	$tcredits=0;
	while($credits=mysql_fetch_assoc($dbresult)) {
		$tcredits+=$credits['credits'];
	}
	$tcredits=$tcredits-1;
	$cresult=db_query('SELECT events FROM clusters WHERE id='.$usr['cluster']);
	$c=mysql_fetch_assoc($cresult);
	$c['events']=nicetime4().' [usr='.$usrid.']'.$usr['name'].'[/usr] spendet dem Cluster '.number_format($tcredits, 0, ',', '.').' Credits.'.LF.$c['events'];
	db_query('UPDATE clusters SET money=money+'.$tcredits.', events="'.mysql_escape_string($c['events']).'" WHERE id='.$usr['cluster']);
	db_query('UPDATE pcs SET credits=1 WHERE owner='.$usrid);
	simple_message('Geld wurde erfolgreich an das Cluster überwiesen. Es waren insgesamt '.number_format($tcredits, 0, ',', '.').' Credits.');
	break;

	case 'distrcredits': // ----------------------- Credits verteilen --------------------------
	$allpcs=true;
	if ($_GET['allpc']=="") { $allpcs=false; }

	if ($allpcs==false) {
		$dbresult=db_query('SELECT credits FROM pcs WHERE owner='.$usr['id'].' && points<=1024 && rh!=10');
		$dbresult2=db_query('SELECT credits FROM pcs WHERE owner='.$usr['id']);
		$dbresult3=db_query('SELECT credits FROM pcs WHERE owner='.$usr['id'].' && points=1024 && rh=10');
		if (mysql_num_rows($dbresult)==0 || mysql_num_rows($dbresult3)==0) { header('Location: game.php?m=pcs&sid='.$sid); exit; }
		$tcredits=0;
		while($credits=mysql_fetch_assoc($dbresult2)) {
			$tcredits+=$credits['credits'];
		}
		$acredits=round($tcredits / mysql_num_rows($dbresult),0);
	    db_query('UPDATE pcs SET credits='.$acredits.' WHERE owner='.$usr['id'].' && points<=1024 && rh!=10');
	    db_query('UPDATE pcs SET credits=0 WHERE owner='.$usrid.' && points=1024 && rh=10');
	} else {
		$dbresult=mysql_fetch_assoc(db_query('SELECT SUM(credits) AS credits, COUNT(id) AS pcs FROM pcs WHERE owner='.$usr['id']));
		$credits=$dbresult['credits']; $pcs=$dbresult['pcs'];
		$acredits=round($credits / $pcs,0);
		db_query('UPDATE pcs SET credits='.$acredits.' WHERE owner='.$usr['id']);
		if ($usr['id']==8) { echo 'UPDATE pcs SET credits='.$acredits.' WHERE owner='.$usr['id']; }
	}
	simple_message('Geld wurde erfolgreich verteilt. Jeder PC hat nun '.number_format($acredits, 0, ',', '.').' Credits.');
	break;

	case 'bugtracker':

	$order='status_number ASC, type_number ASC';
	if ($_POST['sort'] == "id_asc") { $order='id ASC'; }
	if ($_POST['sort'] == "id_desc") { $order='id DESC'; }

	$where='';
	if ($_POST['type']!="") { $where.=($where!= '' ? '&&' : '').' type="'.mysql_escape_string($_POST['type']).'" '; }
	if ($_POST['extra']!="") { $where.=($where!= '' ? '&&' : '').' name="'.mysql_escape_string($usr['name']).'" '; }
	if ($_POST['search_query']!="") { $where.=($where!= '' ? '&&' : '').' titel LIKE "%'.mysql_escape_string($_POST['search_query']).'%" || text LIKE "%'.mysql_escape_string($_POST['search_query']).'%"'; }

	$type=array('mittelschwer'=>'middlebug', 'trivial'=>'lowbug', 'Feature Reg.'=>'featurebug', 'kritisch'=>'criticalbug');
	$status=array('new'=>'newbugstat', 'frozen'=>'frozenbugstat', 'working on'=>'working_onbugstat', 'reopen'=>'reopenedbugstat', 'fixed'=>'fixedbugstat', 'wontfix', 'wontfixbugsat');
	$dbtype=array('low'=>'trivial', 'critical'=>'kritisch', 'middle'=>'mittelschwer', 'feature'=>'Feature Reg.', 'usability'=>'Usability', 'improvement'=>'Verbesserung');

	createlayout_top('HackTheNet - Bugtracker');
	echo '<div class="content" id="subnet"><h2>Bugtracker</h2>
		<dd><a href="?m=addbug&sid='.$sid.'">Bug melden</a></dd><br><br>
		<form action="?m=showbug&sid='.$sid.'" method="post">
<p>Gehe zu Bug #<input type="text" name="id" size="4" maxlength="5" />&nbsp;<input type="submit" value="OK"></p>
</form>

<form action="?m=bugtracker&sid='.$sid.'" method="post">
<p>Filter: 
<select name="type"><option value="" selected="selected">alle</option><option value="feature">Feature Req.</option><option value="improvement">Verbesserung</option><option value="usability">Usability</option><option value="low">trivial</option><option value="middle">mittelschwer</option><option value="critical">kritisch</option></select>
<select name="extra"><option value="" selected="selected">alle</option><option value="bugsbyme">von mir gemeldete</option></select>
&nbsp;<input type="submit" value="OK"></p>
<p>Sortieren: 
<select name="sort"><option value="state" selected="selected">Status</option><option value="id_asc">ID aufsteigend</option><option value="id_desc">ID absteigend</option></select>
&nbsp;<input type="submit" value="OK"></p>
<p>Durchsuchen: <input type="text" name="search_query" size="20" value="">&nbsp;<input type="submit" value="OK"></p>
<p>Die Notizen zu einem Bug werden nicht durchsucht, nur Betreff &amp; Beschreibung der Bugs.</p>

</form><br><br>';

	$result=db_query('
		SELECT id, titel, status, type, n
		FROM bugtracker
		'.($where!="" ? "WHERE $where" : "").'
		ORDER BY '.$order.'
		');
	if (mysql_num_rows($result)==0) { echo '<p>Kein Bug vorhanden.</p>'; }
	else {
		echo '<table>
<tr><th width="25">ID</th><th width="400">Beschreibung</th><th width="20">N</th><th width="100">Typ</th><th width="100">Status</th></tr>';
		while ($b=mysql_fetch_assoc($result)) {
			echo '<tr><td width="25">#'.$b['id'].'</td><td width="400"><a href="?m=showbug&id='.$b['id'].'&sid='.$sid.'">'.$b['titel'].'</a></td><td width="20">'.$b['n'].'</td><td width="100" class="'.$type[$dbtype[$b['type']]].'">'.$dbtype[$b['type']].'</td><td width="100" class="'.$status[$b['status']].'">'.$b['status'].'</td></tr>';
		}
		echo '</table>';
	}
	echo '</div>'; createlayout_bottom();
	break;

	case 'showbug':
	$id=$_GET['id'];
	if ($id == "") { $id=$_POST['id']; }
	if (!is_numeric($id)) { exit; }
	if ($_POST['text']!="") {
		if (strlen(mysql_escape_string($_POST['text']))>=255) { echo 'Der Text darf maximal 254 Zeichen haben.'; }
		elseif ($_POST['text']=="") { echo 'Bitte einen Text eingeben.'; }
		else {
			db_query('INSERT INTO bugtracker_comments VALUES("", "'.$usr['name'].'", "'.mysql_escape_string($_POST['text']).'", '.$id.', '.time().')');
			db_query('UPDATE bugtracker SET n=n+1 WHERE id='.$id);
		}
	}
	if (is_noranKINGuser($usrid)) {
		$javascript='<script type="text/javascript">
						 function confirm_abort() {
						 	return window.confirm(\'Willst du den Eintrag wirklich löschen?\');
						 }
						 </script>';
	}
	createlayout_top('HackTheNet - Bugtracker');

	$type=array('mittelschwer'=>'middlebug', 'trivial'=>'lowbug', 'Feature Reg.'=>'featurebug', 'kritisch'=>'criticalbug');
	$status=array('new'=>'newbugstat', 'frozen'=>'frozenbugstat', 'working on'=>'working_onbugstat', 'reopen'=>'reopenedbugstat', 'fixed'=>'fixedbugstat', 'wontfix'=>'wontfixbugstat');
	$dbtype=array('low'=>'trivial', 'critical'=>'kritisch', 'middle'=>'mittelschwer', 'feature'=>'Feature Reg.', 'usability'=>'Usability', 'improvement'=>'Verbesserung');

	$result=db_query('SELECT * FROM bugtracker WHERE id='.$id.' LIMIT 1');
	if (mysql_num_rows($result)==0) { echo '<p>Bug wurde nicht gefunden.</p>'; }
	else {
		$b=mysql_fetch_assoc($result);

		echo '<div class="content" id="subnet"><h2>Bugtracker</h2>
		<table><tr class="head"><th colspan="2">Details zu diesem Bug';
		if (is_noranKINGuser($usrid)) { echo '<br><a href="?m=editbug&id='.$id.'&sid='.$sid.'">edit</a> |  <a href="?m=delbug&id='.$id.'&sid='.$sid.'" onclick=\"return confirm_abort();\">del</a>'; }
		echo '</th></tr>
		<tr><th width="100">ID:</th><td width="400">'.$b['id'].'</td></tr><tr><th>Thema:</th><td>'.$b['titel'].'</td></tr><tr><th>Typ:</th><td class="'.$type[$dbtype[$b['type']]].'">'.$dbtype[$b['type']].'</td></tr><tr><th>gemeldet:</th><td>von '.$b['name'].', '.date("d.m.Y H:i", $b['date']).'</td></tr><tr><th>Status:</th><td class="'.$status[$b['status']].'">'.$b['status'].'</td></tr><tr><th>zust. Admin:</th><td>'.($b['admin'] == "" && is_norankinguser($usrid) ? '<a href="?m=bugadmin&id='.$id.'&sid='.$sid.'"><i>eintragen</i></a>' : $b['admin'].' <a href="?m=bugadmin&id='.$id.'&sid='.$sid.'"><i>ändern</i></a>').'</td></tr><tr><td colspan="2">'.($b['text']=="" ? '<i>'.$b['titel'].'</i>' : nl2br(text_replace($b['text']))).'</td></tr></table>';
		echo '<div class="info">Notizen zu diesem Bug</div><br><br>';

		$result=db_query('SELECT * FROM bugtracker_comments WHERE parentid='.$id);
		if (mysql_num_rows($result)==0) { echo '<p>Keine Kommentare gefunden.</p>'; }
		else {
			while ($bc=mysql_fetch_assoc($result)) {
				echo '<table><tr><th width="510">Von '.$bc['name'].' am '.date("d.m.Y", $bc['date']).' um '.date("H:i", $bc['date']).' Uhr</th></tr>
					<tr><td width="500">'.nl2br(text_replace($bc['text'])).'</td></tr></table>';
			}
		}
		echo '<br><div class="info">Notiz hinzufügen</div><form action="?m=showbug&id='.$id.'&sid='.$sid.'" method="post">
<table>
<tr><td><textarea name="text" rows="4" cols="60"></textarea></td></tr>
<tr><td><input type="submit" value="Abschicken"></td></tr>
</table>
</form>';
		echo '</div>';
		createlayout_bottom();
	}
	break;

	case 'addbug':
	if ($_POST['text']=="" && $_POST['titel']=="") {
		createlayout_top('HackTheNet - Bugtracker');
		echo '<div class="content" id="subnet"><h2>Bugtracker</h2><br><form action="?m=addbug&sid='.$sid.'" method="post">
<table>
<tr><th>Typ:</th><td>
<select name="type"><option value="feature">Feature Req.</option><option value="improvement">Verbesserung</option><option value="usability">Usability</option><option value="low">trivial</option><option value="middle" selected="selected">mittelschwer</option><option value="critical">kritisch</option></select>
</td></tr>
<tr><th>Kurze präzise Beschreibung:</th><td><input type="text" size="50" maxlength="255" name="titel" value=""></td></tr>
<tr><th>Ausführliche Beschreibung:</th><td><textarea rows="8" cols="60" name="text"></textarea></td></tr>
<tr><td colspan="2"><input type="submit" value="Absenden"></td></tr></table>
</form></div>';
		createlayout_bottom();
	} else {
		$type_numbers=array('feature'=>4, 'improvement'=>5, 'usability'=>6, 'low'=>3, 'middle'=>2, 'critical'=>1);
		$titel=trim(mysql_escape_string($_POST['titel']));
		$text=trim(mysql_escape_string($_POST['text']));
		$type=trim(mysql_escape_string($_POST['type']));
		if (strlen(mysql_escape_string($_POST['text']))>=1000) { simple_message('Der Text darf maximal 1000 Zeichen haben.'); }
		else {
			db_query('INSERT INTO bugtracker VALUES("", "'.mysql_escape_string($usr['name']).'", "", '.time().', "'.$text.'", "'.$type.'", "'.$titel.'", "new", '.$type_numbers[$type].', 1, 0)');
			header('Location: game.php?m=showbug&id='.mysql_insert_id().'&sid='.$sid);
		}
	}
	break;

	case 'editbug':
	if (is_noranKINGuser($usrid)) {
		$id=$_GET['id'];
		if ($id == "") { $id=$_POST['id']; }
		if (!is_numeric($id)) { exit; }
		$result=db_query('SELECT type,status,titel,text FROM bugtracker WHERE id='.$id.' LIMIT 1');
		$b=mysql_fetch_assoc($result);

		if ($_POST['text']=="" && $_POST['titel']=="") {
			createlayout_top('HackTheNet - Bugtracker');
			echo '<div class="content" id="subnet"><h2>Bugtracker</h2><br><form action="?m=editbug&id='.$id.'&sid='.$sid.'" method="post">
<table>
<tr><th>Typ:</th><td>
<select name="type">
<option value="feature" '.($b['type']=="feature" ? 'selected' : '').'>Feature Req.</option>
<option value="improvement" '.($b['type']=="improvement" ? 'selected' : '').'>Verbesserung</option>
<option value="usability" '.($b['type']=="usability" ? 'selected' : '').'>Usability</option>
<option value="low" '.($b['type']=="low" ? 'selected' : '').'>trivial</option>
<option value="middle" '.($b['type']=="middle" ? 'selected' : '').'>mittelschwer</option>
<option value="critical" '.($b['type']=="critical" ? 'selected' : '').'>kritisch</option></select>
</td></tr>
<tr><th>Status:</th><td>
<select name="status">
<option value="new" '.($b['status']=="new" ? 'selected' : '').'>new</option>
<option value="frozen" '.($b['status']=="frozen" ? 'selected' : '').'>frozen</option>
<option value="working on" '.($b['status']=="working on" ? 'selected' : '').'>working on</option>
<option value="reopen" '.($b['status']=="reopen" ? 'selected' : '').'>reopen</option>
<option value="fixed" '.($b['status']=="fixed" ? 'selected' : '').'>fixed</option>
<option value="wontfix" '.($b['status']=="wontfix" ? 'selected' : '').'>wontfix</option>
</td></tr>
<tr><th>Titel:</th><td><input type="text" size="50" maxlength="255" name="titel" value="'.$b['titel'].'"></td></tr>
<tr><th>Text:</th><td><textarea rows="8" cols="60" name="text">'.$b['text'].'</textarea></td></tr>
<tr><td colspan="2"><input type="submit" value="Absenden"></td></tr></table>
</form></div>';
			createlayout_bottom();
		} else {
			$id=$_GET['id'];
			if ($id == "") { $id=$_POST['id']; }
			if (!is_numeric($id)) { exit; }

			$type_numbers=array('feature'=>4, 'improvement'=>5, 'usability'=>6, 'low'=>3, 'middle'=>2, 'critical'=>1);
			$status_numbers=array('new'=>1, 'frozen'=>4, 'working on'=>2, 'reopen'=>3, 'fixed'=>5, 'wontfix'=>6);
			$titel=trim(mysql_escape_string($_POST['titel']));
			$text=trim(mysql_escape_string($_POST['text']));
			$type=trim(mysql_escape_string($_POST['type']));
			$status=trim(mysql_escape_string($_POST['status']));
			if (strlen(mysql_escape_string($_POST['text']))>=1000) { simple_message('Der Text darf maximal 1000 Zeichen haben.'); }
			db_query('UPDATE bugtracker SET titel="'.$titel.'", text="'.$text.'", type="'.$type.'", status="'.$status.'", type_number='.$type_numbers[$type].', status_number='.$status_numbers[$status].' WHERE id='.$id);
			header('Location: game.php?m=showbug&id='.$id.'&sid='.$sid);
		}
	} else { simple_message('Kein Zutritt!'); }
	break;

	case 'delbug':
	if (is_noranKINGuser($usrid)) {
		$id=$_GET['id'];
		if ($id == "") { $id=$_POST['id']; }
		if (!is_numeric($id)) { exit; }

		db_query('DELETE FROM bugtracker WHERE id='.$id.' LIMIT 1');
		header('Location: game.php?m=bugtracker&sid='.$sid);
	} else { simple_message('Kein Zutritt!'); }
	break;

	case 'bugadmin':
	if (is_noranKINGuser($usrid)) {
		$id=$_GET['id'];
		if ($id == "") { $id=$_POST['id']; }
		if (!is_numeric($id)) { exit; }

		db_query('UPDATE bugtracker SET admin="'.$usr['name'].'" WHERE id='.$id.' LIMIT 1');
		header('Location: game.php?m=bugtracker&sid='.$sid);
	} else { simple_message('Kein Zutritt!'); }
	break;

	case 'stats': // ----------------------- STATS --------------------------
createlayout_top('HackTheNet - Statistik');
echo '<div class="content" id="server-statistic">'."\n";
echo '<h2>Statistik</h2>'."\n";
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

$fn='data/_server1/logins_'.strftime('%x',time()-86400).'.txt';
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
}


// ----------------------- Gewinnspiel --------------------------
$wintipp=mt_rand() % 100 + 1;
$jackpotresult=db_query('SELECT jackpot,lastwin FROM win WHERE id=1'); $jackpot=mysql_fetch_assoc($jackpotresult);
$lastwin=$jackpot['lastwin'];
$winnerresult=db_query('SELECT * FROM win WHERE tipp='.$wintipp.' && id!=1');
$looserresult=db_query('SELECT * FROM win WHERE tipp!='.$wintipp.' && id!=1');
if (mysql_num_rows($winnerresult)>1) { $jackpotj=ceil($jackpot['jackpot'] / mysql_num_rows($winnerresult)); }
else { $jackpotj=$jackpot['jackpot']; }
if ((date("H", time())==20 && $lastwin<time()-3600 && $lastwin!=0) || $lastwin<time()-86501) {
	$winner='';
	while($win=mysql_fetch_array($winnerresult)) {
		$winner.=$win['userid'].' ';
		$mails=mysql_fetch_assoc(db_query('SELECT newmail FROM users WHERE id='.$win['userid']));
		$mails['newmail']++;
		db_query('UPDATE users set newmail='.$mails['newmail'].' WHERE id='.$win['userid']);
		$winnerpc=mysql_fetch_assoc(db_query('SELECT id,credits,ip FROM pcs WHERE owner='.$win['userid'].' LIMIT 1'));
		$creditsnow=$winnerpc['credits'] + $jackpotj;
		db_query('UPDATE pcs SET credits='.$creditsnow.' WHERE id='.$winnerpc['id']);
		db_query('INSERT INTO sysmsgs(`msg`, `user`, `time`, `text`, `xread`) VALUES("", "'.$win['userid'].'", "'.time().'", "Du hast soeben beim HTN.Lan Gewinnspiel gewonnen. Dein Gewinn beträgt '.$jackpotj.' Credits. Herzlichen Glückwunsch! Das Geld wurde deinem Computer mit der IP 10.47.'.$winnerpc['ip'].' gutgeschrieben.", "no")');
	}
	$looser=$winner;
	while($loss=mysql_fetch_array($looserresult)) {
		if (strstr($looser, $loss['userid'])=="") {
			$mails=mysql_fetch_assoc(db_query('SELECT newmail FROM users WHERE id='.$loss['userid']));
			$mails['newmail']++;
			db_query('UPDATE users set newmail='.$mails['newmail'].' WHERE id='.$loss['userid']);
			$looser.=$loss['userid'].' ';
			db_query('INSERT INTO sysmsgs(`msg`, `user`, `time`, `text`, `xread`) VALUES("", "'.$loss['userid'].'", "'.time().'", "Du hast beim HTN.Lan Gewinnspiel dieses mal leider kein Glück gehabt. Versuch es einfach nochmal!.", "no")');
		}
	}
	if (mysql_num_rows($winnerresult)==0) { $jackpotn=$jackpot['jackpot']; }
	db_query('TRUNCATE `win`');
	db_query('INSERT INTO win(`id`, `userid`, `tipp`, `time`, `jackpot`, `lastwin`, `wintipp`) VALUES("1", "1", "1", "'.time().'", "'.$jackpotn.'", "'.time().'", "'.$wintipp.'")');
}

?>