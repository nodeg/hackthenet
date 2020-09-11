<?php
/**
 * user_lostpassword template.
 **/

?>

<div class="content">
<h2><?=BOARD_TITLE?></h2>

<?
if($error_occured)
{
  echo htnhtml::error_box('Fehler', $error_msg);
}
?>

<div id="user-lostpassword">

<h3 class="user">Neues Passwort zuschicken lassen</h3>

<form method="post" action="<?=bb_url('user', 'lostpassword', 'sent=1')?>">
<table>
<tr><th>Dein Nickname:</th><td><input type="text" size="25" value="<?=htmlspecialchars($nick)?>" name="nick" maxlength="18" class="stylish" /></td></tr>
<tr><th>Email-Adresse zu diesem Account:</th><td><input type="text" size="25" value="<?=htmlspecialchars($email)?>" name="email" maxlength="32" class="stylish" /><br />
<?=htnhtml::form_submit_row('Neues Passwort anfordern')?>
</table>
</form>

</div>
</div>