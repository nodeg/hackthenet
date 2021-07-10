<?php

if ( !defined('IN_HTN') )
{
  die('Hacking attempt');
}

$action=mt_rand(1,3);

switch($action) {

case 1: // Geld aus Clusterkasse eines Clusters mit mehr als einer Million Credits klauen
  $victim=db_query('SELECT * FROM clusters WHERE money>1000000 ORDER BY RAND() LIMIT 1;');
  if(!$victim) continue;
  $victim=mysql_fetch_assoc($victim);
  if($victim['code']=='') continue;
  $creds=(int)$victim['money'];
  $creds=floor($creds/1.5);
  $stolen=($victim['money']-$creds);
  $ev=nicetime4().' Ein gef&auml;hrlicher Internet-Wurm hat '.$stolen.' Credits aus der Clusterkasse geklaut!'."\n";
  $victim['events']=$ev.$victim['events'];
  db_query('UPDATE `clusters` SET `money`='.mysql_escape_string($creds).', `events`=\''.mysql_escape_string($victim['events']).'\' WHERE `id`=\''.mysql_escape_string($victim['id']).'\';');
  echo mysql_error();
  db_query('INSERT INTO logs SET type=\'worm_clmoney\', usr_id=\''.mysql_escape_string($victim['id']).'\', payload=\'stole '.mysql_escape_string($stolen).' credits from cluster '.mysql_escape_string($victim['id']).'\';');
  break;

case 2: // PC von User aus dem oberen Teil der Rangliste blockieren
  $victim=db_query('SELECT * FROM users WHERE rank<=50 ORDER BY RAND() LIMIT 1;');
  if(!$victim) continue;
  $victim=mysql_fetch_assoc($victim);
  if((int)$victim['id']==0) continue;
  #echo '<br>id='.$victim['id'];
  $vpc=@mysql_fetch_assoc(db_query('SELECT id,ip,name FROM pcs WHERE owner='.$victim['id'].' ORDER BY RAND() LIMIT 1;'));
  $blocked=time() + 6 * 60 * 60;
  db_query('UPDATE pcs SET blocked=\''.mysql_escape_string($blocked).'\' WHERE id='.$vpc['id'].';');
  addsysmsg($victim['id'], 'Dein PC 10.47.'.$vpc['ip'].' ('.$vpc['name'].') wurde durch einen b&ouml;sartigen Wurm, der im Moment im Netz kursiert,
  bis '.nicetime($blocked).' blockiert!');
  db_query('INSERT INTO logs SET type=\'worm_blockpc\', usr_id=\''.mysql_escape_string($victim['id']).'\', payload=\'blocked pc '.$vpc['id'].'\';');
  break;

case 3: // PC von aktivem User aus dem Mittelfeld der Rangliste Credits schenken
  $ts=time()-24*60*60;
  $victim=db_query('SELECT * FROM users WHERE (rank>50 AND login_time>'.mysql_escape_string($ts).') ORDER BY RAND() LIMIT 1;');
  echo mysql_error();
  if(!$victim) continue;
  $victim=mysql_fetch_assoc($victim);
  if((int)$victim['id']==0) continue;
  #echo '<br>id='.$victim['id'];
  $vpc=@mysql_fetch_assoc(db_query('SELECT id,ip,name,credits FROM pcs WHERE owner='.$victim['id'].' ORDER BY RAND() LIMIT 1;'));
  $plus=mt_rand(2000, 10000);
  $creds=$vpc['credits']+$plus;
  db_query('UPDATE pcs SET credits=\''.mysql_escape_string($creds).'\' WHERE id='.mysql_escape_string($vpc['id']).';');
  addsysmsg($victim['id'], 'Auf deinen PC 10.47.'.$vpc['ip'].' ('.$vpc['name'].') wurde durch einen Wurm, der im Moment im Netz kursiert,
  die Summe von '.$plus.' Credits &uuml;berwiesen!');
  db_query('INSERT INTO logs SET type=\'worm_pcsendmoney\', usr_id=\''.mysql_escape_string($victim['id']).'\', payload=\'gave '.mysql_escape_string($plus).' credits to pc '.mysql_escape_string($vpc['id']).'\';');
  break;

}


?>
