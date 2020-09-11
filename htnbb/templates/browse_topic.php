<?php
/**
 * browse_topic template.
 **/

//if($is_memcached) echo '<p>loaded from memcache!</p>';
?>

<div class="content" id="browse-topic">
<?/*<h2><?=BOARD_TITLE?></h2>*/?>

<?

$youarehere_line = '
<p class="youarehere">
<a href="' . bb_url('board', 'index') . '">' . BOARD_TITLE . '</a> » 
<a href="' . bb_url('browse', array('forum', $forum->id)) . '">' . $forum->name . '</a> » ' .
htmlspecialchars($topic_subject) . '
</p>';

$pagination = '';
if($total_pages > 1)
{
  for($p = 1; $p <= $total_pages; $p++)
  {
    if($p != $page) $pagination .= '<a href="' . bb_url('browse', array('topic', $topic->id), 'page='.$p) . '">';
    $pagination .= $p;
    if($p != $page) $pagination .= '</a>';
    if($p < $total_pages) $pagination .= ' | ';
  }
}

if($pagination != '') $pagination = '<p class="pagination"><span class="title">Seite:</span> ' . $pagination . '</p>';

$menu_line = '';

if($user_logged_in)
{
  $menu_line .= '<a href="' . bb_url('watchedtopics', ($user_is_watching_topic ? 'un' : '') . 'watch', 'topic=' . $topic_id) . '">Benachrichtigung bei Antworten ' . ($user_is_watching_topic ? 'abbestellen' : 'erhalten') . '</a>';
}

if(in_array(A_CLOSE_TOPIC, $permissions))
{
  $menu_line .= ' | <a href="' . bb_url('modcp', 'toggleclosedtopic', 'topic=' . $topic_id) . '">Thema ' . ($topic->locked ? ' wiedereröffnen' : 'schließen') . '</a>';
}

if(in_array(A_MOVE_TOPICS, $permissions))
{
  $menu_line .= ' | <a href="' . bb_url('modcp', 'movetopic', 'topic=' . $topic_id) . '">Thema verschieben</a>';
}

if($menu_line != '')
{
  $menu_line = '<div class="submenu"><p>' . trim($menu_line, ' |') . '</p></div>';
}

echo $youarehere_line . $menu_line . $pagination;

echo '<ul class="postview">'.LF;
while($post = ($is_memcached ? current($memcache_data->reply_data) : $r_posts->fetch_object()))
{
  if($is_memcached) next($memcache_data->reply_data);
  echo '<li id="post' . $post->id . '">' . LF;
  
  echo '<div class="user_info">'.LF;
  echo '<span class="poster_name">';
  if(!empty($post->poster_rank_icon)) echo '<img class="rank_icon" src="' . $post->poster_rank_icon . '" alt="' . $post->poster_rank_name . '" /> ';
  echo ($post->poster_id == 0 ? 'Gast' :
    '<a href="' . bb_url('user', array('info', $post->poster_id)) . '">' . $post->poster_name . '</a>') . '</span>'.LF;
 
  if(!empty($post->poster_rank_name) || !empty($post->poster_customtitle))
  {
    echo '<span class="rank">' . (!empty($post->poster_rank_name) ?
      htmlspecialchars($post->poster_rank_name) . '<br />' : '') .
      preg_replace('/(\S{8,}[\-\s])/i', '$1<br />', htmlspecialchars($post->poster_customtitle)) . '</span>'.LF;
  } 
  if(!empty($post->poster_avatar))
  {
    $avatar_url = (substr($post->poster_avatar, 0, 7) == 'http://' ? $post->poster_avatar : FORUM_BASEDIR . '/media/avatars/' . $post->poster_avatar);
  	echo '<div class="avatar"><img src="' . $avatar_url . '" alt="Der Avatar von '. $post->poster_name .'" /></div>'.LF;
  }
  echo '</div>'.LF;
  
  echo '<div class="post_main_area">'.LF;
  
  echo '<div class="post_info">'.LF;
  echo '<span class="subject">' . (empty($post->subject) ? '&nbsp;' : htmlspecialchars($post->subject)) . '</span>'.LF;
  $ipbit = '';
  if($usr->is_admin && !$post->is_admin)
  {
    $host = '';
    if($is_memcached)
    {
      if(!empty($post->poster_hostname)) $host = $post->poster_hostname.' - ';
    }
    elseif(substr_count($post->poster_ip, '/') == 0)
    {
    	$host = gethostbyaddr($post->poster_ip).' - ';
    }
    
    $ipbit = '<br/><span style="font-size: xx-small;">'.$host.$post->poster_ip.'</span>';
  }
  echo '<span class="time">' . nicetime::short($post->time) . $ipbit . '</span>'.LF;
  echo '</div>'.LF;
  
  echo '<div class="text">'.LF;
  if($is_memcached)
  {
    echo $post->text_parsed;
  }
  else
  {
    echo parse_post_text(nl2br(htmlspecialchars(($post->text))), $post->parse_bbcode, $post->replace_smilies);
  }
  echo '</div>'.LF;
  
  echo '</div>'.LF;
  
  echo '<div class="buttons">'.LF;
  if($post->poster_id == ($user_logged_in ? $usr->id : -1) || $others_post_edit_permitted)
  {
    echo htnhtml::small_gray_icon('edit', bb_url('board', 'newpost', 'edit=1&post='.$post->id), 'Bearbeiten');
  }
  if($others_post_deletion_permitted)
  {
    echo '&nbsp;' . htnhtml::small_gray_icon('delete', bb_url('modcp', 'delete', ($post->id == $topic->first_post_id ? 'topic='.$topic->id : 'post='.$post->id)), ($post->id == $topic->first_post_id ? 'ganzes Topic' : 'Post').' löschen', true, 'Wirklich löschen?');
  }
  if($reply_permitted)
  {
    echo '&nbsp;' . htnhtml::small_gray_icon('quote', bb_url('board', 'newpost', 'topic='.$topic->id.'&quote=1&post='.$post->id), 'Mit Zitat antworten');
  }
  echo '</div>'.LF;
  
  if($post->edit_count > 0)
  {
    echo '<span class="edits">' . $post->edit_count . ' Mal bearbeitet, das letzte Mal ' . nicetime::short($post->last_edit_time) . '</span>'.LF;
  }
  
  echo '</li>'.LF;
  if($last_unread_post >= $post->id) $last_unread_post_in_scope = true;
  unset($post);
  unset($host);
}
echo '</ul>'.LF;

echo $pagination;

if($page == $total_pages && $reply_permitted)
{
  $title_required = $is_new_topic = false;
  $subject = '';
  $text = '';
  $parse_bbcode = $replace_smilies = true;
  $form_action = bb_url('board', 'newpost', 'topic=' . $topic_id);
  $watch_topic = $user_is_watching_topic;
  echo '<h3 class="edit">Antworten</h3>';
  include 'templates/_post_form.php';
}
elseif($topic->locked && ($user_logged_in ? $usr->is_admin : 0) == 0)
{
  echo htnhtml::important_box('Achtung', 'Dieses Thema ist gesperrt, hier können keine neuen Antworten mehr geschrieben werden.');
}


echo $menu_line . $youarehere_line;

?>

</div>
