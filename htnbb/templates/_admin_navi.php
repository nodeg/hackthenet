<?php
/**
 * admin_navi
 **/

if(!defined('IN_BB')) die('Hacking attempt!');

echo '<div class="sub_navi">';
echo '<ul>';

echo '<li class="main">Admin</li>';
echo '<li><a href="' . bb_url('admincp', 'overview') . '">Übersicht</a></li>';

echo '<li class="main">Gruppen</li>';
echo '<li><a href="' . bb_url('admincp', 'groups') . '">Gruppen-Verwaltung</a></li>';


echo '</ul>';
echo '</div>';

?>