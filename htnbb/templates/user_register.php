<?php
/**
 * user_register template.
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

<div id="user-register">

<h3 class="user">Account anlegen</h3>

<form method="post" action="<?=bb_url('user', 'register', 'sent=1')?>">
<table>
<tr><th>Nickname:</th><td><input type="text" size="25" value="<?=htmlspecialchars($nick)?>" name="nick" maxlength="18" class="stylish" /></td></tr>
<tr><th>Email-Adresse:</th><td><input type="text" size="25" value="<?=htmlspecialchars($email)?>" name="email" maxlength="32" class="stylish" /><br />
<span class="explanation">An diese Email-Adresse wird dir eine Email geschickt, in der du <br />dein Passwort und den Link
zum Aktivieren deines Accounts findest.</span></td></tr>
<?=htnhtml::form_submit_row(' Abschicken ')?>
</table>
</form>

</div>
</div>