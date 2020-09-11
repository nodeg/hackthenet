<?php

class cluster
{

    function savemycluster()
    { 
    	$dbc = new dbc();
		# Eigenen Cluster speichern
        global $clusterid, $cluster;
        $s = '';
        while (list($bez, $val) = each($cluster))
            $s .= $bez . '=\'' . mysql_escape_string($val) . '\',';
        $s = trim($s, ',');
        $dbc->db_query('UPDATE clusters SET ' . $s . ' WHERE id=\'' . $clusterid . '\'');
    }

    function nocluster()
    {
        # ich bin keinem (existierenden) Cluster
        global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $sid, $usrid, $pcid;
        echo '<div id="cluster-found">
<h3>Cluster gr&uuml;nden</h3>
<form action="cluster.php?page=found&amp;sid=' . $sid . '" method="post">
<table>
<tr>
<th>Name:</th>
<td><input type="text" name="name" maxlength="48" /></td>
</tr>
<tr>
<th>Code:</th>
<td><input type="text" name="code" maxlength="12" /></td>
</tr>
<tr><td colspan="2"><input type="submit" value="Gr&uuml;nden" /></td>
</tr>
</table>
</form>
</div>
<div class="important"><h3>Hinweis</h3>
<p>Um einem existierenden Cluster beizutreten, rufe die Info-Seite eines Clusters auf.
Dort findest du einen "Mitgliedsantrag stellen"-Link.</p></div>
</div>';
    }

    function stat_list_item($id, $c)
    {
    	$ingame = new ingame();
        echo '<option value="' . $id . '"' . ($c == $id ? ' selected="selected">' : '>') .
            $ingame->cscodetostring($id) . '</option>';
    }

    function xpcinfo($item, $ix_usrid, $ix_pcid)
    {
        global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $cluster, $clusterid, $sid;
        static $usr_cache, $cluster_cache, $pc_cache;

        $tmp = $ix_pcid;
        if (isset($pc_cache[$tmp]) == false)
        {
            $p = $get->get_pc($tmp);
            $pc_cache[$tmp] = $p;
        } else
            $p = $pc_cache[$tmp];
        echo '<td><strong>10.47.' . $p['ip'] . '</strong>';

        $tmp = $ix_usrid;
        if (isset($usr_cache[$tmp]) == false)
        {
            $u = $get->get_user($ix_usrid);
            $usr_cache[$tmp] = $u;
        } else
            $u = $usr_cache[$tmp];

        if ($u !== false)
        {
            echo ' von <a href="user.php?page=info&amp;sid=' . $sid . '&amp;user=' . $u['id'] .
                '">' . $u['name'] . '</a>';
            if ($u['cluster'] != $clusterid)
            {

                $tmp = (int)$u['cluster'];
                if (isset($cluster_cache[$tmp]) == false)
                {
                    $c = $get->get_cluster($u['cluster']);
                    $cluster_cache[$tmp] = $c;
                } else
                    $c = $cluster_cache[$tmp];

                if ($c !== false)
                {
                    echo ' (<a href="cluster.php?page=info&amp;sid=' . $sid . '&amp;cluster=' . $u['cluster'] .
                        '">' . $c['code'] . '</a>)</td>' . "\n";
                } else
                    echo '</td>' . "\n";
            } else
            {
                echo '</td>' . "\n";
            }
        } else
            echo '</td>' . "\n";
    }

    function battle_table($dir)
    {
    	$dbc = new dbc();
    	$gres = new gres();
        global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $cluster, $clusterid;

        echo '<table>
<tr>
<th>Zeit</th>
<th>Angreifer</th>
<th>Opfer</th>
<th>Waffe</th>
<th>Erfolg</th>
</tr>
';

        $ts = time() - 24 * 60 * 60;
        $r = $dbc->db_query('SELECT * FROM attacks WHERE ' . ($dir == 'in' ? 'to_cluster' :
            'from_cluster') . '=' . $clusterid . ' AND time>=' . $ts .
            ' ORDER BY time DESC;');

        while ($data = mysql_fetch_assoc($r))
        {
            echo '<tr>' . "\n";

            echo '<td>' . $gres->nicetime2($data['time']) . '</td>' . "\n";

            if ($dir == 'out' || $data['noticed'] == 1)
            {
                $this->xpcinfo($data, $data['from_usr'], $data['from_pc']);
            } else
                echo '<td>?</td>' . "\n";

            #if($dir=='in') {
            xpcinfo($data, $data['to_usr'], $data['to_pc']);
            #} else echo '<td>?</td><td>?</td><td>?</td>';

            $ia = array('scan' => 'Remote Scan', 'trojan' => 'Trojaner', 'smash' =>
                'Remote Smash', 'block' => 'Remote Block', 'hijack' => 'Remote Hijack');

            $data['opt'] = strtoupper($data['opt']);
            switch ($data['type'])
            {
                case 'trojan':
                    $s .= ' (<tt>' . $data['opt'] . '</tt>).';
                    break;
                case 'smash':
                    $s .= ' mit der Option <tt>' . $data['opt'] . '</tt>.';
                    break;
            }

            $s = $ia[$data['type']];
            echo '<td>' . $s . '</td>';

            if ($data['success'] == 1)
            {
                if ($dir == 'in')
                    $c = 'red';
                else
                    $c = 'green';
                $s = '<span style="color:$c;font-weight:bold;">Ja</span>';
            } else
            {
                if ($dir == 'out')
                    $c = 'red';
                else
                    $c = 'green';
                $s = '<span style="color:$c;font-weight:bold;">Nein</span>';
            }

            echo '<td>' . $s . '</td>';

            echo '</tr>';
        }

        echo '</table>';
    }

