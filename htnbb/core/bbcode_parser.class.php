<?php
/**
 * includes/bbcode_parser.class.php
 * Klasse, die BBCode durch HTML ersetzt.
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     1.1.0.0
 **/

if(!defined('IN_BB')) die('Hacking attempt!');

/**
 * Klasse, die BBCode durch HTML ersetzt.
 * @package     included_files
 * @subpackage  classes
 **/
final class bbcode_parser
{
  
  /**
   * Ersetzt URLs im Text durch Hyperlinks.
   * @link http://php.net/function.preg-replace.php
   * @param string $text Text in dem die Links ersetzt werden sollen
   **/
  private static function insert_links($text)
  {
    //  First match things beginning with http:// (or other protocols)
    $NotAnchor = '(?<!"|href=|href\s=\s|href=\s|href\s=)';
    $Protocol = '(http|ftp|https):\/\/';
    $Domain = '[\w]+(.[\w]+)';
    $Subdir = '([\w\-\.,@\?\^=%&:\/~\+#]*[\w\-\@\?\^=%&\/~\+#])?'; # (?!&quot;|&gt;|&lt;|&amp;)
    
    #$text = preg_replace('~(http://.+?)(\s|$)~i', '[link=$1]$1[/link]$2', $text);
    
    $Expr = '/\[link\=(' . $NotAnchor . $Protocol . $Domain . $Subdir . ')\]([^\[\]]+?)\[\/link\]/i';
    $text = preg_replace_callback($Expr, create_function('$match', 'return \'<a href="\' . dereferer($match[1], false) . \'" onclick="window.open(this.href);return false;">\' . $match[5] . \'</a>\';'), $text);
    
    $Expr = '/' . $NotAnchor . $Protocol . $Domain . $Subdir . '/i';
    $text = preg_replace_callback($Expr, create_function('$match', 'return \'<a href="\' . dereferer($match[0], false) . \'" onclick="window.open(this.href);return false;">\' . $match[0] . \'</a>\';'), $text);
    
    return $text;
  }
  
  /**
   * Hilfsfunkion zur XHTML-Konformen Ersetzung von BB-Code-Tags. Falsch verschachtelte Tags werden ignoriert
   * @param string $text Text in dem der BBCode ersetzt werden soll
   * @author Eckhart Wörner / umgestellt von Ingmar
   **/
  private static function xhtml_bb_code($text)
  {
    $expr = '/^(.*?)\[(b|i|u|s|code|box:.{3}|quote:.{3})\](.+?)\[\/\2\](.*)$/is';
    $anzahl = preg_match($expr, $text, $a);
    if($anzahl == 0)
    {
      return $text;
    }
    
    $resultat = $a[1];
    
    $replacements = array(
      'b'=>array('before'=>'<strong>', 'after'=>'</strong>'),
      'i'=>array('before'=>'<em>', 'after'=>'</em>'),
      'u'=>array('before'=>'<span style="text-decoration: underline;">', 'after'=>'</span>'),
      's'=>array('before'=>'<del>', 'after'=>'</del>'),
      'code'=>array('before'=>'<code>', 'after'=>'</code>')
    );
    
    if(isset($replacements[$a[2]]))
    {
        $resultat .= $replacements[$a[2]]['before'] . bbcode_parser::xhtml_bb_code($a[3]) . $replacements[$a[2]]['after'];
    }
    elseif(strtolower(substr($a[2], 0, 4)) == 'box:')
    {
      $resultat .= '<div class="bbbox">' . bbcode_parser::xhtml_bb_code($a[3]) . '</div>';
    }
    elseif(strtolower(substr($a[2], 0, 6)) == 'quote:')
    {
      $resultat .= '<blockquote class="bb">' . bbcode_parser::xhtml_bb_code($a[3]) . '</blockquote>';
    }
    
    $resultat .= bbcode_parser::xhtml_bb_code($a[4]);
    return $resultat;
  }
  
