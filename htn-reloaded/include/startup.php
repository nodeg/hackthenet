<?php
// Alle Fehler anzeigen
//error_reporting (E_ALL);

define ('DIRSEP', DIRECTORY_SEPARATOR);
$site_path = dirname(__FILE__) . DIRSEP . '..' .DIRSEP;
define ('site_path', $site_path);

function __autoload($class_name) {
	$filename = strtolower($class_name) . '.php';
	$file = site_path . 'include' . DIRSEP . $filename;
//echo $file;
	if (file_exists($file) == false) { 
		return false;
	}

	include ($file);
}