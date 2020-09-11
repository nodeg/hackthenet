<?php
/**
 * includes/smilie-data.php
 * :P ;)
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.0.1
 * @package     included_files
 * @subpackage  cboard
 **/

if( !defined('IN_BB') ) die('Hacking attempt!');

$smilies = array(
  ':D'=>'biggrin.gif',
  ':O'=>'eek.gif',
  ':?:'=>'confused.gif',
  '8)'=>'cool.gif',
  ':\\\'('=>'cry.gif',
  'X('=>'dead.gif',
  ':lol:'=>'laugh.gif',
  '>:('=>'mad.gif',
  ':|'=>'none.gif',
  ':rolleyes:'=>'rolleyes.gif',
  ':('=>'sad.gif',
  ':)'=>'smile.gif',
  '>:#'=>'upset.gif',
  ';)'=>'wink.gif',
  ':P'=>'razz.gif',
  '(--)'=>'icon_broetchen.gif'
);

if(!function_exists('smilies_replace'))
{
/**
 * Ersetzt Symbole durch IMG-Tags für die Smilies
 * @return string
 * @param string $string Quell-String
 * @param array $smilies Array der Smilies
 **/
function smilies_replace($string, $smilies)
{
  
  foreach($smilies as $symbol=>$file)
  {
    $string = str_replace(htmlspecialchars(stripslashes($symbol)), '<img src="' . FORUM_BASEDIR . '/media/smilies/' . $file . '" />', $string);
  }
  
  return $string;
}
}

?>