  private static function tag_sort_callback($par1, $par2)
  {
    if($par1['pos'] == $par2['pos'])
    {
      return 0;
    }
    if($par1['pos'] < $par2['pos'])
    {
      return -1;
    }
    else
    {
      return 1;
    }
  }
  
  /**
   * Ersetzt BBCode im Text.
   * @param string $string Text in dem der BBCode ersetzt werden soll
   **/
  public static function parse($string)
  {
    
    $old_string = $string; // Original-String sichern!
    
    // BEGINN DER EINSETZUNG DER BOX/QUOTE-TAGS
    
    // Erstmal :000 anhängen, damit sich die Länge des Strings und damit die vorher gespeicherten
    // Positionen der Tags nicht verändern
    $string = preg_replace('/\[(box|quote|\/box|\/quote)\]/', '[$1:000]', $string);
    
    // Hier kommen alle boxs und quotes rein =)
    $tags = array();
    
    // Alle gültigen Tags rausfischen ...
    #$r = preg_match_all('/\[(box:.{3}|quote:.{3}|\/box:.{3}|\/quote:.{3})\]/', $string, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
    $r = preg_match_all('/\[((?:box|quote|\/box|\/quote):[a-z0-9]{3})\]/i', $string, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
    
    foreach($matches as $match)
    {
      #print_r($match);
      // ... und in ein Array in brauchbarer Form einordnen.
      $tags[] = array(
        'tag' => $match[1][0],
        'pos' => $match[0][1]);
    }
    
    // Sortieren.
    // @see bbcode_parser_tag_sort_callback()
    usort($tags, array('bbcode_parser', 'tag_sort_callback'));
    
    $tags_open = 0; // Anzahl der offenen Tags
    $stack = array(); // Die offenen Tags werden hier drin abgelegt und nachdem LiFo-Prinzip (LastIn-FirstOut) wieder rausgenommen
    foreach($tags as $tag)
    {
      
      if($tag['tag']{0} != '/') // Ein Anfangstag gefunden!
      {
        $tags_open++;
        if($tags_open > 10)
        {
          // Mehr als 10 box/qoute - Tags/Paare ineinander verschachtelt -> Abbruch!
          return 'Fehler, zu viele Tags verschachtelt!';
        }
        // Das gefundene Tag dem Stack hinzufügen und mit einem eindeutigen Schlüssel markieren:
        array_push($stack, array('key'=>random_string(3), 'starttagpos'=>$tag['pos'], 'tag'=>$tag['tag']));
      }
      elseif($tags_open > 0) // Ein Endtag gefunden!
      {
        
        $tags_open--; // Ein Tag weniger offen
        $tag_pair = array_pop($stack); // Letztes hinzugefügtes Element aus Stack nehmen
        $tag_pair['endtagpos'] = $tag['pos']; // $tag ist ja jetzt das Endtag
        
        $tag_pair['tag'] = substr($tag_pair['tag'], 0, strpos($tag_pair['tag'], ':')); // :000 entfernen
        
        $left = substr($string, 0, $tag_pair['starttagpos']); // was steht links des Starttags?
        // Rechts des Tags alles rausnehmen:
        // +4 wegen strlen(':000'), +2 wegen [ und ]
        $right = substr($string, $tag_pair['starttagpos'] + strlen($tag_pair['tag']) + 4 + 2);
        // Mit neuem Tag wieder zusammenbasteln:
        $string = $left . '['.$tag_pair['tag'].':'.$tag_pair['key'].']' . $right;
                
        $left = substr($string, 0, $tag_pair['endtagpos']); // was steht links des Endtags?
        // Rechts des Tags alles rausnehmen:
        // +4 wegen strlen(':000'), +3 wegen [ und / und ]
        $right = substr($string, $tag_pair['endtagpos'] + strlen($tag_pair['tag']) + 4 + 3);
        // Mit neuem Tag wieder zusammenbasteln:
        $string = $left . '[/'.$tag_pair['tag'].':'.$tag_pair['key'].']' . $right;
               
      }
      
    }
    
    // Nicht ersetzte Tags die durch fehlende Start/End-Tags entstehen, entfernen:
    $string = preg_replace('/\[(box:000|quote:000|\/box:000|\/quote:000)\]/i', '', $string);
    
    // ENDE DER EINSETZUNG DER BOX/QUOTE-TAGS
    
    // Normale Tags (b, i, usw.) einsetzen:
    $string = bbcode_parser::xhtml_bb_code($string);
  
    // URLs in Links wandeln:
    $string = bbcode_parser::insert_links($string);
    $string = preg_replace('/&(amp|lt|gt|quot)<\/(\w+)>;/i', '</\\2>&\\1;', $string); # workaround für so nen FUCK!!
        
    // Links auf Spieler / Cluster-Profile einsetzen:
    $profil_link_tags = NULL;
    
    $string = preg_replace_callback('/\[\[([a-zA-Z0-9-_äöüßÄÖÜ\@\.\(\) \:]*?)(?:\|(.*?))?\]\]/i', create_function('$match',
      '$link_title = trim($match[1]); ' .
      '$link_target = $link_title; ' .
      'if(!empty($match[2])) $link_title = trim($match[2]); ' .
      'return \'<a class="extlink2wiki" href="\' . htmlspecialchars(wiki_url($link_target)) . \'">\' . $link_title . \'</a> (im Wiki)\';'), $string);
    
    // Einrückungen HTML-Tauglich machen:
    while(strpos($string, '  ') !== false)
    {
      $string = str_replace('  ', '&nbsp; ', $string);
      $string = str_replace("\t", '&nbsp; ', $string);
    }
    
    // Auf Korrektheit prüfen:
    
    // erstmal Entitäten entfernen (weil der XML-Parser keine HTML-Entities kennt):
    #$string = preg_replace('/&(\#?\w+);/i', '--ENT--\\1--', $string);
    
    #echo '<pre>' . htmlspecialchars($string) . '</pre>';
    
    $xml_parser = xml_parser_create('ISO-8859-1');
    $xml = '<'.'?'.'xml version="1.0" encoding="ISO-8859-1"'.'?'.'><!DOCTYPE none [
   <!ENTITY nbsp "&#160;" >
   <!ENTITY lt "&#60;" >
   <!ENTITY gt "&#62;" >
   <!ENTITY quot "&#34;" >
]>'.LF.'<bb2htmlcode>'.LF.$string.LF.'</bb2htmlcode>';
    $tmp = xml_parse($xml_parser, $xml);
    
    #$string = '<pre>'.htmlspecialchars($xml).'</pre>';
    #return $string;
    
    if(!$tmp)
    {
      // Fehler aufgetreten!
      $string = '<strong style="color:red;">Der Text enthält Fehler wie z.B. falsch verschachtelte BB-Code-Tags.'.
        '<br />Er wird deshalb unformatiert dargestellt:</strong><br /><br />';
      $string .= '<code style="color:blue;">'.htmlspecialchars(xml_error_string(xml_get_error_code($xml_parser)).' = '.substr($xml, xml_get_current_byte_index($xml_parser)-20, 40)).'</code><br /><br />';

      $string .= preg_replace('/\[(\/?)(b|i|u|s|code|box|quote|user|cluster|link\=)\]/is', ' ', $old_string);
    }
    
    #$string = '<pre>'.htmlspecialchars($xml).'</pre>';
    unset($xml);
    xml_parser_free($xml_parser);
    
    // Entfernte Entitäten wieder hinzufügen:
    #$string = preg_replace('/--ENT--(\#?\w+)--/i', '&\\1;', $string);
    
    unset($old_string);
    
    $string = str_replace('</div><br />', '</div>', $string);
    
		return $string;
  }
  
  
  
}

?>