<?php

define('IN_HTN', 1);
$starttime = microtime();
include ('config.php');
if (!$htn_installed)
{
    header("Location: install.php");
}
include ('gres.php');

define('REG_CODE_LEN', 24, false);

$layout = new layout();
$get = new get();
$pub = new pub();
$dbc = new dbc();
$gres = new gres();

$action = $_REQUEST['page'];
if ($action == '')
    $action = $_REQUEST['mode'];
if ($action == '')
    $action = $_REQUEST['action'];
if ($action == '')
    $action = $_REQUEST['a'];
if ($action == '')
    $action = $_REQUEST['m'];
if ($action == '')
    $action = $_REQUEST['d'];

switch ($action)
{

    case 'faq':
        $layout->showdoc('FAQ', 'faq');
        break;
    case 'credits':
        $layout->showdoc('Team', 'credits');
        break;
    case 'newpwd':
        $layout->showdoc('Neues Passwort anfordern', 'newpwd');
        break;
    case 'rules':
        $layout->showdoc('Regeln', 'rules');
        break;
    case 'impressum':
        $javascript = '<meta name="robots" content="noindex,nofollow">';
        $layout->showdoc('Impressum', 'impressum');
        break;

    case 'register':

        #simple_message('Im Moment ist keine Registrierung mehr möglich.');
        #exit;

        $layout->createlayout_top('HackTheNet - Account anlegen');
        echo '<div class="content" id="register">
  <h2>Registrieren</h2>
  ';
        if ($notif == '')
        {
            echo '<div class="important">' . LF;
            echo '<h3>Wichtig!</h3>' . LF;
            echo '<p>Jeder User verpflichtet sich, die <a href="pub.php?d=rules">Regeln</a> einzuhalten.<br />' .
                LF . 'Verst&ouml;&szlig;e gegen die Regeln f&uuml;hren zum Ausschluss vom Spiel!</p>' .
                LF;
            echo '</div>' . LF;
        }

        echo $notif . '
  
  <div id="register-step1">
  <h3>Schritt 1: Zugangsdaten und Server</h3>
  <form action="pub.php?a=regsubmit" method="post">
  <table>
  <tr><th>Server:</th><td>
  <input type="radio" name="server" value="1" checked="checked" /> Server 1<br />
  </td></tr>
  <tr>
  <th>Gew&uuml;nschter NickName:</th>
  <td><input name="nick" id="_nick" maxlength="20" /></td>
  </tr>
  <tr>
  <th>Deine Email-Adresse:</th>
  <td><input name="email" id="_email" maxlength="50" /><br />
  Nur wenn eine korrekte Email-Adresse angegeben wurde, kann der Account aktiviert werden!</td>
  </tr>
  <tr><th>Land für deinen PC:</th><td><select name="country">
  ';
        include ('data/static/country_data.inc.php');
        foreach ($countrys as $c)
        {
            echo "<option value=\"$c[id]\">$c[name] (10.47.$c[subnet].x)</option>";
        }
        echo '
  </select></td></tr>
  <tr>
  <td colspan="2" align="right">
  <input type="submit" value="Abschicken" /></td>
  </tr>
  </table>
  </form>
  </div>
  </div>
  ';
        $layout->createlayout_bottom();

        break;

    case 'regsubmit': // ----------------------- RegSubmit --------------------------

        $email = trim($_POST['email']);
        $nick = trim($_POST['nick']);
        $server = (int)$_POST['server'];
        $country = trim($_POST['country']);


        if ($server < 1 || $server > 2)
            $server = 1;
        $dbc->db_select($dbc->dbname($server));

        $extacc = 'yes';

        $info = $get->get_tableinfo('users', $dbc->dbname($server));
        if ($info['Rows'] >= MAX_USERS_PER_SERVER)
        {
            $gres->simple_messge('Dieser Server ist voll!');
            exit;
        }
        $e = false;

        $badwords = 'admin|king|multi|cheat|fuck|fick|sex|porn|penis|vagina|arsch|hitler|himmler|goebbels|göbbels|hure|nutte|fotze|bitch|schlampe';
        # nein, king ist kein böses, sondern ein reserviertes wort ^^
        $nickzeichen = 'abcdefghijklmnopqrstuvwxyzäüöABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜß0123456789_-:@.!=?$%/&';

        if ($nick != '')
        {
            if ($get->get_user($email, 'email') !== false)
            {
                $e = true;
                $msg .= 'Ein Benutzer mit dieser Emailadresse existiert bereits!<br />';
            }
            if ($get->get_user($nick, 'name') !== false)
            {
                $e = true;
                $msg .= 'Ein Benutzer mit diesem Nicknamen existiert bereits!<br />';
            }
            if ($pub->checknick($nick) == false)
            {
                $e = true;
                $msg .= 'Der Nickname darf NUR die Zeichen <i>' . $nickzeichen .
                    '</i> enthalten. Au&szlig;erdem darf er nicht nur aus Sonderzeichen bestehen.<br />';
            }
            if (strlen($nick) < 3 | strlen($nick) > 20)
            {
                $e = true;
                $msg .= 'Der Nickname muss zwischen 3 und 20 Zeichen lang sein.<br />';
            }
            $x = eregi_replace('[-_:@.!=?$%&/0-9]', '', $nick);
            if (eregi('(' . $badwords . ')', $x) != false)
            {
                $e = true;
                $msg .= 'Der Nickname darf bestimmte W&ouml;rter nicht enthalten.<br />';
            }
        } else
        {
            $e = true;
            $msg .= 'Bitte Nickname eingeben.<br />';
        }
        if (!$gres->check_email($email))
        {
            $e = true;
            $msg .= 'Bitte eine g&uuml;ltige Email-Adresse im Format x@y.z angeben.<br />';
        }

        #$javascript = file_get('data/pubtxt/selcountry_head.txt');

        if ($e == false)
        {

            $pwd = $pub->generateMnemonicPassword();
            $tmpfnx = $gres->random_string(REG_CODE_LEN);
            $tmpfn = 'data/regtmp/' . $tmpfnx . '.txt';
            $get->put_file($tmpfn, $nick . '|' . $email . '|' . $pwd . '|' . $server . '|' . $country .
                '|' . $extacc);

            /* blubb ... durch selectliste ersetzt!
            createlayout_top('HackTheNet - Account anlegen');
            
            $selcode=str_replace('%path%','images/maps',file_get('data/pubtxt/selcountry_body.txt'));
            echo '<div class="content" id="register">
            <h2>Registrierung</h2>
            <div id="register-step2">
            <h3>Schritt 2: Land auswählen</h3>
            <p>Bitte w&auml;hle jetzt, in welchem Land der Erde dein Computer stehen soll. Nat&uuml;rlich nur im Spiel und nicht in echt...</p>
            <form action="pub.php?a=regsubmit2" method="post" name="coolform">
            <input type="hidden" name="code" value="'.$tmpfnx.'" />
            <input type="hidden" name="country" value="" />
            '.$selcode.'
            </form>
            </div>
            </div>
            ';
            createlayout_bottom();*/

            # NEU:
            header('Location: pub.php?a=regsubmit2&code=' . $tmpfnx);

        } else
        {
            header('Location:pub.php?a=register&error=' . urlencode($msg));
        }
        break;

    case 'regsubmit2': // ----------------------- RegSubmit 2 --------------------------

        $tmpfnx = $_REQUEST['code'];
        if (preg_match("/^[a-z0-9]+$/i", $tmpfnx) === false)
            die('FUCK OFF!');
        $fn = 'data/regtmp/' . $tmpfnx . '.txt';
        list($nick, $email, $pwd, $server, $country, $extacc) = explode('|', $get->get_file($fn));
        mysql_select_db($dbc->dbname($server));

        $layout->createlayout_top('HackTheNet - Account anlegen');
        echo '<div class="content" id="register">
  <h2>Registrieren</h2>
  <div id="register-step3">
  ';

        $c = $get->Get_Country('id', $country);
        if ($c === false)
            die('nix da fck nix country da blubb');
        $subnet = $c['subnet'];

        $r = $dbc->db_query('SELECT `id` FROM `pcs` WHERE `ip` LIKE \'' . mysql_escape_string
            ($subnet) . '.%\';');
        $cnt = mysql_num_rows($r);
        $xip = $cnt + 1;

        if ($xip > 254)
        {
            @unlink('data/regtmp/' . $tmpfnx . '.txt');
            echo '  <div class="error"><h3>Sorry</h3>
    <p>Das gew&auml;hlte Land ist schon "voll"! Bitte such dir ein anderes Land aus!</p></div>
    <form action="pub.php?a=regsubmit" method="post">
    <input type="hidden" name="server" value="' . $server . '">
    <input type="hidden" name="nick" value="' . $nick . '">
    <input type="hidden" name="email" value="' . $email . '">
    <p><input type="submit" value=" Zur&uuml;ck "></p>
    </form>';
            echo '</div>' . LF . '</div>';
            $layout->createlayout_bottom();
            exit;
        }

        $get->put_file($fn, $nick . '|' . $email . '|' . $pwd . '|' . $country . '|' . $server .
            '|' . $extacc . '|' . $extacc_nick . '|' . $extacc_mail . '|' . $extacc_id);
        if ($nick == '' || $email == '' || $pwd == '' || $country == '' || (int)$server ==
            0)
        {
            $gres->simple_message('FEHLER AUFGETRETEN!', 'error');
            exit;
        }

        $body = 'Hallo ' . $nick . '!' . LF . LF .
            'Du hast dich bei HackTheNet ( http://www.hackthenet.org/ ) angemeldet!';
        $body .= ' Hier sind deine Zugangsdaten!' . LF . LF . 'Server: Server ' . $server .
            LF . 'Nickname: ' . $nick . LF . 'Passwort: ' . $pwd . LF . LF .
            'Bevor du deinen';
        $body .= ' neuen Account nutzen kannst, musst du ihn aktivieren! Rufe dazu die folgende URL in deinem Browser auf:' .
            LF . LF;
        if (!$account_activation_per_email)
            $body .= '<a href="';
        $body .= 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] .
            '?a=regactivate&code=' . $tmpfnx;
        if (!$account_activation_per_email)
            $body .= '">aktivieren</a>';
        $body .= LF;


        if ($account_activation_per_email)
        {
            if (@mail($email, 'Dein HackTheNet Account', $body,
                'From: HackTheNet <robot@hackthenet.org>'))
            {
                readfile('data/pubtxt/regok.txt');
            } else
            {
                echo 'Beim Verschicken der Email mit deinen Zugangsdaten trat ein Fehler auf!';
            }
        } else
        {
            echo nl2br($body);
        }

        echo '</div>' . LF . '</div>';
        $layout->createlayout_bottom();
        break;

    case 'regactivate': // ----------------------- RegActivate --------------------------

        if (strlen($_GET['code']) <> REG_CODE_LEN)
        {
            simple_message('Keine Hackversuche bitte!');
            exit;
        }

        $fn = 'data/regtmp/' . $_GET['code'] . '.txt';
        if (file_exists($fn) == false)
        {
            $gres->simple_message('Ung&uuml;ltiger Registrierungscode!');
        } else
        {

            $a = explode('|', $get->get_file($fn));
            list($nick, $email, $pwd, $country, $server, $extacc, $extacc_nick, $extacc_mail,
                $extacc_id) = explode('|', $get->get_file($fn));
            unlink($fn);

            mysql_select_db($dbc->dbname($server));

            if ($get->get_user($email, 'email') !== false)
            {
                $e = true;
                $msg .= 'Ein Benutzer mit dieser Emailadresse existiert bereits!<br />';
            }
            if ($get->get_user($nick, 'name') !== false)
            {
                $e = true;
                $msg .= 'Ein Benutzer mit diesem Nicknamen existiert bereits!<br />';
            }

            $tableinfo = $get->Get_TableInfo('users', $dbc->dbname($server));
            $autoindex = $tableinfo['Auto_increment'];
            $r = $gres->addpc($country, $autoindex);
            if ($r != false)
            {

                $ts = time();
                $dbc->db_query('INSERT INTO users(id, name, email,   password, pcs, liu, lic,  clusterstat, login_time) ' .
                    'VALUES(0, \'' . mysql_escape_string($nick) . '\',\'' . mysql_escape_string($email) .
                    '\',\'' . md5($pwd) . '\', \'' . $r . '\', \'' . $ts . '\', \'' . $ts . '\', 0, \'' .
                    $ts . '\');');

                $ownerid = mysql_insert_id();

                if ($extacc == 'yes')
                {
                    $dbc->db_query('UPDATE users SET bigacc=\'yes\', ads=\'no\', extacc_id=\'' . $extacc_id .
                        '\' WHERE id=' . $ownerid . ' LIMIT 1;');
                }


                $dbc->db_query('UPDATE pcs SET owner=\'' . $ownerid . '\', owner_name=\'' .
                    mysql_escape_string($nick) . '\', owner_points=0, owner_cluster=0, owner_cluster_code=\'\' WHERE id=' .
                    $r);

                $dbc->db_query('INSERT INTO rank_users VALUES(0, ' . $ownerid . ', \'' .
                    mysql_escape_string($nick) . '\', 0, 0);');
                $rank = mysql_insert_id();
                $dbc->db_query('UPDATE users SET rank=' . $rank . ' WHERE id=' . $ownerid . ';');

                /*setcookie('ref_user');
                setcookie('regc1','yes',time()+24*60*60);
                $dummy=reloadsperre_CheckIP(true); # IP speichern
                */
                $layout->createlayout_top('HackTheNet - Account aktivieren');
                echo '<div class="content" id="register">
      <h2>Account aktivieren</h2>
      <div id="register-activate">
      ';
                echo '<div class="ok"><h3>Account aktiviert!</h3>';
                echo '<p>Herzlichen Gl&uuml;ckwunsch!<br />Dein Account wurde aktiviert!<br />Du kannst dich jetzt auf der <a href="./">Startseite</a> einloggen!</p></div>';

            } else
            {
                $layout->createlayout_top();
                echo '<div class="content" id="register">
      <h2>Account aktivieren</h2>
      <div id="register-activate">
      ';
                echo '<div class="error"><h3>Sorry</h3>
      
      <p>Das gew&auml;hlte Land ist schon "voll"! Bitte such dir ein anderes Land aus!</p></div>
      <form action="pub.php?a=regsubmit" method="post">
      <input type=hidden name="server" value="' . $server . '">
      <input type=hidden name="nick" value="' . $nick . '">
      <input type=hidden name="email" value="' . $email . '">
      <p><input type=submit value=" Weiter "></p>
      </form>';
            }

            echo '</div>' . LF . '</div>';
            $layout->createlayout_bottom();

        }
        break;

    case 'newpwdsubmit': // ----------------------- NEW PWD SUBMIT --------------------------

        $usrname = strtolower(trim($_REQUEST['nick']));
        $email = strtolower(trim($_REQUEST['email']));
        $server = (int)$_POST['server'];
        if ($server < 0 || $server > 2)
            $server = 2;

        mysql_select_db(dbname($server));

        if (check_email($email) === true)
        {

            $usr = getuser($usrname, 'name');

            if ($usr !== false)
            {
                if ($email == strtolower($usr['email']))
                {
                    $pwd = generateMnemonicPassword();

                    db_query('UPDATE users SET password=\'' . md5($pwd) . '\' WHERE id=\'' . $usr['id'] .
                        '\';');

                    if (@mail($email, 'Zugangsdaten für HackTheNet', LF .
                        'http://www.hackthenet.org/' . LF . LF . 'Server: Server ' . $server . LF .
                        'Benutzername: ' . $usr['name'] . LF . 'Passwort: ' . $pwd . LF,
                        'From: HackTheNet <robot@hackthenet.org>'))
                    {

                        unset($usr);
                        simple_message('Das neue Passwort wurde an Deine Email-Adresse geschickt!');
                    } else
                    {
                        simple_message('Beim Verschicken der Email trat ein Fehler auf!');
                        if ($localhost)
                            echo '<br />Neues Passwort: ' . $pwd;
                    }

                } else
                {
                    unset($usr);
                    simple_message('Falsche Email-Adresse!');
                }
            } else
            {
                unset($usr);
                simple_message('Benutzername unbekannt!');
            }
        } else
        {
            unset($usr);
            simple_message('Email-Adresse ung&uuml;ltig!');
        }

        break;

    case 'stats': // ----------------------- STATS --------------------------
        $layout->createlayout_top('HackTheNet - Statistik');

        echo '<div class="content" id="server-statistic">' . LF;
        echo '<h2>Statistik</h2>' . LF;
        $pub->stats(1);
        #echo '<p>&nbsp;</p>';
        #stats(2);

        echo '<p>&nbsp;<img src="extstats/load.png" /></p>';

        echo LF . '</div>';
        $layout->createlayout_bottom();
        break;

    case 'deleteaccount': // ----------------------- DELETE ACCOUNT --------------------------
        $code = $_GET['code'];
        $x = @file_get('data/regtmp/del_account_' . $code . '.txt');
        if ($x)
        {
            $x = explode('|', $x);

            mysql_select_db(dbname($x[1]));
            if ($usr = @delete_account($x[0]))
            {

                db_query('INSERT INTO logs SET type=\'deluser\', usr_id=\'' .
                    mysql_escape_string($usr['id']) . '\', payload=\'' . mysql_escape_string($usr['name']) .
                    ' ' . mysql_escape_string($usr['email']) . ' self-deleted\';');

                simple_message('Account ' . $usr['name'] . ' (' . $usrid . ') gel&ouml;scht!');

            } else
            {
                simple_message('Account ' . $usr['name'] . ' existiert nicht!');
            }

        } else
            simple_message('Ung&uuml;ltiger Account-L&ouml;sch-Code!');
        break;

    case 'oldhome': // ----------------------- HOME --------------------------
    default:

        $layout->createlayout_top('HackTheNet - browserbasiertes Online-Spiel');
        include ('data/pubtxt/startseite.php');
        $layout->createlayout_bottom();
        break;

}

?>