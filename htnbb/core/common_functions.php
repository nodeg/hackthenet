<?php

/**
 * includes/common_functions.php globale Funktionen.
 * stellt global benötigte Funktionen zur Verfügung.
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.0.5
 **/

if( !defined('IN_BB') ) die('Hacking attempt!');
if( !defined('CONFIG_LOADED') ) die('config file not loaded');


class mysql_crap
{
  public function __get($prop)
  {
    switch($prop)
    {
    case 'insert_id':
      return mysql_insert_id();
    case 'error':
      return mysql_error();
    case 'errno':
      return mysql_errno();
    default:
      trigger_error('unknown property '.__CLASS__.'::'.$prop);
    }
  }
  
  public function query($sql)
  {
    return new mysql_crap_result($sql);
  }
  
  public function close()
  {
    mysql_close();
  }
  
  public function real_escape_string($str)
  {
    return mysql_real_escape_string($str);
  }
  
}

class mysql_crap_result
{
  private $handle;
  
  function __construct($query)
  {
    $this->handle = mysql_query($query);
  }
  
  public function __get($prop)
  {
    switch($prop)
    {
    case 'num_rows':
      return mysql_num_rows($this->handle);
    default:
      trigger_error('unknown property '.__CLASS__.'::'.$prop);
    }
  }
  
  public function fetch_assoc()
  {
    return mysql_fetch_assoc($this->handle);
  }
  
  public function fetch_object()
  {
    return mysql_fetch_object($this->handle);
  }
  
  public function fetch_row()
  {
    return mysql_fetch_row($this->handle);
  }
  
  public function __destruct()
  {
    $this->free();
  }
  
  public function data_seek($row_number)
  {
    return mysql_data_seek($this->handle, $row_number);
  }
  
  public function free()
  {
    #mysql_free_result($this->handle);
  }
  
}

/**
 * Stellt die Verbindung zum MySQL-Server her
 **/
function db_connect()
{
  global $db, $ignore_errors;
  
  $ignore_errors = true;
  /*$db = mysqli_init(); // neues MySQLi-Objekt erstellen
  $db->options(MYSQLI_OPT_LOCAL_INFILE, false); // keine lokalen Dateien laden
  $db->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3); // Timeout setzen
  $db->options(MYSQLI_INIT_COMMAND, 'SET NAMES \'UTF8\'');
  $db->options(MYSQLI_INIT_COMMAND, 'SET collation_connection = utf8_general_ci');
  $db->real_connect('localhost', DB_USER, DB_PASSWORD); // Und los gehts ...
  $ignore_errors = false;
  
  $db->select_db(DB_NAME); */
  
  if($db->errno != 0 || mysqli_connect_errno() != 0) message_die('Verbindung zur Datenbank konnte nicht aufgebaut werden!<br />(<em>'.mysqli_connect_error().'</em>)');
  
  $db = new mysql_crap;
  
  mysql_connect('localhost', DB_USER, DB_PASSWORD);
  mysql_query('SET NAMES \'UTF8\'');
  #mysql_query('SET collation_connection = utf8_general_ci');
  mysql_select_db(DB_NAME);
}


/**
 * Sendet eine Anfrage an den MySQL-Server
 * @return resource
 * @param string $query  SQL-Anfrage
 * @param bool   $silent Fehler ignorieren?
 **/
