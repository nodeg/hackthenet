<?php
/**
 * watchedtopics_show template.
 **/

?>

<div class="content" id="watchedtopics-show">
<h2>Abonnierte Themen</h2>

<p class="explanation">Hier werden alle Themen aufgelistet, bei denen du über neue Antworten per Email
benachrichtigt wirst. Du kannst hier auch einzelne oder viele Themen aus der Liste entfernen, falls
du zu diesen nicht mehr benachrichtigt werden willst.</p>

<?php

if($r_wtopics->num_rows == 0)
{
  echo '<p>Du hast keine Themen abonniert.</p>';
}
else
{
  echo '<form action="' . bb_url('watchedtopics', 'massunwatch') . '" method="post">'.LF;
  echo '<table class="watchedtopiclist">'.LF;
  echo '<thead><tr><th>Forum » Thema</th><th><abbr title="Zum Löschen markieren">LM</abbr></th></tr></thead><tbody>' . LF;
  
  while($topic = $r_wtopics->fetch_object())
  {
    echo '<tr class="watchedtopic">'.LF;
    echo '<td class="forum_subject"><a href="' . bb_url('browse', array('forum', $topic->forum_id)) . '">' . htmlspecialchars($topic->forum_name) . '</a>'.LF;
    echo ' » <a href="' . bb_url('browse', array('topic', $topic->id)) . '">' . htmlspecialchars($topic->subject) . '</a></td>'.LF;
    echo '<td class="checkbox">' . htnhtml::form_checkbox('topic' . $topic->id, '', '') . '</td>' . LF;
    echo '</tr>'.LF;
  }
  echo '</tbody>' . LF . htnhtml::form_submit_row('Markierte löschen') . LF . '</table>'.LF;
  echo '</form>';
}

?>

</div>