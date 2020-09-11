<?php
/**
 * Haupt-Datei vom htnBB
 * Über diese Datei laufen sämtliche Seitenabrufe.
 **/

/**
 * @ignore
 **/
define('IN_BB', 1);

/**
 * Timestamp bei Start des Seitenabrufs in Sekunden
 * @global float $starttime
 **/
$starttime = microtime(true);

/**
 * Anzahl von MySQL-Querys für den Aufbau der Seite (Multiquery zählt als eine Query)
 * @global int $_query_cnt
 **/
$_query_cnt = 0;

/**
 * Echte Anzahl von MySQL-Querys für den Aufbau der Seite (bei Multiquerys wird jede Query einzeln gezählt)
 * @global int $_real_query_cnt
 **/
$_real_query_cnt = 0;

/**
 * Zeit in Sekunden, die für MySQL-Anfragen benötigt wurde
 * @global int $_query_time
 **/
$_query_time = 0;

/**
 * Ist ein Fehler über die Funktion handle_error() ausgelöst worden?
 * @see handle_error()
 * @global bool $error_occured
 **/
$error_occured = false;

/**
 * Fehlermeldung, festgelegt über die Funktion handle_error()
 * @see handle_error()
 * @global string $error_msg
 **/
$error_msg = '';

/**
 * (PHP-)Fehler soweit möglich ignorieren?
 * @global bool $ignore_errors
 **/
$ignore_errors = false;

/**
 * Wird der böse Internet Explorer verwendet?
 * @global bool $is_IE
 **/
$is_IE = (bool)(substr_count($_SERVER['HTTP_USER_AGENT'], 'MSIE') > 0);

/**
 * Handle zur Datenbankverbindung (MySQLi-Objekt)
 * @global object $DB
 **/
$db = NULL;


$bb_settings = array();

/**
 * Einstellungen laden
 **/
require 'core/config.php';

/**
 * gemeinsam genutze Funktionen laden
 **/
require 'core/common_functions.php';

/**
 * Layout-Kram laden ;-D
 **/
require 'core/layout.php';

require 'core/burningboard.inc.php';


// Deutsche Zeit + UTF-8-Zeichensatz global einstellen
setlocale(LC_TIME, 'de_DE.utf-8');
if(function_exists('date_default_timezone_set'))
{
  date_default_timezone_set('Europe/Berlin');
}
ini_set('include_path', dirname(__FILE__) . '/');

/**
 * Pfad zur Datei, die Templates anzeigt
 **/
define('SHOW_TEMPLATE_FILE', 'core/show_template.php');


if(DEBUG_MODE == 1)
{
  error_reporting(E_ALL | E_STRICT);
  ini_set('display_errors', 'On');
  ini_set('track_errors', 1);
}
#else
#{
  // Sollte in php.ini eingestellt sein!
  
  #error_reporting(E_ALL & ~E_NOTICE);
  #ini_set('display_errors', 'Off');
  #ini_set('track_errors', 0);
#}

// Error-Handler setzen (htn_error_handler ist in common_functions.php definiert)
set_error_handler('htn_error_handler');

// Cachen verhindern
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time() + 86400) .' GMT');
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header('Cache-Control: post-check=0, pre-check=0', FALSE);

header('Content-Type: text/html; charset=UTF-8');


db_connect();

$r_settings = db_query('SELECT `key`, `value` FROM settings');
while($tmp = $r_settings->fetch_assoc())
{
  $bb_settings[$tmp['key']] = $tmp['value'];
}
$r_settings->free();
unset($tmp, $r_settings);


/**
 * Magische Callback-Funktion __autoload.
 * Sorgt dafür, dass die Include-Dateien für noch nicht verwendete Klassen
 * automatisch eingebunden werden.
 **/
function __autoload($classname)
{
  $filename = 'core/' . $classname . '.class.php';
  if(file_exists($filename))
  {
    include $filename;
  }
  else
  {
    trigger_error('Attempting to load inexistent class <em>'.$classname.'</em>!', E_USER_ERROR);
  }
}


/**
 * register_shutdown_function('htn_shutdown');
 **/
function htn_shutdown()
{
  global $db;
  
  flush();
  $db->close();
  unset($db);
}

// Dafür sorgen, dass am Ende des Scriptes schön aufgeräumt wird
register_shutdown_function('htn_shutdown');


/**
 * Ein htBB-Modul
 **/
