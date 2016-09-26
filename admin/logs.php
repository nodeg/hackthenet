<?php
if ($usr['stat']<1000) 

{

	simple_message('Wir wollen doch nicht hacken?!?');

	exit;

}
$do=$_REQUEST['do'];

switch($do) 
{
case '';
echo '<div class="content" id="register">
  <h2>Logauswertung</h2>
  <div id="subnet">';
 
  echo '<h4>Logauswertung von HackTheNet</h4>';
   echo'
   <table width="50%">
   <tr>
        <th colspan="2">Logarten</th>
   </tr>
   <tr>
      <th>IP</th><td><a href="user.php?a=acp&amp;do=logs&amp;log=ip">HIER</a></td>
   </tr>
   <tr>
      <th>Badlogins</th><td><a href="user.php?a=acp&amp;do=logs&amp;log=badlogin">HIER</a></td>
   </tr>
   <tr>
      <th>Gelöschte Cluster & User</th><td><a href="user.php?a=acp&amp;do=logs&amp;log=deluser">HIER</a></td>
   </tr>
   </table>
   <br><br>';
break;
case 'ip';
echo '<div class="content" id="register">
  <h2>Logauswertung</h2>
  <div id="subnet">';
 
  echo '<h4>Logauswertung von HackTheNet</h4>';
   echo'
   <table width="50%">
   <tr>
        <th colspan="2">IP-Log</th>
   </tr>';
  $sql['logs']=db_query('SELECT * FROM logins');
  while($logs = mysql_fetch_array($sql['logs'])) {
  $sql['login']=db_query('SELECT * FROM logins WHERE ip="'.$logs['ip'].'"');
  while($login = mysql_fetch_array($sql['login'])) {
  if ($logs['user'] != $login['user']) {
  echo '<tr>
      <th>IP ('.$logs['ip'].') genutzt von:</th><td>'.$login['user'].'</td>
  </tr>';
  }
  }
  }
   echo '
   </table>
   <br><br>';
break;
case 'badlogin';
break;
case 'deluser';
break;
}

?>