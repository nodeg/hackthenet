<?php
/**
 * includes/show_template.php Templateausgabestart
 * Zeigt ein Template anhand von $this->current_pagepage an ...
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.0.1
 **/
 
 if(!defined('IN_BB')) die('Hacking attempt!');
 
 createlayout_top($this->current_page->title);
 /**
  * Template-Datei einbinden
  **/
 include('templates/' . $this->id . '_' . $this->current_page->id . '.php');
 
 createlayout_bottom();
?>