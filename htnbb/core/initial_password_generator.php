<?php

/**
 * Generiert ein sicheres aber relativ leicht zu merkendes Passwort
 * (durch Abwechseln von Vokalen und Konsonanten und dem Einsatz von Sonderzeichen)
 * @return string das Passwort
 **/
function generate_initial_password()
{
  $charset[0] = array('a', 'e', 'i', 'o', 'u', 'y');
  $charset[1] = array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'z');
  $specials = array('!', '&', '/', '=', '?', '+', '-', '.', ':', ',', '*', '#', '_');
  
  $password = '';
  
  for ($i = 1; $i <= 8; $i++)
  {
    $password .= $charset[$i % 2][array_rand($charset[$i % 2])];
  }
  
  // Übelst verschachtelte Klammern:
  
  // irgendwo mittendrin, nicht aber als erstes oder letztes Zeichen ein Sonderzeichen setzen:
  $password{mt_rand(1, strlen($password) - 2)} = $specials[mt_rand(0, count($specials) - 1)];
  
  for($i = 0; $i < 2; $i++)
  {
    // und noch ne Zahl irgendwo rein:
    $password{mt_rand(0, strlen($password) - 1)} = mt_rand(0, 9);
  }
  
  return $password;
}

?>