<?php

/**
* Adressbuch-Verwaltung für Extended Account
**/


define('IN_HTN',1);
$FILE_REQUIRES_PC=FALSE;
include('ingame.php');

if($usr['bigacc']!='yes') 
{
  simple_message('Nur f&uuml;r User mit Extended Account!'); exit; 
}

$action = $_REQUEST['page'];
if($action == '') $action = $_REQUEST['mode'];
if($action == '') $action = $_REQUEST['action'];
if($action == '') $action = $_REQUEST['a'];
if($action == '') $action = $_REQUEST['m'];

switch($action) 
{
  case 'selpage': //------------------------- SELECT PAGE -------------------------------
  
  $javascript='<script language="JavaScript" type="text/javascript">
  function choose(s) 
  {
    window.opener.fill(s);
    self.close();
  }
  </script>';
  basicheader('HackTheNet - Adressbuch',true,false);
  
  echo '<body>
  <div id="abook-selpage">
  <h2>Adressbuch</h2>
  <form name="formular">';
  
  function list_items($g) 
  {
    global $b,$sid,$type,$usrid;
    echo '<table><tr>';
    
    echo '<th>Benutzer</th><th>W&auml;hlen</th></tr>';
    $r = db_query('SELECT u.id AS u_id, u.name AS u_name, a.id AS entry FROM abook_entrys a JOIN users u ON a.remote_user=u.id WHERE a.user=' . $usrid . ' AND a.group=' . $g);
    while($entry = mysql_fetch_object($r))
    {
      echo '<tr><td><a href="user.php?a=info&sid='.$sid.'&user='.$entry->uid.'">'.$entry->u_name.'</a></td>';
      echo '<td><input type="button" value="W&auml;hlen" onclick="choose(\''.str_replace('\'', '\\\'', $entry->u_name).'\')"></td></tr>';
    }
    echo '</table></form>';
  }
  
  echo "\n".'<h3>Gruppe: Allgemein</h3>'; list_items(1);
  echo "\n".'<h3>Gruppe: Cluster</h3>'; list_items(2);
  echo "\n".'<h3>Gruppe: Freunde</h3>'; list_items(3);
  echo "\n".'</div>';
  
  basicfooter();
  
  break;
  
  case 'add': //------------------------- ADD -------------------------------
  $ix = (int)$_REQUEST['user'];
  if($ix==0) $u=getuser($_REQUEST['user'],'name'); else $u=getuser($ix);
  if($u!==false) 
  {
    $g=(int)$_REQUEST['group'];
    if($g<1 || $g>3) $g=1;

    db_query('INSERT INTO `abook_entrys` SET `user`=' . $usrid . ', `group`=' . $g . ', `remote_user`=' . $u['id']);
    
    header('Location: abook.php?sid='.$sid.'&m=admin&saved=1');
  }
  else simple_message('Benutzer inexistent!');
  break;
  
  case 'admin': //------------------------- ADMIN -------------------------------
  createlayout_top('HackTheNet - Adressbuch');
  
  if($_REQUEST['saved']==1) $xxx='<div class="ok"><h3>OK</h3><p>Die &Auml;nderungen wurden &uuml;bernommen!</p></div><br />'."\n";
  echo '<div id="abook-administration" class="content">
  <h2>Adressbuch</h2>
  <h3>Adressbuch verwalten</h3>'.$xxx.'
  <form action="abook.php?action=add&amp;sid='.$sid.'" method="post">
  <table>
  <tr><th colspan="2">Benutzer hinzuf&uuml;gen</th></tr>
  <tr><th>Benutzername:</th><td><input name="user" size="20" maxlength="20" /></td></tr>
  <tr><th>Gruppe:</th><td><select name="group"><option value="1">Allgemein</option><option value=2>Cluster</option><option value=3>Freunde</option></select></td></tr>
  <tr><td colspan="2" align="right"><input type="submit" value="Hinzuf&uuml;gen" /></td></tr>
  </table></form><br />';
  
  function admin_list($ix) 
  {
    global $usrid,$sid;
    eval('$ch'.$ix.'=\' selected\';');
    $r = db_query('SELECT u.id, u.name, a.id AS entry FROM abook_entrys a JOIN users u ON a.remote_user=u.id WHERE a.user=' . $usrid . ' AND a.group=' . $ix);
      if(mysql_num_rows($r) > 0) 
      {
        echo '<form action="abook.php?sid='.$sid.'&amp;m=saveadmin&amp;group='.$ix.'" method="post">
        <table>
        <tr><th>User</th><th>Gruppe</th><th>L&ouml;schen?</th></tr>';
        while($user = mysql_fetch_assoc($r))
        {
          echo '<tr><td width="100"><a href="user.php?a=info&sid='.$sid.'&user='.$user['id'].'">'.$user['name'].'</a></td>';
          echo '<td><select name="group'.$user['entry'].'"><option value="1"'.$ch1.'>Allgemein</option><option value=2'.$ch2.'>Cluster</option><option value=3'.$ch3.'>Freunde</option></select></td>';
          echo '<td><input type="checkbox" value="yes" name="u'.$user['entry'].'" /></td>';
          echo '</tr>';
        }
        echo '<tr><td colspan="3" align="right"><input type="submit" value="Ausf&uuml;hren" /></td></tr></table></form>';
      }
  }
  echo '<h3>Gruppe: Allgemein</h3>'; admin_list(1);
  echo '<h3>Gruppe: Cluster</h3>'; admin_list(2);
  echo '<h3>Gruppe: Freunde</h3>'; admin_list(3);
  echo '</div>';
  createlayout_bottom();
  break;
  
  case 'saveadmin': //------------------------- SAVE ADMIN -------------------------------
  
  $g = (int)$_REQUEST['group'];
  if($g>3 OR $g<1) $g=1;
  
  $r = db_query('SELECT id FROM `abook_entrys` WHERE `user`=' . $usrid . ' AND `group`=' . $g);
  
  while($entry = mysql_fetch_object($r))
  {
    if($_POST['u'.$entry->id] == 'yes')
    {
      db_query('DELETE FROM `abook_entrys` WHERE `user`=' . $usrid . ' AND `id`=' . $entry->id . ' LIMIT 1');
    }
    elseif((int)$_POST['group'.$entry->id] != $g)
    {
      db_query('UPDATE `abook_entrys` SET `group`=' . (int)$_REQUEST['group'.$entry->id] . ' WHERE `user`=' . $usrid . ' AND `id`=' . $entry->id . ' LIMIT 1');
    }
  }
  
  header('Location: abook.php?sid='.$sid.'&m=admin&saved=1');
  break;
  
}

?>