<?php
/**
 * user_info template.
 **/

?>

<div class="content">
<h2><?=BOARD_TITLE?> - <?=$user->name?></h2>

<div id="user-info">

<h3 class="user"><?=$user->name?></h3>

<?
if($user->email_public)
  $emailstring = '<a href="mailto:'.$user->email.'">'.$user->email.'</a>';
elseif(!$user->email_public && !$usr->is_admin)
  $emailstring = '<em>Nicht sichtbar</em>';
else
  $emailstring = '<a href="mailto:'.$user->email.'">'.$user->email.'</a> (<em>nicht sichtbar</em>)';
if(!$user->invisible){ 
  if((time()-$user->sess_lastcall) > SESSION_INACTIVE_TIME){
    $status = 'Offline';
    $statuscol = 'C00';
  } else {
    $status = 'Online';
    $statuscol = '050';
  }
} elseif($user->invisible && !$usr->is_admin){
  $status = '<em>versteckt</em>';
  $statuscol = '000';
} else {
    if((time()-$user->sess_lastcall) > SESSION_INACTIVE_TIME){
      $status = 'Offline (<em>versteckt</em>)';
      $statuscol = 'C00';
    } else {
      $status = 'Online (<em>versteckt</em>)';
      $statuscol = '050';
    }
}
$groupname = db_get_first_row('SELECT name FROM groups WHERE id = (SELECT group_id FROM user_groups WHERE user_id='.$user->id.' AND is_main_group=1)');
if($usr->is_admin){
 $postcount = db_get_first_row('SELECT count(*) FROM posts WHERE poster_id='.$user->id);
 $postline='<tr><th>Postcount (admin-only):</th><td>'.$postcount.' Beitr&auml;ge</td></tr>';
}
function echoifnotempty($string){
  if($string) return $string;
  else return "-";
}
?>
<table>
<?if($user->avatar){?>
<tr><th colspan="2" style="text-align: center"><img src="<?=FORUM_BASEDIR.'/media/avatars/'.$user->avatar?>" alt="Avatar"/></th></tr>
<?}?>
<tr><th>Status:</th><td style="font-weight: bold; color: #<?=$statuscol?>"><?=$status?></td></tr>
<tr><th>Gruppe:</th><td><?=$groupname?><?if($user->customtitle)echo' (<em>'.$user->customtitle.'</em>)'?></td></tr>
<tr><th>Wohnort:</th><td><?=echoifnotempty($user->residence)?></td></tr>
<tr><th>Cluster:</th><td><?=echoifnotempty($user->cluster)?></td></tr>
<tr><th>Registriert:</th><td><?=nicetime::extended($user->registered_time)?></td></tr>
<tr><th>E-Mail:</th><td><?=$emailstring?></td></tr>
<tr><th>Jabber-ID:</th><td><?=echoifnotempty($user->jabber_id)?></td></tr>
<tr><th>Verwarnungen:</th><td><?=$user->warnings?> Verwarnung(en)</td></tr>
<?if($postline)echo $postline;?>
</table>

</div>
</div>
