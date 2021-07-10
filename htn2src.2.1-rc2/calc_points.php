<?php

if ( !defined('IN_HTN') )
{
  die('Hacking attempt');
}

$starttime=microtime();

ignore_user_abort(0);
set_time_limit(1200);

file_put('data/calc-running.dat','yes');
file_put('data/calc-time.dat',time()+UPDATE_INTERVAL);
file_put('data/calc-stat.dat','gerade angefangen');

$no_ranking_cluster_dat = $no_ranking_cluster;
$no_rankings_user_dat = $no_ranking_users;

function server_update_points($server)
{

global $no_ranking_cluster_dat, $no_rankings_user_dat;
global $no_ranking_cluster, $no_ranking_users;

$no_ranking_cluster = $no_ranking_cluster_dat['server' . $server];
$no_ranking_users = $no_rankings_user_dat['server' . $server];

db_select(dbname($server));
#mysql_query('UPDATE pcs SET credits=1000 WHERE credits<0');

if(!is_writable('data/calc-time.dat')) die('Bitte dem Data-Ordner und allen Dateien und Unterordner darin die Linux-Rechte-Flags 0777 geben.');

file_put('data/calc-stat.dat','Berechnung von Server '.$server.' ...');

ignore_user_abort(0);
$clusters=array();

// Alle Datens&auml;tze zurcksetzen,
// damit es bei herrenlosen PCs keine falschen Anzeigen gibt:
#db_query('UPDATE pcs SET owner_name=\'\', owner_points=0, owner_cluster=0, owner_cluster_code=\'\';');

$current=0;
$u_result=db_query('SELECT * FROM users WHERE calcrank=\'yes\' ORDER BY id ASC;');
$total=mysql_num_rows($u_result);
while($user=mysql_fetch_assoc($u_result))
{
  $current++;
  $upoints=0;
  if($current % 100 == 0) file_put('data/calc-stat.dat','Berechnung von Server '.$server.' ... '.$current.' / '.$total);
  
  $pc_result=db_query('SELECT * FROM pcs WHERE owner=\''.$user['id'].'\';');
  #file_put('data/calc-debug.dat','User: '.$user['id']);
  $pc_cnt=mysql_num_rows($pc_result);
  #file_put('data/calc-debug.dat',$user['id'].' while start\n');
  while($pc=mysql_fetch_assoc($pc_result))
  {
    processupgrades($pc);
    #file_put('data/calc-debug.dat',$user['id'].' prozupgrades done\n');
    $pcpoints=getpcpoints($pc,'bydata');
    db_query('UPDATE pcs SET points=\''.$pcpoints.'\' WHERE id=\''.$pc['id'].'\';');
    $upoints+=$pcpoints;
    #file_put('data/calc-debug.dat',$user['id'].' rest auch\n');
  }
  #file_put('data/calc-debug.dat',$user['id'].' while weg');

  #reset($pcs);
  #foreach($pcs As $pcid):
    #$sql='UPDATE pcs SET owner_points=$upoints,owner_name=\''.mysql_escape_string($user['name']).'\' ';
    #$cluster=getcluster($user[cluster]);
    #if($cluster!==false) {
    #  $sql.=',owner_cluster='.mysql_escape_string($cluster['id']).', owner_cluster_code=\''.mysql_escape_string($cluster['code']).'\' ';
    #}
    #$sql.='WHERE id=\''.$pcid.'\'';
    #db_query($sql);
  #endforeach;

  $c=$user[cluster];
  if($c!='' && $c!=0) {
    #$r=db_query('SELECT id FROM clusters WHERE id=\''.mysql_escape_string($c).'\' LIMIT 1');
    #if(mysql_num_rows($r)>0) {
      $clusters['c'.$c]['points']+=$upoints;
      $clusters['c'.$c]['members']+=1;
      $clusters['c'.$c]['pcs']+=$pc_cnt;
    #}
  }
    
  if(is_noranKINGuser($user['id'])==false)
  {
    $rank[$user['id'].';'.$user['name'].';'.$user['cluster']]=$upoints;
  }
  else
  {
    db_query('UPDATE users SET points=\''.$upoints.'\',rank=\'0\' WHERE id=\''.$user['id'].'\';');
  }
}

#$pcinfo=gettableinfo('pcs',dbname($server));
#file_put('data/_server'.$server.'/pc-count.dat', $pcinfo[Rows]);
file_put('data/_server'.$server.'/user-count.dat', mysql_num_rows($u_result));

ignore_user_abort(0);
file_put('data/calc-stat.dat','Berechnung von Server '.$server.' ... Berechnung abgeschlossen: Schreiben in DB ...');

arsort($rank);
db_query('TRUNCATE TABLE rank_users'); # Tabelle leeren
#$platz=0;
while(list($dat,$points)=each($rank))
{
  #$platz++;
  $dat=explode(';', $dat);
  $dat[2]=(int)$dat[2];
  db_query('INSERT INTO rank_users VALUES(0, \''.$dat[0].'\', \''.$dat[1].'\', '.$points.', \''.$dat[2].'\');');
  db_query('UPDATE users SET points='.$points.', rank='.mysql_insert_id().' WHERE id='.$dat[0].' LIMIT 1;');
}

#file_put('data/_server'.$server.'/rank-user-count.dat', count($rank));

db_query('TRUNCATE TABLE rank_clusters;'); # Tabelle leeren

unset($b); settype($b,'array');
while(list($bez,$val)=each($clusters))
{
  $b[$bez]=$clusters[$bez]['points'];
}

arsort($b);
unset($c); settype($c,'array');
while(list($bez,$val)=each($b))
{
  $c[$bez]['points']=$val;
  $c[$bez]['pcs']=$clusters[$bez]['pcs'];
  $c[$bez]['members']=$clusters[$bez]['members'];
}

while(list($bez,$dat)=each($c)) {
  $bez=substr($bez,1);
  $av_p=round($dat['points']/$dat['members'],2);
  $av_pcs=round($dat['pcs']/$dat['members'],2);
  
  // SUCCESS RATE CALCULATION START
    $cluster=getcluster($bez);

    $total=$cluster['srate_total_cnt'];
    $scnt=$cluster['srate_success_cnt'];
    $ncnt=$cluster['srate_noticed_cnt'];
    if($total>0) {

        $psucceeded=$scnt * 100 / $total;
        $pnoticed=$ncnt * 100 / $total;

        // Erfolg ist gut und z�hlt 75%
        // Bemerkt ist schlecht (deshalb 100-$pnoticed) und z�hlt 25%
        $srate=$psucceeded * 0.75 + (100 - $pnoticed) * 0.25;
    } else $srate=0;
  // SUCCESS RATE CALCULATION END
  
  if($bez!=$no_ranking_cluster) db_query('INSERT INTO rank_clusters VALUES(0,\''.mysql_escape_string($bez).'\',\''.$dat['members'].'\',\''.$dat['points'].'\',\''.$av_p.'\',\''.$dat['pcs'].'\',\''.$av_pcs.'\',\''.$srate.'\');');
  db_query('UPDATE clusters SET points=\''.$dat['points'].'\',rank=\''.mysql_insert_id().'\' WHERE id=\''.mysql_escape_string($bez).'\' LIMIT 1;');
}

file_put('data/calc-stat.dat','Gleich fertig!!!');

cleardir('data/_server'.$server.'/usrimgs');

}

#server_update_points(2);
server_update_points(1);

unlink('data/calc-running.dat');
unlink('data/calc-stat.dat');

echo '<br /><br /><br />ZEIT: '.calc_time($starttime);

?>
