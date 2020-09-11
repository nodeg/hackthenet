<?php
if ( !defined('IN_HTN') )
{
  die('Hacking attempt');
}
//Einstellungen / Optionen
$error="";$ok="";
$dbpdo = new Dbpdo();
$db = $dbpdo->get_db();
/*
$name['update_int'];
$name['min_attack_def'];
$name['min_incative'];
$name['max_members'];
MAX_USERS_PER_SERVER
SID_ONLINE_TIMEOUT
$name['re_install'];
*/
// Umrechnen in Std
$name['remote_hijack']=($name['remote_hijack']/60/60);

// Prüfen ob Formular versendet wurde
if (isset($_POST['submit']) && $_POST['submit'] == "Speichern")
{ 
 if (isset ($_POST['pc']) && $_POST['pc']!=""){$pc=mysql_escape_string($_POST['pc']);}
  else{$error.="Bitte das Feld Maximale Anzahl der Pc pro Spieler Ausfüllen.<br>"; }
 if (isset ($_POST['cluster']) && $_POST['cluster']!=""){$cluster=mysql_escape_string($_POST['cluster']);} 
  else{$error.="Bitte Option Maximale Members pro Cluster Ausfüllen.<br>"; }
 if (isset ($_POST['remote']) && $_POST['remote']!=""){$remote=mysql_escape_string($_POST['remote']);} 
  else{$error.="Bitte Option Remote Hijack Intervale Ausfüllen.<br>"; } 
 if (isset ($_POST['server']) && $_POST['server']!=""){$server=mysql_escape_string($_POST['server']);} 
  else{$error.="Bitte Option Server Limit Ausfüllen.<br>"; } 
 if (isset ($_POST['online']) && $_POST['online']!=""){$online=mysql_escape_string($_POST['online']);} 
  else{$error.="Bitte Option Online Time Ausfüllen.<br>"; }  
 if (isset ($_POST['style']) && $_POST['style']!=""){$style=mysql_escape_string($_POST['style']);} 
  else{$error.="Bitte Option Standard Layout Ausfüllen.<br>"; }     
 if (isset ($_POST['re_install'])){$style=mysql_escape_string($_POST['re_install']);} 
  else{$error.=""; }  
   if (isset ($_POST['ircserver']) && $_POST['ircserver']!=""){$ircserver=mysql_escape_string($_POST['ircserver']);} 
  else{$error.="Bitte den Standard IRC-Server Eintragen.<br>"; }       
 if ($error=="")
 {
  $sqlup="UPDATE setting SET value=:pc WHERE name='max_pc'";
  $stmt=$db->prepare($sqlup);
  $stmt->bindParam(':pc',$pc);
  $stmt->execute();
  
  $sqlup1="UPDATE setting SET value=:cluster WHERE name='max_members'";
  $stmt=$db->prepare($sqlup1);
  $stmt->bindParam( ':cluster', $cluster);
  $stmt->execute();
  
  $sqlup2="UPDATE setting SET value=:remote WHERE name='remote_hijack'";
  $stmt=$db->prepare($sqlup2);
  $remote=(int)($remote*60*60);
  $stmt->bindParam( ':remote', $remote);
  $stmt->execute();
  
  $sqlup3="UPDATE setting SET value=:server WHERE name='server_limit'";
  $stmt=$db->prepare($sqlup3);
  $stmt->bindParam( ':server', $server);
  $stmt->execute();
  
  $sqlup4="UPDATE setting SET value=:style WHERE name='default_style'";
  $stmt=$db->prepare($sqlup4);
  $stmt->bindParam( ':style', $style);
  $stmt->execute();

  $sqlup5="UPDATE setting SET value=:install WHERE name='re_install'";
  $stmt=$db->prepare($sqlup5);
  $stmt->bindParam( ':install', $install);
  $stmt->execute();
  
  $sqlup6="UPDATE setting SET value=:ircserver WHERE name='irc_server'";
  $stmt=$db->prepare($sqlup5);
  $stmt->bindParam( ':ircserver', $ircserver);
  $stmt->execute();
  
  $ok="Einstellungen wurden gespeichert!";	
  //Daten Aktualisieren
  $sql="SELECT name,value From setting";
  $stmt=$db->prepare($sql);
  $stmt->execute();
  while (($row = $stmt->fetch(PDO::FETCH_OBJ)))
  {
   $name[$row->name]=$row->value;
  }
  $name['remote_hijack']=($name['remote_hijack']/60/60);
 }

}  

?>
<div class="content" id="work">
 <p><?=$error;?><?=$ok;?></p>  
 <h3>Einstellung / Option</h3>

 <FORM action="index.php?m=opt&amp;sid=<?=$sid;?>" method="post" class="lock"> 
  <label class="option">Maximale Anzahl der Pc pro Spieler</label>
  <INPUT name="pc" size="1" value="<?if (isset($name['max_pc'])){echo htmlentities($name['max_pc']);}?>"maxlength="60">
  <label class="rechts">Standard Layout</label>
  <select name="style">
   <option value="standard" <? if (isset($name['default_style']) && $name['default_style']=="standard"){echo"selected";}?> >standard</option>
   <option value="crystal" <? if (isset($name['default_style']) && $name['default_style']=="crystal"){echo"selected";}?>>crystal</option>
   <option value="konsole" <? if (isset($name['default_style']) && $name['default_style']=="konsole"){echo"selected";}?>>konsole</option>
   <option value="anti-ie" <? if (isset($name['default_style']) && $name['default_style']=="anti-ie"){echo"selected";}?>>anti-ie</option>
   <option value="modern" <? if (isset($name['default_style']) && $name['default_style']=="modern"){echo"selected";}?>>modern</option>
  </select>
  <br>
  <label class="option">Maximale Members pro Cluster</label>
  <INPUT name="cluster" size="1" value="<?if (isset($name['max_members'])){echo htmlentities($name['max_members']);}?>" maxlength="60"><br>
  <label class="option">Remote Hijack Intervale (Std)</label>
  <INPUT name="remote" size="1" value="<?if (isset($name['remote_hijack'])){echo htmlentities($name['remote_hijack']);}?>"maxlength="60"><br>
  <label class="option">Maximale Spieler Pro Server</label>
  <INPUT name="server" size="1" value="<?if (isset($name['server_limit'])){echo htmlentities($name['server_limit']);}?>"><br>
  <label class="option">Maximale Spieler Login dauer</label>
  <INPUT name="online" size="1" value="<?if (isset($name['online_time'])){echo htmlentities($name['online_time']);}?>"><br>
  <br>
  <label class="rechts">Neu Installieren?</label>
  <select name="install">
   <option value="true" <? if (isset($name['re_install']) && $name['re_install']=="true"){echo"selected";}?> >Nein</option>
   <option value="" <? if (isset($name['re_install']) && $name['re_install']==""){echo"selected";}?>>Ja</option>
  </select>
  <br>
	<h3>IRC</h3>
	<label class="option">Server:</label>
	<INPUT name="ircserver" size="30" value="<?if (isset($name['irc_server'])){echo htmlentities($name['irc_server']);}?>"><br>
	<br>
  <INPUT type="submit" name="submit" value="Speichern">
 </FORM>

</div>