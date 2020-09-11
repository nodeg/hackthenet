<?php

define('IN_HTN',1);
$FILE_REQUIRES_PC=FALSE;
include('ingame.php');
$javascript='<script language="JavaScript" type="text/javascript">
function choose(s) {
  window.opener.fill(s);
  self.close();
}
</script>';

echo $javascript;
basicheader('HackTheNet - Rundmail',true,false);

echo '<body>
<div id="abook-selpage">
<h2>Bitte W&auml;hlen</h2>
<table cellpadding="0" cellspacing="0" border="0">';

echo '<tr><td width="400">Alle Cluster Mitglieder</td>';
$allclustermembers=mysql_query('SELECT id,name,clusterstat FROM users WHERE cluster="'.$usr['cluster'].'"');
$name='';
while($acm=mysql_fetch_array($allclustermembers)) {
	if (!($acm['id']==$usr['id'])) {
		if ($name=='') { $name.=$acm['name']; }
		else { $name.=','.$acm['name']; }
	}
}
echo '<td><input type="button" value="W&auml;hlen" onclick="choose(\''.$name.'\')"></td></tr>';

echo '<tr><td width="400">Alle Cluster Admins</td>';
//$allclustermembers=mysql_query('SELECT id,name FROM users WHERE cluster="'.$usr['cluster'].'" && clusterstat="1000"');
$name='';
while($acm=mysql_fetch_array($allclustermembers)) {
	if ($acm['clusterstat']==1000) {
		if (!($acm['id']==$usr['id'])) {
			if ($name=='') { $name.=$acm['name']; }
			else { $name.=','.$acm['name']; }
		}
	}
}
echo '<td><input type="button" value="W&auml;hlen" onclick="choose(\''.$name.'\')"></td></tr>';

echo '<tr><td width="400">Alle Cluster Lite-Admins</td>';
//$allclustermembers=mysql_query('SELECT id,name FROM users WHERE cluster="'.$usr['cluster'].'" && clusterstat="900"');
$name='';
while($acm=mysql_fetch_array($allclustermembers)) {
	if ($acm['clusterstat']==900) {
		if (!($acm['id']==$usr['id'])) {
			if ($name=='') { $name.=$acm['name']; }
			else { $name.=','.$acm['name']; }
		}
	}
}
echo '<td><input type="button" value="W&auml;hlen" onclick="choose(\''.$name.'\')"></td></tr>';

echo '<tr><td width="400">Alle Cluster Warlords</td>';
//$allclustermembers=mysql_query('SELECT id,name FROM users WHERE cluster="'.$usr['cluster'].'" && clusterstat="90"');
$name='';
while($acm=mysql_fetch_array($allclustermembers)) {
	if ($acm['clusterstat']==90) {
		if (!($acm['id']==$usr['id'])) {
			if ($name=='') { $name.=$acm['name']; }
			else { $name.=','.$acm['name']; }
		}
	}
}
echo '<td><input type="button" value="W&auml;hlen" onclick="choose(\''.$name.'\')"></td></tr>';

echo '<tr><td width="400">Alle Cluster Konventionisten</td>';
//$allclustermembers=mysql_query('SELECT id,name FROM users WHERE cluster="'.$usr['cluster'].'" && clusterstat="80"');
$name='';
while($acm=mysql_fetch_array($allclustermembers)) {
	if ($acm['clusterstat']==80) {
		if (!($acm['id']==$usr['id'])) {
			if ($name=='') { $name.=$acm['name']; }
			else { $name.=','.$acm['name']; }
		}
	}
}
echo '<td><input type="button" value="W&auml;hlen" onclick="choose(\''.$name.'\')"></td></tr>';

echo '<tr><td width="400">Alle Cluster Entwicklungsminister</td>';
//$allclustermembers=mysql_query('SELECT id,name FROM users WHERE cluster="'.$usr['cluster'].'" && clusterstat="70"');
$name='';
while($acm=mysql_fetch_array($allclustermembers)) {
	if ($acm['clusterstat']==70) {
		if (!($acm['id']==$usr['id'])) {
			if ($name=='') { $name.=$acm['name']; }
			else { $name.=','.$acm['name']; }
		}
	}
}
echo '<td><input type="button" value="W&auml;hlen" onclick="choose(\''.$name.'\')"></td></tr>';

echo '<tr><td width="400">Alle Cluster Mitgliederminister</td>';
//$allclustermembers=mysql_query('SELECT id,name FROM users WHERE cluster="'.$usr['cluster'].'" && clusterstat="50"');
$name='';
while($acm=mysql_fetch_array($allclustermembers)) {
	if ($acm['clusterstat']==50) {
		if (!($acm['id']==$usr['id'])) {
			if ($name=='') { $name.=$acm['name']; }
			else { $name.=','.$acm['name']; }
		}
	}
}
echo '<td><input type="button" value="W&auml;hlen" onclick="choose(\''.$name.'\')"></td></tr>';

echo '<tr><td width="400">Alle Cluster Wächter</td>';
//$allclustermembers=mysql_query('SELECT id,name FROM users WHERE cluster="'.$usr['cluster'].'" && clusterstat="20"');
$name='';
while($acm=mysql_fetch_array($allclustermembers)) {
	if ($acm['clusterstat']==20) {
		if (!($acm['id']==$usr['id'])) {
			if ($name=='') { $name.=$acm['name']; }
			else { $name.=','.$acm['name']; }
		}
	}
}
echo '<td><input type="button" value="W&auml;hlen" onclick="choose(\''.$name.'\')"></td></tr>';

echo '<tr><td width="400">Alle Cluster Jackass</td>';
//$allclustermembers=mysql_query('SELECT id,name FROM users WHERE cluster="'.$usr['cluster'].'" && clusterstat="10"');
$name='';
while($acm=mysql_fetch_array($allclustermembers)) {
	if ($acm['clusterstat']==10) {
		if (!($acm['id']==$usr['id'])) {
			if ($name=='') { $name.=$acm['name']; }
			else { $name.=','.$acm['name']; }
		}
	}
}
echo '<td><input type="button" value="W&auml;hlen" onclick="choose(\''.$name.'\')"></td></tr>';

echo '<tr><td width="400">Alle Cluster Member</td>';
//$allclustermembers=mysql_query('SELECT id,name FROM users WHERE cluster="'.$usr['cluster'].'" && clusterstat="0"');
$name='';
while($acm=mysql_fetch_array($allclustermembers)) {
	if (!($acm['id']==$usr['id'])) {
			if ($name=='') { $name.=$acm['name']; }
			else { $name.=','.$acm['name']; }
	}
}
echo '<td><input type="button" value="W&auml;hlen" onclick="choose(\''.$name.'\')"></td></tr>';
// Only for Admin
if ($usr['stat']==1000) {
	echo '<tr><td width="400">Alle Spieler</td>';
	$allplayers=mysql_query('SELECT id,name FROM users');
	$name='';
	while($acm=mysql_fetch_array($allplayers)) {
		if (!($acm['id']==$usr['id'])) {
			if ($name=='') { $name.=$acm['name']; }
			else { $name.=','.$acm['name']; }
		}
	}
	echo '<td><input type="button" value="W&auml;hlen" onclick="choose(\''.$name.'\')"></td></tr>'; } else {}


	echo '</table>';
	basicfooter();
?>