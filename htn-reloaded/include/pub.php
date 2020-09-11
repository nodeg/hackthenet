<?php

class pub
{

    function checknick($nick)
    {
        global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $nickzeichen;
        $b = true;
        $len = strlen($nick);
        for ($i = 0; $i < $len; $i++)
        {
            $zz = substr($nick, $i, 1);
            if (strstr($nickzeichen, (string )$zz) == false)
            {
                $b = false;
                break;
            }
        }
        $x = eregi_replace('[-_:@.!=?$%&/0-9]', '', $nick);
        if (trim($x) == '')
            $b = false;
        return $b;
    }

    function stats($server)
    {
    	$dbc = new dbc();
    	$get = new get();
    	$gres = new gres();
    	
        if (mysql_select_db($dbc->dbname($server)))
        {
            $uinfo = $get->get_tableinfo('users', $dbc->dbname($server));
            $pcinfo = $get->get_tableinfo('pcs', $dbc->dbname($server));
            $mailinfo = $get->get_tableinfo('mails', $dbc->dbname($server));
            $attackinfo = $get->get_tableinfo('attacks', $dbc->dbname($server));
            $upgradeinfo = $get->get_tableinfo('upgrades', $dbc->dbname($server));

            $cnt1 = $uinfo['Rows'];
            $cnt2 = $pcinfo['Rows'];

            $attackinfo = $gres->numfmt($attackinfo['Rows'], 0) . ' (davon ';
            $r = $dbc->db_query('SELECT from_pc FROM attacks WHERE success=1;');
            $attackinfo .= $gres->numfmt(mysql_num_rows($r), 0) . ' erfolgreich)';

            $r = $dbc->db_query('SELECT SUM(credits) FROM transfers;');
            $total_credits = (int)mysql_result($r, 0);

            $cnt = $cnt2 - $cnt1;
            $cnt3 = (int)@$get->get_file('data/_server' . $server . '/logins_' . strftime
                ('%x') . '.txt');

            $cnt4 = $get->Get_OnlineUserCnt($server);

            include ('data/static/country_data.inc.php');
            echo '<h3>Server ' . $server . '</h3>
      <table>
      <tr>
      <th>Registrierte User:</th>
      <td>' . $gres->numfmt($cnt1, 0) . ' von max. ' . $gres->numfmt(MAX_USERS_PER_SERVER,
                0) . '</td>
      </tr>
      <tr>
      <th>Computer:</th>
      <td>' . $gres->numfmt($cnt2, 0) . ' von max. ' . $gres->numfmt(count($countrys) *
                254, 0) . '</td>
      </tr>
      <tr>
      <th>Upgrades am Laufen:</th>
      <td>' . $gres->numfmt($upgradeinfo['Rows'], 0) . '</td>
      </tr>
      <tr>
      <th>Spieler online:</th>
      <td>' . $gres->numfmt($cnt4, 0) . '</td>
      </tr>
      <tr>
      <th>Logins heute:</th>
      <td>' . $gres->numfmt($cnt3, 0) . '</td>
      </tr>
      ';

            $fn = 'data/_server' . $server . '/logins_' . strftime('%x', time() - 86400) .
                '.txt';
            if (file_exists($fn))
            {
                $cnt = (int)$get->get_file($fn);
                echo '<tr>' . LF . '<th>Logins gestern:</th>' . LF . '<td>' . $gres->numfmt($cnt,
                    0) . '</td>' . LF . '</tr>' . LF;
            }

            echo '<tr>
      <th>Verschickte Ingame-Mails:</th>
      <td>' . $gres->numfmt($mailinfo['Auto_increment'], 0) . '</td>
      </tr>
      <tr>
      <th>Angriffe:</th>
      <td>' . $attackinfo . '</td>
      </tr>
      <tr>
      <th>Credits überwiesen:</th>
      <td>insgesamt ' . $gres->numfmt($total_credits, 0) . '</td>
      </tr>
      ';

            echo '</table>' . LF;
        }
    }

    function generateMnemonicPassword()
    {

        $charset[0] = array('a', 'e', 'i', 'o', 'u');
        $charset[1] = array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p',
            'r', 's', 't', 'v', 'w', 'x', 'z');
        $specials = array('!', '$', '%', '&', '/', '=', '?', '+', '-', '.', ':', ',',
            ';', '*', '#', '_');

        $password = '';

        for ($i = 1; $i <= 8; $i++)
        {
            $password .= $charset[$i % 2][array_rand($charset[$i % 2])];
        }

        for ($i = 0; $i < 2; $i++)
        {
            $password{mt_rand(1, strlen($password) - 2)} = $specials[mt_rand(0, count($specials) -
                1)];
            $password{mt_rand(0, strlen($password) - 1)} = mt_rand(0, 9);
        }

        return $password;
    }
}

?>