<?php 
/***
* Template Klasse
* @version 1.1 
* @author Splasch
**/ 
class Template
{
   protected $template;  /* Template Inhalt */ 
   protected $templateDir;	// Der Ordner in dem sich die Template-Dateien befinden.
   
/**
* Klassen aufruf - Lade Template file von $path
* @param Str $path
*/
public function __construct($path = '')
{
 if (file_exists($path) == false)
  {
   throw new Exception ('Template File nicht gefunden path: `' . $path . '`');
  }
 if ($tpl = file_get_contents($path))
  {
    $this->templateDir=dirname($path)."\\";
    $this->replace($tpl); 
  }
}

/**
* replace() - Ersetzt platzhalter für includierte Files und Kommentaren
* @param Str $str enthält der Template
* @return String
*/
protected function replace($str)
{// Includes ersetzen ( {include="..."} )
 $liste=null;
 while(preg_match_all("/".'{'."include=\"(.*)\"".'}'."/isUe", $str,$file,PREG_PATTERN_ORDER))
 {
  foreach ($file[1] as $datei)
  { 
   try
   {
  	$file=$this->templateDir.$datei.".tpl";
  	$liste.=$file.", ";
  	if(file_exists($file) == false)
  	{ 
  	 throw new Exception();
   	}
   }
   catch(Exception $e)
   {
  	die("Template File nicht gefunden path:<b>\"".$file."\"</b><br>"
  	.$e->getFile().$e->getLine().$e->getTraceAsString()
  	."<br><b>Ladereihenfolge der Templatefiles: </b>".$liste);
   }
  } // ende foreach
  $str = preg_replace("/".'{'."include=\"(.*)\"".'}'."/isUe", "file_get_contents(\$this->templateDir.'\\1'.'.'.'\\2'.'tpl')", $str);
 } // ende while
 // Kommentare löschen
 $str = preg_replace("/".'\{\*'."(.*)".'\*\}'."/isUe", "", $str);

 $this->template=$str;
}

/**
* __set() - Ersetze den Platzhalter mit dem Inhalt der Variable $replacement
* @param Str $placeholder
* @param Str $replacement
*/
public function __set($placeholder, $replacement)
{   
 $this->template = str_replace('{$'.$placeholder.'}', $replacement, $this->template);
 # Wenn Include im Template ist neu Parsen
 if (preg_match("/".'{'."include=\"(.*)\"".'}'."/isUe", $this->template))
 {
  $this->replaceLanguage($this->template); 
 } //ende preg_match
} //ende Set

/**
* Ersetze Sprachkonstante Variablen mit einen Text
* @param string $lang
*/

function replaceLanguage($lang)
{
 $this->template = preg_replace("/\{L_(.*)\}/isUe", "\$lang[strtolower('\\1')]", $this->template);
  $this->replace($this->template); 
}


/**
* getTpl() - Gibt den Template Inhalt zurück
* @return String
*/
public function getTpl()
{
  return $this->template;
}

} // Klasse ende
?>  