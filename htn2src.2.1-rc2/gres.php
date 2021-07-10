<?php

if ( !defined('IN_HTN') )
{
  die('Hacking attempt');
}

$_query_cnt = 0;

define('LF', "\n");

if ( file_exists('data/work.txt')== true || file_exists('data/mysql-backup.txt')==true)
{
  $STYLESHEET = 'crystal';
  include_once('layout.php');
  createlayout_top('HackTheNet - Serverarbeiten', true);
  echo '
  <div class="content" id="work">
  <h2>Serverarbeiten</h2>
  <div class="info">
  <h3>Information</h3>
  <p>Im Moment wird am Server gearbeitet.<br />
  Bitte probiere es doch später noch einmal.<br />
  Du kannst auch so lange dem <a href="http://forum.hackthenet.org/">Forum</a> einen Besuch abstatten.</p>
  </div>
  </div>
  ';
  createlayout_bottom();
  exit();
}

include 'config.php';
$STYLESHEET = $standard_stylesheet;

if ( $db_use_this_values )
{
  $dbcon = @mysql_connect($db_host, $db_username, $db_password);
}
else
{
  $dbcon = @mysql_connect();
}

if(!$dbcon) die('Datenbankzugriff gescheitert! Bitte nochmal probieren.');

if ( file_exists('data/mysql-backup-prepare.txt')== true ) 
{
  $notif='<div class="work">
  <h3>Server-Arbeiten</h3>
  <p>Das Spiel wird für ca. eine Minute nicht zugänglich sein.</p></div>';
}
if ( file_exists('data/longwork-prepare.txt')== true ) 
{
  $notif='<div class="work">
  <h3>Server-Arbeiten</h3>
  <p>Das Spiel wird in ca. 2 Minuten für längere Zeit nicht zugänglich sein.</p></div>';
}
if(isset($_GET['ok'])) 
{
  $ok=nl2br(strip_tags($_GET['ok'],'<br /><br>'));
  $notif.='<div class="ok">
  <h3>Aktion ausgef&uuml;hrt</h3>
  <p>'.$ok.'</p></div>
  ';
}
if(isset($_GET['error'])) 
{
  $errmsg=nl2br(strip_tags($_GET['error'],'<br /><br>'));
  $notif.='<div class="error">
  <h3>Fehler</h3>
  <p>'.$errmsg.'</p></div>
  ';
}

$host=$_SERVER['HTTP_HOST'];
$localhost= (($host == 'localhost' || $host == 'htn.lh') ? true : false);
if($host == 'htnsrv.org') $localhost = false;


function dbname($srvid = -1)
{
  global $database_prefix, $database_suffix, $enable_multi_server_support, $database_name;
  if( $enable_multi_server_support )
  {
    return $database_prefix . $srvid . $database_suffix;
  }
  else
  {
    return $database_name;
  }
}

function db_select($name)
{
  $r = mysql_select_db($name);
  if(mysql_error() != '' || $r == false)
  {
    die('Fehler beim Auswaehlen der DB:<br />'.mysql_error());
  }
  return $r;
}

function db_query($q)
{
  global $_query_cnt;
  
  $r = mysql_query($q);
  $_query_cnt++;
  if(mysql_error() != '')
  {
    die('<tt>'.$q.'</tt><br />caused an error:<br />'.mysql_error());
  }
  return $r;
}

if($localhost) 
{
  setlocale(LC_TIME,'ge');
}
else 
{
  setlocale(LC_TIME,'de_DE');
}

$cpu_levels=array(0=>120, 1=>266, 2=>300, 3=>450, 4=>600, 5=>800,
6=>1000, 7=>1200, 8=>1500, 9=>1800, 10=>2000, 11=>2200, 12=>2400,
13=>2600, 14=>2800, 15=>3000, 16=>3200, 17=>3400, 18=>3600,
19=>3800, 20=>4000, 21=>4400);

$ram_levels=array(0=>16, 1=>32, 2=>64, 3=>128, 4=>256, 5=>512,
6=>1024, 7=>2048, 8=>3072, 9=>4096);

