<?php

/**
 * search.php 
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.0.1
 **/

if(!defined('IN_BB')) die('Hacking attempt!');

/**
 * user module data
 **/
class htnbb_module_search extends i_htnbb_module
{
  
  public function __construct()
  {
    $this->m_id = 'search';
  }
  
  public function &set_page($pageid)
  {
    switch($pageid)
    {
    case 'search':          $this->current_page = new htn_module_page($this, $pageid, 'Suche'); break;
    default: $this->current_page = false;
    }
    return $this->current_page;
  }
  
  /**
   * 
   **/
  public function search()
  {
    global $user_logged_in, $error_occured, $error_msg, $ignore_errors;
    
    $exec = (getifset($_GET, 'exec') == 1);
    
    if($exec)
    {
      $query = trim($_POST['query']);
      
      // dumme Zeichen rausschmeißen
      $query = ' ' . preg_replace('/[^a-zA-Z0-9_äüößÄÖÜ ]/iu', ' ', $query);
      
      // Leerzeichen strippen
      while (strpos($query, '  ') > 0)
      {
        $query = str_replace('  ', ' ', ' ' . $query);
      }
      
      // für BOOLEAN MODE vorbereiten, +se vorranstellen
      $query = str_replace(' ', ' +', $query);
      $pquery = prepare_string_for_query($query);
      
      // und los geht's:
      $r = db_query('SELECT topic_id, id AS post_id, subject, text, time, (SELECT subject FROM posts p2 WHERE p2.id=(SELECT first_post_id FROM topics t2 WHERE t2.id=p.topic_id)) AS topic_subject, '.
        'MATCH (subject, text) AGAINST(\'' . $pquery . '\' IN BOOLEAN MODE) AS rank FROM posts p WHERE MATCH (subject, text) '.
        'AGAINST(\'' . $pquery . '\' IN BOOLEAN MODE) ORDER BY rank DESC, topic_id ASC, post_id ASC');
      
      $results = array();
      
      while($entry = $r->fetch_assoc())
      {
        // Seiten, in denen ein oder mehrer Suchwörter vorkommen, durchgehen
        
        preg_match_all('/.{0,25}(' . substr(str_replace(' \+', '|', quotemeta($query)), 1) . ').{0,25}/isu', $entry['text'], $matches);
        
        $entry['text'] = '';
        foreach($matches[0] as $index => $match)
        {
          // Die Suchwörter aus dem Text schnippeln und highlighten:
          $searchword = $matches[1][$index];
          $match = htmlspecialchars($match);
          $match = substr($match, (int)strpos($match, ' '));
          $match = substr($match, 0, -(strlen($match) - (int)strrpos($match, ' ')));
          $entry['text'] .= '... ' . str_replace($searchword, '<span class="searchword">' . $searchword . '</span>', $match) . ' ... ';
        }
        if(empty($entry['subject'])) $entry['subject'] = 'Post #' . $entry['post_id'];
        $entry['subject'] = htmlspecialchars($entry['subject']);
        
        $results[$entry['topic_id']][] = $entry;
        $results[$entry['topic_id']]['subject'] = $entry['topic_subject'];
      }
      
      #print_r($results);
      
    }
    
    include(SHOW_TEMPLATE_FILE);
  }
  
}


?>