<?php

define('IN_HTN',1);
$FILE_REQUIRES_PC=FALSE;
include('ingame.php');

$action=$_REQUEST['page'];

if($usr['da_avail']!='yes') 
{
  simple_message('Noch nicht verf&uuml;gbar!'); exit; 
}

# Cluster-Daten lesen:
$clusterid=$usr['cluster'];
$cluster=getcluster($clusterid);
if($cluster==false) die('Du hast keinen Cluster!');

$pc=getpc($pcid);

$javascript='<script type="text/javascript">
function da_check(e) 
{
  var x=e.form.sellist.options[e.form.sellist.selectedIndex].value;
  if(x==\'userdef\') 
  {
    if(!isNaN(e.form.howmuch.value)) 
    {
      x=parseInt(e.form.howmuch.value);
    }
  }
  else
  if(x==\'all\') x=e.form.elements.length-4; else x=Math.floor(x/100*(e.form.elements.length-4));
  if(!isNaN(x)) e.form.howmuch.value=x;
  for(i=0;i<e.form.elements.length;i++) 
  {
    if(e.form.elements[i].type==\'checkbox\' && e.form.elements[i].name.substr(0,2)==\'pc\') 
    {
      e.form.elements[i].checked=(i<x);
    }
  }
}
function confirm_abort() 
{
  return window.confirm(\'Die Distributed Attack wirklich abbrechen?\');
}
</script>';

createlayout_top('HackTheNet - Cluster - Distributed Attacks');
echo '<div class="content" id="cluster">'."\n";
echo '<h2>Cluster</h2>'."\n";

switch($action) 
{
  
  case 'list':  // -------------------------------- LIST -------------------------------
  
  $r=db_query('SELECT * FROM distr_attacks WHERE cluster='.mysql_escape_string($clusterid).';');
  $cnt=mysql_num_rows($r);
  
  echo '<div id="cluster-distributed-attacks">
  <h3>Distributed Attacks (Anzahl: '.$cnt.')</h3>';
  if($usr['da_avail']=='yes') 
  {
    if(isavailh('da',$pc)==true) echo '<p><a href="distrattack.php?sid='.$sid.'&amp;page=create">Neue Distributed Attack erstellen</a></p>'."\n";
    else echo '<p>Von diesem PC aus kannst du keine DA erstellen!</p>'."\n";
    echo '<p><a href="distrattack.php?sid='.$sid.'&amp;page=joinall">An allen Distributed Attacks teilnehmen</a></p>'."\n";
  }
  
  if($cnt>0) 
  {
    echo '<table>
    <tr>
    <th class="ip">Ziel</th>
    <th class="author">Initiator</th>
    <th class="participants">Teilnehmer</th>
    <th class="actions">Aktion</th>
    </tr>
    ';
    
    $z=-1;
    while($item=mysql_fetch_assoc($r))
    {
      $z++;
      $initusr=getuser($item['initiator_usr']);
      if($initusr!==false) 
      {
        $p=getpc($item['target']);
        $owner=getuser($p['owner']);
        $targetitem=idtoname($item['item']);
        
        $result=db_query('SELECT * FROM da_participants WHERE relative=\''.mysql_escape_string($item['id']).'\'');
        
        $total=mysql_num_rows($result);
        
        echo '<tr>
        <td class="ip">10.47.'.$p['ip'].'<br />'.$targetitem.'<br /><br />Besitzer: <a href="user.php?page=info&amp;sid='.$sid.'&amp;user='.$p['owner'].'">'.$owner['name'].'</a></td>
        <td class="author"><a href="user.php?page=info&amp;sid='.$sid.'&amp;user='.$initusr['id'].'">'.$initusr['name'].'</a><br />
        '.$total.' teilnehmende PCs</td>
        <td class="participants">';
        
        $i=0;
        if($total==1) echo 'keine weiteren'; else 
        {
          while($b=mysql_fetch_assoc($result))
          {
            echo '10.47.'.$b['ip'].' (<a href="user.php?page=info&amp;sid='.$sid.'&amp;user='.$b['owner'].'">'.$b['owner_name'].'</a>)';
            if($i<$total-1) echo '<br />';
            $i++;
          }
        }
        echo '</td>'.LF.'<td>';
        if($usr['da_avail']=='yes') 
        {
          if($initusr['id']==$usrid || $usr['clusterstat']==CS_ADMIN) echo '<a href="distrattack.php?page=cancel&amp;sid='.$sid.'&amp;da='.$item['id'].'" onclick="return confirm_abort();">Abbrechen</a>,'.LF.'<a href="distrattack.php?page=exec&amp;sid='.$sid.'&amp;da='.$item['id'].'">Angriff!</a>, '."\n";
          echo '<a href="distrattack.php?page=join&amp;sid='.$sid.'&amp;da='.$item['id'].'">Mitmachen</a>';
        }
        else echo 'Die DA ist noch auf keinem deiner PCs verf&uuml;gbar.';
        echo '</td>'.LF.'</tr>'."\n";
      }
    }
    echo '</table>'."\n";
  }
  echo '</div>'."\n";
  
  break;
  
  case 'create': // -------------------------------- CREATE -------------------------------
  if(isavailh('da',$pc)!=true) exit;
  echo '<div class="tip">
  <h3>Info</h3>
  <p>Du erstellst jetzt eine Art Einladung. Andere User aus deinem Cluster k&ouml;nnen sich dieser Einladung anschlie&szlig;en.<br />
  Sobald du es bestimmst, wird der Feind mit der gesammelten Power aller Teilnehmer angegriffen!</p>
  </div>
  
  <div id="cluster-create-distributed-attack">
  <h3>Distributed Attack erstellen</h3>
  <form action="distrattack.php?page=create_submit&amp;sid='.$sid.'" method="post">
  <table>
  <tr id="cluster-create-distributed-attack-ip">
  <th>Ziel-Computer:</th>
  <td><input type="text" name="ip" value="10.47." /></td>
  </tr>
  <tr id="cluster-create-distributed-attack-target">
  <th>Zerst&ouml;rungs-Ziel:</th>
  <td><input name="t" value="cpu" checked="checked" type="radio" /> Prozessor<br />
  <input name="t" value="av" type="radio" /> Antivirus-Programm<br />
  <input name="t" value="fw" type="radio" /> Firewall</td>
  </tr>
  <tr id="cluster-create-distributed-attack-confirm">
  <td colspan="2"><input type="submit" value="Erstellen" /></td>
  </tr>
  </table>
  </form>
  </div>
  ';
  
  break;
  
  case 'create_submit': // -------------------------------- CREATE SUBMIT -------------------------------
  
  $t=$_POST['t'];
  $ip=$_POST['ip'];
  $e='';
  $ip=trim($ip," \n\r\x0b\0\t");
  if(eregi('^10\\.47\\.([0-9]{2,3})\\.([0-9]{1,3})$',$ip)==true) 
  {
    $target=getpc(substr($ip,6),'ip');
    if($target==false) $e.='Ung&uuml;ltige Ziel-Adresse: Der PC existiert nicht!<br />';
  }
  else $e.='Ung&uuml;ltige Ziel-Adresse: IP-Adresse muss in der Form 10.47.x.x vorliegen!<br />';
  switch($t) 
  {
    case 'cpu': break;
    case 'av': break;
    case 'fw': break;
    default: exit;
  }
  $owner=getuser($target['owner']);
  if($owner['id']==$usrid || ($owner['cluster']==$clusterid && $owner!=false)) $e.='Du kannst weder dich selber noch deinen eigenen Cluster angreifen!<br />';
  
  if($e=='') 
  {
    $country=getcountry('id',$target['country']);
    $credits=$country['in']*20;
    if($pc['credits']<$credits) $e.='Du hast nicht gen&uuml;gend Geld, um die Einfuhr-Geb&uuml;hr von '.$credits.' Credits nach '.$country['name'].', dem Standort von 10.47.'.$target['ip'].', zu bezahlen!';
    else 
    {
      $code=random_string(10);
      echo '<div id="cluster-create-distributed-attack2">
      <h3>Distributed Attack erstellen</h3>
      <form action="distrattack.php?page=create_final&amp;sid='.$sid.'" method="post">
      <table>
      <tr>
      <th>Ziel-PC:</th>
      <td>10.47.'.$target['ip'].'</td>
      </tr>
      <tr>
      <th>Eigentümer:</th>
      <td><a href="user.php?page=info&amp;sid='.$sid.'&amp;user='.$target['owner'].'">'.$owner['name'].'</a></td>
      </tr>
      <tr>
      <th>Kosten:</th>
      <td>F&uuml;r die Einfuhr nach '.$country['name'].' fallen '.$credits.' Credits an.<br />
      Diese werden schon jetzt vom Konto deines PCs 10.47.'.$pc['ip'].' ('.$pc['name'].') abgezogen.</td>
      </tr>
      <tr id="cluster-create-distributed-attack2-confirm">
      <td colspan="2"><input type="hidden" name="code" value="'.$code.'" /><input type="submit" value="Erstellen!" /></td>
      </tr>
      </table>
      </form>
      </div>
      ';
      file_put($DATADIR.'/tmp/da_'.$code.'.txt',$target['id'].'|'.$credits.'|'.$t);
    }
  }
  
  if($e!='') echo '<div class="error">'.LF.'<h3>Fehler!</h3>'.LF.'<p>'.$e.'</p>'.LF.'</div>'."\n";
  
  break;
  
  case 'create_final': // -------------------------------- CREATE FINAL -------------------------------
  
  $fn=$DATADIR.'/tmp/da_'.$_POST['code'].'.txt';
  if(file_exists($fn)==false) exit;
  echo '<div class="ok"><h3>Aktion ausgef&uuml;hrt</h3>'.LF.'<p>Die Distributed Attack wurde erstellt.</p>'.LF.'</div>'."\n";
  list($target,$credits,$item)=explode('|',file_get($fn));
  @unlink($fn);
  db_query('INSERT INTO distr_attacks VALUES(\'0\', \''.mysql_escape_string($clusterid).'\', \''.$pcid.'\', \''.$usrid.'\', \''.mysql_escape_string($target).'\', \''.mysql_escape_string($item).'\');');
  $id=mysql_insert_id();
  db_query('INSERT INTO da_participants SET relative='.mysql_escape_string($id).', pc='.mysql_escape_string($pc['id']).', ip=\''.mysql_escape_string($pc['ip']).'\', owner='.$usrid.', owner_name=\''.mysql_escape_string($usr['name']).'\';');
  #echo mysql_error();
  $pc['credits']-=$credits;
  db_query('UPDATE pcs SET credits='.mysql_escape_string($pc['credits']).' WHERE id='.$pcid.';');
  
  break;
  
  case 'join': // -------------------------------- JOIN -------------------------------
  
  $i=(int)$_REQUEST['da'];
  $a=@mysql_fetch_assoc(db_query('SELECT * FROM distr_attacks WHERE id='.mysql_escape_string($i).' LIMIT 1;'));
  if(isset($a['id'])==true && $a['cluster']==$clusterid) 
  {
    echo '<div id="cluster-distributed-attack-join-start">'."\n";
    echo '<h3>Mitmachen</h3>'."\n";
    echo '<p><strong>W&auml;hle die PCs, mit denen du bei der DA mithelfen willst:</strong></p>'."\n";
    echo '<p>Das Mitmachen kostet 1000 Credits pro PC. Nur PCs mit mindestens 1000 Credits im BucksBunker und solche, die noch nicht an der DA teilnehmen, werden angezeigt.</p>'."\n";
    echo '<form action="distrattack.php?sid='.$sid.'&amp;da='.$a['id'].'&amp;page=join2" method="post">'."\n";
    echo '<table>';
    $gcnt=0;
    $sql=db_query('SELECT * FROM pcs WHERE owner='.mysql_escape_string($usr['id']).' ORDER BY points DESC;');
    while($x=mysql_fetch_assoc($sql))
    {
      if(isavailh('da',$x)!=true) continue;
      $result=db_query('SELECT relative FROM da_participants WHERE relative=\''.$a['id'].'\' AND pc=\''.$x['id'].'\';');
      if(mysql_num_rows($result)>0) continue;
      
      # Geld updaten:
      if($x['lmupd']+60<=time()) 
      {
        $plus=(int)round(get_gdph($x)*((time()-$x['lmupd'])/3600),0);
        $x['credits']+=$plus;
        $x['lmupd']=time();
        $max=getmaxbb($x);
        if($x['credits']>$max) $x['credits']=$max;
        db_query('UPDATE pcs SET lmupd=\''.$x['lmupd'].'\', credits=\''.$x['credits'].'\' WHERE id='.mysql_escape_string($x['id']).';');
      }
      
      if($x['credits'] >= 1000) 
      {
        $gcnt++;
        echo '<tr><th>10.47.'.$x['ip'].' ('.$x['name'].', '.$x['points'].' Punkte)</th><td><input checked="checked" type="checkbox" name="pc'.$x['id'].'" value="1" /></td></tr>';
      }
    }
    echo '</table>'."\n";
    if($gcnt>0)
    {
      echo '<p>W&auml;hlen: <select name="sellist" onchange="da_check(this)"><option value="all">Alle</option><option value="75">75%</option><option value="50">50%</option><option value="25">25%</option><option value="0">keine</option><option value="userdef">Anzahl:</option></select>
      <input type="text" name="howmuch" value="" size="3" maxlength="3"/><input type="button" value="OK" onclick="da_check(this)" /></p>'."\n";
      echo '<p><input type="submit" value=" Weiter " /></p>'."\n";
    }
    else
    echo '<p><strong>Leider erf&uuml;llt keiner deiner PCs die Bedingungen, um an dieser DA teilzunehmen!</strong></p>'."\n";
    echo '</form>'.LF.'</div>'."\n";
  }
  
  break;
  
  case 'join2': // -------------------------------- JOIN FINAL -------------------------------
  
  // Eine Lockfile sicher erzeugen, sodass das Anlegen nicht parallel geschieht.
  // Sollte die Lockfile nicht angelegt werden können, wird das selbe bis zu zehn
  // Mal weiter probiert, sollte es dann noch fehlschlagen, wird das Script
  // beendet
  /*$uniqueFileName = $sidfile.getmypid().microtime();
  $lockfp = fopen($uniqueFileName, 'w+');
  if (!fclose($lockfp)) 
  {
    die('Konnte DA-Kram nicht serialisieren: Sperrdatei konnte nicht vorbereitet werden.');
  }
  $i = 0;
  while (!@rename($uniqueFileName, $sidfile.'.dalock')) 
  {
    usleep(mt_rand(100, 2000));
    if ($i++ > 100) 
    {
      die('Konnte DA-Kram nicht serialisieren: Zeitueberschreitung beim Anlegen der Sperrdatei.');
    }
  }*/
  
  $i=(int)$_REQUEST['da'];
  $a=@mysql_fetch_assoc(db_query('SELECT * FROM distr_attacks WHERE id='.mysql_escape_string($i).' LIMIT 1;'));
  if(isset($a['id'])==true && $a['cluster']==$clusterid) 
  {
    
    $gcnt=0;
    $sql=db_query('SELECT * FROM pcs WHERE owner='.$usr['id'].';');
    while($xpc=mysql_fetch_assoc($sql))
    {
      if($_POST['pc'.$xpc['id']]!=1) continue;
      $creds=$xpc['credits'];
      if(isavailh('da',$xpc)!=true || $xpc['owner']!=$usrid || $creds<1000) continue;
      $result=db_query('SELECT relative FROM da_participants WHERE relative=\''.mysql_escape_string($a[id]).'\' AND pc=\''.mysql_escape_string($xpc['id']).'\';');
      if(mysql_num_rows($result)>0) continue;
      $creds-=1000;
      db_query('UPDATE pcs SET credits=\''.mysql_escape_string($creds).'\' WHERE id=\''.mysql_escape_string($xpc['id']).'\';');
      db_query('INSERT INTO da_participants SET relative=\''.mysql_escape_string($a['id']).'\', pc=\''.mysql_escape_string($xpc['id']).'\', ip=\''.mysql_escape_string($xpc['ip']).'\', owner=\''.$usrid.'\', owner_name=\''.mysql_escape_string($usr['name']).'\';');
      $cnt++;
      #if($gcnt > 100) break;
    }
    
    echo '<div class="ok"><h3>Angriff</h3>'.LF.'<p>Du bist dabei!</p>'.LF.'</div><br />
    <p><a href="distrattack.php?page=list&amp;sid='.$sid.'">Weiter</a></p>';
  }
  
  // Die oben angelegt Lockfile wieder löschen und damit anderen Anfragen
  // Platz machen
  /*if (!unlink($sidfile.'.dalock')) 
  {
    die('Konnte Upgradeanfragen nicht serialisieren: Sperrdatei konnte nicht gelöscht werden. Die Anfrage wurde jedoch bearbeitet.');
  }*/
  
  break;
  
  case 'joinall': // -------------------------------- JOIN ALL -------------------------------
  
  $r=db_query('SELECT id FROM distr_attacks WHERE cluster='.mysql_escape_string($clusterid).';');
  $cnt=mysql_num_rows($r);
  
  echo '<p><strong>W&auml;hle die PCs, mit denen du an allen DAs teilnehmen m&ouml;chtest!</strong><br />
  Es werden nur PCs angezeigt, auf denen die DA und gen&uuml;gend Geld vorhanden ist!</p>
  <form action="distrattack.php?page=joinall2&amp;sid='.$sid.'" method="post">
  <table>';
  
  $gcnt=0;
  $sql=db_query('SELECT * FROM pcs WHERE owner='.mysql_escape_string($usr['id']).';');
  while($x=mysql_fetch_assoc($sql))
  {
    if(isavailh('da',$x)!=true) continue;
    
    # Geld updaten:
    if($x['lmupd']+60<=time()) 
    {
      $plus=(int)round(get_gdph($x)*((time()-$x['lmupd'])/3600),0);
      $x['credits']+=$plus;
      $x['lmupd']=time();
      $max=getmaxbb($x);
      if($x['credits']>$max) $x['credits']=$max;
      db_query('UPDATE pcs SET lmupd=\''.mysql_escape_string($x['lmupd']).'\', credits=\''.mysql_escape_string($x['credits']).'\' WHERE id='.mysql_escape_string($x['id']).';');
    }
    
    if($x['credits'] >= 1000) 
    {
      $gcnt++;
      echo '<tr><th>10.47.'.$x['ip'].' ('.$x['name'].')</th><td><input type="checkbox" name="pc'.$x['id'].'" value="1" /></td></tr>';
    }
    
  }
  echo '</table>'."\n";
  if($gcnt>0)
  {
    echo '<p>W&auml;hlen: <select name="sellist" onchange="da_check(this)"><option value="all">Alle</option><option value="75">75%</option><option value="50">50%</option><option value="25">25%</option><option value="0">keine</option><option value="userdef">Anzahl:</option></select>
    <input type="text" name="howmuch" value="" size="3" maxlength="3"/><input type="button" value="OK" onclick="da_check(this)" /></p>'."\n";
    echo '<p><input type="submit" value=" Weiter " /></p>'."\n";
  }
  else
  echo '<p><strong>Leider erf&uuml;llt keiner deiner PCs die Bedingungen, um an dieser DA teilzunehmen!</strong></p>'."\n";
  echo '</form>'."\n";
  
  
  break;
  
  case 'joinall2': // -------------------------------- JOIN ALL FINAL -------------------------------
  
  // Eine Lockfile sicher erzeugen, sodass das Anlegen nicht parallel geschieht.
  // Sollte die Lockfile nicht angelegt werden können, wird das selbe bis zu zehn
  // Mal weiter probiert, sollte es dann noch fehlschlagen, wird das Script
  // beendet
  /*$uniqueFileName = $sidfile.getmypid().microtime();
  $lockfp = fopen($uniqueFileName, 'w+');
  if (!fclose($lockfp)) 
  {
    die('Konnte DA-Kram nicht serialisieren: Sperrdatei konnte nicht vorbereitet werden.');
  }
  $i = 0;
  while (!@rename($uniqueFileName, $sidfile.'.dalock')) 
  {
    usleep(mt_rand(100, 2000));
    if ($i++ > 100) 
    {
      die('Konnte DA-Kram nicht serialisieren: Zeitueberschreitung beim Anlegen der Sperrdatei.');
    }
  }
  */
  
  $r=db_query('SELECT id FROM distr_attacks WHERE cluster='.mysql_escape_string($clusterid).';');
  
  $gcnt=0;
  while($a=mysql_fetch_assoc($r)) 
  {
    $sql=db_query('SELECT * FROM pcs WHERE owner='.mysql_escape_string($usr['id']).';');
    while($xpc=mysql_fetch_assoc($sql))
    {
      if($_POST['pc'.$xpc['id']]!=1) continue;
      $creds=$xpc['credits'];
      if(isavailh('da',$xpc)!=true || $xpc['owner']!=$usrid || $creds<1000) continue;
      $result=db_query('SELECT relative FROM da_participants WHERE relative=\''.mysql_escape_string($a['id']).'\' AND pc=\''.mysql_escape_string($xpc['id']).'\';');
      if(mysql_num_rows($result)>0) continue;
      $creds-=1000;
      db_query('UPDATE pcs SET credits=\''.mysql_escape_string($creds).'\' WHERE id=\''.mysql_escape_string($xpc['id']).'\';');
      db_query('INSERT INTO da_participants SET relative=\''.mysql_escape_string($a['id']).'\', pc=\''.mysql_escape_string($xpc['id']).'\', ip=\''.mysql_escape_string($xpc['ip']).'\', owner=\''.$usrid.'\', owner_name=\''.mysql_escape_string($usr['name']).'\';');
      $gcnt++;
      #if($gcnt > 10) break;
    }
  }
  
  echo '<div class="ok"><h3>Angriff</h3>'.LF.'<p>Du bist dabei!</p>'.LF.'</div><br />
  <p><a href="distrattack.php?page=list&amp;sid='.$sid.'">Weiter</a></p>';
  
  // Die oben angelegt Lockfile wieder löschen und damit anderen Anfragen
  // Platz machen
  /*if (!unlink($sidfile.'.dalock')) 
  {
    die('Konnte Upgradeanfragen nicht serialisieren: Sperrdatei konnte nicht gelöscht werden. Die Anfrage wurde jedoch bearbeitet.');
  }*/
  
  break;
  
  case 'cancel': // -------------------------------- CANCEL -------------------------------
  
  $i=(int)$_REQUEST['da'];
  $a=@mysql_fetch_assoc(db_query('SELECT * FROM distr_attacks WHERE id='.mysql_escape_string($i).' LIMIT 1;'));
  if($usr['clusterstat']==CS_ADMIN || $a['initiator_usr']==$usrid) 
  {
    if(isset($a['id'])==true && $a['cluster']==$clusterid) 
    {
      db_query('DELETE FROM distr_attacks WHERE id='.mysql_escape_string($a['id']).' LIMIT 1;');
      db_query('DELETE FROM da_participants WHERE relative='.mysql_escape_string($a['id']).';');
      echo '<div class="important"><h3>Abgebrochen</h3><p>Distributed Attack abgebrochen!</p></div>';
    }
  }
  else die('Keine Berechtigung!');
  
  break;
  
  case 'exec': // -------------------------------- EXECUTE -------------------------------
  
  // Eine Lockfile sicher erzeugen, sodass das Anlegen nicht parallel geschieht.
  // Sollte die Lockfile nicht angelegt werden können, wird das selbe bis zu zehn
  // Mal weiter probiert, sollte es dann noch fehlschlagen, wird das Script
  // beendet
  /*$uniqueFileName = $sidfile.getmypid().microtime();
  $lockfp = fopen($uniqueFileName, 'w+');
  if (!fclose($lockfp)) 
  {
    die('Konnte DA-Kram nicht serialisieren: Sperrdatei konnte nicht vorbereitet werden.');
  }
  $i = 0;
  while (!@rename($uniqueFileName, $sidfile.'.dalock')) 
  {
    usleep(mt_rand(100, 1000));
    if ($i++ > 100) 
    {
      die('Konnte DA-Kram nicht serialisieren: Zeitueberschreitung beim Anlegen der Sperrdatei.');
    }
  }*/
  
  $i=(int)$_REQUEST['da'];
  $a=@mysql_fetch_assoc(db_query('SELECT * FROM distr_attacks WHERE id='.mysql_escape_string($i).' LIMIT 1;'));
  db_query('DELETE FROM distr_attacks WHERE id='.mysql_escape_string($i).' LIMIT 1;');
  
  if(isset($a['id'])==true && $a['cluster']==$clusterid) 
  {
    
    if($a['initiator_usr']!=$usrid && $usr['clusterstat']!=CS_ADMIN) 
    {
      exit; 
    }
    $result=db_query('SELECT * FROM da_participants WHERE relative=\''.mysql_escape_string($a['id']).'\'');
    $total=mysql_num_rows($result);
    if($total > 200) die('Zu viele PCs!');
    if($total > 1) 
    {
      
      $attack=0;
      while($b=mysql_fetch_assoc($result)) 
      {
        $p=getpc($b['pc']);
        $attack+=$p[cpu] + $p[ips]*2 + $p[mk]*2 + $p[sdk] + $p[lan]/2 + mt_rand(0,5);
      }
      
      if($total > 100)
      {
        $attack = $attack / $total;
      }
      
      $remote=getpc($a[target]);
      
      
      $owner=@getuser($remote['owner']);
      $xdefence=$remote['fw'] + $remote['av'] + $remote['ids']/2;
      $rscan=(int)(isavailh('scan',$remote));
      
      if( count(explode(',',$owner['pcs'])) < 2 && (
      ($xdefence<=MIN_ATTACK_XDEFENCE && $rscan==0)
      )) 
      {
        
        echo '<div class="error"><h3>N00b-Schutz</h3><p>Ziel zu schwach. Angriff nicht möglich!</p></div>';
        createlayout_bottom();
        exit;
        
      }
      
      $defend=$remote['cpu']*count($item) + $remote['fw']*2 + $remote['ids'] + $remote['av']*2 + mt_rand(0,8*count($item));
      $dif=$attack-$defend;
      #echo 'attack='.$attack.' defend='.$defend.' dif='.$dif.'<br />';
      
      ############
      
      $nx=false;
      $lv=$remote[$a['item']];
      if($a['item']=='cpu') 
      {
        $lv-=$dif/550;
        $lv=ceil($lv);
        
        if($lv>0) 
        {
          $lv=$cpu_levels[$lv];
        }
        elseif($remote[cpu]==$cpu_levels[0]) 
        {
          $lv=$cpu_levels[0]; $nx=true; 
        }
        else $lv=$cpu_levels[0];
      }
      else 
      {
        #echo 'dif='.$dif.' lv='.$lv.'<br />';
        $lv-=$dif/700;
        #echo 'dif='.$dif.' lv='.$lv.'<br />';
        if($lv>1) 
        {
          $in=floor($lv);
          $tmp=$lv-$in;
          if($tmp<0.5) $lv=$in; else $lv=$in+0.5;
        }
        elseif($remote[$a['item']]==1) 
        {
          $lv=1; $nx=true; 
        }
        else $lv=1;
      }
      
      ############
      
      if($dif<10 || $remote[$a['item']]==$lv || ($a[item]=='cpu' && $remote[$a['item']]==array_search($lv,$cpu_levels)) ) 
      {
        echo '<div class="error"><h3>Fehlgeschlagen!</h3><p>Der Angriff war zu schwach und konnte so abgewehrt werden!</p></div><br />';
        $owner=getuser($remote['owner']);
        $msg='Die Distributed Attack auf 10.47.'.$remote['ip'].' (Besitzer: [usr='.$owner['id'].']'.$owner['name'].'[/usr]), an der du teilgenommen hast, war leider nicht erfolgreich!';
        addsysmsg($owner['id'],'Von ' . $total . ' PCs von Spielern des Clusters [cluster='.$clusterid.']'.$cluster['code'].'[/cluster] wurde eine Distributed Attack gegen dich ausgef&uuml;hrt!<br />Sie konnte aber abgewehrt werden: Auf deinem PC 10.47.'.$remote['ip'].' ('.$remote['name'].') gab es keinen Schaden!');
      }
      else 
      {
        
        if($nx==false) 
        {
          $remote[$a['item']]=($a['item']!='cpu' ? $lv : array_search($lv,$cpu_levels));
          savepc($a['target'],$remote);
        }
        $notif='Der Angriff war erfolgreich!!<br />'.
        idtoname($a['item']).' wurde auf Level '.$lv.' zerst&ouml;rt!!';
        echo '<div class="ok"><h3>Erfolg!</h3><p>'.$notif.'</p></div><br />';
        $name=getuser($remote['owner']);
        $msg='Die Distributed Attack auf 10.47.'.$remote['ip'].' (Besitzer: [usr='.$remote['owner'].']'.$name['name'].'[/usr]), an der du teilgenommen hast, war erfolgreich!!<br />'.idtoname($a['item']).' wurde auf Level '.$lv.' zerst&ouml;rt!!';
        addsysmsg($remote['owner'],'Von ' . $total . ' PCs von Spielern des Clusters [cluster='.$clusterid.']'.$cluster['code'].'[/cluster] wurde eine Distributed Attack gegen dich ausgef&uuml;hrt!<br />Sie war erfolgreich: Auf deinem PC 10.47.'.$remote['ip'].' ('.$remote['name'].') wurde '.idtoname($a['item']).' auf '.$lv.' zerst&ouml;rt!');
      }
      
      $sended=array();
      $i=(int)$_REQUEST['da'];
      $result=db_query('SELECT owner FROM da_participants WHERE relative=\''.mysql_escape_string($i).'\'');
      while($b=mysql_fetch_assoc($result)) 
      {
        $uix=$b['owner'];
        if($sended[$uix]!=true) 
        {
          addsysmsg($uix,$msg);
          $sended[$uix]=true;
        }
      }
      
      db_query('DELETE FROM da_participants WHERE relative='.$i.';');
    }
    else echo '<div class="error"><h3>Fehler</h3><p>Es m&uuml;ssen sich noch andere User dem Angriff anschlie&szlig;en!</p></div>';
  }
  
  // Die oben angelegt Lockfile wieder löschen und damit anderen Anfragen
  // Platz machen
  /*if (!unlink($sidfile.'.dalock')) 
  {
    die('Konnte Upgradeanfragen nicht serialisieren: Sperrdatei konnte nicht gelöscht werden. Die Anfrage wurde jedoch bearbeitet.');
  }*/
  
  break;
}

echo '</div>'."\n";
createlayout_bottom();

?>