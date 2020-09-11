<?php
include ('config.php');
if (!defined('IN_HTN'))
{
    die('Hacking attempt');
}

$_query_cnt = 0;

define('LF', "\n");

$dbc = new dbc();
$layout = new layout();

if (file_exists('data/work.txt') == true || file_exists('data/mysql-backup.txt') == true)
{
    $STYLESHEET = 'crystal';
    $layout->createlayout_top('HackTheNet - Serverarbeiten', true);
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
    $layout->createlayout_bottom();
    exit();
}

$STYLESHEET = $standard_stylesheet;

if ($db_use_this_values)
{
    $dbcon = @mysql_connect($db_host, $db_username, $db_password);
} else
{
    $dbcon = @mysql_connect();
}

if (!$dbcon)
    die('Datenbankzugriff gescheitert! Bitte nochmal probieren.');

if (file_exists('data/mysql-backup-prepare.txt') == true)
{
    $notif = '<div class="work">
  <h3>Server-Arbeiten</h3>
  <p>Das Spiel wird für ca. eine Minute nicht zugänglich sein.</p></div>';
}
if (file_exists('data/longwork-prepare.txt') == true)
{
    $notif = '<div class="work">
  <h3>Server-Arbeiten</h3>
  <p>Das Spiel wird in ca. 2 Minuten für längere Zeit nicht zugänglich sein.</p></div>';
}
if (isset($_GET['ok']))
{
    $ok = nl2br(strip_tags($_GET['ok'], '<br /><br>'));
    $notif .= '<div class="ok">
  <h3>Aktion ausgef&uuml;hrt</h3>
  <p>' . $ok . '</p></div>
  ';
}
if (isset($_GET['error']))
{
    $errmsg = nl2br(strip_tags($_GET['error'], '<br /><br>'));
    $notif .= '<div class="error">
  <h3>Fehler</h3>
  <p>' . $errmsg . '</p></div>
  ';
}

$host = $_SERVER['HTTP_HOST'];
$localhost = (($host == 'localhost' || $host == 'htn.lh' || $host == '127.0.0.1') ? true : false);
if ($host == 'htnsrv.org')
    $localhost = false;


if ($localhost)
{
    setlocale(LC_TIME, 'ge');
} else
{
    setlocale(LC_TIME, 'de_DE');
}

$cpu_levels = array(0 => 120, 1 => 266, 2 => 300, 3 => 450, 4 => 600, 5 => 800,
    6 => 1000, 7 => 1200, 8 => 1500, 9 => 1800, 10 => 2000, 11 => 2200, 12 => 2400,
    13 => 2600, 14 => 2800, 15 => 3000, 16 => 3200, 17 => 3400, 18 => 3600, 19 =>
    3800, 20 => 4000, 21 => 4400);

$ram_levels = array(0 => 16, 1 => 32, 2 => 64, 3 => 128, 4 => 256, 5 => 512, 6 =>
    1024, 7 => 2048, 8 => 3072, 9 => 4096);

define('DPH_ADS', 22, false);
define('DPH_DIALER', 24, false);
define('DPH_AUCTIONS', 26, false);
define('DPH_BANKHACK', 32, false);



/*function write_session_data()
{
global $usrid,$pcid,$sid,$server;
file_put('data/login/'.$sid.'.txt', $server."\x0b".$usrid."\x0b".$pcid);
}*/



if (!function_exists('html_entity_decode'))
{
    // For users prior to PHP 4.3.0 do this:
    function html_entity_decode($string)
    {
        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);
        return strtr($string, $trans_tbl);
    }
}

#function xpoint($v)

//{
//    return 3 * pow((float)1.43047659, (float)$v);
//}


$reload_lock_time = 120; // in Minuten
$lock_ip = 1; // 1=an 0=aus

$ipfn = 'data/reloadsperre_IPs.dat';




settype($rem_e_a, 'array');
 // human2int()


?>