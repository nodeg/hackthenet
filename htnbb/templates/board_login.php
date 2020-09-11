<?

if($error_occured)
{
  echo htnhtml::error_box('Fehler', $error_msg);
}

?>


<form action="<?=bb_url('board', 'login', array('doit' => 1, 'redir' => getifset($_REQUEST, 'redir')))?>" method="post">

<table>

<tr class="head"><th colspan="2">Log In</th></tr>
<tr><th>Nickname:</th><td><input type="text" size="25" name="nick" maxlength="18" class="stylish" /></td></tr>
<tr><th>Passwort:</th><td><input type="password" size="25" name="password" class="stylish" /><br /><span class="explanation">
<a href="<?=bb_url('user', 'lostpassword')?>">Passwort vergessen?</a></span></td></tr>
<tr><th>Optionen:</th><td><?=htnhtml::form_checkbox('autologin', true, 'Eingeloggt bleiben')?>
<span class="explanation"> (nur mit Cookies)</span><br />
<?=htnhtml::form_checkbox('nocookies', false, 'Keine Cookies verwenden')?></td></tr>
<?=htnhtml::form_submit_row(' Enter ')?>

</table>

</form>