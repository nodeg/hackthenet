<?php
/**
 * includes/layout.php Layout-Verwaltung.
 * Stellt generelle Funktionen zur
 * einheitlichen Formatierung aller Seiten zur Verfügung
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.1.1
 * @package     included_files
 **/

if( !defined('IN_BB') ) die('Hacking attempt!');

$head_code = '';
$submenucode = '';

define('MI_INDEX', 1);
define('MI_LOGIN', 2);
define('MI_LOGOUT', 4);
define('MI_PRIVMSGS', 8);
define('MI_WATCHED_TOPICS', 16);
define('MI_REGISTER', 32);
define('MI_PROFILE', 64);
define('MI_ADMIN', 128);
define('MI_HTNHOME', 256);
define('MI_USERLIST', 512);
define('MI_SEARCH', 1024);

/**
 * gibt den in jeder Seite benötigten HTML-Kopf aus
 * @param $title HTML-Seiten-Titel (wird nach "HTN -" eingefügt)
 **/

function basicheader($title = '', $clock = true)
{
  global $head_code, $usr, $user_logged_in, $is_IE, $_module;
  global $color_styles, $icon_styles, $stylesheets;
  
  $title = ($title == '' ? '' : ' - ' . $title);

  /*if($user_logged_in)
  {
    $base_style = $usr->stylesheet;
    $color_style = $usr->style_colors;
    $icon_style = $usr->style_icons;
  }
  else
  {*/
    $base_style = DEFAULT_STYLESHEET;
    $color_style = DEFAULT_STYLESHEET_COLORS;
    $icon_style = DEFAULT_STYLESHEET_ICONS;
  #}
  
  echo '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>';
  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'.LF;
  echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">'.LF;
  echo '<head>'.LF;
  echo '<script type="text/javascript">//<![CDATA[' . LF .
  'var stm=' . microtime(1) . ',lstm=new Date()';
  if(($user_logged_in ? $usr->scroll_menu == '1' : true) && $stylesheets[$base_style]['menuscroll'] === true) echo ',scroll_menu=1';
  if($clock != true) echo ',noclock=1';
  echo ';';
  echo LF . '//]]></script>'.LF;
  echo '<script src="' . FORUM_BASEDIR . '/global.js" type="text/javascript"></script>'.LF;
  
  $css_files = array($base_style . '/global');
  
  if(is_object($_module))
  {
    if($_module instanceof i_htnbb_module)
    {
      foreach($_module->stylesheets as $cssfile)
      {
        $css_files[] = $base_style . '/' . $cssfile;
      }
    }
  }
  
  $css_files[] = $color_style . '/colors';
  $css_files[] = $icon_style . '/icons';
  
  echo '<style type="text/css">' . LF;
  foreach($css_files as $cssfile)
  {
    echo '@import url(' . FORUM_BASEDIR . '/media/styles/' . $cssfile . '.css);'.LF;
  }
  echo '</style>'.LF;
  
  if($is_IE)
  {
    echo '<!--[if IE]><link rel="stylesheet" href="' . FORUM_BASEDIR . '/media/styles/' . $base_style . '/ie-fixes.css" type="text/css" /><![endif]-->'.LF;
  }
  
  echo '<title>' . BOARD_TITLE . ' ' . $title . '</title>'.LF;
  echo $head_code;
}

/**
 * generiert den Code für einen Eintrag im Hauptmenü
 * @param string $url Link-Ziel
 * @param string $text Link-Text
 * @param string $help Hilfe-Text
 * @param string $em Extra-Info
 * @param string $emclass Extra-Info-CSS-Klasse
 * @param string $selftags werden eigene Tags übergeben?
 * @param string $inatag Code im <a>-Tag
 **/