function db_query($query, $silent=false)
{
  global $_query_cnt, $db, $_real_query_cnt, $_query_time;
  
  $_query_cnt ++;
  $_real_query_cnt ++;
  
  #echo "<br /><tt>-------------------------------<br />$query<br />------------------------------</tt><br />";
  /*if(substr($query, 0, 6) == 'SELECT')
  {
    $x = $db->query("EXPLAIN $query");
    
    echo "<br /><tt>-------------------------------<br />$query<br /><br />";
    while($xx=$x->fetch_assoc())
    {  echo print_r($xx,1).'<br />'; }
    echo "<br />------------------------------</tt><br />";
  }*/
  
  $l_start_time = microtime(true);
  if($silent == false)
  {
    $retval = $db->query($query);
    $_query_time += (microtime(true) - $l_start_time);
    $err = $db->error;
    if($err != '')
    {
      inform_admin(__METHOD__."\n$query\n$err (".$db->errno.")");
      #fatal_exit(); # << ^^ common_functions.php
      return false;
    }
    
    /*if(DEBUG_MODE == 1)
    {
      // folgende 9 Zeilen von php.net
      if ($db->warning_count)
      {
        if ($result = $db->query('SHOW WARNINGS'))
        {
            $row = $result->fetch_row();
            printf("%s (%d): %s (%s)<br />\n", $row[0], $row[1], $row[2], $query);
            inform_admin(__METHOD__."\n$query\n".$row[0] . '(' . $row[1] . '):' . $row[2]);
            $result->free();
        }
      }
    }*/
    
    return $retval;
  }
  else
  {
    $tmp = @$db->query($query);
    $_query_time += (microtime(true) - $l_start_time);
    return $tmp;
  }
}



/**
 * Gibt das erste Feld der ersten Ergebnis-Zeile der Query zurück
 * @param string $query SQL-Query
 * @return mixed das erste Feld der ersten Ergebnis-Zeile
 **/
function db_get_first_row($query)
{ 
  $retval = false;
  $r_tmp = db_query($query);
  if($r_tmp)
  {
    if($r_tmp->num_rows > 0)
    {
      $retval = $r_tmp->fetch_row();
      $retval = $retval[0];
    }
    $r_tmp->free();
  }
  
  return $retval;
}


/**
 * Gibt den ersten Datensatz einer MySQL-Query als stdClass-Objekt zurück
 * @param string $query SQL-Query
 * @return stdClass erster Datensatz
 **/
function db_query_fetch($query)
{ 
  $retval = false;
  $r_tmp = db_query($query);
  if($r_tmp)
  {
    if($r_tmp->num_rows > 0)
    {
      $retval = $r_tmp->fetch_object();
    }
    $r_tmp->free();
  }
  
  return $retval;
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
  $s = substr($s, mt_rand(0, 40 - $chars), $chars);
  
  return $s;
}


/**
 * Liefert IP-Adresse des zum Server verbundenenen Users
 * @return string IP-Adresse (evtl. inkl. Proxyinfos)
 **/
