<?



# Serveradmin by BODY-SNATCHER

if ($usr['stat']<1000) 

{

	simple_message('Wir wollen doch nicht hacken?!?');

	exit;

}


if ($_POST['backupserver'] == "1") {

unlink('data/-mysql-backup.txt');
file_put('data/mysql-backup.txt','1');
echo 'Server für Wartungsarbeiten gesperrt.';
}
if ($_POST['stopserver'] == "1") {
$start_h = $_POST['start_t'] + 16;
$start_t = $_POST['stop_t'];
while ( $start_h >= 24 ) {
$start_h = $start_h - 24;
$start_t = $start_t + 1;
}

$stoptime = mktime(16, 0, 0, $_POST['stop_m'], $_POST['stop_t'], $_POST['stop_j']);
$starttime = mktime($start_h, 0, 0, $_POST['stop_m'], $start_t, $_POST['stop_j']);

unlink('data/-serverstop.txt');
file_put('data/serverstop.txt',''.$stoptime.'');
unlink('data/-newround.txt');
file_put('data/newround.txt',''.$starttime.'');
echo 'Server für eine neue Runde vorbereitet, du hast '.$_POST['start_t'].' Stunden zeit um die Änderungen durchzuführen.';

}

echo '

<div id="settings-settings"><h3>Server Optionen</h3>
<p>
<form action="user.php" method="POST">

<input type="hidden" name="a" value="server">

<input type="hidden" name="sid" value='.$sid.'>

<p><b>Sollen Serverarbeiten angekündigt werden?</b></p>

<p><input type="radio" value="1" name="backupserver">Ja<br /></p>

<p><input type="submit" value="Los" name="baschick"><input type="reset" value="Zurücksetzen" name="B3">
</form>
</p>
<p>
<form action="user.php" method="POST">

<input type="hidden" name="a" value="server">

<input type="hidden" name="sid" value='.$sid.'>

<p><b>Eine neue Runde gestartet werden?</b></p>
<p>Ab Wann?<br /></p>
<p>Tag <input type="text" name="stop_t"> Monat <input type="text" name="stop_m"> Jahr <input type="text" name="stop_j"><br /></p>
<p>Wieviele Stunden sollen bis zum Start vergehen? <input type="text" name="start_t"><br /></p>
<p><input type="radio" value="1" name="stopserver">Ja<br /></p>
<p><input type="submit" value="Los" name="baschick"><input type="reset" value="Zurücksetzen" name="B3">
</form>
</p>
</div>
';





mysql_close();



?>