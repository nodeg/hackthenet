<?php
if ( !defined('IN_HTN') )
{
  die('Hacking attempt');
}
// Wartungs Arbeiten
$grund="Im Moment wird am Server gearbeitet.
Bitte probiere es doch später noch einmal.";
$error="";$ok="";

// Prüfen ob Formular versendet wurde
if (isset($_POST['submit']) && $_POST['submit'] == "Speichern")
{ 
 if (isset ($_POST['grund']) && $_POST['grund']!=""){$grund=mysql_escape_string($_POST['grund']);}
  else{$error.="Bitte das Feld Grund der Wartung Ausfüllen.<br>"; }
 if (isset ($_POST['wartung']) && $_POST['wartung']!=""){$wartung=($_POST['wartung']);} 
  else{$error.="Bitte Option Wartung Ein/Aus Auswählen.<br>"; }
 if ($error=="" && $wartung!="" )
 {
  //Wartung ein
  if ($wartung=="yes"){@rename('../data/-work.txt','../data/work.txt');} 	
  //Wartung aus
  if ($wartung=="no"){@rename('../data/work.txt','../data/-work.txt');} 
  $ok="Einstellung wurde gespeichert!(Wartung:".htmlentities($wartung).")";		
 }
}   	

?>
<div class="content" id="work">
 <p><?=$error;?><?=$ok;?></p> 
  <h2>Serverarbeiten / Wartungs Modus</h2>
  <FORM action="index.php?m=wart&amp;sid=<?=$sid;?>" method="post" class="lock"> 
  <label>Grund der Wartung / Serverarbeiten</label><br>
  <TEXTAREA name="grund" rows="5" cols="45" wrap="virtual"><?
  if (isset($grund)){echo zeile(strip_tags($grund));}?></TEXTAREA><br>
  <INPUT type="radio" name="wartung" value="yes" >Einschalten
  <INPUT type="radio" name="wartung" value="no">Auschalten<br>
  <INPUT type="submit" name="submit" value="Speichern">
 </FORM>
  </div>
<?


