<?php
session_start();
define('IN_HTN',1);
$FILE_REQUIRES_PC = FALSE;
include ('ingame.php');

$action = $_REQUEST['page'];
if($action == '') $action = $_REQUEST['mode'];
if($action == '') $action = $_REQUEST['action'];
if($action == '') $action = $_REQUEST['a'];
if($action == '') $action = $_REQUEST['m'];

$layout = new layout();

switch($action) 
{
  case 'start': // ----------------------------------- START --------------------------------
  
  $layout->createlayout_top('HackTheNet - Forschung');
  
  $layout->createlayout_bottom();
  
  break;
}
?>