<?php

if ($_GET['lock'] != "") {
  $ids = explode(",", $_GET['lock']);
  for ($i = 0; $i < count($ids); $i++) {
    db_query('UPDATE `users` SET locked="Multi", sid="", sid_ip="" WHERE id="'.$ids[$i].'"');
  }
  echo "<b>locked</b><br><br>";
} else {
	/**
	 * Liest alle sid_ips aus mit der Anzahl wie oft wie vorkommen.
	 **/
	$result=db_query('SELECT users1.sid_ip AS ip, COUNT("users1.sid_ip") AS anzahl
                      FROM users AS users1, users AS users2
                      WHERE users1.sid_ip = users2.sid_ip && users1.locked="" && users1.sid_ip!=""
                      GROUP BY users1.sid_ip');
	
	$multis=array();	
	while ($m=mysql_fetch_assoc($result)) {
		if ($m['anzahl']>1) { 
			$without_proxy=explode(" ", $m['ip']);
			$m_result=db_query('SELECT id,name,email,cluster,points FROM users WHERE sid_ip="'.$m['ip'].'" || sid_ip="'.$without_proxy[0].'"'); 
			if (mysql_num_rows($m_result)>1) {
				$user=''; $userid=array(); $ns=false;
				while ($multi=mysql_fetch_assoc($m_result)) {
					if (is_norankinguser($multi['id'])) { $ns=true; }
					$user.="<tr><td><a href=\"?a=info&user=".$multi['id']."&sid=".$sid."\" target='_blank'>".$multi['name']."</a></td><td>(".$multi['email'].")</td><td>".$multi['cluster']."</td><td>".$multi['points']."</td><td><a href=\"?a=multi&lock=".$multi['id']."&sid=".$sid."\">[sperren]</a></td></tr>";
					$userid[$m['sid_ip']][$multi['id']]=$multi['id'];
				}
				if ($ns!=true) { echo "IP: <b> ".$m['ip']." </b> User: <br><table><tr><th>Name</th><th>Email</th><th>Cluster</th><th>Punkte</th><th></th></tr>".$user."</b> <tr><td colspan='2'><a href=\"user.php?a=multi&lock=".joinex($userid[$m['sid_ip']], ',', true, true)."&sid=".$sid."\">Multis sperren</a></td><td>&nbsp;</td><td></td><td></td></tr></table>"; }				
			}
		}
	}
}
?>
