<?php
if ( !defined('IN_HTN') )
{
  die('Hacking attempt');
}

$get = new get();
$gres = new gres();
$points = new points();

$starttime=microtime();

ignore_user_abort(0);
set_time_limit(1200);

$get->put_file('data/calc-running.dat','yes');
$get->put_file('data/calc-time.dat',time()+UPDATE_INTERVAL);
$get->put_file('data/calc-stat.dat','gerade angefangen');

$no_ranking_cluster_dat = $no_ranking_cluster;
$no_rankings_user_dat = $no_ranking_users;

#server_update_points(2);
$points->server_update_points(1);

unlink('data/calc-running.dat'); // Datei löschen
unlink('data/calc-stat.dat');

echo '<br /><br /><br />ZEIT: '.$gres->calc_time($starttime);

?>
