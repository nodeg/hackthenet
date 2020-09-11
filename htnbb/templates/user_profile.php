<?php
/**
 * user_profile template.
 **/

?>

<div class="content">
<h2><?=BOARD_TITLE?> - Dein Profil</h2>

<?
if($error_occured)
{
  echo htnhtml::error_box('Fehler', $error_msg);
}
elseif($save_pw)
{
  echo htnhtml::ok_box('Gespeichert', 'Dein neues Passwort wurde gespeichert.');
}
?>

<div id="user-profile">

<h3 class="user">Dein Profil</h3>

<form method="post" action="<?=bb_url('user', 'profile', 'save_profile=1')?>">
<table>
<?
foreach($editable_profile_rows as $name => $dat)
{
  echo '<tr><th>' . $dat['caption'] . ':</th><td>';
  switch($dat['type'])
  {
    case 'text':
    case 'email':
    echo '<input class="stylish" type="text" name="' . $name . '" maxlength="' . $dat['maxlen'] . '" value="' . htmlspecialchars($usr->$name) . '" size="30" />';
    break;
    case 'bool':
    echo htnhtml::form_checkbox($name, $usr->$name, $dat['caption2']);
    break;
  }
  if(isset($dat['help'])) echo '<br /><span class="explanation">' . $dat['help'] . '</span>';
  echo '</td></tr>'.LF;
}
echo htnhtml::form_submit_row(' Speichern ');
?>
</table>
</form>

<h3 class="avatar">Avatar ändern</h3>
<p>
Maximale Größe: <?=numfmt(AVA_MAX_SIZE / 1024)?> KB<br />
Maximale Ausmaße: <?=AVA_MAX_WIDTH?>x<?=AVA_MAX_HEIGHT?> Pixel<br />
Erlaubte Dateitypen: <?=joinex($AVA_ALLOWED_MIME, ', ', true, true)?>
</p>

<form method="post" action="<?=bb_url('user', 'profile', 'save_avatar=1')?>" enctype="multipart/form-data">
<table>

<tr><th>Avatar hochladen:</th><td><input type="file" name="new_ava" /><br />
<span class="explanation">Zum Löschen des Avatars das Feld leer lassen.</span></td></tr>

<!--<tr><th>HTN 2.5 Benutzerinfobild:</th><td>URL: <input type="text" class="stylish" name="usrimg_url" size="30" /><br />
<span class="explanation">Du kannst hier die URL zu einem Benutzerinfobild von HTN 2.5 (steht nur in Extended Accounts zur
Verfügung) eintragen.</span></td></tr>//-->

<?=htnhtml::form_submit_row(' Hochladen ');?>
</table>
</form>

<h3 class="password">Passwort ändern</h3>

<form method="post" action="<?=bb_url('user', 'profile', 'save_pw=1')?>">
<table>
<tr><th>Altes Passwort:</th><td><input type="password" size="25" name="old_pw" class="stylish" /></td></tr>
<tr><th>Neues Passwort:</th><td><input type="password" size="25" name="new_pw1" class="stylish" /></td></tr>
<tr><th>Neues Passwort (Wdh.):</th><td><input type="password" size="25" name="new_pw2" class="stylish" /></td></tr>
<?=htnhtml::form_submit_row(' Speichern ')?>
</table>
</form>

</div>
</div>