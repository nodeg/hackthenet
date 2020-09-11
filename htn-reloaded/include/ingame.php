<?php

class ingame
{

    function badsession($s)
    {
    	$gres = new gres();
        global $sid;
        $sid = '';
        $gres->simple_message('Sitzung ung&uuml;ltig!<br />Bitte auf der <a href="./">Startseite</a> neu einloggen!<br /><br /><span style="font-size:9pt;">Grund: ' .
            $s . '</span>');
        exit;
    }

    function SetUserVal($name, $val, $usr = -1)
    {
    	$dbc = new dbc();
        global $usrid;
        if ($usr == -1)
            $usr = $usrid;
        $dbc->db_query('UPDATE users SET ' . $name . '=\'' . mysql_escape_string($val) .
            '\' WHERE id=' . $usr . ' LIMIT 1');
    }

    function SaveUserData()
    {
        //------------------------- Save User Data -------------------------------
        global $usrid, $usr;
        $this->SaveUser($usrid, $usr);
    }

    function SaveUser($usrid, $usr)
    {
    	$dbc = new dbc();
        //------------------------- Save User -------------------------------
        $s = '';
        while (list($bez, $val) = each($usr))
        {
            $s .= mysql_escape_string($bez) . '=\'' . mysql_escape_string($val) . '\',';
        }
        $s = trim($s, ',');
        if ($s != '')
            $dbc->db_query('UPDATE users SET ' . $s . ' WHERE id=\'' . $usrid . '\'');
    }

    function SaveCluster($id, $dat)
    {
        //------------------------- Save User -------------------------------
        $s = '';
        while (list($bez, $val) = each($dat))
        {
            $s .= mysql_escape_string($bez) . '=\'' . mysql_escape_string($val) . '\',';
        }
        $s = trim($s, ',');
        if ($s != '')
            $dbc->db_query('UPDATE clusters SET ' . $s . ' WHERE id=\'' . $id . '\'');
    }

    function SavePC($pcid, $pc)
    {	
    	$dbc = new dbc();
        //------------------------- Save PC -------------------------------
        $s = '';
        while (list($bez, $val) = each($pc))
        {
            $s .= mysql_escape_string($bez) . '=\'' . mysql_escape_string($val) . '\',';
        }
        $s = trim($s, ',');
        if (strlen($s) > 4)
            $dbc->db_query('UPDATE pcs SET ' . $s . ' WHERE id=\'' . $pcid . '\'');
    }

    function cscodetostring($code)
    {
        //----------------- Cluster Stat Code to String ------------------
        switch ($code)
        {
            case CS_ADMIN:
                $s = 'Admin';
                break;
            case CS_COADMIN:
                $s = 'LiteAdmin';
                break;
            case CS_WAECHTER:
                $s = 'W&auml;chter';
                break;
            case CS_JACKASS:
                $s = 'JackAss';
                break;
            case CS_WARLORD:
                $s = 'Warlord';
                break;
            case CS_KONVENTIONIST:
                $s = 'Konventionist';
                break;
            case CS_SUPPORTER:
                $s = 'Entwicklungsminister';
                break;
            case CS_MEMBER:
                $s = 'Mitglied';
                break;
            case CS_EXMEMBER:
                $s = 'Ex-Mitglied';
                break;
            case CS_MITGLIEDERMINISTER:
                $s = 'Mitgliederminister';
        }
        return $s;
    }

    function duration_faktor($cpu, $ram)
    {
        global $cpu_levels, $ram_levels;
        $r = (1 / (($cpu_levels[21] - $cpu_levels[0]) / (3 - 1))) * ($cpu_levels[21] - $cpu_levels[$cpu]) +
            1;
        $r = $r * 2;
        $tmp = (1 / (($ram_levels[9] - $ram_levels[0]) / (3 - 1))) * ($ram_levels[9] - $ram_levels[$ram]) +
            1;
        $r += $tmp;
        return round($r / 2.8, 5);
    }

    function IDToName($id)
    {
        //------------------------- ID to Name -------------------------------
        $s = '';
        switch (strtolower($id))
        {
            case 'cpu':
                $s = 'Prozessor';
                break;
            case 'ram':
                $s = 'Arbeitsspeicher';
                break;
            case 'mm':
                $s = 'MoneyMarket';
                break;
            case 'fw':
                $s = 'Firewall';
                break;
            case 'lan':
                $s = 'Internet-Anbindung';
                break;
            case 'mk':
                $s = 'Malware Kit';
                break;
            case 'av':
                $s = 'Anti-Virus-Programm';
                break;
            case 'sdk':
                $s = 'SDK (Software Development Kit)';
                break;
            case 'ips':
                $s = 'IP-Spoofing';
                break;
            case 'ids':
                $s = 'IDS (Intrusion Detection System)';
                break;
            case 'bb':
                $s = 'BucksBunker';
                break;
            case 'rh':
                $s = 'Remote Hijack';
                break;
            case 'trojan':
                $s = 'Trojaner';
                break;
            case 'da':
                $s = 'Distributed Attack';
                break;
        }
        return $s;
    }

