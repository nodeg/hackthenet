<?php

class mail
{

    function mail_format_text($str, $xnl2br = true)
    {
        $str = html_entity_decode($str);
        $str = htmlentities($str);
        $str = str_replace('script', 'scr*pt', $str);
        $str = str_replace("\r", '', $str);
        if ($xnl2br)
            $str = nl2br($str);
        return $str;
    }

    function newmailcount($save = true)
    {
        global $usrid;
        
        $dbc = new dbc();
        
        $result = $dbc->db_query('SELECT mail FROM mails WHERE user=\'' . $usrid . '\' AND box=\'in\' AND xread=\'no\'');
        $anz = mysql_num_rows($result);
        $result = $dbc->db_query('SELECT msg FROM sysmsgs WHERE user=\'' . $usrid . '\' AND xread=\'no\'');
        $anz += mysql_num_rows($result);
        if ($save)
            $dbc->db_query('UPDATE users SET newmail=\'' . $anz . '\' WHERE id=\'' . $usrid . '\';');
        return $anz;
    }

    function maillist($boxid)
    {
    	$dbc = new dbc();
    	$get = new get();
    	$gres = new gres();
        // ------------------------- MAIL LIST -----------------------------
        global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $usrid, $sid, $full;
        global $usr_cache, $maillist_mcnt;
        $output = '';
        $mcnt = 0;

        $result = $dbc->db_query('SELECT * FROM mails WHERE user=\'' . $usrid . '\' AND box=\'' .
            mysql_escape_string($boxid) . '\' ORDER BY time DESC');
        $anz = mysql_num_rows($result);
        while ($data = mysql_fetch_assoc($result))
        {
            $mcnt++;
            $u = $data['user2'];
            /*if(isset($usr_cache[$u])==false)
            {
            $sender=GetUser($u);
            $usr_cache[$u]=$sender;
            
            }
            else $sender=$usr_cache[$u];*/
            $time = $gres->nicetime($data['time']);
            if ($data['xread'] == 'yes')
                $new = '';
            else
                $new = ' class="not-read"';
            $output .= '<tr' . $new . '>' . "\n";
            $output .= '<td class="number">' . $mcnt . '</td>' . "\n";
            $output .= '<td class="from"><a href="user.php?a=info&amp;user=' . $data['user2'] .
                '&amp;sid=' . $sid . '">' . $data['user2_name'] . '</a></td>' . "\n";
            $output .= '<td class="time">' . $time . '</td>' . "\n";
            $output .= '<td class="title"><a href="mail.php?a=read&amp;msg=' . $data['mail'] .
                '&amp;sid=' . $sid . '">' . $data['subject'] . '</a></td>' . "\n";
            $output .= '<td class="checkbox"><input name="c' . $data['mail'] .
                '" type="checkbox" value="1"></input></td>' . "\n";
            $output .= '</tr>';
        }

        $maillist_mcnt = $mcnt;
        $full = ($mcnt < $get->get_maxmails($boxid) ? false : true);
        return $output;
    }

    function newmailform($recip = '', $subject = '', $text = '', $xnl2br = true)
    {
        // -------- NEW MAIL FORM -----------
        global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $sid, $usrid, $usr;
        if ($text != '')
            $text = mail_format_text($text, $xnl2br);
        if ($subject != '')
            $subject = mail_format_text($subject);
        if ($text == '' && $usr['sig_mails'] != '')
            $text = "\n\n\n\n" . $usr['sig_mails'];
        if ($usr['bigacc'] == 'yes')
            $bigacc = '<tr id="messages-compose-address-book">' . LF .
                '<td colspan="2"><input type="button" value="Adressbuch..." onclick="show_abook(\'user\');"/></td>' .
                LF . '</tr>' . "\n";
        echo '<div id="messages-compose">
  <h3>Mail verfassen</h3>
  <form action="mail.php?a=sendmail&amp;sid=' . $sid .
            '" method="post" name="newmail">
  <table>
  <tr id="messages-compose-recipient">
  <th>Empf&auml;nger:</th>
  <td><input name="recipient" type="text" value="' . $recip . '" /></td>
  </tr>
  ' . $bigacc . '
  <tr id="messages-compose-subject">
  <th>Betreff:</th>
  <td><input name="subject" type="text" value="' . $subject . '" /></td>
  </tr>
  <tr id="messages-compose-text">
  <th>Text:</th>
  <td><textarea name="text" rows="5" cols="50">' . $text . '</textarea></td>
  </tr>
  <tr id="messages-compose-confirm">
  <td colspan="2"><input type="submit" value="Absenden" /></td>
  </tr>
  </table>
  </form>
  </div>
  ';
    }

