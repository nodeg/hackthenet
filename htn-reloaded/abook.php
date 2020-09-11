<?php
session_start();
/**
 * Adressbuch-Verwaltung für Extended Account
 **/


define('IN_HTN', 1);
$FILE_REQUIRES_PC = false;
include ('ingame.php');

$gres = new gres();
$abook = new abook();
$dbc = new dbc();

if ($usr['bigacc'] != 'yes')
{
    $gres->simple_message('Nur f&uuml;r User mit Extended Account!');
    exit;
}

$action = $_REQUEST['page'];
if ($action == '')
    $action = $_REQUEST['mode'];
if ($action == '')
    $action = $_REQUEST['action'];
if ($action == '')
    $action = $_REQUEST['a'];
if ($action == '')
    $action = $_REQUEST['m'];

switch ($action)
{
    case 'selpage': //------------------------- SELECT PAGE -------------------------------

        $javascript = '<script language="JavaScript" type="text/javascript">
  function choose(s) 
  {
    window.opener.fill(s);
    self.close();
  }
  </script>';
        $layout->basicheader('HackTheNet - Adressbuch', true, false);

        echo '<body>
  <div id="abook-selpage">
  <h2>Adressbuch</h2>
  <form name="formular">';

        echo "\n" . '<h3>Gruppe: Allgemein</h3>';
        $abook->list_items(1);
        echo "\n" . '<h3>Gruppe: Cluster</h3>';
        $abook->list_items(2);
        echo "\n" . '<h3>Gruppe: Freunde</h3>';
        $abook->list_items(3);
        echo "\n" . '</div>';

        $layout->basicfooter();

        break;

    case 'add': //------------------------- ADD -------------------------------
        $ix = (int)$_REQUEST['user'];
        if ($ix == 0)
            $u = $get->get_user($_REQUEST['user'], 'name');
        else
            $u = $get->get_user($ix);
        if ($u !== false)
        {
            $g = (int)$_REQUEST['group'];
            if ($g < 1 || $g > 3)
                $g = 1;

            $dbc->db_query('INSERT INTO `abooks_entrys` SET `user`=' . $usrid . ', `group`=' .
                $g . ', `remote_user`=' . $u['id']);

            header('Location: abook.php?sid=' . $sid . '&m=admin&saved=1');
        } else
            $game->simple_message('Benutzer inexistent!');
        break;

    case 'admin': //------------------------- ADMIN -------------------------------
        $layout->createlayout_top('HackTheNet - Adressbuch');

        if ($_REQUEST['saved'] == 1)
            $xxx = '<div class="ok"><h3>OK</h3><p>Die &Auml;nderungen wurden &uuml;bernommen!</p></div><br />' .
                "\n";
        echo '<div id="abook-administration" class="content">
  <h2>Adressbuch</h2>
  <h3>Adressbuch verwalten</h3>' . $xxx . '
  <form action="abook.php?action=add&amp;sid=' . $sid . '" method="post">
  <table>
  <tr><th colspan="2">Benutzer hinzuf&uuml;gen</th></tr>
  <tr><th>Benutzername:</th><td><input name="user" size="20" maxlength="20" /></td></tr>
  <tr><th>Gruppe:</th><td><select name="group"><option value="1">Allgemein</option><option value=2>Cluster</option><option value=3>Freunde</option></select></td></tr>
  <tr><td colspan="2" align="right"><input type="submit" value="Hinzuf&uuml;gen" /></td></tr>
  </table></form><br />';

        echo '<h3>Gruppe: Allgemein</h3>';
        $abook->admin_list(1);
        echo '<h3>Gruppe: Cluster</h3>';
        $abook->admin_list(2);
        echo '<h3>Gruppe: Freunde</h3>';
        $abook->admin_list(3);
        echo '</div>';
        $layout->createlayout_bottom();
        break;

    case 'saveadmin': //------------------------- SAVE ADMIN -------------------------------

        $g = (int)$_REQUEST['group'];
        if ($g > 3 or $g < 1)
            $g = 1;

        $r = $dbc->db_query('SELECT id FROM `abooks_entrys` WHERE `user`=' . $usrid .
            ' AND `group`=' . $g);

        while ($entry = mysql_fetch_object($r))
        {
            if ($_POST['u' . $entry->id] == 'yes')
            {
                $dbc->db_query('DELETE FROM `abooks_entrys` WHERE `user`=' . $usrid .
                    ' AND `id`=' . $entry->id . ' LIMIT 1');
            } elseif ((int)$_POST['group' . $entry->id] != $g)
            {
                $dbc->db_query('UPDATE `abooks_entrys` SET `group`=' . (int)$_REQUEST['group' .
                    $entry->id] . ' WHERE `user`=' . $usrid . ' AND `id`=' . $entry->id . ' LIMIT 1');
            }
        }

        header('Location: abook.php?sid=' . $sid . '&m=admin&saved=1');
        break;

}

?>