function menu_entry($url, $text, $help = '', $em = '', $emclass = '', $selftags = false, $inatag = '')
{
  echo '<li><a href="'.$url.'"'.$inatag.'><b>'.$text.'</b>';
  if($em != '')
  {
    if(!$selftags) echo '<br /><em' . ($emclass != '' ? ' class="'.$emclass.'"' : '') . '>'.$em.'</em>'; else echo $em;
  }
  echo '</a>';
  #if($help != '') echo '<div class="help">'.$help.'</div>';
  
  echo '</li>'.LF;
}

/**
 * generiert den Code für in jeder Seite vor allen anderen Ausgaben benötigte HTML-Elemente
 * @param string $bodytag Zusätzlicher Code, der ins <body>-Tag eingefügt wird
 **/
function pageheader($bodytag = '')
{
  global $sid, $session_mode, $usr, $head_code, $user_logged_in, $_module, $menu_entry_flags;
  
  $mflags = $menu_entry_flags;
  
  $bodytag = ($bodytag == '' ? '' : ' ' . $bodytag);
  echo '</head>';
  echo '<body'.$bodytag.'>'.LF;
  
  echo '<h1>HTN.BB Beta</h1>'.LF;
  
  echo '<ul class="navigation" id="navi">'.LF;
  
  if(in_bit(MI_INDEX, $mflags)) menu_entry(bb_url('board', 'index'), 'Foren-Index');
  menu_entry('http://backen.pimpmybyte.de/', 'Wiki');
  if(in_bit(MI_LOGIN, $mflags)) menu_entry(bb_url('board', 'login'), 'Log In');
  //if(in_bit(MI_PRIVMSGS, $mflags)) menu_entry(bb_url('privmsgs', 'inbox'), 'Mailfach');
  if(in_bit(MI_WATCHED_TOPICS, $mflags)) menu_entry(bb_url('watchedtopics', 'show'), 'Abos');
  if(in_bit(MI_USERLIST,  $mflags)) menu_entry(bb_url('user', 'mlist'), 'Mitgliederliste');
  if(in_bit(MI_REGISTER, $mflags)) menu_entry(bb_url('user', 'register'), 'Registrieren');
  if(in_bit(MI_SEARCH, $mflags)) menu_entry(bb_url('search', 'search'), 'Suche');
  if(in_bit(MI_PROFILE, $mflags)) menu_entry(bb_url('user', 'profile'), 'Mein Profil');
  if(in_bit(MI_ADMIN, $mflags)) menu_entry(bb_url('admincp', 'overview'), 'Administration');
  if(in_bit(MI_LOGOUT, $mflags)) menu_entry(bb_url('board', 'logout'), 'Log Out', '', '<br />Account: <em>'.$usr->name.'</em>', '', true);
  
  echo '</ul>'.LF;
}


/**
 * basicfooter()
 **/
function basicfooter()
{
  echo LF.'</body></html>';
}

/**
 * pagefooter()
 **/
function pagefooter()
{
  global $_query_cnt, $_real_query_cnt, $user_logged_in, $usr, $_query_time, $_module;
  
  echo '<div id="clock">' . strftime('%H:%M:%S') . ' Uhr</div>'.LF;
  
  echo LF.'<div id="generation-time">[TT: ' . numfmt(get_execution_time()) . 'ms' . ($_query_cnt > 0 ? ', QT: '.
    numfmt($_query_time * 1000) . 'ms, Qs: ' . $_query_cnt . ($_query_cnt != $_real_query_cnt ? ', Rqs: ' . $_real_query_cnt : '') : '') . ']</div>'.LF;
  
  echo LF.'<p class="pagefooter">powered by <a href="http://www.hackthenet.org">HTN.BB</a> - © 2005-2007 Ingmar Runge</p>'; # - using XHTML 1.1, CSS 2.0, PHP 5, MySQL 4.1</p>';
}

/**
 * createlayout_top
 * @param string $title Seiten-Titel
 **/
function createlayout_top($title = '', $headcode = '')
{
  global $head_code;
  $head_code .= $headcode;
  basicheader($title);
  pageheader();
}

/** 
 * createlayout_bottom
 **/
function createlayout_bottom()
{
  pagefooter();
  basicfooter();
}



?>
