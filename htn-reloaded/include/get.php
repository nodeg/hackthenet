<?php

class get
{
    public function get_file($filename)
    {
        //----------- File Get -----------------
        if (function_exists('file_get_contents'))
            return file_get_contents($filename);
        $file = @fopen($filename, 'r');
        if ($file)
        {
            if ($fsize = @filesize($filename))
            {
                $fdata = fread($file, $fsize);
            } else
            {
                while (!feof($file))
                    $fdata .= fread($file, 1024);
            }
            fclose($file);
        }
        return $fdata;
    }

    public function put_file($filename, $strContent)
    {
        //----------- File Put -----------------
        if (function_exists('file_put_contents'))
            return file_put_contents($filename, $strContent);

        $fpms = (int)@fileperms($filename);
        if ($fpms < 666 && $fmps != 0)
            @chmod($filename, 0666);
        if (strlen($strContent) < 1)
        {
            @unlink($filename);
            return true;
        }
        $file = @fopen($filename, 'w+');
        if ($file)
        {
            fwrite($file, $strContent);
            fclose($file);
            $r = true;
        } else
            $r = false;
        $fpms = (int)@fileperms($filename);
        if ($fpms < 666 && $fmps != 0)
            @chmod($filename, 0666);
        return $r;
    }

    function get_filecount($verz)
    {
        //------------------------- Get File Count -------------------------------
        $cnt = 0;
        $h = opendir($verz);
        while ($fn = readdir($h))
        {
            if (is_file($verz . '/' . $fn))
                $cnt++;
        }
        closedir($h);
        return $cnt;
    }
    function get_Cluster($val, $by = 'id')
    {
        $dbc = new dbc();
        $r = $dbc->db_query('SELECT * FROM clusters WHERE ' . mysql_escape_string($by) .
            ' LIKE \'' . mysql_escape_string($val) . '\' LIMIT 1');
        return ((int)@mysql_num_rows($r) > 0 ? mysql_fetch_assoc($r) : false);
    }
    
    function get_news()
    {
        $dbc = new dbc();
        $r = $dbc->db_query('SELECT * FROM news ORDER BY Datum DESC');
        return ((int)@mysql_num_rows($r) > 0 ? mysql_fetch_assoc($r) : false);
    }
    
    function get_pwd($id)
    {
        $dbc = new dbc();
        $r = $dbc->db_query('SELECT * FROM users WHERE id=' . mysql_escape_string($id) . '');
        return ((int)@mysql_num_rows($r) > 0 ? mysql_fetch_assoc($r) : false);
    }

    function get_User($val, $by = 'id')
    {
        $dbc = new dbc();
        $val = str_replace('_', '\_', $val);
        $val = str_replace('%', '\%', $val);
        $r = $dbc->db_query('SELECT * FROM users WHERE ' . mysql_escape_string($by) .
            ' LIKE \'' . mysql_escape_string($val) . '\' LIMIT 1');
        return ((int)@mysql_num_rows($r) > 0 ? mysql_fetch_assoc($r) : false);
    }
    
    function get_lang($id)
    {
        $dbc = new dbc();
        $r = $dbc->db_query('SELECT * FROM users WHERE id=' . mysql_escape_string($id) . '');
        return ((int)@mysql_num_rows($r) > 0 ? mysql_fetch_assoc($r) : false);
    }

    function get_PC($id, $by = 'id')
    {
        $dbc = new dbc();
        $r = $dbc->db_query('SELECT * FROM pcs WHERE ' . mysql_escape_string($by) .
            ' LIKE \'' . mysql_escape_string($id) . '\' LIMIT 1');
        return ((int)@mysql_num_rows($r) > 0 ? mysql_fetch_assoc($r) : false);
    }

    function get_Country($type, $val)
    {
        //---------- Get Country -----------------
        include ('data/static/country_data.inc.php');
        reset($countrys);
        if ($type != 'id')
        {
            while (list($bez, $item) = each($countrys)):
                if ($item[$type] == $val)
                {
                    return $item;
                    break;
                }
            endwhile;
        } else
        {
            if (isset($countrys[$val]))
                return $countrys[$val];
        }
        return false;
    }
    function get_PCPoints($pc, $mode = 'byid')
    {
        $gres = new gres();
        //---------- Get PC Points -----------------
        global $STYLESHEET, $DATADIR;
        global $cpu_levels, $ram_levels;
        if ($mode == 'byid')
            $pcdat = @mysql_fetch_assoc(db_query('SELECT * FROM pcs WHERE id=\'' .
                mysql_escape_string($pc) . '\''));
        else
            $pcdat = $pc;
        $pcpoints = 0;
        $pcpoints += $pcdat['cpu'] * 10;
        $pcpoints += $pcdat['ram'] * 10;
        $pcpoints += $gres->xpoint($pcdat['mm']);
        $pcpoints += $gres->xpoint($pcdat['bb']);
        $pcpoints += $gres->xpoint($pcdat['lan']);
        $pcpoints += $gres->xpoint($pcdat['fw']);
        $pcpoints += $gres->xpoint($pcdat['mk']);
        $pcpoints += $gres->xpoint($pcdat['av']);
        $pcpoints += $gres->xpoint($pcdat['sdk']);
        $pcpoints += $gres->xpoint($pcdat['ips']);
        $pcpoints += $gres->xpoint($pcdat['ids']);
        $pcpoints = round($pcpoints, 0);
        $pcpoints -= 31;
        return $pcpoints;
    }


