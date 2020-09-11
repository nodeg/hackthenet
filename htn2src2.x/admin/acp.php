<?php
######### Das ACP von HackTheNet coded by BODY-SNATCHER

if ($usr['stat']<1000) 

{

	simple_message('Wir wollen doch nicht hacken?!?');

	exit;

}
$do=$_REQUEST['do'];

switch($do) 
{
case 'fillcountry';
    if ($usr['stat']>100) { 
    	createlayout_top('HackTheNet - Subnetze füllen');
    	echo "<div class='content' id='settings'><h2>Subnetze füllen / leeren</h2>";
    	include('admin/fillcountry.php');
    	echo "</div>";
    } else {
    	simple_message('Wir wollen doch nicht hacken?!?');
    }
break;
case 'logs';
    if ($usr['stat']>100) { 
    	createlayout_top('HackTheNet - Subnetze füllen');
    	echo "<div class='content' id='settings'><h2>Subnetze füllen / leeren</h2>";
    	include('admin/logs.php');
    	echo "</div>";
    } else {
    	simple_message('Wir wollen doch nicht hacken?!?');
    }
break;
case 'pcs';
    if ($usr['stat']>100) { 
    	createlayout_top('HackTheNet - ACP');
    	echo "<div class='content' id='settings'><h2>Admin Control Panel/h2>";
    	include('admin/pcs.php');
    	echo "</div>";
    } else {
    	simple_message('Wir wollen doch nicht hacken?!?');
    }
break;


case 'sysmsg';
    if ($usr['stat']>=1000) { 
    	createlayout_top('HackTheNet - Systemmessage erstellen');
    	include('admin/sysmsg.php');
    } else {
    	simple_message('Wir wollen doch nicht hacken?!?');
    }
break;


case 'server';
    if ($usr['stat']>=1000) { 
    	createlayout_top('HackTheNet - Server Optionen');
    	include('admin/server.php');
    } else {
    	simple_message('Wir wollen doch nicht hacken?!?');
    }
break;
case 'newround';
    if ($usr['stat']>=1000) { 
    	createlayout_top('HackTheNet - Neue Runde');
    	include('admin/newround.php');
    } else {
    	simple_message('Wir wollen doch nicht hacken?!?');
    }
break;

case '';
    if ($usr['stat']>=1000) { 
    	createlayout_top('HackTheNet - ACP');
		echo '<div id="settings-settings"><h3>AdminControlPanel</h3>
		<p>
		<p><b>Server Optionen?</b></p>
		<p><a href="user.php?action=acp&do=server">HIER</a></p>
		<p><b>Systemmessage verschicken</b></p>
		<p<a href="user.php?action=acp&do=sysmsg">HIER</a></p>
		<p><b>Subnetze mit PCs füllen</b></p>
		<p<a href="user.php?action=acp&do=fillcountry">HIER</a></p>
		<p><b>Alle Subnetze mit PCs füllen</b></p>
		<p<a href="user.php?action=acp&do=pcs">HIER</a></p>
		<p><b>Neue Runde erstellen</b></p>
		<p<a href="user.php?action=acp&do=newround">HIER</a></p>
		</div>
		';
	} else {
    	simple_message('Wir wollen doch nicht hacken?!?');
    }
break;
}
?>