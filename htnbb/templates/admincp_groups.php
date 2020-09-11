<div class="content" id="admin">
<h2>Administration</h2>
<?php

include 'templates/_admin_navi.php';
echo '<div class="subnavi_content">';

echo '<form action="' . bb_url('admincp', 'groups', 'save=1') . '" method="post"><table>';
echo '<tr class="head"><th>Name</th><th>Rang-Name</th><th>Rang-Symbol-Datei</th><th><abbr title="Versteckt">V</abbr></th><th>Rechte</th><th><abbr title="Löschen">L</abbr></th></tr>';

while($group = $r_groups->fetch_object())
{
  echo '<tr><td><input class="stylish" value="' . htmlspecialchars($group->name) . '" type="text" name="g_' . $group->id . '_name" size="20" maxlength="48" /></td>';
  echo '<td><input class="stylish" value="' . htmlspecialchars($group->rank_name) . '" type="text" name="g_' . $group->id . '_rank_name" size="20" maxlength="32" /></td>';
  echo '<td><input class="stylish" value="' . htmlspecialchars($group->rank_icon) . '" type="text" name="g_' . $group->id . '_rank_icon" size="20" maxlength="255" /></td>';
  echo '<td>' . htnhtml::form_checkbox('g_' . $group->id . '_hidden', (string)$group->hidden, '') . '</td>';
  echo '<td><a href="' . bb_url('admincp', 'grouppermissions', 'group=' . $group->id) . '">Rechte</a></td>';
  echo '<td>' . ($group->undeletable ? '-' : htnhtml::form_checkbox('g_' . $group->id . '_delete', '0', '')) . '</td>';
  echo '</tr>';
}
echo htnhtml::form_submit_row(' Speichern ', 6);
echo '</table></form>';

?>

</div></div>
