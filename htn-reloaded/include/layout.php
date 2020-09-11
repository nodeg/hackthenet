<?php


class layout
{
    public function menu_entry($url, $text, $help = '', $em = '', $emclass = '', $selftags = false, $inatag = '')
    {
        echo "\n" . '<li><a href="' . $url . '"' . $inatag . '><strong>' . $text .
            '</strong>';
        if ($em != '')
        {
            if (!$selftags)
                echo '<br /><em class="' . $emclass . '">' . $em . '</em>';
            else
                echo $em;
        }
        echo '</a>';
        if ($help != '')
            echo '<div class="help">' . $help . '</div>';
        echo '</li>';
    }

    public function basicheader($title)
    {
        global $usr, $javascript, $STYLESHEET, $bodytag, $stylesheets;
        global $STYLESHEET_BASEDIR, $standard_stylesheet;
        $stylesheet = $STYLESHEET;
        if ($stylesheets[$stylesheet]['id'] == '' || $stylesheet == '')
            $stylesheet = $standard_stylesheet;

        $ts = time() + 1;
        echo '<?xml version="1.0" encoding="ISO-8859-1"?>' . "\n";
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
  <html>
  <head>
  <title>' . $title . '</title>
  <link rel="stylesheet" type="text/css" href="' . $STYLESHEET_BASEDIR . $stylesheet .
            '/style.css" />
  <script type="text/javascript">var stm=new Date(); stm.setTime(' . $ts .
            '000); var sltm=new Date();</script>
  <script type="text/javascript" src="global.js"></script>
  <link rel="SHORTCUT ICON" href="favicon.ico" />
  ' . $javascript . '
  </head>
  ';
    }

    public function basicfooter()
    {
        echo '</body>
  </html>
  ';
    }

    public function createlayout_top($title = 'HackTheNet', $nomenu = false)
    {
    	$gres = new gres();
        global $usr, $javascript, $STYLESHEET, $bodytag, $localhost, $FILE_REQUIRES_PC,
            $pc, $pcid;
        global $server, $transfer_ts, $t_limit_server;
        if ($usr['sid'] != '')
            $sid = '&amp;sid=' . $usr['sid'];

        $stylesheet = $STYLESHEET;
        if (@is_dir('styles/' . $stylesheet) == false || $stylesheet == '')
            $stylesheet = 'crystal';

        $this->basicheader($title);

        echo '<body' . $bodytag . '>
  <div class="header">
  <h1>HTN - Reloaded v2.2</h1>
  ' . $ads;
        if ($nomenu == false)
        {
            echo '<ul class="navigation">
    ';

            if ($sid != '')
            {
                // INGAME ITEMS

                $this->menu_entry('game.php?m=start' . $sid . '&amp;' . SID, '&Uuml;bersicht',
                    '&Uuml;bersicht &uuml;ber alles Wichtige auf einen Blick.');

                if ($usr['newmail'] > 0)
                {
                    $hw = ($usr['newmail'] == 1 ? '' : 's');
                    $hw = 'Du hast ' . $usr['newmail'] . ' neue Message' . $hw;
                }
                $this->menu_entry('mail.php?m=start' . $sid . '&amp;' . SID, 'Messages',
                    'Hier kannst du Nachrichten verwalten und neue verfassen.', $hw, 'new-messages');

                $numberofpcs = count(explode(',', $usr['pcs']));
                $url = ($numberofpcs > 1 ? 'game.php?m=pcs' . $sid :
                    'game.php?a=selpc&amp;pcid=' . $usr['pcs'] . $sid);
                $help = ($numberofpcs > 1 ? 'Hier kommst du zu deinen PCs.' :
                    'Hier kommst du direkt zu deinem PC.');
                if ($FILE_REQUIRES_PC && $pc['id'] == $pcid)
                {
                    $pc['name'] = $gres->safeentities($pc['name']);
                    $this->menu_entry($url, 'Computer', $help, '<br />10.47.' . $pc['ip'] . ' (<em>' . $pc['name'] .
                        '</em>)', '', true);
                } else
                {
                    $this->menu_entry($url, 'Computer', $help);
                }

                $help = ((int)$usr['cluster'] > 0 ?
                    'Hier kannst du dich &uuml;ber den aktuellen Stand deines Clusters informieren' :
                    'Hier kannst du einen neuen Cluster gr&uuml;nden oder einem existierenden beitreten.');
                $this->menu_entry('cluster.php?a=start' . $sid, 'Cluster', $help);

                $this->menu_entry('game.php?m=subnet' . $sid, 'Subnet',
                    'Hier kannst du die Computer in deinem oder einem anderen Subnet einsehen.');
                $this->menu_entry('user.php?a=config' . $sid, 'Optionen');
                $this->menu_entry('ranking.php?m=ranking' . $sid, 'Rangliste');
                $this->menu_entry('game.php?m=kb' . $sid, 'Hilfe',
                    'Hier findest du die Hilfe zum Spiel.');
                $this->menu_entry("http://www.seberoth-online.de/forum/", "Forum",
                    "Das offizielle HTN-Forum");

            } else
            {
                // PUBLIC ITEMS
                $this->menu_entry('pub.php', 'Startseite', 'HackTheNet-Startseite (Log In)');
                $this->menu_entry('http://www.seberoth-online.de/forum/', 'Forum',
                    'Das offizielle HTN-Forum');
                $this->menu_entry('pub.php?d=impressum', 'Impressum');
                $this->menu_entry('pub.php?d=credits', 'HTN-Team');
                $this->menu_entry('pub.php?d=stats', 'Statistik',
                    'Statistische Daten &uuml;ber das Spiel');
                $this->menu_entry('pub.php?d=rules', 'Spielregeln', 'Wer die nicht beachtet, fliegt!');
                $this->menu_entry('pub.php?d=faq', 'FAQ', 'H&auml;ufig gestellte Fragen zu HTN');
                $this->menu_entry('pub.php?d=newpwd', 'Neues Passwort',
                    'Ein neues Passwort f&uuml;r deinen Account anfordern');
                $this->menu_entry('pub.php?a=register', 'Registrieren',
                    'Einen HackTheNet-Account anlegen');
            }
            // COMMON ITEMS
            #menu_entry('http://www.seberoth-online.de/forum/', 'Forum');

            if ($sid != '')
            {
                // INGAME ITEMS 2
                $this->menu_entry('login.php?a=logout' . $sid, 'Log Out',
                    'Hier kannst du dich abmelden.', '<br />Account: <em>' . $usr['name'] . '</em>',
                    '', true);
                if ($_SESSION['level'] >= 5)
                {
                    $this->menu_entry('admin/index.php?' . $sid, 'Administration', '');
                }
            } else
            {
                // PUBLIC ITEMS 2

            }
        }

        echo '</ul>
  </div>
  
  <!-- NAVI ENDE -->
  ';
    }

    public function createlayout_bottom()
    {
    	$gresl = new gres();
        global $starttime, $_query_cnt;
        if ($starttime > 0)
        {
            $time = (float)$gresl->calc_time($starttime, 0, 10);
            if ($time < 1)
            {
                $time = number_format($time * 1000, 1, '.', ',') . ' ms';
            } else
            {
                $time = number_format($time, 2, ',', '.') . ' s';
            }
        }
        echo '
  <div id="generation-time">' . $time . ' / ' . (int)$_query_cnt . '</div>
  <div id="server-time">' . $gresl->nicetime3(0, '') . '</div>
  ';

        $this->basicfooter();
    }

	public function showdoc ($te='',$fn)
	{
		$x=' - '.$te;
  		$this->createlayout_top('HackTheNet'.$x);
  		$x='data/pubtxt/'.$fn;
  		if(file_exists($x.'.txt'))
  		readfile($x.'.txt');
  		else @include($x.'.php');
  		$this->createlayout_bottom();
	}
  

}
?>