function client_ip()
{
  if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
  {
    if(isset($_SERVER['HTTP_CLIENT_IP']))
    {
      $proxy = $_SERVER['HTTP_CLIENT_IP'];
    }
    else
    {
      $proxy = $_SERVER['REMOTE_ADDR'];
    }
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else
  {
    if(isset($_SERVER['HTTP_CLIENT_IP']))
    {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    else
    {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
  }
  $r = $ip;
  if( $ip != $_SERVER['REMOTE_ADDR'] )
  {
    $r = $_SERVER['REMOTE_ADDR'] . ' / ' . $r;
  }
  if(isset($proxy))
  {
    if($proxy != $_SERVER['REMOTE_ADDR'] && $proxy != $ip)
    {
      $r .= ' via proxy ' . $proxy;
    }
  }
  return $r;
}


/**
 * Liefert Hostname des zum Server verbundenenen Users
 * @author Sven
 * @return string Hostname
 **/
function client_hostname()
{
  $ip = $_SERVER['REMOTE_ADDR'];
  
  $host = 'unknown.host';
  if (isset($_SERVER['REMOTE_HOST']))
  {
    if ($_SERVER['REMOTE_HOST'] != $ip)
    {
      $host = $_SERVER['REMOTE_HOST'];
    }
    else
    {
      $host = gethostbyaddr($ip);
    }
  }
  else
  {
    $host = gethostbyaddr($ip);
  }
  return $host;
}

/**
 * HTN-Debug-Mode-Error-Handler-Callback-Funktion
 **/
function htn_error_handler($type, $text, $file, $line, $vardump)
{
  global $usr, $user_logged_in, $ignore_errors;
  
  if($ignore_errors) return;
  
  $do_exit = false;
  $dealed = false;
  
  if($type == E_NOTICE)
  {
    if(DEBUG_MODE == 1)
    {
      echo '<p><strong>Notice</strong>: '.$text.' in <em>'.basename($file).'</em> in line '.$line.'</p>';
    }
    $dealed = true;
  }
  elseif(DEBUG_MODE == 0)
  {
    echo '<p><strong>FEHLER! Der Spiel-Administrator wurde informiert!</p></strong>';
    $do_exit = true;
  }
  
  if(!$dealed)
  {
    $str = 'Fehler vom Typ '.$type.' in '.basename($file).' in Zeile '.$line.': '.$text.LF;
    #$str .= 'VARIABLEN: ' . print_r($vardump, true) . LF.'BACKTRACE: ' . print_r(debug_backtrace(), true);
    inform_admin($str);
  }
  
  if($do_exit) exit;
}

/**
 * Schickt eine Fehler-Email an den Spiel-Admin
 * @param string $errmsg Fehler-Beschreibung
 **/
function inform_admin($errmsg = '', $no_output = false)
{
  global $ignore_errors;
  
  if($ignore_errors !== true)
  {
    /*if($_SERVER['HTTP_HOST'] != 'htn25.lh')
    {
      if(DEBUG_MODE == 1 && $no_output == false)
      {
        echo '<p><strong>FEHLER! Der Spiel-Administrator wurde informiert!</strong></p>';
      }
      mail(ADMIN_EMAIL, 'FEHLER IM HTN BB!', $errmsg, FROM_MAIL_HEADER);
    }
    else
    {*/
      echo $errmsg;
    #}
  }
}

/**
 * Bricht das Script unter Ausgabe einer Fehlermeldung ab
 **/
function fatal_exit()
{
  die('<strong>Kritischer Fehler aufgetreten!</strong><br /><em>Der Spiel-Administrator wurde informiert!</em>');
}


/**
 * Prüft, ob check in list vorhanden ist
 * @return mixed
 * @param string $check Übergebener Parameter, der geprüft werden soll
 * @param array  $list  gültige Werte
 **/
function valid_param_value($check, $list)
{
  $x = array_search($check, $list);
  if($x != false)
    return $list[$x];
  else
  {
    reset($list);
    return current($list);
  }
}

/**
 * Prüft, ob $haystack einen Schlüssel namens $needle enthält. Falls ja, wird dessen Wert zurückgegeben, sonst $default.
 * @return mixed
 * @param array|object  $haystack haystack
 * @param string $needle needle
 * @param mixed  $default default value
 **/
function getifset($haystack, $needle, $default = '')
{
  if(is_object($haystack))
  {
    return isset($haystack->$needle) ? $haystack->$needle : $default;
  }
  if( is_array($haystack) == false || isset($haystack[$needle]) == false)
  {
    return $default;
  }
  else
  {
    return $haystack[$needle];
  }
}

/**
 * Prüft, ob $haystack einen Schlüssel namens $needle enthält und $haystack[$needle] nicht leer ist
 * Falls ja, wird dessen Wert zurückgegeben, sonst $default.
 * @return mixed
 * @param array  $haystack haystack
 * @param string $needle needle
 * @param mixed  $default default value
 **/
function getifvalue($haystack, $needle, $default = '')
{
  if( is_array($haystack) == false || isset($haystack[$needle]) == false)
  {
    if(empty($haystack[$needle]) == true)
    {
      return $default;
    }
  }
  return $haystack[$needle];
}

/**
 * Erstellt eine gültige htnBB-URL
 * @return string
 * @param string  $htnfile virtuelle Datei
 * @param string $page page-Parameter
 * @param string $params weitere Parameter
 * @param bool  $html Link als HTML zurückgeben?
 **/
function bb_url($module_id, $page, $params = '', $html = true)
{
  global $sid, $session_mode, $user_logged_in, $server;
  
  if(is_array($page)) $page = trim(join('/', $page), '/');
  if(is_array($params)) $params = html_entity_decode(http_build_query($params));
  
  $url = FORUM_BASEDIR . '/bb.php/' . $module_id . '/' .
    $page . '.' . VIRTUAL_FILE_EXTENSION . ($session_mode == 'url' && $sid != '' ? '?sid=' . $sid : '') .
    ($params == '' ? '' : ($params{0} == '#' ? '' : ($session_mode == 'url' && $sid != '' ? '&' : '?')) . $params);
  
  if($html) $url = htmlspecialchars($url);
  
  return $url;
}

function wiki_url($entry)
{
  $url = 'http://htn25.unkreativ.org/_htn.php/wiki/show/' . urlencode(str_replace(' ', '_', $entry));
  return $url;
}

/**
 * Interner HTTP 302-Redirect
 * @see bb_url()
 **/
function bb_redir($htnfile, $x, $params = '')
{
  header('Location: ' . bb_url($htnfile, $x, $params, false));
  #header('Refresh: 0; URL=' . bb_url($htnfile, $x, $params, false));
}


/**
 * Formatiert eine Zahl
 * @return string
 * @param float  $num Zahl
 * @param int  $precision Nachkommastellen
 **/
function numfmt($num, $precision = 0)
{
  return number_format(round($num, $precision), $precision, ',', '.');
}

/**
 * Prüft anhand eines Musters, ob keine illegalen Zeichen im String vorhanden sind
 * @return bool
 * @param string  $str Zu prüfender String
 * @param string  $pattern_mode username|email Welches Pattern soll verwendet werden?
 **/
function check_string($str, $pattern_mode)
{
  $patterns['username'] = '/^[\w\-_äöüß\|\+\@\*\!\.\°\=\[\]]+$/i';
  $patterns['email'] = '/^[0-9a-z]+([-_.]?[0-9a-z])*@[0-9a-z]+((\.|-|--)[0-9a-z]+)*\.[a-z]{2,4}$/i';
  $patterns['url'] = '/(http|ftp|https):\/\/[\w]+(.[\w]+)([\w\-\.,@?^;=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?/i';
  $patterns['numbers'] = '/^[0-9]+$/i';
  $patterns['alphanumeric'] = '/^[a-z0-9]+$/i';
  
  if(!isset($patterns[$pattern_mode]))
  {
    trigger_error('Wrong pattern in '.__METHOD__, E_USER_WARNING);
  }
  return (bool)preg_match($patterns[$pattern_mode], $str);
}

/**
 * Formatiert eine komplette Fehlermeldung und bricht dann das Script ab.
 **/
function message_die($error_message, $type = 'important', $caption = 'Fehler!')
{
  #require_once 'includes/layout.php';
  
  createlayout_top();
  include 'templates/func-message_die.php';
  createlayout_bottom();
  exit;
}

/**
 * Wird aufgerufen, wenn ein Fehler (ungültige Eingabe etc.) auftritt und 
 * der User darüber informiert werden soll
 * @param string $msg Fehlermeldung
 **/
function handle_error($msg)
{
  global $error_occured, $error_msg;
  $error_occured = true;
  $error_msg .= $msg . '<br />';
}


/**
 * Fügt ein Array unter Verwendung eines Trennzeichens in einen String zusammen
 * @author Ingmar
 * @return string
 * @param array  $a Quell-Array
 * @param string $trenn Trenn-Zeichen
 * @param bool  $unique Sollen doppelte Einträge entfernt werden?
 * @param bool  $rtrim Kein Trennzeichen am Ende des Strings?
 **/
function joinex($a, $trenn, $unique = true, $rtrim = false)
{
  $str = '';
  if($unique === true) $a = array_unique($a);
  foreach($a as $item)
  {
    if(trim($item) != '') $str .= trim($item) . $trenn;
  }
  if(!$rtrim)
    return ltrim($str, $trenn . ' ');
  else
    return trim($str, $trenn . ' ');
}

/**
 * Bereitet den übergebenen String für eine MySQL-Anfrage vor.
 * @param string $str
 * @param bool $nowildcards % und _ (Wildcards) entschärfen
 **/
function prepare_string_for_query($str, $nowildcards = false)
{
  global $db;
  
  $str = str_replace("\r\n", "\n", $str);
  $str = $db->real_escape_string($str);
  if( $nowildcards )
  {
    $str = str_replace('_', '\_', $str);
    $str = str_replace('%', '\%', $str);
  }
  return $str;
}


/**
 * Wählt aus dem zweiten Parameter (sofern dieser ein Array ist) bzw. aus allen weiteren Parametern
 * den zum ersten Parameter passenden aus und gibt diesen zurück
 * @return mixed
 * @param string $key
 **/
function choose($key)
{
  if(func_num_args() <= 1)
  {
    trigger_error('choose expects at least 2 parameters!', E_USER_ERROR);
  }
  if( is_array( func_get_arg(1) ) ) // zweiter Parameter ein Array?
  {
    $dat = func_get_arg(1);
  }
  else
  {
    $dat = func_get_args();
  }
  return getifset($dat, $key, false);
}

/**
 * Macht z.B. aus "20k" 20.000
 * @param string $input
 * @return float
 * @author Unbekannter aus dem HTN-Forum ;D
 **/
function human2int($input)
{
  $scale = 1;
  $notations = array(
  'k' => 1000,
  'm' => 1000 * 1000,
  'g' => 1000 * 1000 * 1000,
  't' => 1000 * 1000 * 1000 * 1000,
  // keys in lowercase!
  );
  $input = strtolower(trim($input));
  $input = str_replace(',', '.', $input);
  $output = preg_replace('/[^0-9\.' . join('', array_keys($notations)) . ']+/', '', $input);
  foreach ($notations as $key => $factor)
  {
    while(substr($output, -1, 1) == $key)
    {
      $scale *= $factor;
      $output = substr($output, 0, -1);
    }
  }
  $output = preg_replace('/[^0-9\.]+/', '', $output);
  $output = round(floatval($output) * $scale);
  
  return $output;
} // ende human2int()


/**
 * Prüft ob $bit in $pattern enthalten ist
 * return (bool)(($pattern & $bit) == $bit);
 * @return bool
 * @param int $bit
 * @param int $pattern
 **/
function in_bit($bit, $pattern)
{
  return (bool)(($pattern & $bit) === $bit);
}


/**
 * arbeitet wie die interne PHP-Funktion array_unique, nur case-INsensitive
 * @return array
 * @param array $arr Array
 **/
function array_unique_ci($arr)
{
/*
ergebnis nach einigen tests: langsamer als die untere lösung
  $arr = array_flip($arr); // Schlüssel und Werte vertauschen
  $arr = array_change_key_case($arr, CASE_LOWER); // Alles in kleinbuchstaben
  $arr = array_flip($arr); // Schlüssel und Werte nochmal vertauschen -> wieder wie ganz am Anfang
  $arr = array_unique($arr);
  return $arr;
*/
  foreach($arr as $k => $v)
  {
    $arr[$k] = strtolower($v);
  }
  $arr = array_unique($arr);
  return $arr;
}


/**
 * berechnet aus der globalen Variable $starttime die bisherige Ausführungszeit des Scripts
 **/
function get_execution_time()
{
  global $starttime;
  $runtime = microtime(true) - $starttime;
  return $runtime * 1000;
}


/**
 * Prüft, ob ein "Derefferieren" der URL nötig ist und gibt einen entsprechenden Wert zurück
 * @autor eckhart
 **/
function dereferer($url, $htmlencode = true)
{
  global $session_mode;
  
  if($session_mode != 'cookie')
  {
    $url = urlencode($url);
    return FORUM_BASEDIR . '/derefer.php?url=' . ($htmlencode ? htmlspecialchars($url) : $url);
  }
  else
  {
    return ($htmlencode ? htmlspecialchars($url) : $url);
  }
}


function ts_expired($timestamp)
{
  return (($timestamp < time() && $timestamp != NULL && $timestamp > 0) ? true : false);
}

?>
