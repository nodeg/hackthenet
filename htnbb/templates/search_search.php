<div class="content" id="search-search">
<h2>Foren-Suche</h2>
<?

if($error_occured)
{
  echo htnhtml::error_box('Fehler', $error_msg);
}

?>


<form action="<?=bb_url('search', 'search', array('exec' => 1))?>" method="post">

<table>

<tr class="head"><th colspan="2">Suche</th></tr>

<tr><th>Suchbegriff(e):</th><td><input type="text" value="<?=htmlspecialchars(getifset($_POST, 'query'))?>" size="25" name="query" class="stylish" /><br />
<span class="explanation">Suchbegriffe durch Leerzeichen trennen, mindestens 4 Zeichen pro Suchbegriff</span></td></tr>

<?=htnhtml::form_submit_row(' Suchen ')?>

</table>

<p>Wichtig: Die Suche ist noch längst nicht fertig, bitte keine Feature Requests / Bugmeldungen hierzu.</p>

<?

if($exec && count($results) > 0)
{
  echo '<dl class="search-topiclist">';
  foreach($results as $topic_id => $topic)
  {
    echo '<dt><a href="' . bb_url('browse', array('topic', $topic_id)) . '">' . $topic['subject'] . '</a></dt>';
    echo '<dd><dl class="search-postlist">';
    foreach($topic as $match)
    {
      if(!is_array($match)) continue;
      echo '<dt><a href="' . bb_url('browse', array('post', $match['post_id'])) . '">' . $match['subject'] . '</a></dt>';
      echo '<dd>' . $match['text'] . '</dd>';
    }
    echo '</dl></dd>';
  }
  echo '</dl>';
}
elseif($exec)
{
  echo htnhtml::important_box('Ergebnis', 'Es wurden keine Beiträge gefunden, die deinen Kriterien entsprechen.');
}

?>

</form>
</div>