abstract class i_htnbb_module
{
  /**
   * @var string Modul-ID
   **/
  protected $m_id = '';
  /**
   * @var array Stylesheet-Dateien dieses Moduls
   **/
  protected $m_stylesheets = array();
  /**
   * @var bool Dieses Modul hat keine Funktion (module page) die ausgeführt werden muss
   **/
  protected $m_no_execute = false;
  /**
   * @var htn_module_page Seite (module page), die aufgerufen wurde
   **/
  public $current_page;
  /**
   * @var string Name der Funktion der vor dem Aufruf der Seite ausgeführt werden soll
   **/
  protected $function_first_to_call = '';
  /**
   * @var array
   **/
  protected $m_virtual_url_rewrite_params = array();
  
  abstract public function &set_page($pageid);
  abstract public function __construct();  
  
  /**
   * @return mixed
   **/
  public function __get($property)
  {
    switch($property)
    {
    case 'id': return $this->m_id;
    case 'stylesheets':
      if(!is_array($this->m_stylesheets)) $this->m_stylesheets = array();
      return $this->m_stylesheets;
    case 'virtual_url_rewrite_params': return $this->m_virtual_url_rewrite_params;
    }
  }
  
  /**
   * @return void
   **/
  public function execute_page()
  {
    if(!empty($this->function_first_to_call))
    {
      $fname = $this->function_first_to_call;
      $fname();
    }
    if(!$this->m_no_execute)
    {
      $fname = $this->current_page->id;
      $this->$fname();
    }
  }
  
}


/**
 * Eine Seite eines HTN-Moduls
 **/
class htn_module_page
{
  /**
   * @var string Titel der Seite
   **/
  protected $m_title;
  /**
   * @var string ID, z.B. 'overview'
   **/
  protected $m_id;
  /**
   * @var i_htn_module Das Modul, zu dem diese Seite gehört
   **/
  protected $m_my_module;
  /**
   * @var bool Seite nur für Admins?
   **/
  protected $m_admin_only;
  
  /**
   * @return void
   **/
  public function __construct(&$my_module, $id, $title = '', $admin_only = false)
  {
    $this->m_id = $id;
    $this->m_my_module = &$my_module;
    if($id !== NULL)
    {
      $this->m_title = $title;
      $this->m_admin_only = $admin_only;
    }
  }
  
  /**
   * @return mixed
   **/
  public function __get($property)
  {
    switch($property)
    {
    case 'id': return $this->m_id;
    case 'title': return $this->m_title;
    case 'my_module': return $this->m_my_module;
    case 'admin_only': return $this->m_admin_only;
    }
  }
  
  /**
   * @return void
   **/
  public function __set($property, $value)
  {
    switch($property)
    {
    case 'title': $this->m_title = $value; break;
    }
  }
  
}

// Auflistung aller Module
// id => (0 = deaktiviert || 1 = aktiviert)
$_modules = array(
  'board' => 1,
  'browse' => 1,
  'user' => 1,
  'watchedtopics' => 1,
  'modcp' => 1,
  'admincp' => 1,
  'search' => 1
);

/**********************************************************************************************************/
/********                         BEGINN DER GRUNDSÄTZLICHEN MODUL-VERWALTUNG                      ********/
/**********************************************************************************************************/

$module_id = getifset($_GET, '_m');
$module_page = getifset($_GET, '_p');

/*
    [REQUEST_URI] => /prebeta/bb.php/pub/home
    [SCRIPT_NAME] => /prebeta/bb.php
    [PATH_INFO] => /pub/home
    
    [REQUEST_URI] => /prebeta/bb.php/pub/home
    [SCRIPT_NAME] => /prebeta/bb.php
    [PATH_INFO] => 
*/

if(!isset($_modules[$module_id]))
{
  $path_info = getifset($_SERVER, 'PATH_INFO');
  if(empty($path_info))
  {
    $path_info = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']));
    $qs_len = strlen($_SERVER['QUERY_STRING']);
    if($qs_len > 0) $path_info = substr($path_info, 0, -$qs_len - 1);
    unset($qs_len);
  }
  $path_info = explode('/', substr($path_info, 1));
  if(count($path_info) >= 2)
  {
    $path_info[count($path_info) - 1] = str_replace('.' . VIRTUAL_FILE_EXTENSION, '', $path_info[count($path_info) - 1]);
    $module_id = $path_info[0];
    $module_page = $path_info[1];
  }
}

if(!isset($_modules[$module_id]))
{
  bb_redir('board', 'index');
}

