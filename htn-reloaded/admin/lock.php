<?php
if ( !defined('IN_HTN') )
{
  die('Hacking attempt');
}
// User Sperren
$error="";$ok="";$grund="";$locktime=""; 
// Daten Abfragen wenn Formular abgeschickt wurde
if (isset($_POST['submit']) && $_POST['submit'] == "Abschicken")
{ 
 if (isset ($_POST['adminname']) && $_POST['adminname']!=""){$adminname=mysql_escape_string($_POST['adminname']);}	
  else{$error.="Bitte das Feld Gesperrt von Ausfüllen.<br>";}
 if (isset ($_POST['username']) && $_POST['username']!=""){$username=mysql_escape_string($_POST['username']);}	
  else{$error.="Bitte das Feld User Name Ausfüllen.<br>";}
 if (isset ($_POST['locktime']) && $_POST['locktime']!=""){$locktime=(int)(60*60*(mysql_escape_string($_POST['locktime'])));$locktime=(time()+$locktime);}
  else{$error.="Bitte das Feld Dauer der Sperrung Ausfüllen.<br>Angaben in Stunden. 0 für Endlose Sperrung<br>";}
 if (isset ($_POST['grund']) && $_POST['grund']!=""){$grund=mysql_escape_string($_POST['grund']);}
  else{$error.="Bitte das Feld Grund der Gesperrt Ausfüllen.<br>"; }
 if (isset ($_POST['lock']) && $_POST['lock']!=""){$lock=mysql_escape_string($_POST['lock']);}
   	
 if ($lock=="no" && $username!=""){$error="";}
 if ($error=="")
 { // Daten in die Db Schreiben
  $Sqlup="UPDATE users SET locked='".$lock."',locked_till='".$locktime."',locked_by='".$adminname."'
  ,locked_reason='".$grund."' 
  WHERE name='".$username."'";	
  db_query($Sqlup);
  $ok="Daten wurden gespeichert!";
 	
 }
}


?>
<div class="content" id="work">
 <p><?=$error;?><?=$ok;?></p> 
 <h3>User Sperren / Sperrung Aufheben</h3>

 <FORM action="index.php?m=lock&amp;sid=<?=$sid;?>" method="post" class="lock"> 
  <label>Gesperrt von</label>
  <INPUT name="adminname" size="15" value="<?if (isset($adminname)){echo htmlentities($adminname);}?>"maxlength="60"><br>
  <label class="lock">User Name</label>
  <INPUT name="username" size="15" maxlength="60">
  <label>Dauer der Sperrung</label>
  <INPUT name="locktime" size="5" maxlength="60"><br>
  <label>Grund der Sperrung</label><br>
  <TEXTAREA name="grund" rows="5" cols="45" wrap="virtual"><?
  if (isset($grund)){echo htmlentities($grund);}?></TEXTAREA><br>
  <INPUT type="radio" name="lock" value="yes" checked>Sperren
  <INPUT type="radio" name="lock" value="no">Aufheben<br>
  <INPUT type="reset" value="Löschen">
  <INPUT type="submit" name="submit" value="Abschicken">
 </FORM>

</div>