    function get_IP()
    {
        //------------------------- Get IP -------------------------------
        if ($_SERVER['HTTP_X_FORWARDED_FOR'])
        {
            if ($_SERVER['HTTP_CLIENT_IP'])
            {
                $proxy = $_SERVER['HTTP_CLIENT_IP'];
            } else
            {
                $proxy = $_SERVER['REMOTE_ADDR'];
            }
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else
        {
            if ($_SERVER['HTTP_CLIENT_IP'])
            {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else
            {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        }
        if ($ip != $_SERVER['REMOTE_ADDR'])
            $ip = $_SERVER['REMOTE_ADDR'] . ' (' . $ip . ')';
        $r['ip'] = $ip;
        $r['proxy'] = $proxy;
        return $r;
    }
    function get_nextramlevel($stage)
    {
        //----------------- Next RAM Level -----------------------
        global $ram_levels;
        if ($stage >= $ram_levels[0])
            $stage = array_search($stage, $ram_levels);
        return $stage + 1;
    }

    function get_nextcpulevel($stage)
    {
        //----------------- Next CPU Level -----------------------
        global $cpu_levels;
        if ($stage >= $cpu_levels[0])
            $stage = array_search($stage, $cpu_levels);
        return $stage + 1;
    }

    function get_lastcpulevel($stage)
    {
        //----------------- Last CPU Level -----------------------
        global $cpu_levels;
        if ($stage >= $cpu_levels[0])
            $stage = array_search($stage, $cpu_levels);
        if ($stage > 0)
        {
            return $stage - 1;
        } else
            return 0;
    }
    function get_TableInfo($table, $db = '')
    {
        global $server;
        if ($db == '')
            $db = dbname($server);
        $sql = 'SHOW TABLE STATUS FROM ' . mysql_escape_string($db) . ' LIKE \'' .
            mysql_escape_string($table) . '\';';
        #echo $sql;
        $r = mysql_db_query($db, $sql);
        #echo mysql_error();
        return mysql_fetch_assoc($r);
    }

    public function get_OnlineUserCnt($server)
    {
        $dbcn = new dbc();
        $r = mysql_db_query($dbcn->dbname($server),
            'SELECT COUNT(id) FROM users WHERE sid_lastcall > ' . (time() -
            SID_ONLINE_TIMEOUT));
        $tmp = mysql_fetch_row($r);
        return $tmp[0];
    }
    function get_gdph($_pc = '')
    {
        $gres = new gres();
        //---------- Get Total Money per Hour -----------------
        global $STYLESHEET, $DATADIR, $pc;
        if ($_pc == '')
            $_pc = $pc;
        return $gres->calc_mph($_pc[ads], DPH_ADS) + $gres->calc_mph($_pc[dialer],
            DPH_DIALER) + $gres->calc_mph($_pc[auctions], DPH_AUCTIONS) + $gres->calc_mph($_pc[bankhack],
            DPH_BANKHACK);
    }

    function get_maxbb($_pc = '')
    {
        //---------- Get Max BucksBunker -----------------
        global $STYLESHEET, $DATADIR, $pc;
        if ($_pc == '')
            $_pc = $pc;
        $max = floor((float)$_pc[bb] * 13130);
        return $max;
    }

    function get_maxmailsforuser($box, $bigacc = 'no')
    {
        //---------------- GET MAX MAILS FOR USER ----------------
        switch ($box)
        {
            case 'in':
                $max = 20;
                break;
            case 'arc':
                $max = 25;
                break;
            case 'out':
                $max = 10;
                break;
            case 'sys':
                $max = 15;
                break;
        }
        if ($bigacc == 'yes')
            $max = $max * 10;
        return $max;
    }

    function get_maxmails($box)
    {
        //---------------- GET MAX MAILS ----------------
        global $usr;
        return $this->get_maxmailsforuser($box, $usr['bigacc']);
    }

    function get_iteminfo($key, $stage)
    {
    	$ingame = new ingame();
        //--------------------- Get Item Info --------------------------
        global $STYLESHEET, $DATADIR, $pc;
        global $cpu_levels, $ram_levels, $server;
        $d;
        $c;
        if ($stage < 1)
            $stage = 1;
        $stage = (float)$stage;
        switch ($key)
        {
            case 'cpu':
                switch ($stage)
                {
                    case 0:
                        $d = 20;
                        $c = 60;
                        break;
                    case 1:
                        $d = 25;
                        $c = 80;
                        break;
                    case 2:
                        $d = 30;
                        $c = 90;
                        break;
                    case 3:
                        $d = 35;
                        $c = 110;
                        break;
                    case 4:
                        $d = 40;
                        $c = 120;
                        break;
                    case 5:
                        $d = 45;
                        $c = 140;
                        break;
                    case 6:
                        $d = 50;
                        $c = 150;
                        break;
                    case 7:
                        $d = 55;
                        $c = 255;
                        break;
                    case 8:
                        $d = 55;
                        $c = 300;
                        break;
                    case 9:
                        $d = 60;
                        $c = 512;
                        break;
                    case 10:
                        $d = 90;
                        $c = 768;
                        break;
                    case 11:
                        $d = 120;
                        $c = 1150;
                        break;
                    case 12:
                        $d = 150;
                        $c = 1730;
                        break;
                    case 13:
                        $d = 180;
                        $c = 2590;
                        break;
                    case 14:
                        $d = 210;
                        $c = 3890;
                        break;
                    case 15:
                        $d = 240;
                        $c = 5800;
                        break;
                    case 16:
                        $d = 300;
                        $c = 8500;
                        break;
                    case 17:
                        $d = 360;
                        $c = 12000;
                        break;
                    case 18:
                        $d = 420;
                        $c = 18000;
                        break;
                    case 19:
                        $d = 460;
                        $c = 25000;
                        break;
                    case 20:
                        $d = 580;
                        $c = 50000;
                        break;
                }
                break;
            case 'ram':
                switch ($stage)
                {
                    case 0:
                        $d = 30;
                        $c = 200;
                        break;
                    case 1:
                        $d = 45;
                        $c = 300;
                        break;
                    case 2:
                        $d = 60;
                        $c = 500;
                        break;
                    case 3:
                        $d = 70;
                        $c = 800;
                        break;
                    case 4:
                        $d = 90;
                        $c = 1000;
                        break;
                    case 5:
                        $d = 120;
                        $c = 1200;
                        break;
                    case 6:
                        $d = 150;
                        $c = 3000;
                        break;
                    case 7:
                        $d = 180;
                        $c = 4000;
                        break;
                    case 8:
                        $d = 210;
                        $c = 10000;
                        break;
                }
                break;
            case 'mm':
                $stage += 0.5;
                $c = $stage * 51;
                $d = $stage * 10;
                break;
            case 'bb':
                $stage += 0.5;
                $c = $stage * 45;
                $d = $stage * 11;
                break;
            case 'lan':
                $stage += 0.5;
                $c = $stage * 150;
                $d = $stage * 25;
                break;
            case 'sdk':
                $stage += 0.5;
                $c = $stage * 100;
                $d = $stage * 15;
                break;
            case 'fw':
                $stage += 0.5;
                $c = $stage * 49;
                $d = $stage * 5;
                break;
            case 'av':
                $stage += 0.15;
                $c = $stage * 50;
                $d = $stage * 6;
                break;
            case 'mk':
                $stage += 0.5;
                $c = $stage * 100;
                $d = $stage * 16;
                break;
            case 'ips':
                $stage += 0.5;
                $c = $stage * 33;
                $d = $stage * 8;
                break;
            case 'ids':
                $stage += 0.5;
                $c = $stage * 44;
                $d = $stage * 7;
                break;
            case 'rh':
                $stage += 0.5;
                $c = $stage * 400;
                $d = $stage * 10;
                break;
            case 'trojan':
                $stage += 0.5;
                $c = $stage * 39;
                $d = $stage * 8;
                break;
        }


        $r['c'] = ceil($c); # Kosten
        $r['d'] = floor($d); # Dauer in Minuten

        if ($key != 'cpu' && $key != 'ram')
        {
            $r['c'] *= 4.5;
            $df = $ingame->duration_faktor($pc['cpu'], $pc['ram']);
            $r['d'] *= $df * 2;

            $r['c'] = floor($r['c']);
            $r['d'] = ceil($r['d']);

        }

        return $r;

    }
    
    function get_checkedmail_str($boxid, $prefix = 'c')
    {
        global $usrid;
        
        $dbc = new dbc();

        settype($a, 'array');
        $result = $dbc->db_query('SELECT mail FROM mails WHERE user=\'' . $usrid . '\' AND box=\'' .
            mysql_escape_string($boxid) . '\'');

        while ($data = mysql_fetch_assoc($result))
        {
            $k = $prefix . $data['mail'];
            if ($_REQUEST[$k] == 1)
                array_push($a, $data['mail']);
        }

        foreach ($a as $item)
        {
            $s .= 'mail=' . mysql_escape_string($item) . ' OR ';
        }
        $s = substr($s, 0, strlen($s) - 4);

        return $s;
    }

}

?>