if($_modules[$module_id] !== 1)
{
  message_die('Dieses Modul ist im Moment von einem Administrator deaktiviert. Gedulde dich ein paar Minuten, dann wird es wieder aktiviert sein.', 'info');
}

$menu_entry_flags = MI_INDEX | MI_HTNHOME | MI_SEARCH;

/**
 * Modul-Datei einbinden
 **/
include 'modules/' . $module_id . '.php';

$_module = 'htnbb_module_' . $module_id;
$_module = new $_module;
$_page = &$_module->set_page($module_page);
if($_page === false)
{
  $_module->current_page = new htn_module_page($_module, NULL); // "leeres Objekt" erstellen
  $_page = &$_module->current_page;
}

if(count($_module->virtual_url_rewrite_params) > 0)
{
  if(count($path_info) > 2)
  {
    for($i = 2; $i < count($path_info); $i++)
    {
      $key = getifset($_module->virtual_url_rewrite_params, $i - 2, 0);
      if($key === 0) continue;
      $_GET[$key] = $path_info[$i];
      $_REQUEST[$key] = &$_GET[$key];
    }
  }
  else
  {
    message_die('Ungültiger Aufruf!');
  }
}

unset($module_id, $module_page, $modules, $key, $i, $path_info);


/**********************************************************************************************************/
/********                         ENDE DER GRUNDSÄTZLICHEN MODUL-VERWALTUNG                        ********/
/**********************************************************************************************************/
/********                         BEGINN DER VERARBEITUNG DER SESSION-DATEN                        ********/
/**********************************************************************************************************/

/**
 * Wird die Seite von einem eingeloggten User aufgerufen?
 * @global bool $user_logged_in
 **/
$user_logged_in = 'n/a';
/**
 * Session-ID
 * @global string $sid
 **/
$sid = '';
/**
 * Session-Modus, wie wird die Session-ID weitergegeben? ('url' oder 'cookie')
 * @global string $session_mode
 **/
$session_mode = 'url';
/**
 * Benutzer-Daten
 * @global object $user
 **/
$usr = false;


// Die Session-ID aus den Umgebungsvariablen auslesen
$sid = getifvalue($_GET, 'sid', false); 
if($sid == false) $sid = getifvalue($_POST, 'sid', false);
if($sid == false)
{
  $sid = getifvalue($_COOKIE, SESSION_ID_COOKIE_NAME, '');
  $session_mode = 'cookie';
}

if(empty($sid))
{
  $sid = false;
}

if($sid == false)
{
  $user_logged_in = false;
}
elseif(!$usr = user::load($sid, 'sess_id'))
{
  $user_logged_in = false;
}

if($usr instanceof user)
{
  if($usr->sess_ipmask != bb_ipmask(client_ip()))
  {
    if($session_mode == 'cookie')
    {
      // Session-Cookie löschen
      setcookie(SESSION_ID_COOKIE_NAME, false, 0, FORUM_BASEDIR.'/');
    }
    $user_logged_in = false;
  }
}


if($user_logged_in == false)
{
  if(isset($_COOKIE[SAVE_LOGIN_COOKIE_NAME]))
  {
    list($nick, $hash) = explode('|', $_COOKIE[SAVE_LOGIN_COOKIE_NAME]);
    if(!$nick || !$hash) exit;
    $password = db_get_first_row('SELECT password FROM users WHERE name = \'' . prepare_string_for_query($nick) . '\' LIMIT 1');
    #echo $hash . ' xxx ' . sha1($nick . '<-|%|->' . $password);
    if($hash == sha1($nick . '<-|%|->' . $password))
    {
      $sid = random_string(16);
      db_query('UPDATE users SET sess_id = \'' . prepare_string_for_query($sid) . '\', sess_ipmask = \'' .
        prepare_string_for_query(bb_ipmask($_SERVER['REMOTE_ADDR'])) . '\', sess_lastcall = ' . time() . ' WHERE '.
        'name = \'' . prepare_string_for_query($nick) . '\' LIMIT 1');
      $user_logged_in = true;
      $usr = user::load($sid, 'sess_id');
      setcookie(SESSION_ID_COOKIE_NAME, $sid, time() + 86400, FORUM_BASEDIR.'/', $_SERVER['HTTP_HOST']);
      $session_mode = 'cookie';
    }
    else
    {
      setcookie(SESSION_ID_COOKIE_NAME, false, time() + 86400, FORUM_BASEDIR.'/', $_SERVER['HTTP_HOST']);
      setcookie(SAVE_LOGIN_COOKIE_NAME, false, time() + 86400 * 32, FORUM_BASEDIR.'/', $_SERVER['HTTP_HOST']);
    }
    unset($password, $nick, $hash);
  }
  if($user_logged_in == false)
  {
    $menu_entry_flags |= MI_LOGIN | MI_REGISTER;
  }
}

