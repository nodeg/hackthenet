<?php

class abook
{
    function list_items($g)
    {
        $dbc = new dbc();
        global $b, $sid, $type, $usrid;
        echo '<table><tr>';

        echo '<th>Benutzer</th><th>W&auml;hlen</th></tr>';
        $r = $dbc->db_query('SELECT u.id AS u_id, u.name AS u_name, a.id AS entry FROM abooks_entrys a JOIN users u ON a.remote_user=u.id WHERE a.user=' .
            $usrid . ' AND a.group=' . $g);
        while ($entry = mysql_fetch_object($r))
        {
            echo '<tr><td><a href="user.php?a=info&sid=' . $sid . '&user=' . $entry->uid .
                '">' . $entry->u_name . '</a></td>';
            echo '<td><input type="button" value="W&auml;hlen" onclick="choose(\'' .
                str_replace('\'', '\\\'', $entry->u_name) . '\')"></td></tr>';
        }
        echo '</table></form>';
    }

    function admin_list($ix)
    {
        $dbc = new dbc();
        global $usrid, $sid;
        eval('$ch' . $ix . '=\' selected\';');
        $r = $dbc->db_query('SELECT u.id, u.name, a.id AS entry FROM abooks_entrys a JOIN users u ON a.remote_user=u.id WHERE a.user=' .
            $usrid . ' AND a.group=' . $ix);
        if (mysql_num_rows($r) > 0)
        {
            echo '<form action="abook.php?sid=' . $sid . '&amp;m=saveadmin&amp;group=' . $ix .
                '" method="post">
        <table>
        <tr><th>User</th><th>Gruppe</th><th>L&ouml;schen?</th></tr>';
            while ($user = mysql_fetch_assoc($r))
            {
                echo '<tr><td width="100"><a href="user.php?a=info&sid=' . $sid . '&user=' . $user['id'] .
                    '">' . $user['name'] . '</a></td>';
                echo '<td><select name="group' . $user['entry'] . '"><option value="1"' . $ch1 .
                    '>Allgemein</option><option value=2' . $ch2 . '>Cluster</option><option value=3' .
                    $ch3 . '>Freunde</option></select></td>';
                echo '<td><input type="checkbox" value="yes" name="u' . $user['entry'] .
                    '" /></td>';
                echo '</tr>';
            }
            echo '<tr><td colspan="3" align="right"><input type="submit" value="Ausf&uuml;hren" /></td></tr></table></form>';
        }
    }

}

?>