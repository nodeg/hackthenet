<?php
/**
 * browse_forum template.
 **/

$youarehere_line = '
<p class="youarehere">
<a href="' . bb_url('board', 'index') . '">' . BOARD_TITLE . '</a> » 
<a href="' . bb_url('browse', array('forum', $forum->id)) . '">' . $forum->name . '</a>
</p>';

$menu_line = '<div class="submenu"><p>
<a href="' . bb_url('board', 'newpost', 'topic=new&forum=' . $forum->id) . '">Neues Thema</a>';
if(count(getifset($unread_post_data, $forum->id, array())) > 0)
{
  $menu_line .= '| <a href="' . bb_url('board', 'setasread', 'forum=' . $forum->id) . '">Forum als gelesen markieren</a>';
}
$menu_line .= '</p></div>';

$pagination = '';
if($total_pages > 1)
{
  for($p = 1; $p <= $total_pages; $p++)
  {
    if($p != $page) $pagination .= '<a href="' . bb_url('browse', array('forum', $forum->id), 'page='.$p) . '">';
    $pagination .= $p;
    if($p != $page) $pagination .= '</a>';
    if($p < $total_pages) $pagination .= ' | ';
  }
}

if($pagination != '') $pagination = '<p class="pagination"><span class="title">Seite:</span> ' . $pagination . '</p>';


?>

<div class="content" id="browse-forum">
<h2><?=BOARD_TITLE?> - <?=$forum->name?></h2>

<?=$youarehere_line . $menu_line . $pagination?>



<?php

if($r_topics->num_rows == 0)
{
  echo '<p>In diesem Forum gibt es noch keine Beiträge.</p>';
}
else
{
  echo '<ul class="topiclist">'.LF;
  
  while($topic = $r_topics->fetch_object())
  {
    echo '<li class="' . ($topic->locked == 1 ? 'locked-' : '') . 'topic';
    if($topic->sticky == 1) echo '-sticky';
    if(isset($unread_post_data[$forum->id][$topic->id])) echo '-unreadposts';
    echo '">'.LF;
    if($topic->sticky == 1) echo '<span class="topic_type">Sticky: </span>';
    echo '<span class="subject"><a href="' . bb_url('browse', array('topic', $topic->id)) . '">' . htmlspecialchars($topic->subject) . '</a></span>'.LF;
    echo '<div class="info">'.LF;
    echo '<span class="original_poster">erstellt von ' . ($topic->topic_starter_id == 0 ? 'Gast' : 
        '<a href="' . bb_url('user', array('info', $topic->topic_starter_id)) . '">' . $topic->topic_starter_name . '</a>') . '</span>, '.LF;
    if($topic->reply_count > 0)
    {
      echo '<span class="replys">' . $topic->reply_count . ' Antwort' . ($topic->reply_count != 1 ? 'en' : '') . '</span>, ';
      echo '<span class="last_post"><a href="' . bb_url('browse', array('post', $topic->last_post_id)) . '">' .
        'Letzte Antwort</a> ' . nicetime::short($topic->last_post_time) . ' von ' . ($topic->last_post_user == 0 ? 'Gast' : 
        '<a href="' . bb_url('user', array('info', $topic->last_post_user)) . '">' . $topic->last_post_user_name . '</a>');
        if(isset($unread_post_data[$forum->id][$topic->id]))
        {
          $first_unread_post_id = $unread_post_data[$forum->id][$topic->id];
          echo '&nbsp;' . htnhtml::small_gray_icon('rightarrow', bb_url('browse', array('post', $first_unread_post_id)), 'Zum ersten ungelesenen Post gehen');
        }
        echo '</span>'.LF;
      $pages = ceil(($topic->reply_count + 1) / $this->posts_per_page);
      if($pages > 1)
      {
        echo '<br /><span class="topic_pagination">Seite: ';
        for($page = 1; $page <= $pages; $page++)
        {
          echo '<a href="' . bb_url('browse', array('topic', $topic->id), 'page='.$page) . '">' . $page . '</a>';
          if($page < $pages) echo ' | ';
        }
        echo '</span>'.LF;
      }
    }
    else
    {
        echo '<span class="replys">noch keine Antworten</span>'.LF;
    }
    echo '</div>'.LF;
    echo '</li>'.LF;
  }
  echo '</ul>'.LF;
}

echo $pagination . $menu_line . $youarehere_line;

?>

</div>