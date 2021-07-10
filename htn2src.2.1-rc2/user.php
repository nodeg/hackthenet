<?php

define('IN_HTN',1);
$FILE_REQUIRES_PC=FALSE;
include('ingame.php');

$action=$_REQUEST['page'];
if($action=='') $action=$_REQUEST['mode'];
if($action=='') $action=$_REQUEST['action'];
if($action=='') $action=$_REQUEST['a'];
if($action=='') $action=$_REQUEST['m'];

switch($action) 
{
  case 'config': //------------------------- CONFIG -------------------------------
  
  createlayout_top('HackTheNet - Optionen');
  
  echo '<div class="content" id="settings">
  <h2>Optionen</h2>';
  
  echo '<br /><br />';
  
  while(list($bez,$val)=each($usr)) 
  {
    $usr[$bez]=safeentities(html_entity_decode($val));
  }
  
  $m=''; $w=''; $x='';
  if($usr['gender']=='x') $x=' checked="checked"';
  elseif($usr['gender']=='m') $m=' checked="checked"';
  elseif($usr['gender']=='w') $w=' checked="checked"';
  
  $dd=explode('.',$usr['birthday']);
  if($dd[0]==0) $xx=' selected'; else $xx='';
  $days='<option'.$xx.' value="0">?</option>'; 
  for($i=1;$i<32;$i++) 
  {
    
    if((int)$dd[0]==$i) 
    $xx=' selected="selected"'; 
    else 
    $xx=''; 
    $days.='<option'.$xx.' value="'.$i.'">'.$i.'</option>'; 
  }
  if($dd[1]==0) 
  $xx=' selected'; 
  else 
  $xx='';
  $months='<option value="0">?</option>'; 
  for($i=1;$i<13;$i++) 
  {
    
    if((int)$dd[1]==$i) 
    $xx=' selected="selected"'; 
    else 
    $xx=''; 
    $months.='<option'.$xx.' value="'.$i.'">'.$i.'</option>'; 
  }
  if($dd[2]==0) 
  $xx=' selected'; 
  else 
  $xx='';
  $years='<option value="0">?</option>'; 
  for($i=1900;$i<2001;$i++) 
  {
    
    if((int)$dd[2]==$i) 
    $xx=' selected="selected"'; 
    else 
    $xx=''; 
    $years.='<option'.$xx.' value="'.$i.'">'.$i.'</option>'; 
  }
  
  if($usr['stat']>1) 
  $statx='<tr>'.LF.'<th>Dein Status:</th>'.LF.'<td>privilegiert<br />F&uuml;r die Sonderfunktionen rufe die Info-Seite eines Users auf!</td>'.LF.'</tr>'."\n";
  if($usr['stat']==1000) 
  $statx='<tr>'.LF.'<th>Dein Status:</th>'.LF.'<td>King</td>'.LF.'</tr>'."\n";
  
  if($usr['bigacc']=='yes') 
  $account='Extended Account ' . ($usr['extacc_id'] != '' ? '(<span style="color:red;">Deine ExtAcc-ID: '.$usr['extacc_id'].'</span>)<br />
  Diese ID bitte gut aufbewahren! Ohne sie kannst du deinen ExtAcc in kommenden Runden/Versionen von HTN nicht mehr benutzen!' : '');
  elseif($usr['ads']=='no') 
  $account='werbefrei'; 
  else 
  $account='normal';
  
  
  if(eregi('http://.*/.*',$usr['avatar'])!==false) $avatar='<br />'.LF.'<img src="'.$usr['avatar'].'" alt="Avatar" />';
  
  $xsinfo='';
  $styles='';
  reset($stylesheets);
  foreach($stylesheets As $data) 
  {
    if(($data['bigacc']=='yes' && $usr['bigacc']=='yes') || $data['bigacc']=='no') 
    {
      $styles.='<option value="'.$data['id'].'"';
      if($usr['stylesheet']==$data['id']) $styles.=' selected';
      $styles.='>'.$data['name'].' (by '.$data['author'].')</option>';
    }
    else 
    {
      $xsinfo.='<em>'.$data['name'].'</em>, ';
    }
  }
  if($xsinfo!='') $xsinfo='<br />Weitere Stylesheets in Extended Accounts: '.substr($xsinfo,0,strlen($xsinfo)-2);
  
  $ipcheck=($usr['noipcheck']=='yes' ? '' : 'checked="checked" ');
  /*
  if($usr['bigacc']=='yes') 
  {
    #$usessl=($usr['usessl']=='yes' ? 'checked="checked" ' : 'no');
    #$usessl='<input type="checkbox" value="yes" name="usessl" '.$usessl.'/>';
    $usessl='<em>Diese Funktion steht in K&uuml;rze f&uuml;r alle Extended Account-User zur Verf&uuml;gung</em>';
  }
  else 
  {
    $usessl='<em>Diese Funktion steht nur in Extended Accounts zur Verf&uuml;gung</em>';
  }
  $usessl.="\n";*/
  
  echo '
  ';
  
  if($usr['bigacc']=='yes') echo '<div class="submenu"><p>
  <a href="abook.php?mode=admin&amp;sid='.$sid.'">Adressbuch verwalten</a>
  </p>
  </div>';
  
  if($usr['bigacc']=='yes') 
  {
    $tmp = parse_url('http://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF']);
    
    $url = 'http://' . $tmp['host'] . $tmp['path'] . '/usrimg.php/'.$server.'-'.$usrid.'.png';
    $url = str_replace('//', '/', $url);
    $usrimg=($usr['enable_usrimg']!='yes' ? '' : 'checked="checked" ');
    $usrimg='<input type="checkbox" value="yes" name="enable_usrimg" '.$usrimg.'/>
    URL des Bildes: <a href="'.$url.'">'.$url.'</a>';
  }
  else $usrimg='<em>Diese Funktion steht nur in Extended Accounts zur Verf&uuml;gung!</em>';
  
  echo $notif.'<div id="settings-settings">
  <h3>'.$usr['name'].'</h3>
  <form action="user.php?a=saveconfig&amp;sid='.$sid.'" method="post">
  <table>
  <tr id="settings-settings-account">
  <th>Account-Typ:</th>
  <td>'.$account.'</td>
  </tr>
  <tr id="settings-settings-gender">
  <th>Geschlecht:</th>
  <td><input type="radio" name="sex" value="m" id="sm"'.$m.' />M&auml;nnlich <input type="radio" name="sex" value="w" id="sw"'.$w.' />Weiblich <input type="radio" name="sex" value="x" id="sx"'.$x.' />Keine Angabe</td>
  </tr>
  <tr id="settings-settings-date-of-birth">
  <th>Geburtsdatum:</th>
  <td><select name="bday">'.$days.'</select>. <select name="bmonth">'.$months.'</select> <select name="byear">'.$years.'</select></td>
  </tr>
  <tr id="settings-settings-style">
  <th>HackTheNet-Style:</th>
  <td><select name="style">'.$styles.'</select>'.$xsinfo.'</td>
  </tr>
  <tr id="settings-settings-homepage">
  <th>Deine Homepage:</th>
  <td><input type="text" name="homepage" value="'.$usr['homepage'].'" maxlength="100" /></td>
  </tr>
  <tr id="settings-settings-city">
  <th>Wohnort:</th>
  <td><input type="text" name="ort" value="'.$usr['wohnort'].'" /></td>
  </tr>
  <tr id="settings-settings-description">
  <th>Beschreibung (max. 2048 Zeichen):</th>
  <td><textarea name="aboutme" rows="5" cols="50">'.$usr['infotext'].'</textarea></td>
  </tr>
  <tr id="settings-settings-avatar">
  <th>Avatar-Bild (http://&nbsp;...):</th>
  <td><input type="text" name="avatar" value="'.$usr['avatar'].'" />'.$avatar.'</td>
  </tr>
  <tr id="settings-settings-mail-signature">
  <th>Signatur f&uuml;r Mails (max. 255 Zeichen):</th>
  <td><textarea name="sig_mails" rows="4" cols="30">'.$usr['sig_mails'].'</textarea></td>
  </tr>
  <tr id="settings-settings-board-signature">
  <th>Signatur f&uuml;r Cluster-Board (max. 255 Zeichen):</th>
  <td><textarea name="sig_board" rows="4" cols="30">'.$usr['sig_board'].'</textarea></td>
  </tr>
  <tr id="settings-settings-mail-maximum">
  <th>&raquo;Posteingang voll&laquo;-Nachricht:</th>
  <td><input type="text" value="'.$usr['inbox_full'].'" name="inbox_full" maxlength="250" /><br />
  Wenn dein Posteingang voll ist, erh&auml;lt ein User, der dir eine Nachricht schicken will, diese Meldung</td>
  </tr>
  <tr id="settings-settings-ipcheck">
  <th>Session an IP-Adresse binden:</th>
  <td><input type="checkbox" value="yes" name="ipcheck" '.$ipcheck.'/></td>
  </tr>
  <tr id="settings-settings-usrimg">
  <th>Benutzerinfo-Bild aktivieren:</th>
  <td>'.$usrimg.'</td>
  </tr>';
  
  /*<!--<tr id="settings-settings-usessl">
  <th>SSL-Verschl&uuml;sselte Verbindung:</th>
  <td>'.$usessl.'</td>
  </tr>-->*/
  
  $usrimg_fmt='';
  $fmts=array('points', 'ranking', 'points ranking', 'cluster points', 'cluster ranking', 'cluster points ranking');
  $fmtnms=array('Punkte', 'Ranglisten-Platz', 'Punkte + Platz', 'Cluster + Punkte', 'Cluster + Platz', 'Cluster + Platz + Punkte');
  for($i=0;$i<count($fmts);$i++) 
  {
    $usrimg_fmt.='<option value="'.$fmts[$i].'"';
    if($usr['usrimg_fmt']==$fmts[$i]) $usrimg_fmt.=' selected="selected"';
    $usrimg_fmt.='>'.$fmtnms[$i].'</option>'."\n";
  }
  
  if($usr['bigacc']=='yes') 
  {
    echo '<tr id="settings-settings-usrimg">
    <th>Format des Benutzerinfo-Bildes:</th>
    <td><select name="usrimg_fmt">
    '.$usrimg_fmt.'
    </select></td>
    </tr>';
  }
  echo '<tr id="settings-settings-delete-account">
  <th>Account l&ouml;schen:</th>
  <td><input type="checkbox" value="yes" name="delete_account" /></td>
  </tr>
  '.$statx.'
  <tr id="settings-settings-confirm">
  <td colspan="2"><input type="submit" value="Speichern" /></td>
  </tr>
  </table>
  </form>
  </div>
  
  <div id="settings-mail">
  <h3>Email-Adresse &auml;ndern</h3>
  <form action="user.php?a=setmailaddy&amp;sid='.$sid.'" method="post">
  <table>
  <tr id="settings-mail-address">
  <th>Deine Email-Adresse:</th>
  <td><input type="text" name="email" value="'.$usr['email'].'" /><br />
  Die Email-Adresse ist f&uuml;r andere Benutzer nicht sichtbar</td>
  </tr>
  <tr id="settings-mail-password">
  <th>Dein Account-Passwort:</th>
  <td><input name="pwd" type="password" /><br />
  Bitte zur Best&auml;tigung eingeben.</td>
  </tr>
  <tr id="settings-mail-confirm">
  <td colspan="2"><input type="submit" value="Speichern" /></td>
  </tr>
  </table>
  </form>
  </div>';
  
  echo '<div id="settings-password">
  <form action="user.php?a=newpwd&amp;sid='.$sid.'" method="post">
  <h3>Passwort &auml;ndern (Sonder-Funktion)</h3>
  <table>
  <tr id="settings-password-password">
  <th>Neues Passwort:</th>
  <td><input name="pwd" type="password" maxlength="16" /></td>
  </tr>
  <tr id="settings-password-confirm">
  <td colspan="2"><input type="submit" value="Speichern" /></td>
  </tr>
  </table>
  </form>
  </div>
  ';
  echo '</div>'."\n";
  createlayout_bottom();
  break;
  
  case 'saveconfig': //------------------------- SAVE CONFIG -------------------------------
  
  if($_POST['delete_account'] == 'yes') 
  {
    echo '<html><head><title>Account l&ouml;schen</title></head><body style="font-family:arial;">
    <br /><br /><br /><br /><hr>
    <b>Dir wurde eine Email zugeschickt ('.$usr['email'].'), in der du die L&ouml;schung des Accounts noch einmal best&auml;tigen musst!
    <br /><br />Du wurdest au&szlig;erdem ausgeloggt!</b>
    <hr>
    </body></html>';
    $code = random_string(16);
    
    $body='Hallo '.$usr['name'].'!'.LF."\n".'Schade, dass du deinen Account bei www.hackthenet.org l�schen m�chtest!'.LF."\n";
    $body.='Wenn du dir ganz sicher bist, klicke auf den folgenden Link:'."\n";
    $body.='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/pub.php?a=deleteaccount&code='.$code;
    
    if(!@mail($usr['email'],'HackTheNet-Account l�schen?',$body,'From: robot@hackthenet.org')) 
    {
      echo nl2br($body);
    }
    
    file_put('data/regtmp/del_account_'.$code.'.txt',$usrid.'|'.$server);
    @unlink('data/login/'.$sid.'.txt');
  }
  else 
  {
    # Nicht Account l�schen sondern Settings speichern
    
    $g=$_POST['sex'];
    if($g=='') $g='x';
    $birthday=$_POST['bday'].'.'.$_POST['bmonth'].'.'.$_POST['byear'];
    $hp=trim($_POST['homepage']);
    $ort=trim($_POST['ort']);
    $text=trim($_POST['aboutme']);
    $sig_mails=trim($_POST['sig_mails']);
    $sig_board=trim($_POST['sig_board']);
    $inbox_full=trim($_POST['inbox_full']);
    $avatar=trim($_POST['avatar']);
    $style=$_POST['style'];
    $noipcheck=($_POST['ipcheck']!='yes' ? 'yes' : 'no');
    
    $usessl=($_POST['usessl']=='yes' ? 'yes' : 'no');
    if($usr['bigacc']!='yes') $usessl='no';
    
    $enable_usrimg=($_POST['enable_usrimg']=='yes' ? 'yes' : 'no');
    if($usr['bigacc']!='yes') $enable_usrimg='no';
    
    $usrimg_fmt=$_POST['usrimg_fmt'];
    
    $pcs=explode(',',$usr['pcs']);
    
    $e=false;
    $error='';
    
    if(eregi('http://.*',$hp)==false) 
    {
      $hp=''; 
    }
    if(eregi('http://.*/.*',$avatar)==false) 
    {
      $avatar=''; 
    }
    if(strlen($ort)<3) $ort='';
    if(strlen($text)>2048) 
    {
      $e=true; $error.='Die Beschreibung darf maximal 2048 Zeichen haben!'; 
    }
    if(strlen($sig_mails)>255) 
    {
      $e=true; $error.='Die Signatur f&uuml;r Mails darf maximal 255 Zeichen haben!'; 
    }
    if(strlen($sig_board)>255) 
    {
      $e=true; $error.='Die Signatur f&uuml;rs Cluster-Board darf maximal 255 Zeichen haben!'; 
    }
    if(strlen($inbox_full)>255) 
    {
      $e=true; $error.='Die Nachricht bei vollem Posteingang darf maximal 255 Zeichen haben!'; 
    }
    
    if($e==false) 
    {
      
      while(list($bez,$val)=each($_POST)) $_POST[$bez]=html_entity_decode($val);
      
      $usr['gender']=$g;
      $usr['birthday']=$birthday;
      $usr['homepage']=safeentities($hp);
      $usr['infotext']=safeentities($text);
      $usr['wohnort']=safeentities($ort);
      $usr['sig_mails']=safeentities($sig_mails);
      $usr['sig_board']=safeentities($sig_board);
      $usr['inbox_full']=safeentities($inbox_full);
      $usr['avatar']=safeentities($avatar);
      if($stylesheets[$style]['bigacc']=='yes' && $usr['bigacc']=='no') $style=$standard_stylesheet;
      $usr['stylesheet']=$style;
      $usr['noipcheck']=$noipcheck;
      $usr['usessl']=$usessl;
      if($usr['usrimg_fmt']!=$usrimg_fmt || $usr['enable_usrimg']!=$enable_usrimg) 
      {
        @unlink('data/_server'.$server.'/usrimgs/'.$usrid.'.png');
      }
      $usr['enable_usrimg']=$enable_usrimg;
      $usr['usrimg_fmt']=$usrimg_fmt;
      saveuserdata();
      header('Location: user.php?a=config&sid='.$sid.'&ok='.urlencode('Die &Auml;nderungen wurden gespeichert.'));
    }
    else 
    {
      site_header('Optionen'); body_start(); echo '<h2>Optionen</h2>';
      echo '<div class="error">FEHLER:<br />'.$msg.'<br /><br />';
      echo 'Aufgrund dieser Fehler wurden die &Auml;nderungen <i>nicht</i> &uuml;bernommen!</div>';
      echo '</div>'; site_footer(); 
    }
    
  }
  
  break;
  
  case 'setmailaddy': //------------------------- SET MAIL ADDY -------------------------------
  $email=trim($_POST['email']);
  if(!check_email($email)) 
  {
    simple_message('Bitte eine g&uuml;ltige Email-Adresse im Format xxx@yyy.zz angeben!');
  }
  else 
  {
    $pwd=trim($_POST['pwd']);
    $real_pwd=$usr['password'];
    
    if($pwd==$real_pwd || md5($pwd)==$real_pwd) 
    {
      db_query('UPDATE users SET email=\''.mysql_escape_string($email).'\' WHERE id=\''.$usrid.'\'');
      echo mysql_error();
      header('Location: user.php?a=config&sid='.$sid.'&saved=1');
    }
    else 
    {
      simple_message('Falsches Passwort!');
    }
  }
  break;
  
  case 'info': //------------------------- INFO -------------------------------
  $index=$_REQUEST['user'];
  $a=getuser($index);
  if($a!=false) 
  {
    
    $u_points=$a['points'];
    createlayout_top('HackTheNet - Benutzerprofil');
    if($a['gender']=='x')
    $geschl='';
    elseif($a['gender']=='m')
    $geschl='M&auml;nnlich';
    elseif($a['gender']=='w')
    $geschl='Weiblich';
    if($geschl!='')
    $geschl='<tr>'.LF.'<th>Geschlecht:</th>'.LF.'<td>'.$geschl.'</td>'.LF.'</tr>'."\n";
    if($a['wohnort']!='') $ort='<tr>'.LF.'<th>Wohnort:</th><td>'.$a['wohnort'].'</td>'.LF.'</tr>'."\n";
    
    if($a['locked']=='yes' && ($a['locked_till']>time() || $a['locked_till']==0)) 
    {
      if($a['locked_till']==0) $lotime='auf unbegrenzte Zeit'; else $lotime=' bis '.nicetime($a['locked_till']);
      $locked='<tr id="account-locked">'.LF.'<th>Besonderheiten:</th>'.LF.'<td>Account gesperrt '.$lotime.'</td>'.LF.'</tr>'.LF;
    }
    
    if($a['birthday']!='0.0.0') 
    {
      list($bday,$bmonth,$byear)=explode('.',$a['birthday']);
      $years=date('Y')-$byear;
      if($bmonth>date('m')) $years--;
      if($bmonth==date('m') AND $bday>date('d')) $years--;
      if($years<=104) 
      {
        $alter=$years.' Jahre';
        $gb='<tr>'.LF.'<th>Alter</th>'.LF.'<td>'.$alter.'</td>'.LF.'</tr>'."\n";
      }
    }
    if(eregi('http://.*',$a['homepage'])!=false) 
    {
      $hp=dereferurl($a['homepage']);
      $hp=safeentities($hp);
      $hp='<tr>'.LF.'<th>Homepage:</th><td><a href="'.$hp.'">'.safeentities($a['homepage']).'</a></td>'.LF.'</tr>'."\n";
    }
    $descr=nl2br($a['infotext']);
    $c=$a['cluster'];
    if($c!=false) 
    {
      $c=getcluster($c);
      $scluster='<a href="cluster.php?a=info&amp;cluster='.$a['cluster'].'&amp;sid='.$sid.'">'.$c['name'].'</a> '.$c['code'];
    }
    else $scluster='keiner';
    
    $spcs='';
    $sql=db_query('SELECT * FROM pcs WHERE owner='.mysql_escape_string($a['id']).' ORDER BY name ASC;');
    $pccnt=mysql_num_rows($sql);
    while($xpc=mysql_fetch_assoc($sql))
    {
      $country=GetCountry('id',$xpc['country']);
      $xpc['name']=htmlentities($xpc['name']);
      if((int)$usr['stat']>=100) $extras=' <a href="secret.php?sid='.$sid.'&amp;m=file&amp;type=pc&amp;id='.$xpc['id'].'">Extras</a>'; else $extras='';
      $spcs.='<li>'.$xpc['name'].' (10.47.'.$xpc['ip'].', <a href="game.php?m=subnet&amp;sid='.$sid.'&amp;subnet='.subnetfromip($xpc['ip']).'">'.$country['name'].'</a>, '.number_format($xpc['points'],0,',','.').' Punkte)'.$extras.'</li>';
    }
    if($a['sid_lastcall'] > time() - SID_ONLINE_TIMEOUT)
    $online='<span style="color:green;">Online</span>';
    else 
    $online='<span style="color:red;">Offline</span>';
    
    if($usr['stat']>=100) $descr.='</td>'.LF.'</tr>'.LF.'<tr>'.LF.'<th>Sonder-Funktionen:</th>'.LF.'<td><a href="secret.php?sid='.$sid.'&amp;m=file&amp;type=user&amp;id='.$a['id'].'">'.($usr['stat']==1000 ? 'Bearbeiten' : 'Daten ansehen').'</a>';
    
    if($usr['stat']==1000) 
    {
      $descr.='<br />'.LF;
      if($a['locked']=='yes' && ($a['locked_till']>time() || $a['locked_till']==0))
      {
        $descr.='<a href="secret.php?a=unlockacc&amp;sid='.$sid.'&amp;user='.$a['id'].'">Account <b>ent</b>sperren</a>';
      }
      else
      {
        $descr.='<a href="secret.php?a=lockacc&amp;sid='.$sid.'&amp;user='.$a['id'].'">Account schnell sperren</a> | ';
        $descr.='<a href="secret.php?a=lockaccex&amp;sid='.$sid.'&amp;user='.$a['id'].'">Account erweitert sperren</a>';
      }
      $descr.='<br /><a href="secret.php?a=delacc1&amp;sid='.$sid.'&amp;user='.$a['id'].'">Account l&ouml;schen</a>';
      $descr.='<br /><a href="secret.php?a=login_ips&amp;sid='.$sid.'&amp;user='.$a['id'].'">Login-IPs untersuchen</a>';
    }
    
    if($usr['bigacc']=='yes') $bigacc='| <a href="abook.php?sid='.$sid.'&amp;action=add&amp;user='.$index.'">User zum Adressbuch hinzuf&uuml;gen</a>';
    
    $attack='(keine Info)';
    $attack='Letztes Login: <i>'.nicetime3($a['login_time']).'</i><br />Angriff: <i>'.$attack.'</i>';
    
    if(eregi('http://.*/.*',$a['avatar'])!==false) 
    {
      #if($usr['sid_ip']!='noip') 
      {
        $avatar=$a['avatar'];
        $avatar='<tr><td colspan="2"><img src="'.$avatar.'" alt="'.$a['name'].'" /></td></tr>';
        #
      }
    }
    
    echo '<div class="content" id="user-profile">
    <h2>Benutzer-Profil</h2>
    <div id="user-profile-profile">
    <h3>'.$a['name'].'</h3>
    <div class="submenu">
    <p><a href="mail.php?m=newmailform&amp;sid='.$sid.'&amp;recip='.$a['name'].'">Mail an User</a> |
    <a href="ranking.php?m=ranking&amp;sid='.$sid.'&amp;type=user&amp;id='.$a['id'].'">User in Rangliste</a>
    '.$bigacc.'</p>
    </div>
    <table>
    '.$avatar.'
    <tr>
    <th>Punkte</th><td>'.number_format($a['points'],0,',','.').'</td>
    </tr>
    '.$geschl.$gb.$ort.$hp.$locked.'
    <tr>
    <th>Cluster</th><td>'.$scluster.'</td></tr>
    <tr>
    <th>Computer ('.$pccnt.')</th>
    <td><ul class="nomargin">'.$spcs.'</ul>'.$pchw.'</td>
    </tr>
    <tr>
    <th>Angriff?</th>
    <td>'.$attack.'</td>
    </tr>
    <tr>
    <th>Online?</th>
    <td>'.$online.'</td>
    </tr>
    ';
    if($descr != '') 
    {
      echo '<tr>
      <th>Beschreibung:</th>
      <td>'.$descr.'</td>
      </tr>
      ';
    }
    echo '</table>
    </div>
    </div>
    ';
    createlayout_bottom();
  }
  else simple_message('Diesen Benutzer gibt es nicht!');
  break;
  
  case 'newpwd': //------------------------- NEW PWD -------------------------------
  $pwd = trim($_POST['pwd']);
  db_query('UPDATE users SET password=\''.md5($pwd).'\' WHERE id='.$usrid.' LIMIT 1');
  simple_message('Passwort ge&auml;ndert auf <i>'.$pwd.'</i>');
  break;
  
}


?>