    function cvCodeToString($code)
    { // -------- CV CODE TO STRING ------
        switch ($code)
        {
            case CV_WAR:
                $s = 'Kriegserkl&auml;rung';
                break;
            case CV_BEISTAND:
                $s = 'Beistandsvertrag';
                break;
            case CV_PEACE:
                $s = 'Friedensvertrag';
                break;
            case CV_NAP:
                $s = 'Nicht-Angriffs-Pakt';
                break;
            case CV_WING:
                $s = 'Wing-Treaty';
                break;
        }
        return $s;
    }

    function conventlist($cid)
    {
    	$dbc = new dbc();
		 // ----------- CONVENTLIST -----------
        global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $clusterid, $cluster, $sid;

        $r = $dbc->db_query('SELECT cl_pacts.convent,clusters.code,clusters.id FROM (cl_pacts RIGHT JOIN clusters ON cl_pacts.partner=clusters.id) WHERE cl_pacts.cluster=' .

            mysql_escape_string($cid) . ' ORDER BY clusters.code ASC;');
        #echo mysql_error();
        if ($r->mysql_num_rows > 0)
        {
            $s = '<table>' . LF . '<tr>' . LF . '<th>Cluster</th>' . LF . '<th>Vertrag</th>' .
                LF . '</tr>' . "\n";
            while ($pact = mysql_fetch_assoc($r))
            {
                #$partner=$get->get_cluster($pact[partner]);
                $temp = cvcodetostring($pact['convent']);
                $s .= '<tr>' . LF . '<td><a href="cluster.php?page=info&amp;sid=' . $sid .
                    '&amp;cluster=' . $pact['id'] . '">' . $pact['code'] . '</a></td>' . LF . '<td>' .
                    $temp . '</td>' . LF . '</tr>' . "\n";
            }
            $s .= '</table>';
        }
        return $s;
    }

    function ext_conventlist()
    {
    	$dbc = new dbc();
        global $clusterid, $usr, $sid;

        $r = $dbc->db_query('SELECT cl_pacts.convent,clusters.code,clusters.id,cl_pacts.partner FROM (cl_pacts RIGHT JOIN clusters ON cl_pacts.partner=clusters.id) WHERE cl_pacts.cluster=' .
            $clusterid . ' ORDER BY clusters.code ASC;');
        if (mysql_num_rows($r) > 0)
        {
            echo '<div id="cluster-convents">
  <h3>Eigene bestehende Vertr&auml;ge</h3>
  <table>
  <tr>
  <th>Cluster</th>
  <th>Vertrag</th>';
            if ($usr['clusterstat'] > 20)
            {
                echo '<th>Löschen?</th>';
            }
            echo '</tr>
  ';
            while ($pact = mysql_fetch_assoc($r))
            {
                $temp = $this->cvcodetostring($pact['convent']);
                echo '<tr>
  <td><a href="cluster.php?page=info&amp;sid=' . $sid . '&amp;cluster=' . $pact['id'] .
                    '">' . $pact['code'] . '</a></td>
  <td>' . $temp . '</td>';
                if ($usr['clusterstat'] > 20)
                {
                    echo '<td><a href="cluster.php?page=delconvent&amp;sid=' . $sid .
                        '&amp;convent=' . $pact['convent'] . '-' . $pact['partner'] .
                        '">L&ouml;schen</a></td>';
                }
                echo '</tr>
  ';
            }
            echo '</table>
  </div>
  ';
        }

        $r = $dbc->db_query('SELECT cl_pacts.convent,clusters.code,clusters.id FROM (cl_pacts RIGHT JOIN clusters ON cl_pacts.cluster=clusters.id) WHERE cl_pacts.partner=' .
            $clusterid . ' ORDER BY clusters.code ASC;');
        if (mysql_num_rows($r) > 0)
        {
            echo '<div id="cluster-convents">
  <h3>Bestehende Vertr&auml;ge anderer Cluster mit uns</h3>
  <table>
  <tr>
  <th>Cluster</th>
  <th>Vertrag</th>
  </tr>
  ';
            while ($pact = mysql_fetch_assoc($r))
            {
                $temp = $this->cvcodetostring($pact['convent']);
                echo '<tr>
  <td><a href="cluster.php?page=info&amp;sid=' . $sid . '&amp;cluster=' . $pact['id'] .
                    '">' . $pact['code'] . '</a></td>
  <td>' . $temp . '</td>
  </tr>
  ';
            }
            echo '</table>
  </div>
  ';
        }
    }

}

?>