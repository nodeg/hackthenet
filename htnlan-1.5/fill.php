<?php

include('data/static/country_data.inc.php');
if (!$x) {
	$x = $computers_to_fill;	
}
$dbcon=mysql_connect($db_host, $db_username, $db_password);
$dbsel=mysql_select_db($database_prefix.$database_suffix);

if(!$dbcon) die('Datenbankzugriff gescheitert! Bitte nochmal probieren.');
if(!$dbsel) die('Datenbankzugriff gescheitert! Bitte nochmal probieren.');
foreach($countrys as $i => $j) {
   for ($y=1; $y<=$x; $y++) {

   	  $land=$countrys[$i]['id'];
      $iip=$countrys[$i]['subnet'];
      $lip=$iip.".".$y;

      $sqll=mysql_query("SELECT id FROM pcs WHERE ip='$lip'");
      if (mysql_num_rows($sqll) >= 1) { $x++; }
      else {
      $cpurand =  mt_rand() % 21 + 1; #CPU-Wert Berechnung
      $ramrand = mt_rand() % 9 + 1;  #RAM-Wert Berechnung

      $lanrand = mt_rand() % 10 + 1;   #LAN-Wert Berechnung
      $moneyrand = mt_rand() % 10 + 1;  #MoneyMarket-Wert Berechnung
      $bucksbunkerrand = mt_rand() % 10 + 1;  #BucksBunker-Wert Berechnung
      if ($cpurand >= "6" AND $ramrand >= "2") { $firewallrand= mt_rand() % 10 + 1; #FireWall-Wert Berechnung
      } else { $firewallrand= "0"; }
      if ($cpurand >= "8" AND $ramrand >= "2") { $sdkrand= mt_rand() % 5 + 1; #SDK-Wert Berechnung
      } else { $sdkrand= "0"; }
      if ($cpurand >= "12" AND $sdkrand >= "3") { $malwarerand= mt_rand() % 10 + 1; #MalwareKit-Wert Berechnung
      } else { $malwarerand= "0"; }
      if ($cpurand >= "10" AND $ramrand >= "3") { $antivirusrand = mt_rand() % 10 + 1; #AntiVirus-Wert Berechnung
      } else { $antivirusrand = "0"; }
      if ($cpurand >= "15" AND $sdkrand >= "3") { $IDSrand = mt_rand() % 10 + 1; #IDS-Wert Berechnung
      } else { $IDSrand = "0"; }
      if ($cpurand >= "8" AND $sdkrand >= "2") { $ipspoofingrand = mt_rand() % 10 + 1; #IPS-Wert Berechnung
      } else { $ipspoofingrand = "0"; }
      if ($cpurand >= "18" AND $sdkrand >= "5" AND $malwarerand >= "10" AND $ramrand >= "7") { $hjrand = mt_rand() % 10 + 1; #HiJack-Wert Berechnung */
      } else { $hjrand = "0"; }

      $onlinewerbungrand = mt_rand() % 5 + 1; #OnlineWerbung-Wert Berechnung
      if ($moneyrand >= "4") { $dialerrand = mt_rand() % 5 + 1; #Dialer-Wert Berechnung
      } else { $dialerrand = "0"; }
      if ($moneyrand >= "8") { $auktionsbetrugrand = mt_rand() % 5 + 1; #AuktionsBetrug-Wert Berechnung
      } else { $auktionsbetrugrand = "0"; }
      if ($moneyrand >= "10") { $bankhackrand = mt_rand() % 5 + 1; #BankHacken-Wert Berechnung
      } else { $bankhackrand = "0"; }
      if ($malwarerand >= "4" AND $ramrand >= "4") { $trojanerrand = mt_rand() % 5 + 1; #Trojaner-Wert Berechnung
      } else { $trojanerrand="0"; }

      #<------------ Punkteberechnung ------------>
      $pcpu = $cpurand * 10;
      $pram = $ramrand * 10;
      $pmm =   3 * pow(1.408659,$moneyrand);
      $pbb = 3 * pow(1.408659,$bucksbunkerrand);
      $plan = 3 * pow(1.408659, $lanrand);
      $pfw = 3 * pow(1.408659,$firewallrand);
      $pmk = 3 * pow(1.408659,$malwarerand);
      $pav = 3 * pow(1.408659,$antivirusrand);
      $psdk = 3 * pow(1.408659,$sdkrand);
      $pips = 3 * pow(1.408659,$ipspoofingrand);
      $pids = 3 * pow(1.408659,$IDSrand);

      $ppc = ($pcpu + $pram + $pmm + $pbb + $plan + $pfw + $pmk + $pav + $psdk + $pips + $pids) - 31;
      $ppc = round($ppc,0);

      $sql=mysql_query("INSERT INTO pcs(name, ip, owner, owner_name, owner_points, owner_cluster, owner_cluster_code, cpu, ram, lan, mm, bb, ads, dialer, auctions, bankhack, fw, mk, av, ids, ips, rh, sdk, trojan, credits, lmupd, country, points)
            values('NoName', '$lip', '', '', '0', '0', '', '$cpurand', '$ramrand', '$lanrand', '$moneyrand', '$bucksbunkerrand', '$onlinewerbungrand', '$dialerrand', '$auktionsbetrugrand', '$bankhackrand', '$firewallrand', '$malwarerand', '$antivirusrand', '$IDSrand', '$ipspoofingrand', '$hjrand', '$sdkrand', '$trojanerrand', '$crand', '".time()."', '".$land."', '$ppc')"); 

         if ($sql) { $counter++; } 
         if (!$sql) { $fehlercounter++; }
      }
   }
}

echo "Es wurden ".$counter. " Computer hinzugefügt";

?>