define('DPH_ADS',22,false);
define('DPH_DIALER',24,false);
define('DPH_AUCTIONS',26,false);
define('DPH_BANKHACK',32,false);

function gFormatText(&$s) 
{
  global $sid;
  # GEILE FUNKTION!!! VOLL DYNAMISCH COOL!!
  
  $dat[0][pattern]='/\\[usr\\=(.*?)\\](.*?)\\[\\/usr\\]/is';
  $dat[0][replace]='<a href="user.php?a=info&amp;sid='.$sid.'&amp;user=\\1">\\2</a>';
  $dat[1][pattern]='/\\[cluster\\=(.*?)\\](.*?)\\[\\/cluster\\]/is';
  $dat[1][replace]='<a href="cluster.php?a=info&amp;sid='.$sid.'&amp;cluster=\\1">\\2</a>';
  
  foreach($dat as $item):
  $s=preg_replace($item[pattern],$item[replace],$s);
  endforeach;
  
  $s=str_replace('%sid%',$sid,$s);
  
  return $s;
}

/*function write_session_data() 
{
  global $usrid,$pcid,$sid,$server;
  file_put('data/login/'.$sid.'.txt', $server."\x0b".$usrid."\x0b".$pcid);
}*/

function is_noranKINGuser($ix)
{
  global $no_ranking_users;
  #echo $no_ranking_users;
  return (in_array($ix, explode(',', $no_ranking_users)));
}

function rem_esc_chars($s) 
{
  return preg_replace('(\\\\|\\\')','',$s);
}

function file_get($filename) 
{
  //----------- File Get -----------------
  if(function_exists('file_get_contents')) return file_get_contents($filename);
  $file = @fopen($filename, 'r');
  if ($file) 
  {
    if ($fsize = @filesize($filename)) 
    {
      $fdata = fread($file, $fsize);
    }
    else 
    {
      while (!feof($file)) $fdata .= fread($file, 1024);
    }
    fclose($file);
  }
  return $fdata;
}

function file_put($filename,$strContent) 
{
  //----------- File Put -----------------
  if(function_exists('file_put_contents')) return file_put_contents($filename,$strContent);
  
  $fpms=(int)@fileperms($filename); if($fpms<666 && $fmps!=0) @chmod($filename,0666);
  if(strlen($strContent)<1) 
  {
    @unlink($filename); return true; 
  }
  $file = @fopen($filename, 'w+');
  if($file) 
  {
    fwrite($file,$strContent);
    fclose($file);
    $r=true;
  }
  else $r=false;
  $fpms=(int)@fileperms($filename); if($fpms<666 && $fmps!=0) @chmod($filename,0666);
  return $r;
}

if(!function_exists('html_entity_decode')) 
{
  // For users prior to PHP 4.3.0 do this:
  function html_entity_decode($string)
  {
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return strtr($string, $trans_tbl);
  }
}

/**
* Generiert einen zufällig zusammengesetzen String
* @return string Zufälliiger String
* @param $chars Länge des Strings
**/
function random_string($chars = 6)
{
  
  $s = uniqid(mt_rand(), true);
  $s = sha1($s);
  $s = substr($s, mt_rand(0, 40-$chars), $chars);
  
  return $s;
}

function randomchar() 
{
  $s=random_string(16);
  mt_srand((double)microtime()*1000000);
  return substr($s,mt_rand(0,strlen($s)),1);
}

function no_($code=-1) 
{
  //------------------------- NO!!! -------------------------------
  simple_message('Ung&uuml;ltige Anforderung!<br /><span style="font-size:10pt;">(Code: '.$code.')</font>');
}

function NiceTime($ts=0) 
{
  //------------------------- NiceTime -------------------------------
  if($ts==0) $ts=time();
  $r=nicetime_getstr($ts,'%A, %d. %B, um ');
  return strftime($r.'%H:%M Uhr',$ts);
}

