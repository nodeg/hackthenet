<?php
session_start();
define('IN_HTN', 1);
define('EDIT_TIME_OUT', 12 * 60 * 60, false);

$FILE_REQUIRES_PC = false;
include ('ingame.php');

$action = $_REQUEST['page'];
if ($action == '')
    $action = $_REQUEST['mode'];
if ($action == '')
    $action = $_REQUEST['action'];
if ($action == '')
    $action = $_REQUEST['a'];
if ($action == '')
    $action = $_REQUEST['m'];

$dbc = new dbc();
$get = new get();
$layout = new layout();
$cboard = new cboard();

$cix = $usr['cluster'];
$cluster = $get->get_cluster($cix);


switch ($action)
{

    case 'board': // ------------------------ BOARD ---------------------------------

        $layout->createlayout_top('HackTheNet - Cluster-Board');
        echo '<div class="content" id="cluster-board">' . LF;
        echo '<h2>Cluster-Board</h2>' . LF;
        echo '<p><a href="cboard.php?sid=' . $sid .
            '&amp;a=newthreadform">Neuen Beitrag erstellen</a></p>' . "\n";

        echo '<div id="cluster-board-folder1">' . LF . '<h3>Ordner 1 (' . $cluster['box1'] .
            ')</h3>';
        $cboard->listboard(0);
        echo '</div>' . LF . '<div id="cluster-board-folder2">' . LF . '<h3>Ordner 2 (' .
            $cluster['box2'] . ')</h3>' . "\n";
        $cboard->listboard(1);
        echo '</div>' . LF . '<div id="cluster-board-folder3">' . LF . '<h3>Ordner 3 (' .
            $cluster['box3'] . ')</h3>' . "\n";
        $cboard->listboard(2);
        echo '</div>' . LF . '</div>' . "\n";
        $layout->createlayout_bottom();

        break;

    case 'admin': // ------------------------ ADMIN ---------------------------------

        if ($usr[clusterstat] != CS_ADMIN && $usr[clusterstat] != CS_COADMIN)
        {
            $gres->no_('mt0');
            exit;
        }

        $box = (int)$_REQUEST['box'];

        $result = $dbc->db_query('SELECT thread,box FROM cboards WHERE cluster LIKE \'' .
            mysql_escape_string($cix) . '\' AND box LIKE \'' . mysql_escape_string($box) . '\' AND relative LIKE \'-1\'');
        $cnt = mysql_num_rows($result);
        while ($data = mysql_fetch_assoc($result))
        {
            $id = $data['thread'];
            if ($_POST['edit' . $id] != 'yes')
                continue;

            $newbox = -1;
            switch ($_POST['axion'])
            {
                case 'delete':
                    $dbc->db_query('DELETE FROM cboards WHERE cluster LIKE \'' . mysql_escape_string($cix) .
                        '\' AND thread LIKE \'' . mysql_escape_string($id) . '\' AND relative LIKE \'-1\'');
                    $dbc->db_query('DELETE FROM cboards WHERE cluster LIKE \'' . mysql_escape_string($cix) .
                        '\' AND relative LIKE \'' . mysql_escape_string($id) . '\'');
                    break;
                case 'folder1':
                    $newbox = 0;
                    break;
                case 'folder2':
                    $newbox = 1;
                    break;
                case 'folder3':
                    $newbox = 2;
                    break;
            }
            if ($newbox != $data['box'] && $newbox > -1)
            {
                $dbc->db_query('UPDATE cboards SET box=\'' . mysql_escape_string($newbox) . '\' WHERE cluster LIKE \'' .
                    mysql_escape_string($cix) . '\' AND thread LIKE \'' . mysql_escape_string($id) .
                    '\' AND relative LIKE \'-1\'');
            }
        }

        header('Location: cboard.php?sid=' . $sid . '&mode=board&rnd=' . $gres->random_string());

        break;

    case 'addthread': // ------------------------ ADD THREAD ---------------------------------

        $ts = time();
        $title = trim($_POST['title']);
        $text = $_POST['text'];
        $boxid = (int)$_POST[box];
        if ($boxid < 0 || $boxid > 2)
            $boxid = 0;

        if ($text != '')
        {

            if ($title == '')
                $title = '(Beitrag ohne Titel)';
            if (strlen($title) > 255)
                $title = substr($title, 0, 255);
            $title = $cboard->formatText($title);
            $text = $cboard->formatText($text);

            $sql = 'INSERT INTO cboards VALUES (\'' . $cix . '\', \'0\', \'-1\', \'' . $usrid .
                '\', \'' . mysql_escape_string($usr['name']) . '\', \'' . $usr['clusterstat'] .
                '\', \'' . $ts . '\', \'' . mysql_escape_string($title) . '\', \'' .
                mysql_escape_string($text) . '\', \'' . mysql_escape_string($boxid) . '\')';
            $dbc->db_query($sql);

            header('Location: cboard.php?sid=' . $sid . '&mode=board&rnd=' . $gres->random_string());

        } else
            $gres->simple_message('Bitte einen Text für den Beitrag eingeben!');
        break;

    case 'newthreadform': // ------------------------ NEW THREAD FORM ---------------------------------

        $layout->createlayout_top('HackTheNet - Cluster-Board - Beitrag erstellen');
        echo '<div class="content" id="cluster-board">' . LF . '<h2>Cluster-Board</h2>' .
            LF . '<div id="cluster-board-newthread">' . LF . '<h3>Beitrag erstellen</h3>' .
            "\n";
        $cboard->showform();
        echo '</div>' . LF . '</div>' . "\n";
        $layout->createlayout_bottom();

        break;

    case 'addreply': // ------------------------ ADD REPLY ---------------------------------

        $ts = time();
        $title = trim($_POST['title']);
        $text = $_POST['text'];
        $thread = (double)$_REQUEST[threadid];

        if ($text != '')
        {

            if ($title == '')
                $title = '(Beitrag ohne Titel)';
            if (strlen($title) > 255)
                $subject = substr($title, 0, 255);
            $title = $cboard->formatText($title);
            $text = $cboard->formatText($text);

            $sql = 'INSERT INTO cboards VALUES (\'' . mysql_escape_string($cix) . '\', \'0\', \'' .
                mysql_escape_string($thread) . '\', \'' . $usrid . '\', \'' .
                mysql_escape_string($usr['name']) . '\', \'' . mysql_escape_string($usr['clusterstat']) .
                '\', \'' . mysql_escape_string($ts) . '\', \'' . mysql_escape_string($title) . '\', \'' .
                mysql_escape_string($text) . '\', \'-1\')';
            $dbc->db_query($sql);

            header('Location: cboard.php?sid=' . $sid . '&mode=board&rnd=' . $gres->random_string());

        } else
            $gres->simple_message('Bitte einen Text für den Beitrag eingeben!');

        break;

    case 'showthread': // ------------------------ SHOW THREAD ---------------------------------

        $layout->createlayout_top('HackTheNet - Cluster-Board - Beitrag');
        echo '<div class="content" id="cluster-board-post">' . "\n";
        echo '<h2>Cluster-Board</h2>' . "\n";

        $id = (double)$_REQUEST['threadid'];


        $result = $dbc->db_query('SELECT * FROM cboards WHERE thread LIKE \'' .
            mysql_escape_string($id) . '\' AND cluster LIKE \'' . mysql_escape_string($cix) .
            '\'');
        if (mysql_num_rows($result) > 0)
        {
            //settype($a,'array');
            $a = array();
            $cboard->show_beitrag(mysql_fetch_assoc($result));
            $result = $dbc->db_query('SELECT * FROM cboards WHERE relative LIKE \'' .
                mysql_escape_string($id) . '\' ORDER BY time ASC;');
            while ($data = mysql_fetch_assoc($result))
            {
                #array_unshift($a,$data);
                $cboard->show_beitrag($data);
            }
            foreach ($a as $item)
                $cboard->show_beitrag($item);
        } else
            exit;

        echo '<br /><br />' . LF . '<div id="cluster-board-post-reply">' . LF .
            '<h3>Antwort schreiben</h3>' . "\n";
        $cboard->showform('addreply');
        echo '</div>';
        echo '<p><a href="cboard.php?sid=' . $sid . '&m=board">Zurück</a></p>' . "\n";

        echo '</div>' . "\n";
        $layout->createlayout_bottom();

        break;

}

?>