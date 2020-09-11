<?php

/**
 * includes/config.php Einstellungen.
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.1.0
 **/


if( !defined('IN_BB') ) die('Hacking attempt!');


//***************************************************************************//
/**
 * Debug-Modus aktivieren?
 **/
define('DEBUG_MODE', 0);
//***************************************************************************//

define('BOARD_TITLE', 'htnBB');

/**
 * Name des Cookies, der LogIn-Daten speichert
 **/
define('SAVE_LOGIN_COOKIE_NAME', 'htnbb_autologindata');

/**
 * Absenderinfo für über mail(...) versendete Emails
 **/
define('FROM_MAIL_HEADER', 'From: ' . BOARD_TITLE .' <bgeforum@irsoft.de>'."\n".'Content-Type: text/plain; charset=UTF-8');

/**
 * Email-Addy des Spiel^WForum-Admins
 **/
define('ADMIN_EMAIL', '');


# Session-Management

/**
 * Name des Cookies, der die SID transportiert
 **/
define('SESSION_ID_COOKIE_NAME', 'bb_session');

/**
 * Interval nach der $usr->sess_lastcall neu geschrieben wird
 **/
define('SESSION_LASTCALL_UPDATE_INTERVAL', 180);

/**
 * Solange wird der User nach $usr->sess_lastcall noch als online angezeigt
 **/
define('SESSION_INACTIVE_TIME', 600);

# Pfade u.ä.:
/**
 * Basis-Verzeichnis von HTN. Wichtig: KEIN / AM ENDE!!
 **/
define('FORUM_BASEDIR', '/forum');

define('VIRTUAL_FILE_EXTENSION', 'xhtml');


/**
 * Username für DB-Zugriff
 * @ignore
 **/
define('DB_USER', 'user');
/**
 * Passwort für DB-Zugriff
 * @ignore
 **/
define('DB_PASSWORD', 'password');
/**
 * Name der DB
 **/
define('DB_NAME', 'db_name');


// Stylesheet-Kram ;D

define('DEFAULT_STYLESHEET', 'crystal2');
define('DEFAULT_STYLESHEET_ICONS', 'crystal');
define('DEFAULT_STYLESHEET_COLORS', 'crystal2');

$stylesheets['crystal2'] = array('id'=>'crystal2', 'name'=>'Crystal 2', 'menuscroll'=>true);

$color_styles['crystal2'] = array('id'=>'crystal2', 'name'=>'Blue', 'for'=>array('crystal2'));

$icon_styles['crystal'] = array('id'=>'crystal', 'name'=>'Crystal Icons', 'for'=>array('crystal2', 'crystallica'));

# verschiedenes - muss evtl. noch in eine andere datei!

/**
 * Zeilenumbruch
 * @ignore
 **/
define('LF', "\n");
/**
 * Nullzeichen
 * @ignore
 **/
define('NC', "\x0b");

// Avatar-Einstellungen

/**
 * Maximale Größe der Avatardateien
 **/
define('AVA_MAX_SIZE', 50 * 1024);

/**
 * Maximal erlaubte Höhe des Avatarbildes in px
 **/
define('AVA_MAX_HEIGHT', 137);

/**
 * Maximal erlaubte Breite des Avatarbildes in px
 **/
define('AVA_MAX_WIDTH', 84);

/**
 * Erlaubte MIME-Typen
 **/
$AVA_ALLOWED_MIME = array('GIF', 'JPG', 'PNG');


/**
 * @ignore
 **/
define('CONFIG_LOADED', 1);

?>
