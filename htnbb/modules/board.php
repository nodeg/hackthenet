<?php

/**
 * board.php 
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.0.1
 **/

if(!defined('IN_BB')) die('Hacking attempt!');

/**
 * board module data
 **/
class htnbb_module_board extends i_htnbb_module
{
  
  public function __construct()
  {
    $this->m_id = 'board';
  }
  
  public function &set_page($pageid)
  {
    switch($pageid)
    {
    case 'index':      $this->current_page = new htn_module_page($this, $pageid, BOARD_TITLE); break;
    case 'newpost':    $this->current_page = new htn_module_page($this, $pageid, 'Neues Thema erstellen'); break;
    case 'login':      $this->current_page = new htn_module_page($this, $pageid, 'Log In'); break;
    case 'logout':     $this->current_page = new htn_module_page($this, $pageid, ''); break;
    case 'setasread':  $this->current_page = new htn_module_page($this, $pageid, ''); break;
    default: $this->current_page = false;
    }
    return $this->current_page;
  }
  
  /**
   * Board-Index
   **/
  public function index()
  {
    global $user_logged_in, $usr, $unread_post_data;
    
    $gid_sql = ($user_logged_in ? 'IN (SELECT group_id FROM user_groups WHERE user_id = ' . $usr->id . ')' : ' = 0');
    
    $r_forums = db_query('
      SELECT forums.id, forums.name, forums.description, forums.last_post_id, forums.last_post_time, forums.last_post_user,
      (SELECT name FROM users WHERE users.id = forums.last_post_user LIMIT 1) AS last_post_user_name,
      (SELECT subject FROM posts WHERE posts.id = forums.last_post_topic_first_post LIMIT 1) AS last_post_topic_title,
      forum_categories.name AS categorie, forums.locked
      FROM forums JOIN forum_categories ON forums.categorie = forum_categories.id WHERE
      forums.id IN (SELECT forum_id FROM group_permissions WHERE action = \'' . A_SEE_EXISTANCE . '\' AND group_id ' . $gid_sql . ')
      ORDER BY forum_categories.sort_index ASC, forums.categorie ASC, forums.sort_index ASC
    ');
    
    $r_online_users = db_query('SELECT id, name, is_admin, invisible FROM users WHERE sess_lastcall > ' . (time() - SESSION_INACTIVE_TIME) . ($usr->is_admin ? '' : ' AND invisible = 0') . ' ORDER BY name ASC');
    
    $stats = db_query_fetch('SELECT (SELECT COUNT(*) FROM posts) AS post_count,
      (SELECT COUNT(*) FROM topics) AS topic_count,
      (SELECT COUNT(*) FROM users) AS user_count');
    
    include(SHOW_TEMPLATE_FILE);
  }
  
  /**
   * Neuen Thread erstellen
   **/
  public function newpost()
  {
    global $user_logged_in, $error_occured, $error_msg, $db, $usr, $ignore_errors;
    
    $is_new_topic = (getifset($_REQUEST, 'topic') == 'new');
    $topic_id = (int)getifset($_REQUEST, 'topic');
    $edit = (getifset($_REQUEST, 'edit') == '1');
    $post_id = (int)getifset($_REQUEST, 'post');
    
    $quote = (getifset($_GET, 'quote') == '1');
    
    $preview = isset($_POST['preview']);
    $save = isset($_POST['submit']);
    
    if(!$is_new_topic)
    {
      if(!$edit)
      {
        // Antwort
        $tmp = db_query_fetch('SELECT forum_id, first_post_id, locked FROM topics WHERE id = ' . $topic_id . ' LIMIT 1');
        $forum_id = $tmp->forum_id;
        if(!$forum_id)
        {
          message_die('Antwort erstellen nicht möglich: Thema nicht vorhanden.');
        }
        
        $topic_title = db_get_first_row('SELECT subject FROM posts WHERE id = ' . $tmp->first_post_id . ' LIMIT 1');
        
        if($quote)
        {
          $qpost = db_query_fetch('SELECT topic_id, text, (SELECT name FROM users WHERE users.id = posts.poster_id LIMIT 1) AS poster_name FROM posts WHERE id = ' . $post_id . ' LIMIT 1');
          $qforum = db_get_first_row('SELECT forum_id FROM topics WHERE id = '. $qpost->topic_id);
          if(!is_permitted_u($usr, $qforum, A_READ_TOPICS)) exit;
          $quote = '[quote][b]Original von ' . $qpost->poster_name . '[/b]' . LF . $qpost->text . '[/quote]';
        }
        
        $topics_first_post_id = $tmp->first_post_id;
        if($tmp->locked == 1 && ($user_logged_in ? $usr->is_admin : 0) != 1)
        {
          message_die('Dieses Thema ist gesperrt. Hier können keine Antworten mehr geschrieben werden.');
        }
        unset($tmp);
      }
      else
      {
        // Bearbeiten
        $post = db_query_fetch('SELECT subject, text, id, topic_id, poster_id, parse_bbcode, replace_smilies
          FROM posts WHERE id = ' . $post_id . ' LIMIT 1');
        
        if(!$post)
        {
          message_die('Bearbeiten nicht möglich: Beitrag nicht vorhanden.');
        }
        
        $topic = db_query_fetch('SELECT id, forum_id, (SELECT subject FROM posts WHERE posts.id = topics.first_post_id LIMIT 1) AS subject,
          first_post_id, locked, sticky FROM topics WHERE id = ' . $post->topic_id . ' LIMIT 1');
        
        $topic_id = $topic->id;
        
        if($topic->locked == 1 && ($user_logged_in ? $usr->is_admin : 0) != 1)
        {
          message_die('Dieses Thema ist gesperrt. Hier können keine Antworten mehr geschrieben werden.');
        }
        
        if($post->poster_id != $usr->id && !is_permitted_u($usr, $topic->forum_id, A_EDIT_OTHERS_POSTS))
        {
          message_die('Du kannst nur deine eigenen Beiträge bearbeiten.');
        }
        
        $forum_id = $topic->forum_id;
        $topic_title = $topic->subject;
        $topics_first_post_id = $topic->first_post_id;
        
        $subject = $post->subject;
        $text = $post->text;
        
        $watch_topic = -1;
        $close_topic = ($topic->locked == 1);
        
        $parse_bbcode = ($post->parse_bbcode == '1');
        $replace_smilies = ($post->replace_smilies == '1');
        $sticky_topic = ($topic->sticky == '1');
      }
    }
    else
    {
      $forum_id = (int)getifset($_GET, 'forum');
    }
    
    $forum = get_forum_by_id($forum_id);
    
    if($forum->locked == 1 && ($user_logged_in ? $usr->is_admin : 0) != 1)
    {
      message_die('Dieses Forum ist gesperrt. Hier können keine Beiträge oder Antworten mehr erstellt oder bearbeitet werden.');
    }
    
    $permissions = get_permissions_u($usr, $forum->id);
    
    if(!in_array(A_READ_TOPICS, $permissions))
    {
      exit;
    }
    
    if(!in_array(($is_new_topic ? A_CREATE_TOPIC : A_WRITE_REPLY), $permissions))
    {
      message_die('Um in diesem Forum ' . ($is_new_topic ? 'Themen erstellen' : 'Beiträge schreiben') . ' zu können, brauchst du spezielle Berechtigungen, über die du aber zur Zeit nicht verfügst.');
    }
    
    if($edit == false || ($save || $preview))
    {
      $subject = trim(getifset($_POST, 'post_subject'));
      $text = trim(getifset($_POST, 'post_text'));
    }
    
    $sticky_topic_permitted = in_array(A_STICKY_TOPIC, $permissions);
    $close_permitted = in_array(A_CLOSE_TOPIC, $permissions);
    
    if($save || $preview)
    {
      $parse_bbcode = (getifset($_POST, 'parse_bbcode') == '1');
      $replace_smilies = (getifset($_POST, 'replace_smilies') == '1');
      
      $sticky_topic = ($sticky_topic_permitted ? (getifset($_POST, 'sticky_topic') == '1') : false);
      $close_topic = ($close_permitted ? (getifset($_POST, 'close_topic') == '1') : false);
      
      $watch_topic = getifset($_POST, 'watch_topic', -1);
    }
    elseif(!$edit)
    {
      $parse_bbcode = $replace_smilies = true;
      $close_topic = $watch_topic = $sticky_topic = false;
    }
    
    if($user_logged_in && !$is_new_topic && !$save)
    {
      if($watch_topic == -1)
        $watch_topic = db_get_first_row('SELECT COUNT(*) FROM watched_topics WHERE topic_id = ' . $topic_id . ' AND user_id = ' . (int)$usr->id);
    }
    
    if(!is_bool($watch_topic)) $watch_topic = ($watch_topic == '1');
    
    if($is_new_topic || $topics_first_post_id == $post_id)
      $title_required  = TRUE;
    else
      $title_required = FALSE;
    // alter zakx hat echt keine Peilung :P
    
    if($save || $preview)
    {
      if($title_required) if(strlen($subject) < 10 || !$subject) handle_error('Der Titel muss mindestens 10 Zeichen haben.'); # (nt='.(int)$is_new_topic.' fpid='.$topic->first_post_id.' pid='.$post_id);
      if(strlen($text) < 10) handle_error('Der Text muss mindestens 10 Zeichen haben. Bitte beachte auch, dass die Forenregeln Ultra-Kurz-Beiträge verbieten.');
    }
    
    if($save)
    {
      if(!$error_occured)
      {
        $sql = ($is_new_topic == false && $edit == true ? 'UPDATE' : 'INSERT INTO');
        $sql .= ' posts SET subject = \'' . prepare_string_for_query($subject) . '\', text = \'' . 
          prepare_string_for_query($text) . '\', parse_bbcode = ' . (int)$parse_bbcode . ', '.
          'replace_smilies = ' . (int)$replace_smilies . ', poster_ip = \'' . prepare_string_for_query(client_ip()) . '\'';
        
        if(!$edit)
        {
          $sql .= ', poster_id = ' . ($user_logged_in ? $usr->id : 0) . ', time = ' . time() . ', topic_id = ';
        }
        
        if($is_new_topic)
        {
          db_query('INSERT INTO topics SET forum_id = ' . (int)$forum_id . ', sticky = ' . (int)$sticky_topic . ', last_post_time = ' . time() . ($close_topic ? ', locked = 1' : ''));
          $new_topic_id = $db->insert_id;
          $sql .= $new_topic_id;
        }
        elseif($edit)
        {
          if($post->poster_id == $usr->id) $sql .= ', edit_count = edit_count + 1, last_edit_time = ' . time();
          $sql .= ' WHERE id = ' . (int)$post_id . ' LIMIT 1';
        }
        else
        {
          $sql .= $topic_id;
        }
        
        db_query($sql);
        
        if(!$edit)
        {
          $new_post_id = $db->insert_id;
        }
        
        if($is_new_topic)
        {
          db_query('UPDATE topics SET first_post_id = ' . $new_post_id . ' WHERE id = ' . $new_topic_id . ' LIMIT 1');
        }
        elseif(!$edit)
        {
          db_query('UPDATE topics SET last_post_id = ' . $new_post_id . ', last_post_user = ' .
            ($user_logged_in ? $usr->id : 0) . ($close_topic ? ', locked = 1' : '') . ', last_post_time = ' . time() . ' WHERE id = ' . $topic_id);
        }
        
        if(!$edit)
        {
          db_query('UPDATE forums SET last_post_id = ' . $new_post_id . ', last_post_topic_first_post = ' .
            ($is_new_topic ? $new_post_id : $topics_first_post_id) .
            ', last_post_user = ' . ($user_logged_in ? $usr->id : 0) . ', last_post_time = ' . time() . ' WHERE id = ' . $forum_id);
          
          set_setting('global_last_post_time', time());
          $usr->set_val('unread_post_data_last_update', time());
          
          // flag;
          // 1 = Alle ungelesenen Beiträge gelesen, Email schicken bei neuer Antwort
          // 0 = Eine oder mehrere Antworten sind noch ungelesen
          
          if(!$is_new_topic)
          {
            $r_abos = db_query('SELECT users.id, users.name, users.email FROM watched_topics JOIN users ON watched_topics.user_id = users.id WHERE topic_id = ' . $topic_id . ' AND flag = 1');
            
            while($watcher = $r_abos->fetch_object())
            {
              if($watcher->id == $usr->id) continue;
              
              $mail_body = 'Hallo ' . $watcher->name . ', ' . LF . LF . ' du wolltest bei Antworten zum Thema ' .
                '"' . $topic_title . '" im HackTheNet-Forum benachrichtigt werden.' . LF . LF .
                'Hier der Link zur ersten ungelesenen Antwort: ' . LF .
                'http://' . $_SERVER['HTTP_HOST'] . bb_url('browse', array('post', $new_post_id)) . LF . LF;
              
              $ignore_errors = true;
              // bool mail ( string to, string subject, string message [, string additional_headers [, string additional_parameters]])
              $success = @mail($watcher->email, 'Benachrichtigung bei Antwort: ' . $topic_title, $mail_body, FROM_MAIL_HEADER);
              $ignore_errors = false;
              
            }
            
            db_query('UPDATE watched_topics SET flag = 0 WHERE topic_id = ' . $topic_id);
          }
        }
        elseif($edit)
        {
          $new_post_id = $post_id;
          $sql = '';
          if($sticky_topic_permitted)
            $sql .= 'sticky = ' . (int)$sticky_topic;
          if($close_permitted)
            $sql .= ', locked = ' . (int)$close_topic;
          $sql = trim($sql, ' ,');
          if($sql != '')
          {
            db_query('UPDATE topics SET ' . $sql . ' WHERE id = ' . $topic_id . ' LIMIT 1');
          }
        }
        
        if($user_logged_in)
        {
          if(!$is_new_topic)
          {
            db_query('DELETE FROM watched_topics WHERE topic_id = ' . $topic_id . ' AND user_id = ' . $usr->id . ' LIMIT 1');
          }
          if($watch_topic)
          {
            db_query('INSERT INTO watched_topics SET topic_id = ' . ($is_new_topic ? $new_topic_id : $topic_id) . ', user_id = ' . $usr->id);
          }
        }

	if(!$is_new_topic)
	{
         /* include 'core/memcache.php';
	  htnbb_memcache_delete_thread($topic_id); // make sure cache stays up2date */
	}
        
        message_die('Dein Beitrag wurde gespeichert!</p></div><div><p><a href="' . bb_url('browse', array('post', $new_post_id)) . '">Zum Beitrag</a></p>' .
          '<p><a href="' . bb_url('browse', array('forum', $forum_id)) . '">Zum Forum</a>', 'info', 'Information');
        
      }
    }
    
    if($quote && !($preview || $save))
    {
      $text = $quote . LF;
    }
    
    $form_action = bb_url('board', 'newpost', ($edit ? 'edit=1&post='.$post_id : 'topic=' . ($is_new_topic ? 'new&forum=' . $forum_id : $topic_id)));
    
    include(SHOW_TEMPLATE_FILE);
    
  }

  
  /**
   * 
   **/
  public function login()
  {
    global $user_logged_in, $error_occured, $error_msg, $sid, $session_mode;
    
    if(getifset($_GET, 'doit') == 1)
    {
      $nick = trim($_POST['nick']);
      $password = trim($_POST['password']);
      $autologin = (getifset($_POST, 'autologin') == '1');
      $nocookies = (getifset($_POST, 'nocookies') == '1');
      
      $correct_pw = db_get_first_row('SELECT password FROM users WHERE name = \'' . prepare_string_for_query($nick) . '\' LIMIT 1');
      
      if(!$correct_pw)
      {
        handle_error('Unbekannter Benutzername!');
      }
      elseif($correct_pw !== sha1($password))
      {
        handle_error('Falsches Passwort!');
      }
      else
      {
        // Login OK
        $sess_id = random_string($nocookies ? 10 : 16);
        // bool setcookie ( string name [, string value [, int expire [, string path [, string domain [, bool secure]]]]] )
        if(!$nocookies)
        {
          setcookie(SESSION_ID_COOKIE_NAME, $sess_id, time() + 86400, FORUM_BASEDIR.'/', $_SERVER['HTTP_HOST']);
          $session_mode = 'cookie';
        }
        else
        {
          $session_mode = 'url';
          $sid = $sess_id;
        }
        
        if($autologin && !$nocookies)
        {
          setcookie(SAVE_LOGIN_COOKIE_NAME, $nick . '|' . sha1($nick . '<-|%|->' . $correct_pw), time() + 86400 * 32, FORUM_BASEDIR.'/', $_SERVER['HTTP_HOST']);
        }
        
        db_query('UPDATE users SET sess_id = \'' . prepare_string_for_query($sess_id) . '\', sess_ipmask = \'' .
          prepare_string_for_query(bb_ipmask($_SERVER['REMOTE_ADDR'])) . '\', sess_lastcall = ' . time() . ' WHERE '.
          'name = \'' . prepare_string_for_query($nick) . '\' LIMIT 1');
        
        bb_redir('board', 'index');
      }
    }
    
    include(SHOW_TEMPLATE_FILE);
  }
  
  /**
   * 
   **/
  public function logout()
  {
    global $sid, $usr;
    
    if($usr instanceof user)
    {
      setcookie(SAVE_LOGIN_COOKIE_NAME, false, time() + 86400 * 32, FORUM_BASEDIR.'/', $_SERVER['HTTP_HOST']);
      setcookie(SESSION_ID_COOKIE_NAME, false, time() + 86400, FORUM_BASEDIR.'/', $_SERVER['HTTP_HOST']);
      $sid = '';
      db_query('UPDATE users SET sess_id = \'\', sess_lastcall = ' . time() . ' WHERE id = ' . (int)$usr->id . ' LIMIT 1');
    }
    bb_redir('board', 'index');
  }
  
  /**
   * 
   **/
  public function setasread()
  {
    global $sid, $usr, $user_logged_in, $unread_post_data;
    
    $forum = getifset($_GET, 'forum', 'all');
    if($forum !== 'all') $forum = (int)$forum;
    
    if($user_logged_in)
    {
      if(is_int($forum))
      {
        unset($unread_post_data[$forum]);
      }
      else
      {
        $unread_post_data = array();
      }
      $usr->set_val('unread_post_data', serialize($unread_post_data));
      $usr->set_val('unread_post_data_last_update', time());
    }
    
    if(is_int($forum))
    {
      bb_redir('browse', array('forum', (int)$forum), 'rnd=' . random_string(10));
    }
    else
    {
      bb_redir('board', 'index', 'rnd=' . random_string(10));
    }
    
  }
  
}


?>
