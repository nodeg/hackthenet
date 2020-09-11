<?php
/**
 * board_index template.
 **/

?>

<div class="content" id="board-index">
<h2><?=BOARD_TITLE?></h2>

<ul class="forumslist">
<?php

$current_categorie = '';
while($forum = $r_forums->fetch_object())
{
  if($current_categorie != $forum->categorie)
  {
    echo '<li class="categorie-header">' . $forum->categorie . '</li>'.LF;
    $current_categorie = $forum->categorie;
  }
  echo '<li class="' . ($forum->locked == 1 ? 'locked-' : '') . 'forum';
  if(isset($unread_post_data[$forum->id])) echo ' unreadposts';
  echo '">'.LF.'<span class="name"><a href="' . bb_url('browse', array('forum', $forum->id)) . '">' . $forum->name . '</a></span>'.LF;
  echo '<span class="description">' . $forum->description . '</span>'.LF;
  if($forum->last_post_id > 0)
  {
    echo '<span class="last_post">Letzter Beitrag: ' . nicetime::short($forum->last_post_time) . ', <a href="' . bb_url('browse', array('post', $forum->last_post_id)) . '">' .
      htmlspecialchars($forum->last_post_topic_title) . '</a> von ' . ($forum->last_post_user == 0 ? 'Gast' :
      '<a href="' . bb_url('user', array('info', $forum->last_post_user)) . '">' . $forum->last_post_user_name . '</a>') . '</span>'.LF;
  }
  else
  {
      echo '<span class="last_post">Noch keine Beiträge</span>'.LF;
  }
  echo '</li>'.LF;
}

?>
</ul>

<div class="submenu"><p>
<a href="<?=bb_url('board', 'setasread', 'forum=all')?>">Alle Foren als gelesen markieren</a>
</p></div>

<div id="board-index-stats">
<h3 class="statistic">Statistiken</h3>

<p>Unsere <span class="val"><?=numfmt($stats->user_count)?></span> registrierten Benutzer
haben <span class="val"><?=numfmt($stats->post_count)?></span> Beiträge in
<span class="val"><?=numfmt($stats->topic_count)?></span> Themen geschrieben.</p>

<p>Benutzer online: <?

$index = 0;
while($user = $r_online_users->fetch_object())
{
  $index++;
  if($user->is_admin || $user->invisible == 1)
  {
    echo '<span class="';
    if($user->is_admin) echo 'admin_user';
    if($user->invisible) echo ' invisible_user';
    echo '">';
  }
  if($user->is_admin == 1) echo htnhtml::small_gray_icon('broetchen', '', 'Admin', false). ' ';
  echo '<a href="' . bb_url('user', array('info', $user->id)) . '">' . $user->name . '</a>';
  if($user->is_admin || $user->invisible == 1)
  {
    echo '</span>';
  }
  if($index < $r_online_users->num_rows) echo ', ';
}

?></p>

</div>

</div> 
