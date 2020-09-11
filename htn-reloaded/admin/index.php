<?php
error_reporting(E_ALL);
session_start();
define('IN_HTN', 1);
// Prüfen ob Berechtig ist
if (!isset($_SESSION['level']) || $_SESSION['level'] !=5){ exit();}
// Game sid holen
if(isset($_GET['sid'])){$sid = $_GET['sid'];}

include_once('gres.php');
include_once('layout.php');

//Standart Menü Ausgeben
createlayout_top('Administration');
include_once('content.php');
createlayout_bottom();


