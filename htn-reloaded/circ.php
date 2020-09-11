<?php
session_start();
define('IN_HTN', 1);

$FILE_REQUIRES_PC = false;
include ('ingame.php');

$dbc = new dbc();
$get = new get();
$layout = new layout();
$cboard = new cboard();

$layout->createlayout_top('HackTheNet - Cluster-Chat');
echo '<div class="content" id="cluster-irc">' . LF;
$layout->createlayout_bottom();

$uix = $usr['id'];
$user = $get->get_User($uix);
$cluster = $get->get_cluster($uix['cluster']);

?>
<applet code=IRCApplet.class archive="irc/irc.jar,irc/pixx.jar" width=640 height=400>
<param name="CABINETS" value="irc.cab,securedirc.cab,pixx.cab">

<param name="nick" value="<?=$user['name'];?>">
<param name="alternatenick" value="<?=$user['name'];?>_alt">
<param name="name" value="Java User">
<param name="host" value="<?=$name['irc_server'];?>">
<param name="gui" value="pixx">

<param name="command1" value="join #<?=$cluster['name'];?>">