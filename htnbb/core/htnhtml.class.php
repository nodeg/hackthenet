<?php
/**
 * includes/htnhtml.class.php
 * In dieser Klasse finden sich einige statische Funktionen zur Ausgabe immer wieder benötigten HTML-Codes.
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.0.1
 * @package     included_files
 * @subpackage  classes
 **/

if( !defined('IN_BB') ) die('Hacking attempt!');


/**
 * In dieser Klasse finden sich einige statische Funktionen zur Ausgabe immer wieder benötigten HTML-Codes.
 * @package     included_files
 * @subpackage  classes
 **/
final class htnhtml
{

  /** 
   * Gibt eine Fehlermeldung entsprechend formatiert aus
   * @param string $title
   * @param string $text
   * @return string
   **/
  public static function error_box($title, $text, $return = false)
  {
    $msg = "\n<div class=\"error\"><h3>$title</h3>\n<p>$text</p></div>\n";
    if($return) return $msg; else echo $msg;
  }
  
  /** 
   * Gibt eine Info-Box entsprechend formatiert aus
   * @param string $title
   * @param string $text
   * @return string
   **/
  public static function info_box($title, $text, $return = false)
  {
    $msg = "\n<div class=\"info\"><h3>$title</h3>\n<p>$text</p></div>\n";
    if($return) return $msg; else echo $msg;
  }
  
  /** 
   * Gibt eine Tip-Box entsprechend formatiert aus
   * @param string $title
   * @param string $text
   * @return string
   **/
  public static function tip_box($title, $text, $return = false)
  {
    $msg = "\n<div class=\"tip\"><h3>$title</h3>\n<p>$text</p></div>\n";
    if($return) return $msg; else echo $msg;
  }
  
  /** 
   * Gibt eine Erfolgsmeldung entsprechend formatiert aus
   * @param string $title
   * @param string $text
   * @return string
   **/
  public static function ok_box($title, $text, $return = false)
  {
    $msg = "\n<div class=\"ok\"><h3>$title</h3>\n<p>$text</p></div>\n";
    if($return) return $msg; else echo $msg;
  }
  
  /** 
   * Gibt eine "Achtung!-Meldung" entsprechend formatiert aus
   * @param string $title
   * @param string $text
   * @return string
   **/
  public static function important_box($title, $text, $return = false)
  {
    $msg = "\n<div class=\"important\"><h3>$title</h3>\n<p>$text</p></div>\n";
    if($return) return $msg; else echo $msg;
  }
  
   /**
   * Gibt den Code für eine <select>-Liste oder eine Radionbutton-Liste zurück
   * @param string name Name des Auswahlfeldes
   * @param array $options Mögliche Auswahlwerte
   * @param mixed $curval Aktueller Wert
   * @param mixed $default Standardwert, wenn $curval leer ist
   * @param bool $valiskey Determiniert, ob die Array-Schlüssel mit den Werten identisch sind = ob der Wert des Items gleich seiner Bezeichnung ist
   * @return string
   **/
  public static function code_form_selectbox($name, $options, $curval, $default='', $valiskey=false, $force='', $inselecttag='')
  {
    if($curval == '') $curval = $default;
    $code = '';
    
    if(count($options) > 3 || $force == 'select' && $force != 'radio')
    {
      $code.=LF.'<select name="'.$name.'"'.$inselecttag.'>';
      foreach($options as $k => $v)
      {
        // Das ist irgendwie komisch hier ...
        $code.='<option';
        if($valiskey == true) { } #$code.=" value=\"$v\"";
        elseif($k !== $v) $code.=" value=\"$k\"";
        if(($k == $curval && $valiskey == false) || ($valiskey == true && $v == $curval) ) $code.=' selected="selected"';
        $code .= '>' . strip_tags($v) . '</option>';
      }
      $code.='</select>'.LF;
    }
    else
    {
      $i=0;
      foreach($options as $k => $v)
      {
        $i++;
        if($valiskey) $k = $v;
        $cssid = str_replace('[', '', $name . 'opt' . $i);
        $cssid = str_replace(']', '', $cssid);
        $code .= "<input type=\"radio\" name=\"$name\" value=\"$k\" id=\"$cssid\"";
        if($k == $curval) $code .= ' checked="checked"';
        $code .= " /><label for=\"$cssid\">$v</label>\n";
      }
    }
    return $code;
  }
  
