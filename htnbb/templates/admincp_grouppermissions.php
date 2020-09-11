<div class="content" id="admin">
<h2>Administration</h2>
<?php

include 'templates/_admin_navi.php';
echo '<div class="subnavi_content">';

echo '<h3>Gruppe: ' . htmlspecialchars($group->name) . '</h3>'.LF;

echo '<form action="' . bb_url('admincp', 'grouppermissions', 'save=1&group=' . $group_id) . '" method="post">'.LF;

echo '<table><thead><tr><th>Forum</th>';
foreach($_permission_actions as $action)
{
  echo '<th><img src="' . bb_url('admincp', 'upstrimg', 'str=' . $action) . '" alt="' . $action . '" title="' . $action . '" /></th>';
}
echo '</tr></thead><tbody>'.LF;

while($forum = $r_forums->fetch_object())
{
  echo '<tr><th>' . $forum->name . '</th>'.LF;
  foreach($perms[$forum->id] as $action => $permitted)
  {
    echo '<td>';
    echo htnhtml::form_checkbox('p_' . $forum->id . '_' . $action, (string)$permitted, '');
    echo '</td>'.LF;
  }
}

echo '</tbody>';
echo htnhtml::form_submit_row('Speichern', 1 + count($_permission_actions));
echo '</table></form>';

?>

</div></div>