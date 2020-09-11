<?php

/**
 * modcp.php 
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.0.1
 **/

if(!defined('IN_BB')) die('Hacking attempt!');

/**
 * modcp module data
 **/
class htnbb_module_modcp extends i_htnbb_module
{
  
  public function __construct()
  {
    $this->m_id = 'modcp';
  }
  
  public function &set_page($pageid)
  {
    switch($pageid)
    {
    case 'toggleclosedtopic':       $this->current_page = new htn_module_page($this, $pageid, ''); break;
    case 'movetopic':               $this->current_page = new htn_module_page($this, $pageid, 'Thema verschieben'); break;
    case 'delete':                  $this->current_page = new htn_module_page($this, $pageid, ''); break;
    default: $this->current_page = false;
    }
    return $this->current_page;
  }
  
  /**
   * 
   **/
  public function toggleclosedtopic()
  {
    global $user_logged_in, $usr;
    
    if(!$user_logged_in) exit;
    
    $topic_id = (int)$_GET['topic'];
    
    $forum_id = db_get_first_row('SELECT forum_id FROM topics WHERE id = ' . $topic_id . ' LIMIT 1');
    if(!$forum_id) exit;
    
    if(!is_permitted_u($usr, $forum_id, A_CLOSE_TOPIC))
    {
      message_die('Keine Berechtigung.');
    }
    
    db_query('UPDATE topics SET locked = IF(locked = 1, 0, 1) WHERE id = ' . $topic_id . ' LIMIT 1');
    
    bb_redir('browse', array('topic', $topic_id), 'rnd=' . random_string(8));
    
  }
  
  /**
   * 
   **/
  public function movetopic()
  {
    global $user_logged_in, $usr;
    
    if(!$user_logged_in) exit;
    
    $topic_id = (int)$_GET['topic'];
    
    $forum_id = db_get_first_row('SELECT forum_id FROM topics WHERE id = ' . $topic_id . ' LIMIT 1');
    if(!$forum_id) exit;
    
    if(!is_permitted_u($usr, $forum_id, A_MOVE_TOPICS))
    {
      message_die('Keine Berechtigung.');
    }
    
    if(isset($_POST['forum']))
    {
      $new_forum_id = (int)$_POST['forum'];
      
      $new_forum = db_query_fetch('SELECT id, last_post_id, last_post_time FROM forums WHERE id = ' . $new_forum_id);
      
      if(!$new_forum) exit;
      
      $last_post_topic = db_get_first_row('SELECT topic_id FROM posts WHERE id = (SELECT last_post_id FROM forums WHERE id = ' . $forum_id . ' LIMIT 1) LIMIT 1');
      
      db_query('UPDATE topics SET forum_id = ' . $new_forum_id . ' WHERE id = ' . $topic_id . ' LIMIT 1');
      
      if($last_post_topic == $topic_id)
      {
        manual_forum_last_post_update($forum_id);
        #echo "update 11";
      }
      
      if($new_forum->last_post_time < db_get_first_row('SELECT time FROM posts WHERE topic_id = ' . $topic_id . ' ORDER BY time DESC LIMIT 1'))
      {
        manual_forum_last_post_update($new_forum->id);
        #echo "update 22";
      }
      
      bb_redir('browse', array('topic', $topic_id), 'rnd=' . random_string(8));
    }
    else
    {
      $r_forums = db_query('SELECT forums.id, forums.name FROM forums WHERE
        forums.id IN (SELECT forum_id FROM group_permissions WHERE action = \'' . A_CREATE_TOPIC . '\'
        AND group_id IN (SELECT group_id FROM user_groups WHERE user_id = ' . $usr->id . '))
        ORDER BY forums.categorie ASC, forums.sort_index ASC');
      
      $forums = array();
      
      while($forum = $r_forums->fetch_object())
      {
        $forums[$forum->id] = $forum->name . ($forum->id == $forum_id ? ' (aktuelles Forum)' : '');
      }
      
      $r_forums->free(); unset($r_forums);
      
      include(SHOW_TEMPLATE_FILE);
    }
    
  }
  
  
  /**
   * 
   **/
  public function delete()
  {
    global $user_logged_in, $usr;
    
    if(!$user_logged_in) exit;
    
    $post_id = (int)$_GET['post'];
    if($post_id == 0)
    {
      $topic_id = (int)$_GET['topic'];
      if($topic_id == 0) exit;
    }
    else
    {
      $topic_id = db_get_first_row('SELECT topic_id FROM posts WHERE id = ' . $post_id . ' LIMIT 1');
    }
    
    $forum_id = db_get_first_row('SELECT forum_id FROM topics WHERE id = ' . $topic_id . ' LIMIT 1');
    if(!$forum_id) exit;
    
    if(!is_permitted_u($usr, $forum_id, A_DELETE_OTHERS_POSTS))
    {
      message_die('Keine Berechtigung.');
    }
    
    /*include 'core/memcache.php';
    htnbb_memcache_delete_thread($topic_id); // make sure cache stays up2date */
    
    if($post_id == 0)
    {
      // ganzes Topic löschen
      db_query('DELETE FROM topics WHERE id = ' . $topic_id . ' LIMIT 1');
      db_query('DELETE FROM posts WHERE topic_id = ' . $topic_id);
    }
    else
    {
      if(db_get_first_row('SELECT COUNT(*) FROM posts WHERE topic_id = ' . $topic_id) == 1)
      {
        message_die('Den letzten Post in einem Topic kann man nicht einfach so löschen.');
      }
      // nur 1 Post löschen
      db_query('DELETE FROM posts WHERE id = ' . $post_id . ' LIMIT 1');
    }
    
    manual_forum_last_post_update($forum_id);
    
    bb_redir('browse', ($post_id == 0 ? array('forum', $forum_id) : array('topic', $topic_id)), 'rnd=' . random_string(8));
    
  }
  
}


?>
