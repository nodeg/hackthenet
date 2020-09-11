<?php
if (!defined('IN_HTN'))
{
    die('Hacking attempt');
}
$navi = "";
if (isset($_GET['m']))
    $navi = $_GET['m'];

$link = array("wart" => "wartung.php", "news" => "news.php", "lock" =>
    "lock.php", "multi" => "multi.php", "opt" => "option.php", "fill" => "fill.php");

if (is_array($link) && array_key_exists($navi, $link))
{
    $filename = $link[$navi];
    // datei vorhanden?
    if (file_exists($filename) && is_readable($filename))
    {
        include_once ($filename);
    }
}
