<?php
/**
 * user_list template.
 **/

$pagination = '';

if($total_pages > 1)
{
  for($p = 1; $p <= $total_pages; $p++)
  {
    if($p != $page) $pagination .= '<a href="' . bb_url('user', 'mlist', 'page='.$p) . '">';
    $pagination .= $p;
    if($p != $page) $pagination .= '</a>';
    if($p < $total_pages) $pagination .= ' | ';
  }
}

if($pagination != '') $pagination = '<p class="pagination"><span class="title">Seite:</span> ' . $pagination . '</p>';

?>

<div class="content">
<h2><?=BOARD_TITLE?> - Mitglieder</h2>

<div id="user-list">

<h3 class="user">Mitgliederliste</h3>

<?=$pagination?>
<table>
<thead>
<tr><th>Nr.</th><th>Name</th><th>Dabei seit</th><th>Rang</th></tr>
</thead>
<tbody>
<?
$ix = $start;
while($user = $r_members->fetch_object())
{
  $ix++;
  echo '<tr><th>' . $ix . '</th><td>';
  if($user->is_admin == 1) echo '&nbsp;' . htnhtml::small_gray_icon('broetchen', '', 'Admin', false) . ' ';
  echo '<a href="' . bb_url('user', array('info', $user->id)) . '">' . $user->name . '</a>';
  echo '</td><td>';
  echo nicetime::usual($user->registered_time) . ' Uhr</td>';
  echo '<td>' . $user->rank_title . '</td>';
  echo '</tr>'.LF;
}
?>
</tbody>
</table>
<?=$pagination?>

</div>
</div>
