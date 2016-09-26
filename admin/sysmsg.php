<?php

# System Message coded by BODY-SNATCHER

if ($usr['stat']<1000) 

{

	simple_message('Wir wollen doch nicht hacken?!?');

	exit;

}

mysql_connect($db_host, $db_username, $db_password);

mysql_select_db(dbname($server));


if ($_POST['new'] == "1") {

$sql = mysql_query('SELECT id FROM `users` ');
while($row = mysql_fetch_array($sql)) {
mysql_query("INSERT INTO sysmsgs(msg, user, time, text, xread) values ('', '".$row['id']."', '".time()."', '".$_POST['text']."', 'no')");
$result=db_query('SELECT mail FROM mails WHERE user=\''.$row['id'].'\' AND box=\'in\' AND xread=\'no\'');
$anz=mysql_num_rows($result);
$result=db_query('SELECT msg FROM sysmsgs WHERE user=\''.$row['id'].'\' AND xread=\'no\'');
$anz2=mysql_num_rows($result);
db_query('UPDATE users SET newmail=\''.$anz.','.$anz2.'\' WHERE id=\''.$row['id'].'\';');
}

}




echo '
<div class="content" id="messages">
<h2>Systemmessage</h2>
<div id="messages-compose">
<h3>Mail verfassen</h3>
<form action="user.php?a=sysmsg&amp;sid='.$sid.'" method="post">
<input type="hidden" name="new" value="1">
<table>
<tr id="messages-compose-recipient">
<th>Empf&auml;nger:</th>

<td>Alle User</td>
</tr>
<tr id="messages-compose-text">
<th>Text:</th>
<td><textarea name="text" rows="5" cols="50"></textarea></td>
</tr>
<tr id="messages-compose-confirm">
<td colspan="2"><input type="submit" value="Absenden" /></td>
</tr>
</table>

</form>
</div>
</div>
';
mysql_close();

?>