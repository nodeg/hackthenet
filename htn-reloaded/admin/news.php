<?php
if (!defined('IN_HTN'))
{
    die('Hacking attempt');
}
// News
$error = "";
$ok = "";

if (isset($_POST['submit']) && $_POST['submit'] == "Abschicken")
{ 

  $Sqlup="UPDATE news SET titel='".$_POST['titel']."',news='".$_POST['news']."' 
  WHERE id=1";	
  db_query($Sqlup);
 	
}



?>

<div class="content" id="news">
<?php

$get = new get();
$game = new game();

$c = $get->get_news();
echo $game->infobox(nl2br($c['titel']), 'info', nl2br($c['news']), 'id');
?>
<p><?=$error;?><?=$ok;?></p> 
<h3>Aktuelle News</h3>
<form action="index.php?m=news&amp;sid=<?=$sid;?>" method="post">
<table>
<tr><th>Titel:</th><td><INPUT name="titel" size="15" maxlength="60"></td></tr>
<tr><th>News:</th><td><textarea name="news" rows="4" cols="30"></textarea></td></tr>
<tr><th>Aktionen:</th><td><INPUT type="submit" name="submit" value="Abschicken">
</td></tr>
</table>
</form>
</div>

