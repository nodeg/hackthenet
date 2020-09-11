<?php
/**
 * index.php
 **/

/**
 * @ignore
 **/
define('IN_BB', 1);

require('core/config.php');
require('core/common_functions.php');

bb_redir('board', 'index');
?>