function NiceTime_GetStr($ts,$default) 
{
  $heute=strftime('%x');
  $gestern=strftime('%x',time()-86400); $vorgestern=strftime('%x',time()-2*86400);
  $morgen=strftime('%x',time()+86400); $uebermorgen=strftime('%x',time()+2*86400);
  switch(strftime('%x', $ts)) 
  {
    case $heute: $r=''; break;
    case $gestern: $r='gestern '; break;
    case $vorgestern: $r='vorgestern '; break;
    case $morgen: $r='morgen '; break;
    case $uebermorgen: $r='&uuml;bermorgen '; break;
    default: $r=$default;
  }
  return $r;
}

function NiceTime2($ts=0,$seconds=false,$zw=' ',$end='') 
{
  //------------------------- NiceTime2 -------------------------------
  if($ts==0) $ts=time();
  $r=NiceTime_GetStr($ts,'%d.%m.');
  if($seconds==false) $x=$r.$zw.'%H:%M'.$end; else $x=$r.$zw.'%H:%M:%S'.$end;
  return strftime($x,$ts);
}

function NiceTime3($ts=0,$zw=' um ') 
{
  //------------------------- NiceTime3 -------------------------------
  if($ts==0) $ts=time();
  $r=NiceTime_GetStr($ts,'%a, %d. %b.');
  return strftime($r.$zw.'%X Uhr',$ts);
}

function NiceTime4($ts=0,$zw=' um ') 
{
  if($ts==0) $ts=time();
  return strftime('%d.%m.'.$zw.'%H:%M',$ts);
}

function calc_time($start,$end=0,$precision=4) 
{
  //------------------------- Calc Time -------------------------------
  if($end==0) $end=microtime();
  list($startmsec,$startsec)=explode(' ',$start);
  list($endmsec,$endsec)=explode(' ',$end);
  $runtime=($endsec+$endmsec)-($startsec+$startmsec);
  if($precision>0) return round($runtime,$precision);
  else return $runtime;
}

function getfilecount($verz) 
{
  //------------------------- Get File Count -------------------------------
  $cnt=0;
  $h=opendir($verz);
  while($fn=readdir($h)) 
  {
    if(is_file($verz.'/'.$fn)) $cnt++;
  }
  closedir($h);
  return $cnt;
}

function joinex($a,$trenn,$unique=true,$rtrim=false) 
{
  //------------------------- JOIN EX -------------------------------
  $str='';
  if($unique===true) $a=array_unique($a);
  foreach($a as $item) 
  {
    if(trim($item)!='') $str.=trim($item).$trenn;
  }
  if(!$rtrim) return ltrim($str,$trenn); else return trim($str,$trenn);
}

function GetCluster($val,$by='id') 
{
  $r=db_query('SELECT * FROM clusters WHERE '.mysql_escape_string($by).' LIKE \''.mysql_escape_string($val).'\' LIMIT 1');
  return ((int)@mysql_num_rows($r)>0 ? mysql_fetch_assoc($r) : false);
}

function GetUser($val,$by='id') 
{
  $val = str_replace('_','\_',$val);
  $val = str_replace('%','\%',$val);
  $r=db_query('SELECT * FROM users WHERE '.mysql_escape_string($by).' LIKE \''.mysql_escape_string($val).'\' LIMIT 1');
  return ((int)@mysql_num_rows($r)>0 ? mysql_fetch_assoc($r) : false);
}

function GetPC($id,$by='id')
{
  $r=db_query('SELECT * FROM pcs WHERE '.mysql_escape_string($by).' LIKE \''.mysql_escape_string($id).'\' LIMIT 1');
  return ((int)@mysql_num_rows($r)>0 ? mysql_fetch_assoc($r) : false);
}

function GetCountry($type,$val) 
{
  //---------- Get Country -----------------
  include('data/static/country_data.inc.php');
  reset($countrys);
  if($type != 'id')
  {
    while(list($bez,$item)=each($countrys)):
    if($item[$type]==$val) 
    {
      return $item; break; 
    }
    endwhile;
  }
  else
  {
    if(isset($countrys[$val])) return $countrys[$val];
  }
  return false;
}

function SubnetFromIP($ip) 
{
  # SUBNET FROM IP - DEPRECATED!!
  return (int)substr($ip,0,strpos($ip,'.'));
}

