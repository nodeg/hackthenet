<?php

class dbc
{
    public function dbname($srvid = -1)
    {
        global $database_prefix, $database_suffix, $enable_multi_server_support, $database_name;
        if ($enable_multi_server_support)
        {
            return $database_prefix . $srvid . $database_suffix;
        } else
        {
            return $database_name;
        }
    }
    public function db_select($name)
    {
        $r = mysql_select_db($name);
        if (mysql_error() != '' || $r == false)
        {
            die('Fehler beim Auswaehlen der DB:<br />' . mysql_error());
        }
        return $r;
    }

    public function db_query($q)
    {
        global $_query_cnt;

        $r = mysql_query($q);
        $_query_cnt++;
        if (mysql_error() != '')
        {
            die('<tt>' . $q . '</tt><br />caused an error:<br />' . mysql_error());
        }
        return $r;
    }

}

?>