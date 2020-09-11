<?php
#adf
if ( !defined('IN_HTN') )
{
  die('Hacking attempt');
}

$action=mt_rand(1,4);

switch($action) {

case 1: // PC von User aus dem oberen Teil der Rangliste blockieren
  $victim=db_query('SELECT * FROM users WHERE rank<=20 ORDER BY RAND() LIMIT 1;');
  if(!$victim) continue;
  $victim=mysql_fetch_assoc($victim);
  if((int)$victim['id']==0) continue;
  #echo '<br>id='.$victim['id'];
  $vpc=@mysql_fetch_assoc(db_query('SELECT id,ip,name FROM pcs WHERE owner='.$victim['id'].' ORDER BY RAND() LIMIT 1;'));
  $blocked=time() + 2 * 60 * 60;
  db_query('UPDATE pcs SET blocked=\''.mysql_escape_string($blocked).'\' WHERE id='.$vpc['id'].';');
  addsysmsg($victim['id'], 'Dein PC 10.47.'.$vpc['ip'].' ('.$vpc['name'].') wurde durch einen b&ouml;sartigen Wurm, der im Moment im Netz kursiert,
  bis '.nicetime($blocked).' blockiert!');
  db_query('INSERT INTO logs SET type=\'worm_blockpc\', usr_id=\''.mysql_escape_string($victim['id']).'\', payload=\'blocked pc '.$vpc['id'].'\';');
  break;

case 2: // PC von aktivem User aus dem Mittelfeld der Rangliste Credits schenken
  $ts=time()-24*60*60;
  $victim=db_query('SELECT * FROM users WHERE (rank>80 AND rank<30 AND login_time>'.mysql_escape_string($ts).') ORDER BY RAND() LIMIT 1;');
  echo mysql_error();
  if(!$victim) continue;
  $victim=mysql_fetch_assoc($victim);
  if((int)$victim['id']==0) continue;
  #echo '<br>id='.$victim['id'];
  $vpc=@mysql_fetch_assoc(db_query('SELECT id,ip,name,credits FROM pcs WHERE owner='.$victim['id'].' ORDER BY RAND() LIMIT 1;'));
  $plus=mt_rand(10000, 100000);
  $creds=$vpc['credits']+$plus;
  db_query('UPDATE pcs SET credits=\''.mysql_escape_string($creds).'\' WHERE id='.mysql_escape_string($vpc['id']).';');
  addsysmsg($victim['id'], 'Auf deinen PC 10.47.'.$vpc['ip'].' ('.$vpc['name'].') wurde durch einen Wurm, der im Moment im Netz kursiert,
  die Summe von '.$plus.' Credits &uuml;berwiesen!');
  db_query('INSERT INTO logs SET type=\'worm_pcsendmoney\', usr_id=\''.mysql_escape_string($victim['id']).'\', payload=\'gave '.mysql_escape_string($plus).' credits to pc '.mysql_escape_string($vpc['id']).'\';');
  break;

case 3: // PC von User aus dem oberen Teil der Rangliste zerstören
  $victim=db_query('SELECT * FROM users WHERE rank<=20 ORDER BY RAND() LIMIT 1;');
  if(!$victim) continue;
  $victim=mysql_fetch_assoc($victim);
  if((int)$victim['id']==0) continue;
  #echo '<br>id='.$victim['id'];
  $vpc=@mysql_fetch_assoc(db_query('SELECT id,ip,name FROM pcs WHERE owner='.$victim['id'].' ORDER BY RAND() LIMIT 1;'));
  $blocked=time() + 2 * 60 * 60;
  db_query('UPDATE pcs SET cpu=\'1\', ram=\'1\', lan=\'1\', mm=\'1\', bb=\'1\', ads=\'0\', dialer=\'0\', auctions=\'0\', bankhack=\'0\', fw=\'0\', mk=\'0\', av=\'0\', ids=\'0\', ips=\'0\', rh=\'0\', trojan=\'0\', sdk=\'0\' WHERE id='.$vpc['id'].';');
  addsysmsg($victim['id'], 'Dein PC 10.47.'.$vpc['ip'].' ('.$vpc['name'].') wurde durch einen b&ouml;sartigen Wurm, der im Moment im Netz kursiert,
  vollständig zerstört!');
  db_query('INSERT INTO logs SET type=\'worm_destroypc\', usr_id=\''.mysql_escape_string($victim['id']).'\', payload=\'destroy pc '.$vpc['id'].'\';');
  break;

case 4: // PC von User aus dem oberen Teil der Rangliste zerstören
  $victim=db_query('SELECT * FROM users WHERE rank<=20 ORDER BY RAND() LIMIT 1;');
  if(!$victim) continue;
  $victim=mysql_fetch_assoc($victim);
  if((int)$victim['id']==0) continue;
  #echo '<br>id='.$victim['id'];
  $vpc=@mysql_fetch_assoc(db_query('SELECT id,ip,name FROM pcs WHERE owner='.$victim['id'].' ORDER BY RAND() LIMIT 1;'));
  $blocked=time() + 2 * 60 * 60;
  db_query('UPDATE pcs SET cpu=\'1\', ram=\'1\', lan=\'1\', mm=\'1\', bb=\'1\', ads=\'0\', dialer=\'0\', auctions=\'0\', bankhack=\'0\', fw=\'0\', mk=\'0\', av=\'0\', ids=\'0\', ips=\'0\', rh=\'0\', trojan=\'0\', sdk=\'0\' WHERE id='.$vpc['id'].';');
  addsysmsg($victim['id'], 'Dein PC 10.47.'.$vpc['ip'].' ('.$vpc['name'].') wurde durch einen b&ouml;sartigen Wurm, der im Moment im Netz kursiert,
  vollständig zerstört!');
  db_query('INSERT INTO logs SET type=\'worm_destroypc\', usr_id=\''.mysql_escape_string($victim['id']).'\', payload=\'destroy pc '.$vpc['id'].'\';');
  break;

}


?>