  /**
   * Gibt den Code für eine <input type="checkbox">-Checkbox zurück
   * @param string name Name der Checkbox
   * @param array $value Aktueller Wert, siehe auch $checkedval
   * @param mixed $caption Label-Text
   * @param mixed $checkedval Wert der bei aktivem Häkchen zum Server übertragen werden soll
   * @return string
   **/
  public static function form_checkbox($name, $value, $caption, $checkedval='1')
  {
    $code = "<input type=\"checkbox\" name=\"$name\" "; if($caption != '') $code .= "id=\"chk_$name\" ";
    if ($value == $checkedval) $code.='checked="checked" ';
    $code .= "value=\"$checkedval\" />";
    if($caption != '') $code .= "<label for=\"chk_$name\">$caption</label>";
    
    return $code;
  }
  
  /**
   * Gibt den Code für die letzte Zeile einer Tabelle mit einem Submit-Button zurück
   * @param string $caption Text auf dem Button
   * @param int $colspan Wie viele Spalten soll die Zeile umfassen?
   * @return string
   **/
  public static function form_submit_row($caption='Absenden', $colspan = 2)
  {
    $colspan_string = '';
    if($colspan > 1) $colspan_string = ' colspan="' . $colspan . '"';
    return '<tr class="submit"><td' . $colspan_string . '><input type="submit" class="flatbutton" value=" &nbsp; ' . $caption . ' &nbsp; " accesskey="s" onclick="setTimeout(\'this.disabled=true\', 500)" /></td></tr>';
  }
  
  /**
   * Gibt den Code für ein anklickbares 16x16-Icon zurück
   * @param string $type Typ / Dateiname
   * @param string $url Link-Ziel
   * @param string $alt Alternativer Text für Bild und Title für Link
   * @return string
   **/
  public static function small_gray_icon($type, $url, $alt, $clickable = true, $confirm = '')
  {
    $data_path = FORUM_BASEDIR . '/media/styles';
    
    $str = '';
    if($clickable)
    {
      $str .= '<a href="' . $url . '"';
      if($confirm != '')
      {
        $str .= ' onclick="return ask(\''.$confirm.'\')"';
      }
      $str .= ' title="' . $alt . '">';
    }
    $str .= '<img src="' . $data_path . '/crystal/16-gray/' . $type . '.png" alt="' . $alt . '" title="' . $alt . '" width="16" height="16" class="smallimg" />';
    if($clickable)
    {
       $str .= '</a>';
    }
    return $str;
  }
  
  /**
   * Gibt den Code (<img>-Tag) für ein hochgeladenes Bild zurück
   * @return string
   * @param object $obj User- oder Cluster-Klasse
   * @param string $type Typ der Klasse explizit angeben
   **/
  public static function uploaded_image($obj, $type = '')
  {
    $filename = '';
    if($obj instanceof user || $type == 'user')
    {
      $filename = $obj->avatar_file;
    }
    elseif($obj instanceof cluster || $type == 'cluster')
    {
      $filename = $obj->logofile;
    }
    
    if($filename != '')
    {
      if( file_exists($filename) )
      {
        $img_data = getimagesize($filename);
        return '<img src="' . FORUM_BASEDIR .'/' . $filename . '" ' . $img_data[3] . ' alt="' . htmlentities((isset($obj->name) ? $obj->name : '')) . '" />';
      }
    }
  }
  
  /**
   * Gibt den Code für einen Popup-Link zu einem Hilfe-Eintrag zurück
   * @return string
   **/
  public static function help_popup_link($key, $caption)
  {
    $url = wiki_url($key, 'show', true, 'popup=1', true);
    return '<a href="' . $url . '" onclick="toolwin(\'' . $url .'\',600,400);return false;">' . $caption . '</a>';
  }
}

?>