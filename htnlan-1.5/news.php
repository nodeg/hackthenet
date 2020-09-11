<?php




$action=$_GET['action'];
if ($action=='') { $action=$_GET['m']; }
switch($action) {

  default:
  echo '<div class="info"><h3>News</h3></div>';
  $page=$_GET['p'];
  $maxpages=ceil(mysql_num_rows(mysql_query('SELECT id FROM news'))/$news_pro_page);
  if ($page=="") { $page=1; }
  if (!is_numeric($page)) { echo 'hacking attempt.'; exit(); }
  if ($page<1) { $page=1; }
  if ($page>$maxpages) { $page=1; }
  $result=mysql_query('SELECT * FROM news ORDER BY id DESC LIMIT '.($page -1) * $news_pro_page.','.$news_pro_page);
  if ($usr['stat']>=100) { echo "<dl><a href=\"secret.php?a=news&b=add&sid=".$sid."\">News-Eintrag hinzufügen</a></dl><br>"; }
  if (mysql_num_rows($result)>0) {
    while($news=mysql_fetch_array($result)) {
      $newstext=explode(" ", $news['text']); $newstext2='';
      for ($i=0; $i<150; $i++) {
        $newstext2.=$newstext[$i].' ';
      }
      $relatedlinks='';
      if ($news['url1']!="" && $news['link1']!="") { $relatedlinks.="<a href=\"derefer.php?u=".$news['url1']."\" target=\"_blank\">".$news['link1']."</a><br>"; }
      if ($news['url2']!="" && $news['link2']!="") { $relatedlinks.="<a href=\"derefer.php?u=".$news['url2']."\" target=\"_blank\">".$news['link2']."</a><br>"; }
      if ($news['url3']!="" && $news['link3']!="") { $relatedlinks.="<a href=\"derefer.php?u=".$news['url3']."\" target=\"_blank\">".$news['link3']."</a><br>"; }
      if ($relatedlinks=="") { $relatedlinks="<i>nicht vorhanden</i>"; }
      if ($usr['stat']>=100) { $adminoption="<a href=\"secret.php?a=news&b=edit&id=".$news['id']."&sid=".$sid."\">edit</a> | <a href=\"secret.php?a=news&b=del&id=".$news['id']."&sid=".$sid."\">del</a>"; }
      else { $adminoption=""; }
      echo "
      <table width=\"80%\">
      <tr><th>".$news['kategorie']." :: ".$news['title']." ".$adminoption."</th><th width=\"20%\">Related Links:</th></tr>
      <tr><td>".text_replace($newstext2)."...<br><a href=\"news.php?action=comment&id=".$news['id']."&sid=".$sid."\">Read more ...</a></td><td valign=\"top\">".$relatedlinks."</td></tr>
      <tr><th>erstellt am: ".date("d.m.Y", $news['time']).", ".date("H:i", $news['time'])." Uhr von <a href=\"user.php?a=info&user=".$news['autor_id']."&sid=".$sid."\">".$news['autor']."</a></th><th>&nbsp;</th></tr>
      </table>";
    }
    if ($maxpages>1) {
      $pagem = $page - 1;
      $pagep = $page + 1;
      $pagepp = $page + 2;
      echo "<dl><a href=\"?m=start&p=1&sid=".$sid."\">«</a> ";
      if (!($page == "1")) {
        echo "<a href=\"?m=start&p=".$pagem."&sid=".$sid."\"><</a> ";
      }
      else {
        echo "<a href=\"#\"><</a> ";
      }
      if ($page == "1") {
        echo "1 ";
        if ($maxpages >= "2") { echo "<a href=\"?m=start&p=".$pagep."&sid=".$sid."\">2</a> "; }
        if ($maxpages >= "3") { echo "<a href=\"?m=start&p=".$pagepp."&sid=".$sid."\">3</a> "; }
      }
      else {
        if ($maxpages >= $pagem) { echo "<a href=\"?m=start&p=".$pagem."&sid=".$sid."\">".$pagem."</a> "; }
        if ($maxpages >= $page) { echo $page." "; }
        if ($maxpages >= $pagep) { echo "<a href=\"?m=start&p=".$pagep."&sid=".$sid."\">".$pagep."</a> "; }
      }
      if ($pagep > $maxpages) {
        echo "<a href=\"#\">></a> ";
      }
      else {
        echo "<a href=\"?m=start&p=".$pagep."&sid=".$sid."\">></a> ";
      }
      echo "<a href=\"?m=start&p=".$maxpages."&sid=".$sid."\">»</a></dl> ";
    }
  } else { echo '<dl>Es wurden noch keine News geschrieben</dl>'; }
  break;

  case 'comment':
  $newsid=$_REQUEST['id'];
  if (!is_numeric($newsid)) { echo 'hacking attempt.'; exit(); }
  $news=getnews($newsid);
  if($news!=false) {
    createlayout_top('HackTheNet - News - '.$news['kategorie'].' :: '.$news['title'].'');
    echo '<div class="content" id="overview">
    <h2>'.$news['kategorie'].' :: '.$news['title'].'</h2>';

    echo '<div class="info"><h3>Erstellt am: '.date("d.m.Y", $news['time']).', '.date("H:i", $news['time']).' Uhr</h3></div>';
    if ($_GET['b']=="") {
      $relatedlinks='';
      if ($news['url1']!="" && $news['link1']!="") { $relatedlinks.="<a href=\"derefer.php?u=".$news['url1']."\" target=\"_blank\">".$news['link1']."</a><br>"; }
      if ($news['url2']!="" && $news['link2']!="") { $relatedlinks.="<a href=\"derefer.php?u=".$news['url2']."\" target=\"_blank\">".$news['link2']."</a><br>"; }
      if ($news['url3']!="" && $news['link3']!="") { $relatedlinks.="<a href=\"derefer.php?u=".$news['url3']."\" target=\"_blank\">".$news['link3']."</a><br>"; }
      if ($relatedlinks=="") { $relatedlinks="<i>nicht vorhanden</i>"; }
      if ($usr['stat']>=100) { $adminoption="<a href=\"secret.php?a=news&b=edit&id=".$news['id']."&sid=".$sid."\">edit</a> | <a href=\"secret.php?a=news&b=del&id=".$news['id']."&sid=".$sid."\">del</a>"; }
      else { $adminoption=""; }
      echo "
      <table width=\"80%\">
      <tr><th>".$news['kategorie']." :: ".$news['title']." ".$adminoption."</th><th width=\"20%\">Related Links:</th></tr>
      <tr><td>".text_replace(nl2br($news['text']))."</td><td valign=\"top\">".$relatedlinks."</td></tr>
      <tr><th>erstellt am: ".date("d.m.Y", $news['time']).", ".date("H:i", $news['time'])." Uhr von <a href=\"user.php?a=info&user=".$news['autor_id']."&sid=".$sid."\">".$news['autor']."</a></th><th>&nbsp;</th></tr>
      </table><br>";
      $comments_pro_page=5;
      $page=$_GET['p'];
      $maxpages=ceil(mysql_num_rows(mysql_query('SELECT id FROM news_comment WHERE news_id='.$newsid))/$comments_pro_page);
      if ($page=="") { $page=1; }
      if (!is_numeric($page)) { echo 'hacking attempt.'; exit(); }
      if ($page<1) { $page=1; }
      if ($page>$maxpages) { $page=1; }
      $sql=mysql_query('SELECT * FROM news_comment WHERE news_id='.mysql_escape_string($newsid).' ORDER BY id ASC LIMIT '.($page -1) * $comments_pro_page.','.$comments_pro_page);
      if (mysql_num_rows($sql)>0) {
        echo "<h3>Kommentare</h3>";
        echo "<table id=\"comments\" width=\"80%\">";
        while($comments=mysql_fetch_array($sql)) {
          if ($usr['stat']>=100) { $adminoption="<a href=\"secret.php?a=newsc&b=edit&id=".$comments['id']."&sid=".$sid."\">edit</a> | <a href=\"secret.php?a=newsc&b=del&id=".$comments['id']."&sid=".$sid."\">del</a>"; }
          else { $adminoption=""; }
          echo "<tr><th>".$comments['titel']." von <a href=\"user.php?a=info&user=".$comments['autor_id']."&sid=".$sid."\">".$comments['autor']."</a> am: ".date("d.m.Y", $comments['time']).", um ".date("H:i", $comments['time'])." Uhr ".$adminoption."</th></tr>
          <tr><td>".text_replace(nl2br($comments['text']))."</td></tr>";
        }
        echo "</table><br>";

        if ($maxpages>1) {
          $pagem = $page - 1;
          $pagep = $page + 1;
          $pagepp = $page + 2;
          echo "<dl><a href=\"?m=comment&id=".$newsid."&p=1&sid=".$sid."\">«</a> ";
          if (!($page == "1")) {
            echo "<a href=\"?m=comment&id=".$newsid."&p=".$pagem."&sid=".$sid."\"><</a> ";
          }
          else {
            echo "<a href=\"#\"><</a> ";
          }
          if ($page == "1") {
            echo "1 ";
            if ($maxpages >= "2") { echo "<a href=\"?m=comment&id=".$newsid."&p=".$pagep."&sid=".$sid."\">2</a> "; }
            if ($maxpages >= "3") { echo "<a href=\"?m=comment&id=".$newsid."&p=".$pagepp."&sid=".$sid."\">3</a> "; }
          }
          else {
            if ($maxpages >= $pagem) { echo "<a href=\"?m=comment&id=".$newsid."&p=".$pagem."&sid=".$sid."\">".$pagem."</a> "; }
            if ($maxpages >= $page) { echo $page." "; }
            if ($maxpages >= $pagep) { echo "<a href=\"?m=comment&id=".$newsid."&p=".$pagep."&sid=".$sid."\">".$pagep."</a> "; }
          }
          if ($pagep > $maxpages) {
            echo "<a href=\"#\">></a> ";
          }
          else {
            echo "<a href=\"?m=comment&id=".$newsid."&p=".$pagep."&sid=".$sid."\">></a> ";
          }
          echo "<a href=\"?m=comment&id=".$newsid."&p=".$maxpages."&sid=".$sid."\">»</a></dl>";
        }

      } else { echo '<h3>Kommentare</h3>'."\n"; echo'<table><tr><th>Es wurden noch keine Kommentare geschrieben</th></tr></table>'."\n"; }
      if ($usr['name']!="") {
        echo "<form action=\"news.php?action=comment&b=add&id=".$newsid."&sid=".$sid."\" method=\"post\"><h3>Kommentare hinzufügen</h3>
        <table width=\"80%\">
        <tr><th>Username:</th><td>".$usr['name']."</td></tr>
        <tr><th>Titel:</th><td><input type=\"text\" name=\"titel\" maxlength=\"40\" value=\"Re: ".$news['title']."\" size=\"40\"></td></tr>
        <tr><th>Text:</th><td><textarea name=\"text\" cols=70 rows=6></textarea></td></tr>
        <tr><th>&nbsp;</th><td><input type=\"submit\" name=\"submit\" accesskey=\"s\" value=\" Weiter \"></td>
        </table></form><br>";
      }
    }
    if ($_GET['b']=="add" && $usr['name']!="") {
      if (strlen($_POST['text'])>1000) { echo 'Übertreib es nicht. Text maximal 1000 Zeichen'; exit(); }
      elseif (strlen($_POST['titel'])>40) { echo 'Übertreib es nicht. Titel maximal 40 Zeichen'; exit(); }
      elseif ($_POST['titel']=="") { echo 'Titel wurde nicht angegeben.'; }
      elseif ($_POST['text']=="") { echo 'Text wurde nicht angegeben.'; }
      else {
        mysql_query('INSERT INTO news_comment(`id`, `autor`, `autor_id`, `ip`, `time`, `news_id`, `text`, `titel`) VALUES("", "'.$usr['name'].'", "'.$usrid.'", "'.$usr['sid_ip'].'", "'.time().'", "'.$newsid.'", "'.text_replace(mysql_escape_string(htmlspecialchars($_POST['text']))).'", "'.text_replace(mysql_escape_string(htmlspecialchars($_POST['titel']))).'")');
        echo '<br><dl>Kommentar wurde hinzugefügt.</dl>';
      }
    }
  } else simple_message('Diesen News-Eintrag gibt es nicht!');
  createlayout_bottom();

  break;

}

?>
