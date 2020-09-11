<?php

class cboard
{

    function listboard($boxid)
    {
    	$dbc = new dbc();
    	$gres = new gres();
        global $DATADIR, $sid, $usrid, $pcid, $cix, $usr, $cluster;

        $admin = ($usr['clusterstat'] == CS_ADMIN || $usr['clusterstat'] == CS_COADMIN);

        $result = $dbc->db_query('SELECT * FROM cboards WHERE cluster LIKE \'' .
            mysql_escape_string($cix) . '\' AND box LIKE \'' . mysql_escape_string($boxid) .
            '\' AND relative LIKE \'-1\' ORDER BY time ASC;');
        $cnt = mysql_num_rows($result);
        if ($cnt == 0)
            echo '<p>Es gibt keine Eintr&auml;ge in diesem Ordner.</p>' . "\n";
        else
        {
            if ($admin)
                echo '<form action="cboard.php?a=admin&amp;box=' . $boxid . '&amp;sid=' . $sid .
                    '" method="post">' . "\n";
            echo '<table>' . "\n";
            echo '<tr>' . LF . '<th>Titel</th>' . LF . '<th>Autor</th>' . LF .
                '<th>Datum</th>' . LF . '<th>Ge&auml;ndert</th>' . LF . '<th>Antworten</th>' . "\n";
            if ($admin)
                echo '<th>Markieren</th>' . "\n";
            echo '</tr>' . "\n";

            $output = '';
            while ($data = mysql_fetch_assoc($result))
            {
                $tmp = '';
                $r = $dbc->db_query('SELECT * FROM cboards WHERE cluster LIKE \'' .
                    mysql_escape_string($cix) . '\' AND relative LIKE \'' . mysql_escape_string($data['thread']) .
                    '\' ORDER BY time ASC;');
                $aws = mysql_num_rows($r);
                if ($aws > 0)
                {
                    $lastm_usr['id'] = mysql_result($r, $aws - 1, 'user');
                    $lastm_usr['name'] = mysql_result($r, $aws - 1, 'user_name'); #=getuser(mysql_result($r,$aws-1,'user'),'id');
                    $lastm_time = mysql_result($r, $aws - 1, 'time');
                } else
                    $lastm_time = $data['time'];

                $tmp .= '<tr>' . "\n";
                #$sender=getuser($data['user'],'id');

                $tmp .= '<td><a href="cboard.php?sid=' . $sid .
                    '&amp;a=showthread&amp;threadid=' . $data['thread'] . '">' . $data['subject'] .
                    '</a></td>' . "\n";
                $tmp .= '<td><a href="user.php?a=info&amp;sid=' . $sid . '&amp;user=' . $data['user'] .
                    '">' . $data['user_name'] . '</a></td>' . LF . '<td>' . $gres->nicetime($data['time']) .
                    '</td>' . "\n";
                if ($lastm_time == $data['time'])
                    $slm = 'nein';
                else
                    $slm = $gres->nicetime($lastm_time, true) . ' von <a href="user.php?a=info&amp;sid=' .
                        $sid . '&amp;user=' . $lastm_usr['id'] . '">' . $lastm_usr['name'] . '</a>';
                $tmp .= '<td>' . $slm . '</td>';
                if ($aws < 1)
                    $aws = 'keine';
                $tmp .= '<td>' . $aws . '</td>';
                if ($admin)
                {
                    $t = $data[thread];
                    $ch1 = ($data['box'] == 0 ? ' selected' : '');
                    $ch2 = ($data['box'] == 1 ? ' selected' : '');
                    $ch3 = ($data['box'] == 2 ? ' selected' : '');
                    $tmp .= '<td>';
                    /* if(time() > $lastm_time+EDIT_TIME_OUT)
                    {
                    */
                    $tmp .= '<input type="checkbox" name="edit' . $t . '" value="yes" id="d' . $t .
                        '" />';
                    /*
                    }
                    */
                    $tmp .= '</td>';
                }
                $tmp .= '</tr>';
                $output = $tmp . $output;
            }
            echo $output;
            if ($admin)
            {
                echo '<tr id="cluster-board-folder' . ($boxid + 1) . '-confirm">' . "\n";
                echo '<td colspan="6"><select name="axion">';
                echo '<option value="delete">Löschen</option>';
                if ($boxid != 0)
                    echo '<option value="folder1">In Ordner 1 verschieben</option>';
                if ($boxid != 1)
                    echo '<option value="folder2">In Ordner 2 verschieben</option>';
                if ($boxid != 2)
                    echo '<option value="folder3">In Ordner 3 verschieben</option>';
                echo '</select> <input type="submit" value="Ausführen" /></td>' . LF . '</tr>' .
                    "\n";
            }
            echo '</table>';
            if ($admin)
                echo '</form>';
        }
    }

