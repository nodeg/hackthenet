<?
// erweiterte Statistik by SofaLord
include('statistik/dataquery.class.php');
mysql_connect($db_host, $db_username, $db_password) OR die(mysql_error());
mysql_select_db(dbname($server)) OR die(mysql_error());

$day = time()-(60*60*24);
 //$day = 0;

// scan anonym
$text[0] = "REMOTE SCAN anonyme";
$sql[0] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'scan' AND success = 1 AND noticed = 0 AND `time` >= '$day' ";
$sql2[0] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'scan' AND success = 1 AND noticed = 0 ";
$sql3[0] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='scan' && attacks.success=1 && attacks.noticed=0 && users.id=attacks.from_usr LIMIT 1;";
// scan erwischt
$text[1] = "REMOTE SCAN nicht anonyme";
$sql[1] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'scan' AND success = 1 AND noticed = 1 AND `time` >= '$day' ";
$sql2[1] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'scan' AND success = 1 AND noticed = 1 ";
$sql3[1] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='scan' && attacks.success=1 && attacks.noticed=1 && users.id=attacks.from_usr LIMIT 1;";
// trojan defacement anonym
$text[2] = "TROJAN DEFACEMENT anonym";
$sql[2] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'trojan' AND `option` = 'defacement' AND success = 1 AND noticed = 0 AND `time` >= '$day' ";
$sql2[2] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'trojan' AND `option` = 'defacement' AND success = 1 AND noticed = 0 ";
$sql3[2] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='trojan' && attacks.option='defacement' && attacks.success=1 && attacks.noticed=0 && users.id=attacks.from_usr LIMIT 1;";
// trojan defacement erwischt
$text[3] = "TROJAN DEFACEMENT nicht anonym";
$sql[3] = "SELECT COUNT( * ) as anzahl  FROM `attacks` WHERE `type` = 'trojan' AND `option` = 'defacement' AND success = 1 AND noticed = 1 AND `time` >= '$day' ";
$sql2[3] = "SELECT COUNT( * ) as anzahl  FROM `attacks` WHERE `type` = 'trojan' AND `option` = 'defacement' AND success = 1 AND noticed = 1 ";
$sql3[3] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='trojan' && attacks.option='defacement' && attacks.success=1 && attacks.noticed=1 && users.id=attacks.from_usr LIMIT 1;";
// trojan transfer anonym
$text[4] = "TROJAN TRANSFER anonym";
$sql[4] = "SELECT COUNT( * ) as anzahl  FROM `attacks` WHERE `type` = 'trojan' AND `option` = 'transfer' AND success = 1 AND noticed = 0 AND `time` >= '$day' ";
$sql2[4] = "SELECT COUNT( * ) as anzahl  FROM `attacks` WHERE `type` = 'trojan' AND `option` = 'transfer' AND success = 1 AND noticed = 0 ";
$sql3[4] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='trojan' && attacks.option='transfer' && attacks.success=1 && attacks.noticed=0 && users.id=attacks.from_usr LIMIT 1;";
// trojan transfer erwischt
$text[5] = "TROJAN TRANSFER nicht anonym";
$sql[5] = "SELECT COUNT( * ) as anzahl  FROM `attacks` WHERE `type` = 'trojan' AND `option` = 'transfer' AND success = 1 AND noticed = 1 AND `time` >= '$day' ";
$sql2[5] = "SELECT COUNT( * ) as anzahl  FROM `attacks` WHERE `type` = 'trojan' AND `option` = 'transfer' AND success = 1 AND noticed = 1 ";
$sql3[5] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='trojan' && attacks.option='transfer' && attacks.success=1 && attacks.noticed=1 && users.id=attacks.from_usr LIMIT 1;";
// trojan deactivate anonym
$text[6] = "TROJAN DEACTIVATE anonym";
$sql[6] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'trojan' AND `option` = '' AND success = 1 AND noticed = 0 AND `time` >= '$day' ";
$sql2[6] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'trojan' AND `option` = '' AND success = 1 AND noticed = 0 ";
$sql3[6] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='trojan' && attacks.option='' && attacks.success=1 && attacks.noticed=0 && users.id=attacks.from_usr LIMIT 1;";
// trojan deactivate erwischt
$text[7] = "TROJAN DEACTIVATE nicht anonym";
$sql[7] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'trojan' AND `option` = '' AND success = 1 AND noticed = 1 AND `time` >= '$day' ";
$sql2[7] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'trojan' AND `option` = '' AND success = 1 AND noticed = 1";
$sql3[7] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='trojan' && attacks.option='' && attacks.success=1 && attacks.noticed=1 && users.id=attacks.from_usr LIMIT 1;";
// trojan SEND ANNA anonym
$text[8] = "TROJAN SEND ANNA anonym";
$sql[8] = "SELECT COUNT( * ) as anzahl  FROM `attacks` WHERE `type` = 'trojan' AND `option` = 'send anna' AND success = 1 AND noticed = 0 AND `time` >= '$day' ";
$sql2[8] = "SELECT COUNT( * ) as anzahl  FROM `attacks` WHERE `type` = 'trojan' AND `option` = 'send anna' AND success = 1 AND noticed = 0 ";
$sql3[8] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='trojan' && attacks.option='send anna' && attacks.success=1 && attacks.noticed=0 && users.id=attacks.from_usr LIMIT 1;";
// trojan SEND ANNA erwischt
$text[9] = "TROJAN SEND ANNA nicht anonym";
$sql[9] = "SELECT COUNT( * ) as anzahl  FROM `attacks` WHERE `type` = 'trojan' AND `option` = 'send anna' AND success = 1 AND noticed = 1 AND `time` >= '$day' ";
$sql2[9] = "SELECT COUNT( * ) as anzahl  FROM `attacks` WHERE `type` = 'trojan' AND `option` = 'send anna' AND success = 1 AND noticed = 1 ";
$sql3[9] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='trojan' && attacks.option='send anna' && attacks.success=1 && attacks.noticed=1 && users.id=attacks.from_usr LIMIT 1;";
// block anonym
$text[10] = "REMOTE BLOCK anonym";
$sql[10] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'block' AND success = 1 AND noticed = 0 AND `time` >= '$day' ";
$sql2[10] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'block' AND success = 1 AND noticed = 0 ";
$sql3[10] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='block' && attacks.success=1 && attacks.noticed=0 && users.id=attacks.from_usr LIMIT 1;";
// block erwischt
$text[11] = "REMOTE BLOCK nicht anonyme";
$sql[11] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'block' AND success = 1 AND noticed = 1 AND `time` >= '$day' ";
$sql2[11] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'block' AND success = 1 AND noticed = 1";
$sql3[11] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='block' && attacks.success=1 && attacks.noticed=1 && users.id=attacks.from_usr LIMIT 1;";
// erfolgreiche smash  cpu anonym
$text[12] = "REMOTE SMASH CPU anonym";
$sql[12] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'smash' AND `option` = 'cpu' AND success = 1 AND noticed = 0 AND `time` >= $day";
$sql2[12] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'smash' AND `option` = 'cpu' AND success = 1 AND noticed = 0 ";
$sql3[12] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='smash' && attacks.option='cpu' && attacks.success=1 && attacks.noticed=0 && users.id=attacks.from_usr LIMIT 1;";
// erfolgreiche smash cpu erwischt
$text[13] = "REMOTE SMASH CPU nicht anonym";
$sql[13] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'smash' AND `option` = 'cpu' AND success = 1 AND noticed = 1 AND `time` >= $day";
$sql2[13] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'smash' AND `option` = 'cpu' AND success = 1 AND noticed = 1";
$sql3[13] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='smash' && attacks.option='cpu' && attacks.success=1 && attacks.noticed=1 && users.id=attacks.from_usr LIMIT 1;";
// erfolgreiche smash  firewall anonym
$text[14] = "REMOTE SMASH FIREWALL anonym";
$sql[14] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'smash' AND `option` = 'firewall' AND success = 1 AND noticed = 0 AND `time` >= $day";
$sql2[14] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'smash' AND `option` = 'firewall' AND success = 1 AND noticed = 0 ";
$sql3[14] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='smash' && attacks.option='firewall' && attacks.success=1 && attacks.noticed=0 && users.id=attacks.from_usr LIMIT 1;";
// erfolgreiche smash firewall erwischt
$text[15] = "REMOTE SMASH FIREWALL nicht anonym";
$sql[15] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'smash' AND `option` = 'firewall' AND success = 1 AND noticed = 1 AND `time` >= $day";
$sql2[15] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'smash' AND `option` = 'firewall' AND success = 1 AND noticed = 1 ";
$sql3[15] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='smash' && attacks.option='firewall' && attacks.success=1 && attacks.noticed=1 && users.id=attacks.from_usr LIMIT 1;";
// erfolgreiche smash  sdk anonym
$text[16] = "REMOTE SMASH SDK anonym";
$sql[16] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'smash' AND `option` = 'sdk' AND success = 1 AND noticed = 0 AND `time` >= $day";
$sql2[16] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'smash' AND `option` = 'sdk' AND success = 1 AND noticed = 0 ";
$sql3[16] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='smash' && attacks.option='sdk' && attacks.success=1 && attacks.noticed=0 && users.id=attacks.from_usr LIMIT 1;";
// erfolgreiche smash sdk erwischt
$text[17] = "REMOTE SMASH SDK nicht anonym";
$sql[17] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'smash' AND `option` = 'sdk' AND success = 1 AND noticed = 1 AND `time` >= $day";
$sql2[17] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'smash' AND `option` = 'sdk' AND success = 1 AND noticed = 1";
$sql3[17] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='smash' && attacks.option='sdk' && attacks.success=1 && attacks.noticed=1 && users.id=attacks.from_usr LIMIT 1;";
// hijack anonym
$text[18] = "REMOTE HIJACK anonyme";
$sql[18] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'hijack' AND success = 1 AND noticed = 0 AND `time` >= '$day' ";
$sql2[18] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'hijack' AND success = 1 AND noticed = 0";
$sql3[18] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='hijack' && attacks.success=1 && attacks.noticed=0 && users.id=attacks.from_usr LIMIT 1;";
// hijack erwischt
$text[19] = "REMOTE HIJACK nicht anonyme";
$sql[19] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'hijack' AND success = 1 AND noticed = 1 AND `time` >= '$day' ";
$sql2[19] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE `type` = 'hijack' AND success = 1 AND noticed = 1 ";
$sql3[19] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.type='hijack' && attacks.success=1 && attacks.noticed=1 && users.id=attacks.from_usr LIMIT 1;";
// erfolgreiche angriffe gesamt
$text[20] = "Erfolgreiche angriffe";
$sql[20] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE success = 1 AND `time` >= $day";
$sql2[20] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE success = 1 ";
$sql3[20] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.success=1 && users.id=attacks.from_usr LIMIT 1;";
// nicht erfolgreiche angriffe gesamt
$text[21] = "Nicht erfolgreiche angriffe";
$sql[21] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE success = 0 AND `time` >= $day";
$sql2[21] = "SELECT COUNT( * ) as anzahl FROM `attacks` WHERE success = 0 ";
$sql3[21] = "SELECT users.name AS name FROM `attacks`, `users` WHERE attacks.success=0 && users.id=attacks.from_usr LIMIT 1;";

