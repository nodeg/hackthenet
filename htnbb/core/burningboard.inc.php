<?php

define('GUEST_GROUP', 0);
define('ADMIN_GROUP', 1);
define('MEMBER_GROUP', 2);

define('A_BROWSE_TOPIC_LIST', 'browse_topic_list');
define('A_READ_TOPICS', 'read_topics');
define('A_WRITE_REPLY', 'write_reply');
define('A_CREATE_TOPIC', 'create_topic');
define('A_STICKY_TOPIC', 'sticky_topic');
define('A_SEE_EXISTANCE', 'see_existance');
define('A_CLOSE_TOPIC', 'close_topic');
define('A_EDIT_OTHERS_POSTS', 'edit_others_posts');
define('A_DELETE_OTHERS_POSTS', 'delete_others_posts');
define('A_MOVE_TOPICS', 'move_topics');

$_permission_actions = array(
  A_SEE_EXISTANCE,
  A_BROWSE_TOPIC_LIST,
  A_READ_TOPICS,
  A_WRITE_REPLY,
  A_CREATE_TOPIC,
  A_STICKY_TOPIC,
  A_CLOSE_TOPIC,
  A_EDIT_OTHERS_POSTS,
  A_DELETE_OTHERS_POSTS,
  A_MOVE_TOPICS
);

function get_forum_by_id($forum_id)
{
  $forum_id = (int)$forum_id;
  $r_forum = db_query('SELECT id, name, description, locked FROM forums WHERE id = ' . $forum_id);
  if($r_forum->num_rows == 0) message_die('Dieses Forum gibt es nicht.');
  return $r_forum->fetch_object();
}


function parse_post_text($text, $bbcode = true, $smilies = true)
{
  if($smilies)
  {
    include 'core/smilie-data.php';
    $text = smilies_replace($text, $smilies);
  }
  
  if($bbcode)
    $text = bbcode_parser::parse($text);
  
  return $text;
}


function bb_ipmask($ip)
{
  if(preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3})\.\d{1,3}$/i', $ip, $match))
  {
    return $match[1];
  }
}

function is_permitted_gid($group_id, $forum_id, $action)
{
  return (db_get_first_row('SELECT COUNT(*) FROM group_permissions WHERE group_id = ' . (int)$group_id .
    ' AND forum_id = ' . (int)$forum_id . ' AND action = \'' .
    prepare_string_for_query($action) . '\' LIMIT 1') != false);
}


function is_permitted_u(&$user, $forum_id, $action)
{
  return is_permitted_uid(($user instanceof user ? $user->id : 0), $forum_id, $action);
}


function is_permitted_uid($user_id, $forum_id, $action)
{
  $gid_sql = ($user_id != 0 ? 'IN (SELECT group_id FROM user_groups WHERE user_id = ' . (int)$user_id . ')' : ' = ' . GUEST_GROUP);
  
  return (db_get_first_row('SELECT COUNT(*) FROM group_permissions WHERE group_id ' . $gid_sql .
    ' AND forum_id = ' . (int)$forum_id . ' AND action = \'' .
    prepare_string_for_query($action) . '\' LIMIT 1') != false);
}

function get_permissions_u(&$user, $forum_id)
{
  return get_permissions_uid(($user instanceof user ? $user->id : 0), $forum_id);
}

function get_permissions_uid($user_id, $forum_id)
{
  $gid_sql = ($user_id != 0 ? 'IN (SELECT group_id FROM user_groups WHERE user_id = ' . (int)$user_id . ')' : ' = ' . GUEST_GROUP);
  
  $r = db_query('SELECT action FROM group_permissions WHERE group_id ' . $gid_sql . ' AND forum_id = ' . (int)$forum_id);
  
  $ret = array();
  while($entry = $r->fetch_object())
  {
    $ret[] = $entry->action;
  }
  
  return $ret;
}


function set_setting($key, $val)
{
  global $bb_settings;
  
  $upd_sql = '`value` = \'' . prepare_string_for_query($val) . '\'';;
  db_query('INSERT INTO settings SET ' . $upd_sql . ', `key` = \'' . prepare_string_for_query($key) . '\' ON DUPLICATE KEY UPDATE ' . $upd_sql);
  
  $bb_settings[$key] = $val;
}


function manual_forum_last_post_update($forum_id)
{
  
  $last_post = db_query_fetch('SELECT id, poster_id, time,
    (SELECT first_post_id FROM topics WHERE topics.id = posts.topic_id LIMIT 1) AS first_post_id 
    FROM posts WHERE topic_id IN (SELECT id FROM topics WHERE forum_id = ' . $forum_id . ') ORDER BY time DESC LIMIT 1');

  db_query('UPDATE forums SET last_post_id = ' . getifset($last_post, 'id', 0) . ', last_post_topic_first_post = ' .
    getifset($last_post, 'first_post_id', 0) . ', last_post_user = ' . getifset($last_post, 'poster_id', 0) . ', ' .
    'last_post_time = ' . getifset($last_post, 'time', 0) . ' WHERE id = ' . $forum_id . ' LIMIT 1');

}


?>