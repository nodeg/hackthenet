<?php
/**
 * includes/nicetime.class.php
 * Formatiert Timestamps "hübsch"
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.0.1
 **/

if(!defined('IN_BB')) die('Hacking attempt!');


/**
 * Formatiert Timestamp "hübsch"
 * @package     included_files
 * @subpackage  classes
 **/
final class nicetime
{
  
  /**
   * Vorformatiert einen "Zeit-String"
   * Gibt z.B. "heute" "gestern" "morgen" etc. zurück
   * @return string
   * @param int  $ts Timestamp ( 0=aktuelle Zeit )
   * @param string $default Default, wenn Zeit nicht in vorgestern bis übermorgen
   **/
  private static function _getstr(&$ts, $default)
  {
    if($ts == 0) $ts = time();
    
    $heute = strftime('%x');
    $gestern = strftime('%x', time()-86400);
    $vorgestern = strftime('%x', time()-2*86400);
    $morgen = strftime('%x', time()+86400);
    $uebermorgen = strftime('%x', time()+2*86400);
    switch(strftime('%x', $ts))
    {
      case $heute: $r = 'heute '; break;
      case $gestern: $r = 'gestern '; break;
      case $vorgestern: $r = 'vorgestern '; break;
      case $morgen: $r = 'morgen '; break;
      case $uebermorgen: $r = 'übermorgen '; break;
      default: $r = $default;
    }
    return $r;
  }
  
  
  /**
   * Formatiert einen Timestamp wie "Montag, 2. August um 01:38 Uhr"
   * @return string
   * @param int  $ts Timestamp ( 0=aktuelle Zeit )
   **/
  static function Extended($ts=0)
  {
    $r = nicetime::_getstr($ts,'%A, %d. %B, um ');
    return strftime($r.'%H:%M Uhr', $ts);
  }
  
  /**
   * Formatiert einen Timestamp wie "Mon, 2. Aug um 01:38:56 Uhr"
   * @return string
   * @param int  $ts Timestamp ( 0=aktuelle Zeit )
   * @param string  $zw Text zwischen Datum & Uhrzeit
   **/
  static function Short($ts=0, $zw=' um ')
  {
    $r = nicetime::_getstr($ts,'%a, %d. %b.');
    return strftime($r.$zw.'%X Uhr', $ts);
  }
  
  /**
   * Formatiert einen Timestamp wie "2.8. 01:38" oder "2.8. 01:38:56"
   * @return string
   * @param int  $ts Timestamp ( 0=aktuelle Zeit )
   * @param bool  $seconds Sekunden anzeigen?
   * @param string  $zw Text zwischen Datum & Uhrzeit
   **/
  static function Usual($ts=0, $seconds=false, $zw=' ')
  {
    $r = nicetime::_getstr($ts,'%d.%m.');
    if($seconds == false) $x = $r.$zw.'%H:%M'; else $x = $r.$zw.'%H:%M:%S';
    return strftime($x, $ts);
  }
  
  /**
   * Formatiert einen Timestamp wie "2.8. um 01:38"
   * @return string
   * @param int  $ts Timestamp ( 0=aktuelle Zeit )
   * @param string  $zw Text zwischen Datum & Uhrzeit
   * @see Usual()
   **/
  static function Full($ts=0, $zw=' um ')
  {
    if($ts == 0) $ts = time();
    return strftime('%d.%m.'.$zw.'%H:%M', $ts);
  }
  
  /**
   * Gibt die in Sekunden übergebene Zeitspanne in Tagen, Stunden, Minuten zurück
   * @return string
   * @param int  $seconds Zeitspanne
   **/
  static function duration_fmt($seconds)
  {
    $result = $unit = '';
    $minutes = $hours = 0;
    $orig_duration_negative = ($seconds < 0);
    
    $format_func = create_function('$s', 'return str_pad($s, 2, \'0\', STR_PAD_LEFT);');
    
    $seconds = abs($seconds);
    $days = $seconds / 86400;
    
    if($days > 1)
    {
      $result .= floor($days) . ' Tag' . (floor($days) == 1 ? '' : 'e');
      $seconds %= 86400;
    }
    
    $hours = floor($seconds / 3600);
    $seconds %= 3600;
    $minutes = floor($seconds / 60);
    $seconds %= 60;
    
    if(($hours > 0 || $minutes > 0 || $seconds > 0) && $days > 1) $result .= ', ';
    
    if($hours == 0 && $minutes == 0 && $seconds > 0)
    {
      $unit = 'Sekunde' . ($seconds == 1 ? '' : 'n');
      $result .= $seconds;
    }
    elseif($hours == 0 && $minutes > 0)
    {
      $unit = 'Minute' . ($minutes == 1 && $seconds == 0 ? '' : 'n');
      if($seconds > 0)
        $result .= $format_func($minutes) . ':' . $format_func($seconds);
      else
        $result .= $minutes;
    }
    elseif($hours > 0)
    {
      $unit = 'Stunde' . ($hours == 1 && $minutes == 0 && $seconds == 0 ? '' : 'n');
      $result .= ($minutes == 0 && $seconds == 0 ? $hours : $format_func($hours)) . ($minutes > 0 || $seconds > 0 ? ':' . $format_func($minutes) . ($seconds > 0 ? ':' . $format_func($seconds) : '') : '');
    }
    
    return $result.($unit != '' ? ' '.$unit : '');
  }
  
  /*
    echo nicetime::duration_fmt(86400).'<br />';
  echo nicetime::duration_fmt(86400 + 5).'<br />';
  echo nicetime::duration_fmt(86400 * 2).'<br />';
  echo nicetime::duration_fmt(86400 * 2 + 5).'<br />';
  echo nicetime::duration_fmt(86400 + 60).'<br />';
  echo nicetime::duration_fmt(86400 + 60 + 5).'<br />';
  echo nicetime::duration_fmt(86400 + 3600).'<br />';
  echo nicetime::duration_fmt(86400 + 3600 + 5).'<br />';
  echo nicetime::duration_fmt(86400 + 3600 + 60).'<br />';
  echo nicetime::duration_fmt(86400 + 3600 + 60 + 5).'<br />';
  echo nicetime::duration_fmt(5).'<br />';
  echo nicetime::duration_fmt(60).'<br />';
  echo nicetime::duration_fmt(3600).'<br />';
  echo nicetime::duration_fmt(3600 + 60 + 5).'<br />';
  */
  
  
}