    function show_beitrag($data)
    {
    	$gres = new gres();
    	$ingame = new ingame();
        global $STYLESHEET, $DATADIR, $sid;
        #$sender=getuser($data[user]);
        if ($data[relative] == -1)
            echo '<h3>Beitrag: ' . $data['subject'] . '</h3><br />' . "\n";
        $cstat = $ingame->cscodetostring($data['user_cstat']);
        $time = $gres->nicetime($data['time']);
        echo '<table>
    <tr><th>' . $data['subject'] . ' von <a href="user.php?a=info&user=' . $data['user'] .
            '&sid=' . $sid . '">' . $data['user_name'] . '</a>
    (' . $cstat . '), ' . $time . '</th></tr>
    <tr valign="top"><td>' . $data['content'] . '</td></tr>
    </table><br />';
    }

    function LoadSmilies()
    {
        // ------------------------ LOAD SMILIES ---------------------------------
        $smilies = array();
        $smilies[] = array('file' => 'angry.gif', 'symbol' => '&gt;:(');
        $smilies[] = array('file' => 'biggrin.gif', 'symbol' => ':D');
        $smilies[] = array('file' => 'cheesy.gif', 'symbol' => ':lol:');
        $smilies[] = array('file' => 'confused.gif', 'symbol' => '%-)');
        $smilies[] = array('file' => 'cool.gif', 'symbol' => '8-)');
        $smilies[] = array('file' => 'dead.gif', 'symbol' => 'X-(');
        $smilies[] = array('file' => 'eek.gif', 'symbol' => ':-O');
        $smilies[] = array('file' => 'glubsch.gif', 'symbol' => ':glubsch:');
        $smilies[] = array('file' => 'king.gif', 'symbol' => ':king:');
        $smilies[] = array('file' => 'sad.gif', 'symbol' => ':-(');
        $smilies[] = array('file' => 'sleep.gif', 'symbol' => ':sleep:');
        $smilies[] = array('file' => 'smile.gif', 'symbol' => ':-)');
        $smilies[] = array('file' => 'tongue.gif', 'symbol' => ':-P');
        $smilies[] = array('file' => 'rolleyes.gif', 'symbol' => ':rolleyes:');
        $smilies[] = array('file' => 'wink.gif', 'symbol' => ';-)');
        $smilies[] = array('file' => 'twisted.gif', 'symbol' => ':evil:');
        $smilies[] = array('file' => 'mg.gif', 'symbol' => ':mg:');
        return $smilies;
    }

    function formatText($s)
    {
        // ------------------------ FORMAT TEXT ---------------------------------
        global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $sid, $usrid, $pcid, $cix;

        $text = $s;
        $text = htmlentities($text);
        $text = nl2br($text);

        #$text=preg_replace('/[code](.*?)[\\/code]/is', '<pre>\\1</pre>', $text);
        $text = str_replace('  ', ' ', $text);
        $a = $this->LoadSmilies();
        for ($i = 0; $i < count($a); $i++)
        {
            $text = str_replace(htmlentities($a[$i][symbol]), '<img src="smilies/' . $a[$i]['file'] .
                '" align=middle border=0>', $text);
        }
        #$text=replace_uri($text);

        return $text;
    }

    function showform($action = 'addthread')
    {
        // ---------------------------- SHOW FORM -----------------------
        global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $sid, $usrid, $pcid, $usr, $cluster;

        if ($action == 'addreply')
            $xval = 'Antwort';
        else
            $xval = '';
        echo '<form method="post" name="formular" action="cboard.php?sid=' . $sid .
            '&amp;a=' . $action . '">
  <table>
  <tr>
  <th>Titel:</th>
  <td><input name="title" maxlength="40" value="' . $xval . '" /></td>
  </tr>
  ';
        if ($action == 'addthread')
            echo '<tr>' . LF . '<th>Ordner:</th>' . LF .
                '<td><select name="box"><option value="0">1 (' . $cluster['box1'] .
                ')</option><option value="1">2 (' . $cluster['box2'] .
                ')</option><option value="2">3 (' . $cluster['box3'] .
                ')</option></select></td></tr>';

        echo '<tr><th>Dein Beitrag:</th>
  <td><textarea name="text" cols="50" rows="5" onkeyup="setCaret(this)" onmouseup="setCaret(this)" onchange="setCaret(this)">';
        if (trim($_REQUEST['text']) != '')
            $t = trim($_REQUEST['text']);
        elseif ($usr['sig_board'] != '')
            $t = "\n\n\n\n" . $usr['sig_board'];
        echo stripslashes($t);
        echo '</textarea><br />';

        $a = $this->LoadSmilies();
        for ($i = 0; $i < count($a); $i++)
        {
            echo '<a href="javascript:smily(\'' . htmlentities($a[$i]['symbol']) . '\');"><img src="smilies/' .
                $a[$i]['file'] . '" title="' . $a[$i]['symbol'] .
                '" alt="Smilie" border="0" /></a> ';
        }

        echo '</td></tr>
  <tr id="cluster-board-newthread-confirm">
  <td colspan="2"><input type="submit" value="Beitrag abschicken" /></td>
  </tr>
  </table>';
        if (isset($_REQUEST['threadid']))
            echo '<input name="threadid" type=hidden value="' . $_REQUEST['threadid'] . '">';
        echo '</form>';
    }

}

?>