if($user_logged_in === 'n/a' || $user_logged_in === true)
{
  
  // Zeit des letzen Seitenabrufs aktualisieren (für on/off-Anzeige und anderes):
  if($usr->sess_lastcall < time() - SESSION_LASTCALL_UPDATE_INTERVAL)
  {
    $usr->set_val('sess_lastcall', time());
    
    if($usr->sess_lastcall <= time() - 7200)
    {
      $usr->set_val('sess_id', random_string(10));
      
      if(count($_POST) == 0)
      {
        unset($_GET['sid']);
        $_GET['sid'] = $usr->sess_id;
        bb_redir($_module->id, $_page->id, $_GET);
        exit;
      }
    }
  }
  
  $user_logged_in = true;
  $menu_entry_flags |= MI_LOGOUT | MI_PRIVMSGS | MI_WATCHED_TOPICS | MI_PROFILE | MI_USERLIST;
  
  if($usr->is_admin == 1)
  {
    $menu_entry_flags |= MI_ADMIN;
  }
  
  $last_unread_data_update = $usr->unread_post_data_last_update;
  $unread_post_data = $usr->unread_post_data;
}

$last_post_time = getifset($bb_settings, 'global_last_post_time', 0);

if($user_logged_in == false)
{
  $sid = '';
  /*if(isset($_COOKIE['htnbb_unread_post_data']))
  {
    $unread_post_data = $_COOKIE['htnbb_unread_post_data'];
    $last_unread_data_update = (int)$_COOKIE['htnbb_unread_post_data_last_update'];
  }*/
}

if($_page->admin_only && ($user_logged_in ? $usr->is_admin : 0) != 1)
{
  message_die('Die angeforderte Seite wurde nicht gefunden.', 'error');
}

if(isset($unread_post_data) && $user_logged_in)
{
  $unread_post_data = @unserialize($unread_post_data);
  
  if(!is_array($unread_post_data))
  {
    $unread_post_data = array();
  }
  
  if($last_unread_data_update <= $last_post_time)
  {
    // Update nötig
    
    $r_data = db_query('SELECT id AS post_id, topic_id, (SELECT forum_id FROM topics
      WHERE id = topic_id) AS forum_id FROM posts WHERE time > ' . (int)$last_unread_data_update);
    
    $data = array();
    /*
      data = array
      (
        forum_1 => array
        (
          topic1 => post132,
          topic3 => post432,
          topicX => postY
        ),
        forum_2 => array
        (
          topic11 => post3332,
          topic23 => post3334,
        )
      )
    */
    
    while($post = $r_data->fetch_object())
    {
      $post->forum_id = (int)$post->forum_id;
      $post->topic_id = (int)$post->topic_id;
      $post->post_id = (int)$post->post_id;
      
      $tmp = getifset(getifset($data, $post->forum_id), $post->topic_id, false);
      
      if($tmp === false || $post->post_id < $tmp)
      {
        $data[$post->forum_id][$post->topic_id] = $post->post_id;
      }
    }
    
    // da array_merge_recursive hier mit den Keys falsch arbeitet, mussten wir uns selber was basteln,
    // um die Arrays zusammenzufügen
    foreach($data as $forum_id => $topics)
    {
      if(!isset($unread_post_data[$forum_id]))
      {
        $unread_post_data[$forum_id] = $topics;
      }
      else
      {
        foreach($topics as $topic => $post)
        {
          $old_last_post = getifset($unread_post_data[$forum_id], $topic, false);
          if($old_last_post > $post || $old_last_post == false)
          {
            $unread_post_data[$forum_id][$topic] = $post;
          }
        }
      }
    }
    
    $r_data->free();
    unset($r_data, $data, $forum_id, $topics, $topic, $post, $old_last_post);
    
    #print_r($unread_post_data);
    $usr->set_val('unread_post_data', serialize($unread_post_data));
    $usr->set_val('unread_post_data_last_update', time());
  }
}

if($_page->id == NULL)
{
  // ja scheiße, ne :P
  message_die('Die angeforderte Seite wurde nicht gefunden.', 'error');
}

/**********************************************************************************************************/
/********                         ENDE DER VERARBEITUNG DER SESSION-DATEN                          ********/
/**********************************************************************************************************/

$_module->execute_page();

?>