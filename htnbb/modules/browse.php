<?php

/**
 * board.php 
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.0.1
 **/

if(!defined('IN_BB')) die('Hacking attempt!');

/**
 * browse module data
 **/
class htnbb_module_browse extends i_htnbb_module
{
  private $posts_per_page = 20;
  private $topics_per_page = 30;
  
  public function __construct()
  {
    $this->m_id = 'browse';
  }
  
  public function &set_page($pageid)
  {
    switch($pageid)
    {
    case 'forum':   $this->current_page = new htn_module_page($this, $pageid, '');
                    $this->m_virtual_url_rewrite_params = array('forum_id');
                    break;
    case 'topic':   $this->current_page = new htn_module_page($this, $pageid, '');
                    $this->m_virtual_url_rewrite_params = array('topic_id');
                    break;
    case 'post':    $this->current_page = new htn_module_page($this, $pageid, '');
                    $this->m_virtual_url_rewrite_params = array('post_id');
                    break;
    default: $this->current_page = false;
    }
    return $this->current_page;
  }
  
  /**
   * Forum browsen
   **/
  public function forum()
  {
    global $user_logged_in, $usr, $unread_post_data, $_page;
    
    $forum_id = (int)$_GET['forum_id'];
    
    $forum = get_forum_by_id($forum_id);
    
    if(!is_permitted_u($usr, $forum->id, A_BROWSE_TOPIC_LIST))
    {
      message_die('Um in diesem Forum lesen zu können, brauchst du spezielle Berechtigungen, über die du aber zur Zeit nicht verfügst.');
    }
    
    $total_topics = db_get_first_row('SELECT COUNT(*) FROM topics WHERE forum_id = ' . $forum_id);
    
    $start = 0;
    $total_pages = ceil($total_topics / $this->topics_per_page);
    
    if($total_pages > 1)
    {
      $page = (int)getifset($_GET, 'page', 1);
      
      if($page > $total_pages || $page < 1) $page = 1;
      
      $start = ($page - 1) * $this->topics_per_page;
    }
    
    $_page->title = $forum->name;
    
    $r_topics = db_query('
      SELECT posts.subject, posts.time, posts.poster_id AS topic_starter_id, topics.last_post_user, topics.last_post_time,
      topics.last_post_id, topics.sticky, topics.locked, topics.id,
      (SELECT name FROM users WHERE users.id = posts.poster_id LIMIT 1) AS topic_starter_name,
      (SELECT name FROM users WHERE users.id = topics.last_post_user LIMIT 1) AS last_post_user_name,
      (SELECT COUNT(*) FROM posts WHERE posts.topic_id = topics.id) - 1 AS reply_count
      FROM topics JOIN posts ON topics.first_post_id = posts.id WHERE topics.forum_id = ' . $forum_id .
      ' ORDER BY topics.sticky DESC, topics.last_post_time DESC LIMIT ' . $start . ', ' . $this->topics_per_page . '
    ');
    
    include(SHOW_TEMPLATE_FILE);
  }
  
  /**
   * Topic browsen
   **/
  public function topic()
  {
    global $user_logged_in, $usr, $unread_post_data;
    global $error_occured, $error_msg, $_page;
    
    $topic_id = (int)$_GET['topic_id'];
    
    $total_posts = db_get_first_row('SELECT COUNT(*) FROM posts WHERE topic_id = ' . $topic_id);
    if($total_posts == 0)
    {
      message_die('Dieses Thema gibt es nicht.');
    }
    $topic = db_query_fetch('SELECT forum_id, locked, id, first_post_id FROM topics WHERE id = ' . $topic_id);
    $forum = get_forum_by_id($topic->forum_id);
    
    $permissions = get_permissions_u($usr, $forum->id);
    
    if(!in_array(A_READ_TOPICS, $permissions))
    {
      message_die('Um in diesem Forum lesen zu können, brauchst du spezielle Berechtigungen, über die du aber zur Zeit nicht verfügst.');
    }
    
    if($user_logged_in)
    {
      $user_is_watching_topic = (bool)db_get_first_row('SELECT COUNT(*) FROM watched_topics WHERE topic_id = ' . $topic_id . ' AND user_id = ' . (int)$usr->id);
    }
    else
    {
      $user_is_watching_topic = false;
    }
    
    $start = 0;
    $page = 1;
    $total_pages = ceil($total_posts / $this->posts_per_page);
    
    if($total_pages > 1)
    {
      $page = (int)getifset($_GET, 'page', 1);
      
      if($page > $total_pages || $page < 1) $page = 1;
      
      $start = ($page - 1) * $this->posts_per_page;
    }
     
    /*include 'core/memcache.php';
    $memcache_data = htnbb_memcache_get_thread($topic_id);
    $is_memcached = $memcache_data !== false;
    
    if(!$is_memcached)
    {*/
      $sql = 'SELECT posts.id, posts.subject, posts.time, posts.poster_id, users.name AS poster_name, users.avatar AS poster_avatar,
        posts.poster_ip, users.is_admin AS is_admin, users.customtitle AS poster_customtitle, posts.text, posts.parse_bbcode,
        posts.replace_smilies, posts.edit_count, posts.last_edit_time,
        groups.rank_icon AS poster_rank_icon, groups.rank_name AS poster_rank_name,
        edit_count, last_edit_time
        FROM posts LEFT JOIN users ON posts.poster_id = users.id LEFT JOIN groups ON groups.id =
        (SELECT group_id FROM user_groups WHERE is_main_group = 1 AND user_id = users.id LIMIT 1) WHERE
         posts.topic_id = ' . $topic_id . ' ORDER BY posts.time ASC';
      
      $r_posts = db_query($sql .' LIMIT ' . $start . ', ' . $this->posts_per_page);
      
      /*if($total_pages > 1)
      {
        htnbb_memcache_store_thread($topic_id, db_query($sql));
      }
      else
      {
        htnbb_memcache_store_thread($topic_id, $r_posts);
      }
      
      $r_posts->data_seek(0);
    }
    elseif($total_pages > 1)
    {
      $memcache_data->reply_data = array_slice($memcache_data->reply_data, $start, $this->posts_per_page);
    }*/
    
    $topic_subject = db_get_first_row('SELECT subject FROM posts WHERE id = ' . $topic->first_post_id . ' LIMIT 1');
    
    $_page->title = htmlspecialchars($topic_subject);
    
    $reply_permitted = in_array(A_WRITE_REPLY, $permissions) && ($topic->locked == false || ($user_logged_in ? $usr->is_admin : 0) == 1);
    $others_post_edit_permitted = in_array(A_EDIT_OTHERS_POSTS, $permissions);
    $others_post_deletion_permitted = in_array(A_EDIT_OTHERS_POSTS, $permissions);
    
    $last_unread_post = getifset(getifset($unread_post_data, $forum->id), $topic_id, 0);
    $last_unread_post_in_scope = false;
    
    include(SHOW_TEMPLATE_FILE); ///////////////////////\\\\\\\\\\\\\\\\\\\\\\
    
    if($last_unread_post_in_scope && $user_logged_in)
    {
      unset($unread_post_data[$forum->id][$topic_id]);
      if(count($unread_post_data[$forum->id]) == 0) unset($unread_post_data[$forum->id]);
      $usr->set_val('unread_post_data', serialize($unread_post_data));
      
      if($user_is_watching_topic) db_query('UPDATE watched_topics SET flag = 1 WHERE topic_id = ' . $topic_id . ' AND user_id = ' . $usr->id . ' LIMIT 1');
    }
  }
  
  /**
   * Zu Post gehen
   **/
  public function post()
  {
    global $user_logged_in, $usr;
    
    $post_id = (int)$_GET['post_id'];
    
    $post = db_query_fetch('SELECT topic_id, time FROM posts WHERE id = ' . $post_id . ' LIMIT 1');
    
    if(!$post)
    {
      message_die('Diesen Beitrag gibt es nicht.');
    }
    
    $post_index = (int)db_get_first_row('SELECT COUNT(*) FROM posts WHERE topic_id = ' . $post->topic_id . ' AND time <= ' . $post->time);
    $page = ceil($post_index / $this->posts_per_page);
    
    header('Location: ' . bb_url('browse', array('topic', $post->topic_id), 'page=' . $page) . '#post' . $post_id);
    
  }
  
}


?>