    function AddSysMsg($user, $msg, $save = true)
    {
        //----- ADD SYSTEM MESSAGE -----
        global $STYLESHEET, $DATADIR, $usrid, $usr;

        $udat = getuser($user);
        if ($udat !== false)
        {
            $ts = time();
            $dbc->db_query('INSERT INTO sysmsgs VALUES(\'0\',\'' . mysql_escape_string($user) .
                '\',\'' . mysql_escape_string($ts) . '\',\'' . mysql_escape_string($msg) . '\',\'no\');');
            if ($save == true)
            {
                if ($user == $usrid)
                    $u = $usr;
                else
                    $u = $udat;
                $u[newmail] += 1;
                setuserval('newmail', $u[newmail], $user);
                if ($user == $usrid)
                    $usr = $u;
            }
            $r = $dbc->db_query('SELECT * FROM sysmsgs WHERE user=' . mysql_escape_string($user) .
                ' ORDER BY time ASC');
            $max = 15;
            $cnt = mysql_num_rows($r);
            if ($cnt > $max)
            {
                $cnt = $cnt - $max;
                for ($i = 0; $i < $cnt; $i++)
                {
                    $id = mysql_result($r, $i, 'msg');
                    $dbc->db_query('DELETE FROM sysmsgs WHERE msg=' . mysql_escape_string($id));
                }
            }
        }
    }

    function isattackallowed(&$ret, &$ret2)
    {
        //---------------- IS ATTACK ALLOWED ----------------
        global $STYLESHEET, $DATADIR, $usr, $pc, $usrid, $localhost;
        if ($localhost || is_noranKINGuser($usrid))
            return true;
        define('TO_1', 2 * 60, false);
        $x = floor((5 / 3) * (10 - $pc['lan']) + 5) * 60;
        define('TO_2', $x, false);
        $a = $usr['la'] + TO_1;
        $b = $pc['la'] + TO_2;
        if ($a > $b)
        {
            $ret = $a;
            $ret2 = $pc['la'];
        } else
        {
            $ret = $b;
            $ret2 = $usr['la'];
        }
        if ((($a <= time()) && ($b <= time())))
            return true;
        else
            return false;
    }

    function write_pc_list($usrid)
    {
        //---------------- WRITE PC LIST ----------------
        $s = '';
        $r = $dbc->db_query('SELECT id FROM pcs WHERE owner=\'' . $usrid . '\';');
        while ($x = mysql_fetch_assoc($r)):
            $s .= $x['id'] . ',';
        endwhile;
        $s = trim($s, ',');
        $dbc->db_query('UPDATE users SET pcs=\'' . mysql_escape_string($s) . '\' WHERE id=\'' .
            $usrid . '\';');
    }


    function tIsAvail($key, $_pc = -1)
    {
        //---------------- TROJANER IS AVAIL ----------------
        global $STYLESHEET, $DATADIR, $pc;
        if ($_pc == -1)
            $_pc = $pc;
        $b = false;
        if ($_pc['trojan'] >= 1 && $key == 'defacement')
            $b = true;
        elseif ($_pc['trojan'] >= 2.5 && $key == 'transfer')
            $b = true;
        elseif ($_pc['trojan'] >= 5 && $key == 'deactivate')
            $b = true;
        return $b;
    }

    function is_pc_attackable($pcdat)
        //---------------- IS PC ATTACKABLE ? ----------------

    {
        $xdefence = $pcdat['fw'] + $pcdat['av'] + $pcdat['ids'] / 2;
        $rscan = (int)(isavailh('scan', $pcdat));
        # ^^ 0 <= $xdefence <= 25 ^^
        #echo '<br />xdefence='.$xdefence.' min='.MIN_ATTACK_XDEFENCE.' scan='.(int)(isavailh('scan',$pcdat));
        if (count(explode(',', $owner['pcs'])) < 2 && (($xdefence <= MIN_ATTACK_XDEFENCE &&
            isavailh('scan', $pcdat) == false)))
        {
            #echo '<br>p1='.(int)($xdefence<MIN_ATTACK_XDEFENCE XOR isavailh('scan',$pcdat));

            return false;

        }
        return true;
    }

}
?>