function simple_message($msg,$type='warning') 
{
  include_once('layout.php');
  switch($type) 
  {
    case 'success': 
    {
      $id='ok'; $c='Erfolg'; 
    }
    break;
    case 'error': 
    {
      $id='error'; $c='Fehler'; 
    }
    break;
    case 'tip': 
    {
      $id='tip'; $c='Tipp'; 
    }
    break;
    case 'info': 
    {
      $id='info'; $c='Information'; 
    }
    break;
    default: 
    {
      $id='important'; $c='Hinweis'; 
    }
  }
  createlayout_top('HackTheNet - Hinweis');
  echo '<div class="content">';
  echo '<h2>HackTheNet</h2>';
  echo '<br /><br />';
  echo '<div class="'.$id.'"><h3>'.$c.'</h3><p>'.$msg.'</p></div>';
  createlayout_bottom();
}

#function xpoint($v) 
{
  return 3*pow((float)1.43047659,(float)$v);  
}
function xpoint($v) 
{
  return 3*pow((float)1.408659,(float)$v);  
}

function getPCPoints($pc,$mode='byid') 
{
  //---------- Get PC Points -----------------
  global $STYLESHEET,  $DATADIR;
  global $cpu_levels, $ram_levels;
  if($mode=='byid')
  $pcdat=@mysql_fetch_assoc(db_query('SELECT * FROM pcs WHERE id=\''.mysql_escape_string($pc).'\''));
  else
  $pcdat=$pc;
  $pcpoints=0;
  $pcpoints+=$pcdat['cpu']*10;
  $pcpoints+=$pcdat['ram']*10;
  $pcpoints+=xpoint($pcdat['mm']);
  $pcpoints+=xpoint($pcdat['bb']);
  $pcpoints+=xpoint($pcdat['lan']);
  $pcpoints+=xpoint($pcdat['fw']);
  $pcpoints+=xpoint($pcdat['mk']);
  $pcpoints+=xpoint($pcdat['av']);
  $pcpoints+=xpoint($pcdat['sdk']);
  $pcpoints+=xpoint($pcdat['ips']);
  $pcpoints+=xpoint($pcdat['ids']);
  $pcpoints=round($pcpoints,0);
  $pcpoints-=31;
  return $pcpoints;
}


