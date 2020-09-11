<?php
/**
 * board_newpost template.
 **/

?>

<div class="content" id="board-newpost">
<h2><?=($is_new_topic ? $forum->name . ' - Neuer ' : 'Neuer ')?>Beitrag<?=($edit ? ' bearbeiten' : '')?></h2>

<?php

$youarehere_line = '
<p class="youarehere">
<a href="' . bb_url('board', 'index') . '">' . BOARD_TITLE . '</a> » 
<a href="' . bb_url('browse', array('forum', $forum->id)) . '">' . $forum->name . '</a> » ' .
(!$is_new_topic ? '<a href="' . bb_url('browse', array('topic', $topic_id)) . '">' . htmlspecialchars($topic_title) . '</a>' . ($edit ? ' » Beitrag bearbeiten' : ' » Antwort erstellen') : 'Neues Thema') . '
</p>';

echo $youarehere_line;

if($preview)
{
  echo '<h3 class="contents">Vorschau</h3>';
  
  echo '<ul class="postview"><li id="preview"><div class="user_info">';
  echo '<span class="poster_name">';
  echo (!$user_logged_in ? 'Gast' : $usr->name) . '</span>';
  echo '</div>';
  
  echo '<div class="post_main_area">';
  
  echo '<div class="post_info">'.LF;
  echo '<span class="subject">'  . htmlspecialchars($subject) . '&nbsp;</span>';
  echo '<span class="time">' . nicetime::short() . '</span>';
  echo '</div>'.LF;
  
  echo '<div class="text">' . parse_post_text(nl2br(htmlspecialchars($text)), $parse_bbcode, $replace_smilies) .
    '</div></div><div class="buttons">&nbsp;</div></li></ul>';
}

include 'templates/_post_form.php';

echo $youarehere_line;

?>

</div>