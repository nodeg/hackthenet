<?php

/**
 * watchedtopics.php 
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.0.1
 **/

if(!defined('IN_BB')) die('Hacking attempt!');

/**
 * watchedtopics module data
 **/
class htnbb_module_watchedtopics extends i_htnbb_module
{
  
  public function __construct()
  {
    $this->m_id = 'watchedtopics';
  }
  
  public function &set_page($pageid)
  {
    switch($pageid)
    {
    case 'show':          $this->current_page = new htn_module_page($this, $pageid, 'Abonnierte Themen'); break;
    case 'massunwatch':   $this->current_page = new htn_module_page($this, $pageid, ''); break;
    case 'unwatch':       $this->current_page = new htn_module_page($this, $pageid, ''); break;
    case 'watch':         $this->current_page = new htn_module_page($this, $pageid, ''); break;
    default: $this->current_page = false;
    }
    return $this->current_page;
  }
  
  /**
   * 
   **/
  public function show()
  {
    global $user_logged_in, $usr;
    
    if(!$user_logged_in)
    {
      message_die('Diese Funktion steht nur registrierten und eingeloggten Benutzern zur Verfügung.');
    }
    
    $r_wtopics = db_query('SELECT id, (SELECT subject FROM posts WHERE posts.id = topics.first_post_id) AS subject,
      (SELECT name FROM forums WHERE forums.id = topics.forum_id) AS forum_name, forum_id FROM 
      topics WHERE id IN (SELECT topic_id FROM watched_topics WHERE user_id = ' . (int)$usr->id . ')');
    
    include(SHOW_TEMPLATE_FILE);
  }
  
  /**
   * 
   **/
  public function watch()
  {
    global $user_logged_in, $usr;
    
    if(!$user_logged_in)
    {
      message_die('Diese Funktion steht nur registrierten und eingeloggten Benutzern zur Verfügung.');
    }
    
    $topic_id = (int)$_GET['topic'];
    
    if(db_get_first_row('SELECT (SELECT COUNT(*) FROM watched_topics WHERE topic_id = ' . $topic_id . ' AND user_id = ' . (int)$usr->id . ')
      - (SELECT COUNT(*) FROM topics WHERE id = ' . $topic_id . ')') == -1)
    {
      db_query('INSERT INTO watched_topics SET topic_id = ' . $topic_id . ', user_id = ' . (int)$usr->id);
    }
    
    bb_redir('browse', array('topic', $topic_id));
  }
  
  /**
   * 
   **/
  public function unwatch()
  {
    global $user_logged_in, $usr;
    
    if(!$user_logged_in)
    {
      message_die('Diese Funktion steht nur registrierten und eingeloggten Benutzern zur Verfügung.');
    }
    
    $topic_id = (int)$_GET['topic'];
    
    if(db_get_first_row('SELECT COUNT(*) FROM watched_topics WHERE topic_id = ' . $topic_id . ' AND user_id = ' . (int)$usr->id) == 1)
    {
      db_query('DELETE FROM watched_topics WHERE topic_id = ' . $topic_id . ' AND user_id = ' . (int)$usr->id . ' LIMIT 1');
    }
    
    bb_redir('browse', array('topic', $topic_id));
  }
  
  /**
   * 
   **/
  public function massunwatch()
  {
    global $user_logged_in, $usr;
    
    if(!$user_logged_in)
    {
      message_die('Diese Funktion steht nur registrierten und eingeloggten Benutzern zur Verfügung.');
    }
    
    $r_wtopics = db_query('SELECT topic_id FROM watched_topics WHERE user_id = ' . (int)$usr->id);
    
    $sql = '';
    while($topic = $r_wtopics->fetch_object())
    {
      $topic = $topic->topic_id;
      if(getifset($_POST, 'topic' . $topic) == '1')
      {
        $sql .= $topic . ',';
      }
    }
    $sql = trim($sql, ', ');
    
    if(!empty($sql))
    {
      db_query('DELETE FROM watched_topics WHERE topic_id IN (' . $sql . ')');
    }
    
    bb_redir('watchedtopics', 'show', 'rnd='.random_string(8));
  }
  
}


?>