function GetIP() 
{
  //------------------------- Get IP -------------------------------
  if($_SERVER['HTTP_X_FORWARDED_FOR']) 
  {
    if($_SERVER['HTTP_CLIENT_IP']) 
    {
      $proxy=$_SERVER['HTTP_CLIENT_IP']; 
    }
    else 
    {
      $proxy=$_SERVER['REMOTE_ADDR']; 
    }
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else 
  {
    if($_SERVER['HTTP_CLIENT_IP']) 
    {
      $ip=$_SERVER['HTTP_CLIENT_IP']; 
    }
    else 
    {
      $ip=$_SERVER['REMOTE_ADDR']; 
    }
  }
  if($ip != $_SERVER['REMOTE_ADDR']) $ip = $_SERVER['REMOTE_ADDR'] . ' (' . $ip . ')';
  $r['ip']=$ip; $r['proxy']=$proxy;
  return $r;
}

function addpc($country,$usrid,$byid=true) 
{
  //--------- ADD PC ------------
  global $STYLESHEET,  $DATADIR, $server;
  
  if($byid==true) 
  {
    $c=GetCountry('id',$country);
    $subnet=$c['subnet'];
  }
  else 
  {
    $c=getcountry('subnet',$country);
    $subnet=$country;
  }
  
  $r=db_query('SELECT id FROM pcs WHERE ip LIKE \''.mysql_escape_string($subnet).'.%\'');
  $cnt=mysql_num_rows($r);
  $xip=$cnt+1;
  
  if($xip<=254) 
  {
    $ip=$subnet.'.'.$xip;
    
    $ts=time();
    
    db_query('INSERT INTO pcs(id, name,     ip,    owner,  cpu, ram, lan, mm, bb, ads, dialer, auctions, bankhack, fw, mk, av, ids, ips, rh, sdk, trojan, credits, lmupd, country, points, la, di, dt, lrh) VALUES('
    .'0, \'NoName\', \''.mysql_escape_string($ip).'\', \''.$usrid.'\', 0, 0,   1,   1,  1,  1,   0,      0,        0,        0,  0,  0,  0,   0,   0,  0,   0,      13130,    \''.$ts.'\', \''.mysql_escape_string($c['id']).'\',0,      \'\',  \'\', \'\', \'\')');
    
    return mysql_insert_id();
    
  }
  else 
  {
    return false;
  }
}

function getnextramlevel($stage) 
{
  //----------------- Next RAM Level -----------------------
  global $ram_levels;
  if($stage>=$ram_levels[0]) $stage=array_search($stage,$ram_levels);
  return $stage+1;
}

function getnextcpulevel($stage) 
{
  //----------------- Next CPU Level -----------------------
  global $cpu_levels;
  if($stage>=$cpu_levels[0]) $stage=array_search($stage,$cpu_levels);
  return $stage+1;
}

function getlastcpulevel($stage) 
{
  //----------------- Last CPU Level -----------------------
  global $cpu_levels;
  if($stage>=$cpu_levels[0]) $stage=array_search($stage,$cpu_levels);
  if($stage>0) 
  {
    return $stage-1;
  }
  else return 0;
}

function cleardir($verz) 
{
  // ----------------- CLEAR DIR ----------------
  $h=@opendir($verz);
  while($fn=@readdir($h)) 
  {
    if(@is_file($verz.'/'.$fn)) 
    {
      @unlink($verz.'/'.$fn);
    }
  }
  @closedir($h);
}

$reload_lock_time=120; // in Minuten
$lock_ip=1; // 1=an 0=aus

$ipfn='data/reloadsperre_IPs.dat';


function delete_account($usrid) 
{
  // ------------------ DELETE ACCOUNT ---------------
  $usr=@getuser($usrid);
  if($usr!==false) 
  {
    $c=$usr[cluster];
    if($c!='') 
    {
      $c=@mysql_num_rows(@db_query('SELECT * FROM users WHERE cluster='.mysql_escape_string($c)));
      if($c < 2) 
      {
        deletecluster($c,true);
      }
      else 
      {
        $r=db_query('SELECT id FROM users WHERE cluster='.$usr['cluster'].' AND clusterstat=1000;');
        $admins=@mysql_num_rows($r);
        if($usr[clusterstat]==1000 && $admins<2) 
        {
          $r=db_query('SELECT * FROM users WHERE cluster='.mysql_escape_string($usr['cluster']).';');
          db_query('UPDATE users SET clusterstat=1000 WHERE id='.mysql_result($r,0,'id').';');
        }
      }
    }
    db_query('DELETE FROM mails WHERE user=\''.$usrid.'\';');
    db_query('DELETE FROM sysmsgs WHERE user=\''.$usrid.'\';');
    db_query('DELETE FROM users WHERE id=\''.$usrid.'\';');
    db_query('DELETE FROM abooks WHERE user=\''.$usrid.'\';');
    return $usr;
  }
  else return false;
}

function deletecluster($cid,$silent=false) 
{
  // ------- DELETE CLUSTER ---------
  global $sid;
  
  db_query('DELETE FROM clusters WHERE id=\''.mysql_escape_string($cid).'\' LIMIT 1');
  db_query('DELETE FROM cboards WHERE cluster=\''.mysql_escape_string($cid).'\'');
  db_query('DELETE FROM cl_reqs WHERE cluster=\''.mysql_escape_string($cid).'\'');
  db_query('UPDATE users SET clusterstat=0, cm=\'\', cluster=0 WHERE cluster=\''.mysql_escape_string($cid).'\'');
  
  if($silent===false) simple_message('Der Cluster '.$cid.' wurde gel&ouml;scht.<br /><a href="cluster.php?mode=start&sid='.$sid.'">Weiter</a>');
}

settype($rem_e_a,'array');
function rem_emptys(&$a) 
{
  // ------------------ REMOVE EMPTY ARRAY ELEMENTS ---------------
  global $rem_e_a;
  $rem_e_a=array_splice($rem_e_a,0,0);
  settype($rem_e_a,'array');
  array_walk($a,'rem_e_callback');
  $a=$rem_e_a;
  return $rem_e_a;
}

function rem_e_callback($s) 
{
  global $rem_e_a;
  $s=trim($s);
  if($s!='') array_push($rem_e_a,$s);
}

function check_email($email) 
{
  // ------------------ CHECK EMAIL ---------------
  return (eregi('^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-.]?[0-9a-zA-Z])*\\.[a-zA-Z]{2,4}$',$email)===false ? false : true);
}

function GetTableInfo($table,$db='') 
{
  global $server;
  if($db=='') $db=dbname($server);
  $sql='SHOW TABLE STATUS FROM '.mysql_escape_string($db).' LIKE \''.mysql_escape_string($table).'\';';
  #echo $sql;
  $r=mysql_db_query($db,$sql);
  #echo mysql_error();
  return mysql_fetch_assoc($r);
}

function GetOnlineUserCnt($server) 
{
  $r = mysql_db_query(dbname($server),'SELECT COUNT(id) FROM users WHERE sid_lastcall > ' . (time() - SID_ONLINE_TIMEOUT));
  $tmp = mysql_fetch_row($r);
  return $tmp[0];
}

function dereferurl($url) 
{
  return 'derefer.php?u='.urlencode($url);
}

function processupgrades(&$pc,$savepc=true) 
{
  //----------------- PROCRESS UPGRADES -------------------
  global $bucks;
  $pcid=$pc['id']; # h4ck
  
  # Upgrade-Vorgänge verarbeiten
  $r = db_query('SELECT * FROM `upgrades` WHERE `pc`=\''.$pcid.'\' AND `end`<=\''.time().'\' ORDER BY `start` ASC');
  $cnt = @mysql_num_rows($r);
  if($cnt > 0)
  {
    $maxxupgrades = 5;
    if($cnt > $maxxupgrades)
    {
      mysql_query('DELETE FROM `upgrades` WHERE `pc`=\''.$pcid.'\' ORDER BY `end` DESC LIMIT '.($cnt-$maxxupgrades).';');
    }
    $sql='UPDATE `pcs` SET ';
    $sql2='DELETE FROM `upgrades` WHERE id IN(';
    $i=0;
    while($data = mysql_fetch_assoc($r))
    {
      #print_r($data);
      $item=$data['item'];
      if(isavailb($item, $pc) == true)
      {
        $newlv = itemnextlevel($item,$pc[$item]);
        $newlv = (float)$newlv;
        $pc[$item]=$newlv;
      }
      else
      {
        $newlv = '';
        $sql2.=$data['id'];
        if($i<$cnt-1) $sql2.=', ';
      }
      if($newlv != '')
      {
        $sql.=' `'.mysql_escape_string($item).'`=\''.mysql_escape_string($newlv).'\'';
        $sql2.=$data['id'];
        if($i<$cnt-1)
        {
          $sql.=', '; $sql2.=', '; 
        }
      }
      $i++;
      if($i > $maxxupgrades) break;
    }
    $sql=trim($sql, ' ,');
    $sql2=rtrim($sql2, ' ,');
    $sql2.=');';
    if(strlen($sql) > strlen('UPDATE `pcs` SET '))
    {
      $sql=$sql.' WHERE `id`=\''.mysql_escape_string($pc['id']).'\' LIMIT 1;';
      if($savepc && strlen($sql) > strlen('UPDATE `pcs` SET  WHERE `id`=\'1\' LIMIT 1;')) db_query($sql);
    }
    if(strlen($sql2) > strlen('DELETE FROM `upgrades` WHERE id IN();')) db_query($sql2);
  }
  
  # Geld updaten:
  if($pc['lmupd']+60<=time())
  {
    $plus=(int)round(get_gdph($pc)*((time()-$pc['lmupd'])/3600),0);
    $pc['credits']+=$plus;
    $max=getmaxbb($pc);
    if($pc['credits']>$max) 
    {
      $c=getcluster($usr['cluster']);
      if($c!==false && $c['id']!=19) 
      {
        $credits=$c['money']+($pc['credits']-$max);
        db_query('UPDATE clusters SET money='.mysql_escape_string($credits).' WHERE id=\''.mysql_escape_string($usr['cluster']).'\'');
      }
      $pc['credits']=$max;
    }
    db_query('UPDATE pcs SET credits=\''.mysql_escape_string($pc['credits']).'\', lmupd=\''.time().'\' WHERE id=\''.$pcid.'\'');
  }
  $bucks=number_format($pc['credits'],0,',','.');
}


function itemmaxval($id) 
{
  //-------------------- ITEM MAX VALUE ------------------------
  global $cpu_levels, $ram_levels, $GAME_MODE;
  switch($id) 
  {
    case 'cpu': return count($cpu_levels)-1; break;
    case 'ram': return count($ram_levels)-1; break;
    case 'sdk': return 5; break;
    case 'trojan': return 5; break;
    case 'rh': return 5; break;
    case 'mm': return 10; break;
    case 'mk': return 12; break;
    default:
      return ($GAME_MODE == '2.1' ? 1000 : 10);
    break;
  }
}

function itemnextlevel($id,$curlevel) 
{
  //------------------ ITEM NEXT LEVEL -----------------------
  if($id == 'cpu') 
  {
    return getnextcpulevel($curlevel);
  }
  elseif($id == 'ram') 
  {
    return getnextramlevel($curlevel);
  }
  else 
  {
    if($curlevel<1) $curlevel=0.5;
    $curlevel+=0.5;
    return $curlevel;
  }
}
function formatitemlevel($id,$val) 
{
  //--------------------- FORMAT ITEM LEVEL ----------------------
  global $cpu_levels,$ram_levels;
  if($id=='ram') $val=$ram_levels[$val];
  elseif($id=='cpu') $val=$cpu_levels[$val];
  elseif((float)$val==0) $val='0.0';
  elseif(strlen((string)$val)==1 || $val==10) $val=$val.'.0';
  if($id=='cpu') $sval=$val.' Mhz'; elseif($id=='ram') $sval=$val.' MB RAM'; else $sval='v '.$val;
  return $sval;
}

function calc_mph($level,$factor) 
{
  //---------- Calc Money per Hour -----------------
  return floor(pow($factor,2)*$level/20);
}

function get_gdph($_pc='') 
{
  //---------- Get Total Money per Hour -----------------
  global $STYLESHEET,  $DATADIR, $pc;
  if($_pc=='') $_pc=$pc;
  return calc_mph($_pc[ads],DPH_ADS)+calc_mph($_pc[dialer],DPH_DIALER)+
  calc_mph($_pc[auctions],DPH_AUCTIONS)+calc_mph($_pc[bankhack],DPH_BANKHACK);
}

function getmaxbb($_pc='') 
{
  //---------- Get Max BucksBunker -----------------
  global $STYLESHEET,  $DATADIR, $pc;
  if($_pc=='') $_pc=$pc;
  $max=floor((float)$_pc[bb]*13130);
  return $max;
}

function isavailb($id,$pc) 
{
  // ------------------------- Is Available (build) -------------------------------
  global $cpu_levels,$ram_levels;
  $b=false;
  /*
  $cpu_levels=array(0=>120, 1=>266, 2=>300, 3=>450, 4=>600, 5=>800,
  6=>1000, 7=>1200, 8=>1500, 9=>1800, 10=>2000, 11=>2200, 12=>2400,
  13=>2600, 14=>2800, 15=>3000, 16=>3200, 17=>3400, 18=>3600,
  19=>3800, 20=>4000, 21=>4400);
  
  $ram_levels=array(0=>16, 1=>32, 2=>64, 3=>128, 4=>256, 5=>512,
  6=>1024, 7=>2048, 8=>3072, 9=>4096);
  */
  switch($id) 
  {
    case 'cpu': $b=($pc[$id] < itemmaxval($id)); break;
    case 'ram': $b=($pc[$id] < itemmaxval($id)); break;
    case 'mm': $b=($pc[$id] < itemmaxval($id)); break;
    case 'bb': $b=($pc[$id] < itemmaxval($id)); break;
    case 'lan': $b=($pc[$id] < itemmaxval($id)); break;
    case 'fw': $b=($pc['cpu']>=6 && $pc['ram']>=2 && $pc[$id]<itemmaxval($id)); break;
    case 'mk': $b=($pc['cpu']>=12 && $pc['sdk']>=3 && $pc[$id]<itemmaxval($id)); break;
    case 'av': $b=($pc['cpu']>=10 && $pc['ram']>=3 && $pc[$id]<itemmaxval($id)); break;
    case 'sdk': $b=($pc['cpu']>=8 && $pc['ram']>=2 && $pc[$id]<itemmaxval($id)); break;
    case 'ips': $b=($pc['cpu']>=8 && $pc['sdk']>=2 && $pc[$id]<itemmaxval($id)); break;
    case 'ids': $b=($pc['cpu']>=15 && $pc['sdk']>=3 && $pc[$id]<itemmaxval($id)); break;
    case 'trojan': $b=($pc['mk']>=4 && $pc[$id]<itemmaxval($id) && $pc['ram']>=4); break;
    case 'rh': $b=($pc['cpu']>=19 && $pc['ram']>=8 && $pc['sdk']>=5 && $pc['mk']>=12 && $pc[$id]<itemmaxval($id)); break;
    default: $b=2; break;
  }
  return $b;
}

function isavailh($id,$pc) 
{
  //------------------------- Is Available (have)-------------------------------
  global $cpu_levels,$ram_levels;
  $b=false;
  switch($id) 
  {
    case 'cpu': $b=true; break;
    case 'ram': $b=true; break;
    case 'mm': $b=true; break;
    case 'bb': $b=true; break;
    case 'lan': $b=true; break;
    case 'fw': $b=((float)$pc[$id]>=1); break;
    case 'mk': $b=((float)$pc[$id]>=1); break;
    case 'av': $b=((float)$pc[$id]>=1); break;
    case 'sdk': $b=((float)$pc[$id]>=1); break;
    case 'ips': $b=((float)$pc[$id]>=1); break;
    case 'ids': $b=((float)$pc[$id]>=1); break;
    case 'trojan': $b=((float)$pc[$id]>=1); break;
    case 'rh': $b=((float)$pc[$id]>=1); break;
    
    case 'scan': $b=($pc['mk']>=2); break;
    case 'smash': $b=($pc['mk']>=6); break;
    case 'block': $b=($pc['mk']>=8 && $pc['ram']>=9); break;
    
    case 'da': $b=($pc['lan']>=2 && $pc['sdk']>=4 && $pc['mk']>=7 && $pc['ram']>=8); break;
    default: $b=2; break;
  }
  return $b;
}

/** string safeentities(text)
* ----------------------------
* Wandelt Sonderzeichen in Entitäten um vermeidet jedoch mehrfache Entschärfungen wie wie &amp;amp;auml;
* aus Marcels lib_string_tools
* Parameter:
* string $text : Umzuwandelnder Text
**/
function safeentities($text) 
{
  return preg_replace('/&(amp;)+([\\w#]+);/i', '&\\2;', htmlentities($text));
}

/**
* Formatiert eine Zahl
* @return string
* @param float  $num Zahl
* @param int  $precision Nachkommastellen
**/
function numfmt($num, $precision=1) 
{
  return number_format($num, $precision, ',', '.');
}

function human2int($input)
{
  $scale=1;
  $notations=array(
  'k'=>1000,
  'm'=>1000*1000,
  'g'=>1000*1000*1000,
  't'=>1000*1000*1000*1000,
  // keys in lowercase!
  );
  $input=strtolower(trim($input));
  $input=preg_replace("/[^0-9,".implode('', array_keys($notations))."]+/", '', $input);
  foreach ($notations as $key=>$factor)
  {
    while (substr($input,-1,1)==$key)
    {
      $scale*=$factor;
      $input=substr($input,0,-1);
    }
  }
  $input=preg_replace("/[^0-9,]+/",'',$input);
  $input=str_replace(',','.',$input); // float
  $output=round(floatval($input)*$scale);
  return $output;
} // human2int()

?>