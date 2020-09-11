<?php
session_start();
define('IN_HTN', 1);
$FILE_REQUIRES_PC = false;
include ('ingame.php');

$mail = new mail();
$dbc = new dbc();
$layout = new layout();
$get = new get();

$javascript = '
<script type="text/javascript">
function ch_all(e,ks) 
{
  for(i=0;i<e.form.elements.length;i++) 
  {
    if(e.form.elements[i].type==\'checkbox\' && e.form.elements[i].name.substr(0,ks.length)==ks) 
    {
      e.form.elements[i].checked=e.checked;
    }
  }
}
';

if ($usr['bigacc'] == 'yes')
{
    $javascript .= 'function fill(s) 
  {
    document.newmail.recipient.value=s; 
  }
  ';
}
$javascript .= "\n" . '</script>';

$full = false;
$usr_cache;
$maillist_mcnt;

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

    case 'start': //------------------------- START -------------------------------

        $inbox = $mail->maillist('in');

        $dbc->db_query('UPDATE users SET lic=\'' . time() . '\' WHERE id=\'' . $usrid . '\' LIMIT 1;');

        $result = $dbc->db_query('SELECT * FROM sysmsgs WHERE user=\'' . $usrid . '\' ORDER BY time DESC');
        $anz = mysql_num_rows($result);
        $mcnt = 0;
        $sysmsgs = '';
        while ($data = mysql_fetch_assoc($result))
        {
            $mcnt++;
            $time = nicetime($data['time'], '<br />um ');
            if ($data[xread] == 'yes')
            {
                $new = '';
                $ch = '';
            } else
            {
                $new = ' class="not-read"';
                $ch = 'checked ';
            }
            $text = gformattext($data[text]);
            $sysmsgs .= '<tr ' . $new . '>' . "\n";
            $sysmsgs .= '<td class="number">' . $mcnt . '</td>' . "\n";
            $sysmsgs .= '<td class="time">' . $time . '</td>' . "\n";
            $sysmsgs .= '<td class="title">' . $text . '</td>' . "\n";
            $sysmsgs .= '<td class="checkbox"><input type="checkbox" ' . $ch . 'name="c' . $data['msg'] .
                '" value="1" /></td>' . "\n";
            $sysmsgs .= '</tr>';
        }

        $inboxstate = '<p><strong>' . $maillist_mcnt . ' von maximal ' . $get->get_maxmails('in') .
            ' Mails im Posteingang</strong></p>' . "\n";
        #$arcstate=(int)@mysql_num_rows(db_query('SELECT mail FROM mails WHERE user='.$usrid.' AND box=\'arc\';')).'/'.getmaxmails('arc');

        if ($full == true)
            $full = '<div class="important">' . LF . '<h3>Dein Posteingang ist voll!</h3>' .
                LF . '<p>Bitte l&ouml;sche Mails, damit andere User dir wieder schreiben k&ouml;nnen.</p>' .
                LF . '</div>' . "\n";
        $layout->createlayout_top('HackTheNet - Messages');
        if ($inbox != '')
            $link_inbox = '<a href="#messages-inbox">Posteingang</a> | ';
        if ($sysmsgs != '')
            $link_sysmsgs = '<a href="#messages-system">System-Nachrichten</a> | ';
        echo '<div class="content" id="messages">
  <h2>Messages</h2>
  <div class="submenu">
  <p>' . $link_inbox . $link_sysmsgs .
            '<a href="#messages-compose">Mail verfassen</a></p>
  <p><a href="mail.php?m=archiv&amp;sid=' . $sid .
            '">Nachrichten-Archiv</a> | <a href="mail.php?m=outbox&amp;sid=' . $sid .
            '">Zuletzt gesendete Mails</a> | <a href="mail.php?m=transmit1&amp;sid=' . $sid .
            '">Mails &uuml;bertragen</a></p>
  </div>
  ' . $notif . $full;
        #echo '<div class="important"><h3>Achtung!</h3><p>Ab sofort werden Nachrichten die älter als 7 Tage sind aus dem Postein und -ausgang automatisch gelöscht!</p></div>';
        if ($inbox != '')
        {
            echo '<div id="messages-inbox">
    <h3>Posteingang</h3>
    ' . $inboxstate . '
    <form action="mail.php?type=in&amp;sid=' . $sid .
                '&amp;redir=start" method="post">
    <table>
    <tr>
    <th class="number">Nummer</th>
    <th class="from">Absender</th>
    <th class="time">Zeit</th>
    <th class="title">Betreff</th>
    <th class="checkbox">Markieren</th>
    </tr>
    ' . $inbox . '
    <tr>
    <td colspan="4" class="options"><select name="action">
    <option value="markread">Als gelesen markieren</option>
    <option value="archive">Ins Archiv verschieben</option>
    <option value="delete">L&ouml;schen</option>
    <option value="markunread">Als neu markieren</option>
    </select> <input type="submit" value="Ausf&uuml;hren" /></td>
    <td class="checkbox"><input type="checkbox" onclick="ch_all(this,\'c\')" /></tr>
    </table>
    </form>
    </div>
    ';
        }
        if ($sysmsgs != '')
        {
            echo '<div id="messages-system">
    <h3>System-Nachrichten</h3>
    <form action="mail.php?sid=' . $sid . '&amp;a=sysmsg_exec" method="post">
    <table>
    <tr>
    <th class="number">Nummer</th>
    <th class="time">Zeit</th>
    <th class="title">Betreff</th>
    <th class="checkbox">Markieren</th>
    </tr>
    ' . $sysmsgs . '
    <tr>
    <td colspan="3" class="options"><select name="opt">
    <option value="read">Als gelesen markieren</option>
    <option value="del">L&ouml;schen</option>
    </select> <input type="submit" value="Ausf&uuml;hren" /></td>
    <td class="checkbox"><input type="checkbox" onclick="ch_all(this,\'c\')" />
    </tr>
    </table>
    </form>
    </div>
    ';
        }
        $mail->newmailform();
        echo '</div>' . "\n";
        $layout->createlayout_bottom();
        break;

    case 'sysmsg_exec': //------------------------- sysmsg_exec -------------------------------
        $cnt = 0;

        $opt = $_POST['opt'];
        $result = $dbc->db_query('SELECT msg FROM sysmsgs WHERE user=\'' . $usrid . '\' ORDER BY time DESC');
        while ($data = mysql_fetch_assoc($result))
        {
            if ($_POST['c' . $data['msg']] == '1')
            {
                $cnt++;
                if ($opt == 'del')
                {
                    $dbc->db_query('DELETE FROM sysmsgs WHERE msg=\'' . mysql_escape_string($data['msg']) .
                        '\' LIMIT 1;');
                } elseif ($opt == 'read')
                {
                    $dbc->db_query('UPDATE sysmsgs SET xread=\'yes\' WHERE msg=\'' . mysql_escape_string($data['msg']) .
                        '\';');
                }
            }
        }

        $mail->newmailcount(true);

        if ($opt == 'del')
            $xtxt = 'gel&ouml;scht';
        elseif ($opt == 'read')
            $xtxt = 'als gelesen markiert';
        header('Location: mail.php?a=start&sid=' . $sid . '&' . ($cnt != 0 ? 'ok=' .
            urlencode('Es wurden ' . $cnt . ' System-Nachrichten ' . $xtxt . '!') : 'error=' .
            urlencode('Es muss mindestens eine Nachricht markiert werden.')));
        break;

    case 'newmailform': //------------------------- NewMailForm -------------------------------
        $layout->createlayout_top('HackTheNet - Messages');
        echo '<div class="content" id="messages">' . LF . '<h2>Messages</h2>' . LF . $notif;
        $mail->newmailform($_REQUEST['recip'], $_REQUEST['subject'], $_REQUEST['text']);
        echo '</div>' . "\n";
        $layout->createlayout_bottom();
        break;


    case 'archiv': //------------------------- ARCHIV -------------------------------
        $layout->createlayout_top('HackTheNet - Messages');
        $x = $mail->maillist('arc');
        echo '<div class="content" id="messages">
  <h2>Messages</h2>
  ' . $notif . '<div id="messages-archive">
  <h3>Nachrichtenarchiv</h3>
  ';
        if ($x != '')
        {
            echo '<form action="mail.php?type=arc&amp;sid=' . $sid .
                '&amp;redir=archiv&amp;action=delete"  method="post">
    <table>
    <tr>
    <th class="number">Nummer</th>
    <th class="from">Absender</th>
    <th class="time">Zeit</th>
    <th class="title">Betreff</th>
    <th class="checkbox">Markieren</th>
    </tr>
    ' . $x . '
    <tr id="messages-archive-confirm">
    <td colspan="4" class="options"><input type="submit" value="L&ouml;schen" /></td>
    <td class="checkbox"><input type="checkbox" onclick="ch_all(this,\'c\')" />
    </tr>
    </table>
    </form>
    ';
        } else
            echo '<p>Es sind keine Nachrichten im Archiv.</p>' . "\n";
        echo '</div>' . LF . '</div>' . "\n";
        $layout->createlayout_bottom();
        break;

    case 'outbox': //------------------------- OUTBOX -------------------------------
        $layout->createlayout_top('HackTheNet - Messages');
        $x = $mail->maillist('out');
        echo '<div class="content" id="messages">
  <h2>Messages</h2>
  ' . $notif . '<div id="messages-outbox">
  <h3>Postausgang</h3>
  <form action="mail.php?type=out&amp;sid=' . $sid .
            '&amp;redir=outbox" method="post">
  <table>
  <tr>
  <th class="number">Nummer</th>
  <th class="from">Empf&auml;nger</th>
  <th class="time">Zeit</th>
  <th class="title">Betreff</th>
  <th class="checkbox">Markieren</th>
  </tr>
  ' . $x . '
  <tr id="messages-outbox-confirm">
  <td colspan="4" class="options"><select name="action">
  <option value="delete">L&ouml;schen</option>
  <option value="archive">Ins Archiv verschieben</option>
  </select> <input type="submit" value="Ausf&uuml;hren" /></td>
  <td class="checkbox"><input type="checkbox" onclick="ch_all(this,\'c\')" />
  </tr>
  </table>
  </form>
  </div>
  </div>
  ';
        $layout->createlayout_bottom();
        break;

    case 'delete': //------------------------- DELETE -------------------------------
        $box = $_REQUEST['type'];
        $s = $get->get_checkedmail_str($box);
        if ($s != '')
            $dbc->db_query('DELETE FROM mails WHERE ' . $s . ';');
        $mail->newmailcount(true);
        header('Location: mail.php?m=' . $_REQUEST['redir'] . '&sid=' . $sid . '&ok=' .
            urlencode('Die gew&auml;hlten Mails wurden gel&ouml;scht.'));
        break;

    case 'archive': //------------------------- ARCHIVE -------------------------------
        $cnt = @mysql_num_rows(db_query('SELECT * FROM mails WHERE user=' . $usrid .
            ' AND box=\'arc\''));
        if ($cnt < $get->get_maxmails('arc'))
        {
            $box = $_REQUEST['type'];
            $s = $get->get_checkedmail_str($box);
            if ($s != '')
                $dbc->db_query('UPDATE mails SET xread=\'yes\',box=\'arc\' WHERE ' . $s . ';');
            $mail->newmailcount(true);
            $ok = 'Die gew&auml;hlten Mails wurden ins Archiv verschoben.';
        } else
        {
            $error = 'Das Archiv ist voll. Es k&ouml;nnen maximal ' . $get->get_maxmails('arc') .
                ' Mails gelagert werden.';
        }
        header('Location: mail.php?m=' . $_REQUEST['redir'] . '&sid=' . $sid . '&' . ($ok !=
            '' ? 'ok=' . urlencode($ok) : 'error=' . urlencode($error)));
        break;

    case 'markread': //------------------------- Mark Read -------------------------------
        $box = $_REQUEST['type'];
        $s = $get->get_checkedmail_str($box);
        if ($s != '')
            $dbc->db_query('UPDATE mails SET xread=\'yes\' WHERE ' . $s . ';');
        $mail->newmailcount(true);
        header('Location: mail.php?m=' . $_REQUEST['redir'] . '&sid=' . $sid . '&ok=' .
            urlencode('Die gew&auml;hlten Mails wurden als gelesen markiert.'));
        break;

    case 'markunread': //------------------------- Mark Unread -------------------------------
        $box = $_REQUEST['type'];
        $s = $get->get_checkedmail_str($box);
        if ($s != '')
            $dbc->db_query('UPDATE mails SET xread=\'no\' WHERE ' . $s . ';');
        $mail->newmailcount(true);
        header('Location: mail.php?m=' . $_REQUEST['redir'] . '&sid=' . $sid . '&ok=' .
            urlencode('Die gew&auml;hlten Mails wurden als ungelesen markiert.'));
        break;

    case 'sendmail': //------------------------- SENDMAIL -------------------------------
        $rec = trim($_POST['recipient']);
        $subject = trim($_POST['subject']);
        if ($subject == '')
            $subject = '(Kein Betreff)';
        $text = trim($_POST['text']);

        $recip = $get->get_user($rec, 'name');
        $rec = $recip[name];

        $ok = '';
        $error = '';
        $e = false;

        if ($recip != false)
        {
            if (strlen($text) > 5120)
            {
                $e = true;
                $error .= 'Die Nachricht darf maximal 5120 Zeichen haben.';
            } else
            {
                if ($text == '')
                {
                    $e = true;
                    $error .= 'Einen Text sollte die Nachricht schon haben, oder?';
                } else
                {
                    $ts = time();
                    if ($usr[lastmail] + 15 <= $ts || $localhost || $usrid == 1)
                    {

                        $r = $dbc->db_query('SELECT mail FROM mails WHERE user=' . mysql_escape_string($recip['id']) .
                            ' AND box=\'in\'');
                        $cnt = mysql_num_rows($r);
                        if ($cnt < $get->get_maxmailsforuser('in', $recip['bigacc']))
                        {

                            if ($subject == '')
                                $subject = '(kein Betreff)';
                            $subject = $mail->mail_format_text($subject);
                            $text = $mail->mail_format_text($text);
                            if (strlen($subject) > 255)
                                $subject = substr($subject, 0, 255);

                            $dbc->db_query('UPDATE users SET liu=\'' . mysql_escape_string($ts) . '\' WHERE id=\'' .
                                mysql_escape_string($recip['id']) . '\'');
                            $dbc->db_query('INSERT INTO mails VALUES (\'0\', \'' . mysql_escape_string($recip['id']) .
                                '\', \'' . $usrid . '\', \'' . mysql_escape_string($usr['name']) . '\', \'' .
                                mysql_escape_string($ts) . '\', \'' . mysql_escape_string($subject) . '\', \'' .
                                mysql_escape_string($text) . '\', \'in\', \'no\')');
                            $dbc->db_query('INSERT INTO mails VALUES (\'0\', \'' . $usrid . '\', \'' .
                                mysql_escape_string($recip['id']) . '\', \'' . mysql_escape_string($recip['name']) .
                                '\', \'' . mysql_escape_string($ts) . '\', \'' . mysql_escape_string($subject) .
                                '\', \'' . mysql_escape_string($text) . '\', \'out\', \'yes\')');

                            $r = $dbc->db_query('SELECT mail FROM mails WHERE user=' . $usrid . ' AND box=\'out\' ORDER BY time ASC');
                            $cnt = mysql_num_rows($r);
                            $max = $get->get_maxmails('out');
                            if ($cnt > $max)
                            {
                                $cnt = $cnt - $max;
                                for ($i = 0; $i < $cnt; $i++)
                                {
                                    $id = mysql_result($r, $i, 'mail');
                                    $dbc->db_query('DELETE FROM mails WHERE mail=' . mysql_escape_string($id));
                                }
                            }

                            $ok .= 'Die Nachricht wurde erfolgreich an ' . $rec . ' verschickt!' . "\n";
                            $dbc->db_query('UPDATE users SET lastmail=\'' . time() . '\' WHERE id=\'' . $usrid . '\'');
                        } else
                        {
                            $e = true;
                            $error .= 'Das Postfach von ' . $rec .
                                ' ist leider voll. Du kannst ihm/ihr keine Nachricht schicken.<br />' . $rec .
                                ' hat f&uuml;r diesen Fall aber folgende Nachricht hinterlassen:<br />' . $recip['inbox_full'];
                        }
                    } else
                    {
                        $e = true;
                        $error .= 'Du kannst innerhalb von 15 Sekunden nicht mehr als eine Nachricht verschicken.';
                    }
                }
            }
        } else
        {
            $e = true;
            $error .= 'Ein User mit diesem Namen ' . $rec . ' existiert nicht.';
        }

        if ($e == false)
        {
            header('Location: mail.php?m=newmailform&sid=' . $sid . '&ok=' . urlencode($ok));
        } else
        {
            $err = '<div class="error"><h3>Fehler</h3><p>' . $error . '</p></div>';
            $layout->createlayout_top('HackTheNet - Messages');
            echo '<div class="content" id="messages">' . LF . '<h2>Messages</h2>' . LF . $err .
                "\n";
            $mail->newmailform($_REQUEST['recip'], $_REQUEST['subject'], $_REQUEST['text'], false);
            echo '</div>' . "\n";
            $layout->createlayout_bottom();
        }


        break;

    case 'read': //------------------------- READ -------------------------------
        $msg = (int)$_REQUEST[msg];

        $result = $dbc->db_query('SELECT * FROM mails WHERE USER=\'' . $usrid . '\' AND mail LIKE \'' .
            mysql_escape_string($msg) . '\' LIMIT 1;');

        if (mysql_num_rows($result) != 1)
            exit;

        $data = mysql_fetch_assoc($result);

        $sender = $get->get_user($data[user2]);
        $time = $gres->nicetime($data['time']);

        if ($data['xread'] != 'yes')
        {
            $dbc->db_query('UPDATE mails SET xread=\'yes\' WHERE mail LIKE \'' .
                mysql_escape_string($msg) . '\';');
            $unread = (int)$usr[newmail] - 1;
            $dbc->db_query('UPDATE users SET newmail=\'' . mysql_escape_string($unread) . '\' WHERE id=\'' .
                $usrid . '\'');
            $usr['newmail'] = $unread;
        }

        switch ($data['box'])
        {
            case 'in':
                $redir = 'start';
                $xcap = 'Absender';
                break;
            case 'arc':
                $redir = 'archiv';
                $xcap = 'Absender oder Empf&auml;nger';
                break;
            case 'out':
                $redir = 'outbox';
                $xcap = 'Empf&auml;nger';
                break;
        }
        $links = '<a href="mail.php?m=' . $redir . '&amp;sid=' . $sid .
            '">Zur&uuml;ck</a> | <a href="mail.php?sid=' . $sid . '&amp;action=delete&amp;c' .
            $msg . '=1&amp;redir=' . $redir . '&amp;type=' . $data['box'] .
            '">L&ouml;schen</a>';
        if ($data['box'] != 'out')
            $links .= ' | <a href="mail.php?a=reply&amp;msg=' . $msg . '&amp;sid=' . $sid .
                '">Antworten</a>';
        if ($data['box'] == 'in')
            $links .= ' | <a href="mail.php?a=markunread&amp;c' . $msg . '=1&amp;sid=' . $sid .
                '&amp;redir=' . $redir . '&amp;type=in">Als neu markieren</a>';

        $layout->createlayout_top('HackTheNet - Messages');
        echo '<div class="content" id="messages">
  <h2>Messages</h2>
  <div id="messages-message-read">
  <h3>Message</h3>
  <table>
  <tr>
  <th>' . $xcap . ':</th>
  <td><a href="user.php?a=info&amp;user=' . $sender['id'] . '&amp;sid=' . $sid .
            '">' . $sender['name'] . '</a></td>
  </tr>
  <tr>
  <th>Zeit:</th>
  <td>' . $time . '</td>
  </tr>
  <tr>
  <th>Betreff:</th>
  <td>' . $data['subject'] . '</td>
  </tr>
  <tr><td colspan="2">' . $data['text'] . '</td>
  </tr>
  <tr>
  <td colspan="2">' . $links . '</td>
  </tr>
  </table>
  </div>
  </div>
  ';
        $layout->createlayout_bottom();

        break;

    case 'reply': //------------------------- REPLY -------------------------------
        $msg = (int)$_REQUEST['msg'];

        $data = @mysql_fetch_assoc(@db_query('SELECT * FROM mails WHERE mail LIKE \'' .
            $msg . '\';'));

        if ($data['user'] != $usrid)
        {
            die('Fuck off!');
        }

        $sender = $get->get_user($data['user2']);
        $time = nicetime($data['time']);
        $subject = $data['subject'];
        $text = $data['text'];
        $text = str_replace("\r", '', $text);
        $text = str_replace("\n", '', $text);
        $text = str_replace('<br />', "\n", $text);

        if (substr($subject, 0, 3) == 'Re[')
        {
            $cnt = substr($subject, 3, strpos($subject, ']', 3) - 3);
            $cnt++;
            $subject = 'Re[' . $cnt . ']: ' . substr($subject, strpos($subject, ']:') + 3);
        } else
        {
            $subject = 'Re: ' . $subject;
            $a = explode(' ', $subject);
            $i = 0;
            $cnt = 0;
            while ($a[$i] == 'Re:')
            {
                $i++;
                $cnt++;
            }
            if ($cnt > 1)
            {
                $subject = joinex(array_slice($a, $i), ' ');
                $subject = 'Re[' . $cnt . ']: ' . $subject;
            }
        }

        $text = '|' . str_replace("\n", "\n" . '|', wordwrap($text, 50, "\n"));

        $layout->createlayout_top('HackTheNet - Messages');
        echo '<div class="content" id="messages">' . LF . '<h2>Messages</h2>' . "\n";
        $mail->newmailform($sender['name'], $subject, "\n" . LF . "\n" . LF .
            '------------------------' . LF . $sender['name'] . ' schrieb:' . LF . $text, false);
        echo '</div>' . "\n";
        $layout->createlayout_bottom();

        break;

    case 'transmit1': //------------------------- TRANSMIT 1 -------------------------------
        $layout->createlayout_top('HackTheNet - Messages - Mails übertragen');
        echo '<div class="content" id="messages">
  <h2>Messages</h2>
  <div id="messages-transmit1">
  <h3>Mails &uuml;bertragen</h3>
  <p>Mit dieser Funktion kannst du ausgew&auml;hlte HTN-Ingame-Nachrichten zur Archivierung oder,
  um Platz im Postfach zu schaffen,
  an deine Email-Adresse schicken.<br />
  Du erh&auml;ltst dann eine Email, in deren Anhang du alle gew&auml;hlten Ingame-Messages als Textdateien findest.</p>
  <form action="mail.php?a=transmit2&amp;sid=' . $sid . '" method="post">
  <table>
  <tr id="messages-transmit1-mail-address">
  <th>Email-Adresse:</th>
  <td><input type="text" value="' . $usr['email'] . '" name="email" /></td>
  </tr>
  <tr id="messages-transmit1-mail-subject">
  <th>Email-Betreff:</th>
  <td><input type="text" value="HackTheNet-Ingame-Mails" name="subject" /></td>
  </tr>
  <tr><th>Ordner:</th>
  <td><input type="checkbox" name="in" value="yes" checked="checked" /> Nachrichten-Eingang<br />
  <input type="checkbox" name="out" value="yes" checked="checked" /> Versendete Nachrichten<br />
  <input type="checkbox" name="arc" value="yes" checked="checked" /> Nachrichten-Archiv</td>
  </tr>
  <tr id="messages-transmit1-confirm">
  <td colspan="2"><input type="submit" value="Weiter &raquo;" /></td>
  </tr>
  </table>
  </form>
  </div>
  </div>
  ';
        $layout->createlayout_bottom();
        break;

    case 'transmit2': //------------------------- TRANSMIT 2 -------------------------------
        $layout->createlayout_top('HackTheNet - Messages - Mails übertragen');
        echo '<div class="content" id="messages">
  <h2>Messages</h2>
  <div id="messages-transmit2">
  <h3>Mails &uuml;bertragen</h3>
  ';
        $email = $_REQUEST['email'];
        $subject = $_REQUEST['subject'];
        $e = '';
        if (!$mail->check_email($email))
            $e .= 'Bitte eine g&uuml;ltige Email-Adresse angeben!' . "\n";
        if ($subject == '')
            $e .= 'Bitte einen Betreff f&uuml;r die Email eingeben!' . "\n";
        if (!($_REQUEST['in'] == 'yes' || $_REQUEST['out'] == 'yes' || $_REQUEST['arc'] ==
            'yes'))
            $e .= 'Bitte mindestens einen Ordner ausw&auml;hlen!' . "\n";
        if ($e != '')
        {
            echo '<div class=error>' . $e . '</div>';
            site_footer();
            exit;
        }
        $code = random_string(10);
        $get->put_file($DATADIR . '/tmp/mail_transmit_' . $code . '.txt', $email . "\x0b" . $subject .
            "\x0b" . $_REQUEST['in'] . "\x0b" . $_REQUEST['out'] . "\x0b" . $_REQUEST['arc']);
        echo '<p>W&auml;hle jetzt die Mails, die du &uuml;bertragen m&ouml;chtest!</p>
  <form action="mail.php?a=transmit3&amp;sid=' . $sid . '&amp;code=' . $code .
            '" method="post">
  <table>
  <tr>
  <th class="number">Nummer</th>
  <th class="from">Absender</th>
  <th class="time">Zeit</th>
  <th class="title">Betreff</th>
  <th class="checkbox">Markieren</th>
  </tr>
  ';

        if ($_REQUEST['in'] == 'yes')
            $mail->transmit2_mail_list('in');
        if ($_REQUEST['out'] == 'yes')
            $mail->transmit2_mail_list('out');
        if ($_REQUEST['arc'] == 'yes')
            $mail->transmit2_mail_list('arc');
        echo '<tr id="messages-transmit2-confirm">
  <td colspan="7"><input type="submit" value="Fertigstellen" /></td>
  </tr>
  </table>
  </form>
  </div>
  </div>
  ';
        $layout->createlayout_bottom();
        break;

    case 'transmit3': //------------------------- TRANSMIT 3 -------------------------------
        $fn = $DATADIR . '/tmp/mail_transmit_' . $_REQUEST['code'] . '.txt';
        if (file_exists($fn) == false)
        {
            simple_message('FEHLER!');
            exit;
        }
        list($email, $subject, $inbox, $outbox, $archive) = explode("\x0b", $get->get_file($fn));
        #unlink($fn); unset($fn);

        settype($list, 'array');

        $bound = 'BOUND_' . random_string(5) . '_HTN';
        #$bound='BOUND';

        $body = '';

        if ($inbox == 'yes')
            transmit3_addbox('in');
        if ($outbox == 'yes')
            transmit3_addbox('out');
        if ($archive == 'yes')
            transmit3_addbox('arc');

        $msg = nicetime() . LF . 'http://www.hackthenet.org/' . LF . "\n" . 'Hallo ' . $usr['name'] .
            '!' . LF . 'Hier kommen deine HackTheNet-Ingame-Messages!' . LF . "\n" .
            'Es sind insgesamt ' . count($list) .
            ' Nachrichten, die jeweils als Textdatei an diese Mail angehangen wurden:' . "\n";
        foreach ($list as $mail)
        {
            $msg .= '    » ' . $mail . "\n";
        }
        $msg .= "\n" . LF . 'Greetz' . LF . 'HackTheNet-Team';
        $header = 'From: HackTheNet-Mail-Robot <robot@hackthenet.org>' . LF . 'To: ' . $usr['name'] .
            ' <' . $email . '>' . LF . 'X-Mailer: HackTheNet-Mail-Robot by IR' . LF .
            'MIME-Version: 1.0' . LF . 'Content-Type: multipart/mixed; boundary="' . $bound .
            '"';

        $body = '--' . $bound . "\n" . 'Content-Type: text/plain; charset="ISO-8859-1"' .
            "\n" . 'Content-Disposition: inline' . "\n" . 'Content-Transfer-Encoding: 8bit' .
            "\n" . LF . $msg . "\n" . $body;

        $layout->createlayout_top('HackTheNet - Messages');
        echo '<div class="content" id="messages">' . "\n";
        #if(!$localhost)
        {
            if (@mail($email, $subject, $body, $header))
            {
                echo '<div class="ok">' . LF . '<h3>Aktion ausgeführt</h3>' . LF .
                    '<p>Die Email wurde an ' . $email . ' verschickt.</p>' . LF . '</div>' . "\n";
                echo '<div class="important">' . LF . '<h3>Achtung!</h3>' . LF .
                    '<p>Pr&uuml;fe erst, ob sie angekommen ist, bevor du die Original-Mails l&ouml;schst.</p>' .
                    LF . '</div>' . "\n";
            } else
            {
                echo '<div class="error">' . LF . '<h3>Fehler!</h3>' . LF .
                    '<p>Beim Verschicken der Email trat ein Fehler auf.</p>' . LF . '</div>' . "\n";
            }

        }
        /*else
        {
        $get->file_put('F:\COOOOL.eml','Subject: '.$subject."\n".$header."\n"."\n".$body);
        echo 'FRESH';
        
        }*/
        echo '</div>';
        $layout->createlayout_bottom();


        break;

}



?>