    function transmit2_mail_list($box)
    {
        global $STYLESHEET, $usrid, $sid, $full;
        echo '<tr id="messages-transmit2-' . ($box == 'in' ? 'inbox' : ($box == 'out' ?
            'outbox' : 'archive')) . '">' . LF . '<td colspan="4">' . ($box == 'in' ?
            'Nachrichten-Eingang' : ($box == 'out' ? 'Versendete Mails' :
            'Nachrichten-Archiv')) .
            '</b></td><td class="checkbox"><input type="checkbox" onclick="ch_all(this,\'' .
            $box . '\');" /></td></tr>';
        $mcnt = 0;

        $result = db_query('SELECT * FROM mails WHERE user=\'' . $usrid . '\' AND box=\'' .
            mysql_escape_string($box) . '\' ORDER BY time DESC');
        $anz = mysql_num_rows($result);
        while ($data = mysql_fetch_assoc($result))
        {
            $mcnt++;
            $sender = GetUser($data['user2']);
            $time = nicetime2($data['time']);
            $text = html_entity_decode($data['text']);
            $text = str_replace('<br />', ' ', $text);
            if (strlen($text) > 50)
                $text = substr($text, 0, 50) . ' ...';
            $text = htmlentities($text);
            $subject = html_entity_decode($data[subject]);
            if (strlen($subject) > 30)
                $subject = substr($subject, 0, 30) . ' ...';
            $subject = htmlentities($subject);
            echo '<tr>
      <td class="number">' . $mcnt . '</td>
      <td><a href="user.php?a=info&amp;user=' . $data['user2'] . '&amp;sid=' . $sid .
                '">' . $sender['name'] . '</td>
      <td>' . $time . '</td>
      <td><a href="mail.php?a=read&amp;msg=' . $data['mail'] . '&amp;sid=' . $sid .
                '">$subject</a></td>
      <td class="checkbox"><input name="' . $box . $data['mail'] .
                '" type="checkbox" value="1" /></td>
      </tr>
      ';
        }

    }

    function transmit3_addbox($box)
    {
        global $usrid, $body, $list, $bound;
        $sql = trim(getcheckedmail_str($box, $box));
        if ($sql != '')
        {
            $r = db_query('SELECT * FROM mails WHERE ' . $sql . ';');
            #echo mysql_error().' <tt>SELECT * FROM mails WHERE '.$sql.';</tt><br />';
            while ($data = mysql_fetch_assoc($r))
            {
                $partner = getuser($data['user2']);
                $data['subject'] = html_entity_decode($data['subject']);
                $data['text'] = str_replace('<br />', "\r\n", html_entity_decode($data['text']));
                $subject = $data['subject'];
                if (strlen($subject) > 10)
                    $subject = substr($subject, 0, 10);
                $ofn = $box . '_' . strftime('%d-%b-%y@%H-%M-%S', $data['time']) . '_' . $partner['name'] .
                    '_' . $subject;
                $ofn = eregi_replace('[\\\\\\/"*:?<>|]', '', $ofn);
                $fn = $ofn;
                $x = 0;
                while (isset($list[$fn]))
                {
                    $x++;
                    $fn = $ofn . '_' . $x;
                }
                $list[$fn] = $partner['name'] . ' <> ' . $data['subject'];
                if ($box == 'in')
                    $t = 'Absender';
                elseif ($box == 'out')
                    $t = 'Empf&auml;nger';
                else
                    $t = 'Absender/Empf&auml;nger';
                $msg = $t . ':' . "\t" . $partner['name'] . "\r" . LF . 'Zeit:' . "\t" . "\t" .
                    nicetime($data['time']) . "\r" . LF . 'Betreff:' . "\t" . $data['subject'] . "\r" .
                    LF . "\r" . LF . $data['text'];
                $msg = chunk_split(base64_encode($msg));
                $body .= '--' . $bound . "\n" . 'Content-Type: text/plain; name="' . $fn .
                    '.txt"' . "\n" . 'Content-Transfer-Encoding: base64' . "\n" .
                    'Content-Disposition: attachment; filename="' . $fn . '.txt"' . "\n" . LF . $msg .
                    "\n";
            }
        }
    }
    
        function check_email($email)
    {
        // ------------------ CHECK EMAIL ---------------
        return (eregi('^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-.]?[0-9a-zA-Z])*\\.[a-zA-Z]{2,4}$',
            $email) === false ? false : true);
    }
}

?>