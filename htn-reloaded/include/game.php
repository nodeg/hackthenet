<?php

class game
{
    function infobox($titel, $class, $text, $param = 'class')
    {
        return '<div ' . $param . '="' . $class . '">' . LF . '<h3>' . $titel . '</h3>' .
            LF . '<p>' . $text . '</p>' . LF . '</div>' . "\n";
    }
    function showinfo($id, $txt, $val = -1)
    {
        $ingame = new ingame();
        global $pc, $sid, $pcid, $usrid, $ram_levels, $cpu_levels;
        if ($val == -1)
            $val = $pc[$id];
        if ($id == 'ram')
            $val = $ram_levels[$val];
        elseif ($id == 'cpu')
            $val = $cpu_levels[$val];
        $name = $ingame->idtoname($id);
        if ($val && $val != '0.0')
        {
            if (strlen((string )$val) == 1 || $val == 10)
                $val = $val . '.0';
            echo '<a href="game.php?m=item&amp;item=' . $id . '&amp;sid=' . $sid . '">' . $name .
                '</a>';
            if ($txt != '')
                echo ' (' . str_replace('%v', $val, $txt) . ')';
            echo "\n";
        }
    }

    function br()
    {
        echo '<br />' . "\n";
    }

    function mmitem($name, $id, $av, $f)
    {
        $gres = new gres();
        global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $pc, $bucks, $pcid, $usrid, $sid;
        $v = (float)$pc['mm'];
        if ($v >= $av)
        {
            echo '<tr class="name">' . LF . '<td>' . $name . '</td>' . LF .
                '<td class="level">Level ' . $pc[$id] . '</td>' . LF . '<td class="profit">' . $gres->
                calc_mph($pc[$id], $f) . ' Credits/h</td>' . LF . '<td>';
            if ($pc[$id] < 5)
            {
                $c = (((int)$pc[$id] + 1) * 15 * $f);
                if ($pc['credits'] - $c >= 0)
                    echo '<a href="game.php?mode=update&item=' . $id . '&sid=' . $sid .
                        '">Update</a>';
                else
                    echo 'Update';
                echo ' kostet ' . $c . ' Credits';
            } else
            {
                echo 'Kein Update mehr m&ouml;glich!';
            }
            echo '</td>' . LF . '</tr>' . "\n";
        }
    }

    function updredir($x = '')
    {
        global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $pcid, $usrid, $sid;
        header('Location: game.php?mode=item&item=mm&sid=' . $sid . $x);
    }

    function buildinfo($id)
    {
    	
    	$gres = new gres();
    	$ingame = new ingame();
    	$get = new get();
    	
        global $STYLESHEET, $DATADIR, $pc, $bucks, $sid, $usrid, $pcid;
        global $ram_levels, $cpu_levels, $r, $full;
        global $upgrcode;
        if ($gres->isavailb($id, $pc))
        {
            $inf = $get->get_iteminfo($id, $pc[$id]);

            $m = intval($inf['d']);
            $xm = $m;

            if ($m >= 60)
            {
                $m = floor($m / 60) . ' h';
                if (floor($xm % 60) > 0)
                    $m .= ' : ' . floor($xm % 60) . ' min';
            } else
                $m .= ' min';
            $xm *= 60;
            #$xm+=time();

            $lastend = ($full < 1 ? time() : mysql_result($r, $full - 1, 'end'));
            $xm += $lastend;

            $m .= '</td><td>' . $gres->nicetime2($xm, false, ' um ', ' Uhr');
            $name = $ingame->idtoname($id);
            $val = $pc[$id];
            $sval = $gres->formatitemlevel($id, $val);
            $s = $name . ' (' . $sval . ')';
            echo '<tr>' . LF . '<td>';
            echo $s;
            echo '</td>' . "\n";
            echo '<td>' . $m . '</td><td>' . $inf['c'] . ' Credits</td>';
            echo '<td>';
            if ($pc['credits'] >= $inf['c'])
            {
                echo '<a href="game.php?m=upgrade&amp;z=' . $upgrcode . '&amp;item=' . $id .
                    '&amp;sid=' . $sid . '" class="buy">';
                if ($pc[$id] > 0 || $id == 'ram' || $id == 'cpu')
                    $s = 'Upgrade kaufen';
                else
                    $s = 'Kaufen';

                echo $s . '</a>';
            } else
            {
                echo 'Nicht gen&uuml;gend Geld';
            }
            echo '</td></tr>';
            return true;

        }
        return false;
    }

}
?>