$text[22] = "Überwiesene Credits";
$sql[22] = "SELECT SUM( credits ) as anzahl FROM `transfers` WHERE `time` >= '".$day."'";
$sql2[22] = "SELECT SUM( credits ) as anzahl FROM `transfers` ";
$sql3[22] = "SELECT users.name AS name FROM `transfers`, `users` WHERE users.id=transfers.from_usr LIMIT 1;";


$counter = 0;
echo "<table style='margin:0; padding:0;' width='700px'>";
echo "<tr><th>&nbsp;</th><th>in den letzten 24h</th><th>gesamt</th><th>erster User</th>";
foreach($sql as $query)
{
	$scan1 = new dataquery($query);
	if($result=$scan1->fetch())
	{
	    $ausgabe = @mysql_fetch_row(mysql_query($sql2[$counter]));	
	    $ausgabe2 = @mysql_fetch_row(mysql_query($sql3[$counter]));
	    echo "<tr><th>".$text[$counter].":</th><td>".$result['anzahl']."</td><td>".$ausgabe[0]."</td><td>".($ausgabe2[0]=="" ? '<i>noch keiner</i>' : $ausgabe2[0])."</td></tr>";
	    $counter++;
	}
	$scan1->free();
	unset($scan1);
}
echo "</table>";
/*
$sql = "SELECT from_cluster,to_cluster, to_usr, from_usr FROM `attacks` WHERE success = 1 AND `time` >= $day";
$scan2 = new dataquery($sql);
while($result=$scan2->fetch())
{
       $clusterfrom[$result['from_cluster']] = $clusterfrom[$result['from_cluster']] + 1;
       $clusterfrom2[$result['from_cluster']] = $clusterfrom2[$result['from_cluster']] + 1;
       if($result['to_cluster'] > 0) 
       {
          $clusterto[$result['to_cluster']] = $clusterto[$result['to_cluster']] + 1;
          $clusterto2[$result['to_cluster']] = $clusterto2[$result['to_cluster']] + 1;
       }
       $userfrom[$result['from_usr']] = $userfrom[$result['from_usr']] + 1;
       $userfrom2[$result['from_usr']] = $userfrom2[$result['from_usr']] + 1;
       if($result['to_usr'] > 0) 
       {
              $userto[$result['to_usr']] = $userto[$result['to_usr']] + 1;
              $userto2[$result['to_usr']] = $userto2[$result['to_usr']] + 1;
       }
}

asort($clusterfrom);
asort($clusterfrom2);
$anzahl_att = array_pop($clusterfrom);
$clusterfrom2 = array_flip($clusterfrom2);
$sql = "SELECT `name` FROM `clusters` WHERE `id` = '".$clusterfrom2[$anzahl_att]."'";
$scan1 = new dataquery($sql);
if($result=$scan1->fetch())
{
  $statistiken .= "Cluster mit den meisten Angriffen : ".$result['name']." : ".$anzahl_att."\n";
}
$scan1->free();
unset($scan1);

asort($clusterto);
asort($clusterto2);
$anzahl_def = array_pop($clusterto);
$clusterto2 = array_flip($clusterto2);
$sql = "SELECT `name` FROM `clusters` WHERE `id` = '".$clusterto2[$anzahl_def]."'";
$scan1 = new dataquery($sql);
if($result=$scan1->fetch())
{
  $statistiken .= "Cluster das am meisten angegriffen wurde : ".$result['name']." : ".$anzahl_def."\n";
}
$scan1->free();
unset($scan1);

asort($userfrom);
asort($userfrom2);
$anzahl_def = array_pop($userfrom);
$userfrom2 = array_flip($userfrom2);
$sql = "SELECT `name` FROM `users` WHERE `id` = '".$userfrom2[$anzahl_def]."'";
$scan1 = new dataquery($sql);
if($result=$scan1->fetch())
{
  $statistiken .= "User mit den meisten Angriffen : ".$result['name']." : ".$anzahl_att."\n";
}
$scan1->free();
unset($scan1);

asort($userto);
asort($userto2);
$anzahl_def2 = array_pop($userto);
$userto2 = array_flip($userto2);
$sql2 = "SELECT `name` FROM `users` WHERE `id` = '".$userto2[$anzahl_def2]."'";
$scan1 = new dataquery($sql2);
if($result2=$scan1->fetch())
{
  $statistiken .= "User der am meisten angegriffen wurde : ".$result2['name']." : ".$anzahl_def2."\n";
}
$scan1->free();
unset($scan1);



$scan2->free();
unset($scan2);
*/

// wenn man das alle 24 std per email haben will , cronjob drauf und kommentare weg...
// mail("deine mail da rein", "HTN-Statistiken", $statistiken);

?>

