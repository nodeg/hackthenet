<?php

define('IN_HTN',1);
define('EDIT_TIME_OUT',12*60*60,false);

$FILE_REQUIRES_PC=FALSE;
include('ingame.php');

$action = $_REQUEST['page'];
if($action=='') $action = $_REQUEST['mode'];
if($action=='') $action = $_REQUEST['action'];
if($action=='') $action = $_REQUEST['a'];
if($action=='') $action = $_REQUEST['m'];

$cix = $usr['cluster'];
$cluster = getcluster($cix);


switch($action) 
{
  
  case 'board': // ------------------------ BOARD ---------------------------------
  
  createlayout_top('HackTheNet - Cluster-Board');
  echo '<div class="content" id="cluster-board">'.LF;
  echo '<h2>Cluster-Board</h2>'.LF;
  echo '<p><a href="cboard.php?sid='.$sid.'&amp;a=newthreadform">Neuen Beitrag erstellen</a></p>'."\n";
  
  function listboard($boxid) 
  {
    global $DATADIR, $sid, $usrid, $pcid, $cix, $usr, $cluster;
    
    $admin = ($usr['clusterstat']==CS_ADMIN || $usr['clusterstat']==CS_COADMIN);
    
    $result = db_query('SELECT * FROM cboards WHERE cluster LIKE \''.mysql_escape_string($cix).'\' AND box LIKE \''.mysql_escape_string($boxid).'\' AND relative LIKE \'-1\' ORDER BY time ASC;');
    $cnt = mysql_num_rows($result);
    if ($cnt == 0) echo '<p>Es gibt keine Eintr&auml;ge in diesem Ordner.</p>'."\n";
    else 
    {
      if($admin) echo '<form action="cboard.php?a=admin&amp;box='.$boxid.'&amp;sid='.$sid.'" method="post">'."\n";
      echo '<table>'."\n";
      echo '<tr>'.LF.'<th>Titel</th>'.LF.'<th>Autor</th>'.LF.'<th>Datum</th>'.LF.'<th>Ge&auml;ndert</th>'.LF.'<th>Antworten</th>'."\n";
      if($admin) echo '<th>Markieren</th>'."\n";
      echo '</tr>'."\n";
      
      $output='';
      while($data = mysql_fetch_assoc($result))
      {
        $tmp='';
        $r=db_query('SELECT * FROM cboards WHERE cluster LIKE \''.mysql_escape_string($cix).'\' AND relative LIKE \''.mysql_escape_string($data['thread']).'\' ORDER BY time ASC;');
        $aws=mysql_num_rows($r);
        if($aws>0) 
        {
          $lastm_usr['id']=mysql_result($r,$aws-1,'user');
          $lastm_usr['name']=mysql_result($r,$aws-1,'user_name'); #=getuser(mysql_result($r,$aws-1,'user'),'id');
          $lastm_time=mysql_result($r,$aws-1,'time');
        }
        else $lastm_time=$data['time'];
        
        $tmp.='<tr>'."\n";
        #$sender=getuser($data['user'],'id');
        
        $tmp.='<td><a href="cboard.php?sid='.$sid.'&amp;a=showthread&amp;threadid='.$data['thread'].'">'.$data['subject'].'</a></td>'."\n";
        $tmp.='<td><a href="user.php?a=info&amp;sid='.$sid.'&amp;user='.$data['user'].'">'.$data['user_name'].'</a></td>'.LF.'<td>'.nicetime($data['time']).'</td>'."\n";
        if($lastm_time==$data['time']) $slm='nein'; else $slm=nicetime($lastm_time,true).' von <a href="user.php?a=info&amp;sid='.$sid.'&amp;user='.$lastm_usr['id'].'">'.$lastm_usr['name'].'</a>';
        $tmp.='<td>'.$slm.'</td>';
        if($aws<1) $aws='keine';
        $tmp.='<td>'.$aws.'</td>';
        if($admin) 
        {
          $t = $data[thread];
          $ch1 = ($data['box'] == 0 ? ' selected' : '');
          $ch2 = ($data['box'] == 1 ? ' selected' : '');
          $ch3 = ($data['box'] == 2 ? ' selected' : '');
          $tmp.='<td>';
          /* if(time() > $lastm_time+EDIT_TIME_OUT) 
          {
            */
            $tmp.='<input type="checkbox" name="edit'.$t.'" value="yes" id="d'.$t.'" />';
            /* 
          }
          */
          $tmp.='</td>';
        }
        $tmp.='</tr>';
        $output=$tmp.$output;
      }
      echo $output;
      if($admin) 
      {
        echo '<tr id="cluster-board-folder'.($boxid+1).'-confirm">'."\n";
        echo '<td colspan="6"><select name="axion">';
        echo '<option value="delete">Löschen</option>';
        if ($boxid!=0) echo '<option value="folder1">In Ordner 1 verschieben</option>';
        if ($boxid!=1) echo '<option value="folder2">In Ordner 2 verschieben</option>';
        if ($boxid!=2) echo '<option value="folder3">In Ordner 3 verschieben</option>';
        echo '</select> <input type="submit" value="Ausführen" /></td>'.LF.'</tr>'."\n";
      }
      echo '</table>';
      if($admin) echo '</form>';
    }
  }
  
  echo '<div id="cluster-board-folder1">'.LF.'<h3>Ordner 1 ('.$cluster['box1'].')</h3>';
  listboard(0);
  echo '</div>'.LF.'<div id="cluster-board-folder2">'.LF.'<h3>Ordner 2 ('.$cluster['box2'].')</h3>'."\n";
  listboard(1);
  echo '</div>'.LF.'<div id="cluster-board-folder3">'.LF.'<h3>Ordner 3 ('.$cluster['box3'].')</h3>'."\n";
  listboard(2);
  echo '</div>'.LF.'</div>'."\n";
  createlayout_bottom();
  
  break;
  
  case 'admin': // ------------------------ ADMIN ---------------------------------
  
  if($usr[clusterstat]!=CS_ADMIN && $usr[clusterstat]!=CS_COADMIN) 
  {
    no_('mt0'); exit; 
  }
  
  $box=(int)$_REQUEST['box'];
  
  $result=db_query('SELECT thread,box FROM cboards WHERE cluster LIKE \''.mysql_escape_string($cix).'\' AND box LIKE \''.mysql_escape_string($box).'\' AND relative LIKE \'-1\'');
  $cnt=mysql_num_rows($result);
  while($data=mysql_fetch_assoc($result))
  {
    $id=$data['thread'];
    if($_POST['edit'.$id]!='yes') continue;
    
    $newbox=-1;
    switch($_POST['axion']) 
    {
      case 'delete':
      db_query('DELETE FROM cboards WHERE cluster LIKE \''.mysql_escape_string($cix).'\' AND thread LIKE \''.mysql_escape_string($id).'\' AND relative LIKE \'-1\'');
      db_query('DELETE FROM cboards WHERE cluster LIKE \''.mysql_escape_string($cix).'\' AND relative LIKE \''.mysql_escape_string($id).'\'');
      break;
      case 'folder1':
      $newbox=0;
      break;
      case 'folder2':
      $newbox=1;
      break;
      case 'folder3':
      $newbox=2;
      break;
    }
    if($newbox!=$data['box'] && $newbox>-1) 
    {
      db_query('UPDATE cboards SET box=\''.mysql_escape_string($newbox).'\' WHERE cluster LIKE \''.mysql_escape_string($cix).'\' AND thread LIKE \''.mysql_escape_string($id).'\' AND relative LIKE \'-1\'');
    }
  }
  
  header('Location: cboard.php?sid='.$sid.'&mode=board&rnd='.random_string());
  
  break;
  
  case 'addthread': // ------------------------ ADD THREAD ---------------------------------
  
  $ts=time();
  $title=trim($_POST['title']);
  $text=$_POST['text'];
  $boxid=(int)$_POST[box];
  if($boxid<0 || $boxid>2) $boxid=0;
  
  if($text!='') 
  {
    
    if($title=='') $title='(Beitrag ohne Titel)';
    if(strlen($title)>255) $title=substr($title,0,255);
    $title=formatText($title);
    $text=formatText($text);
    
    $sql='INSERT INTO cboards VALUES (\''.$cix.'\', \'0\', \'-1\', \''.$usrid.'\', \''.mysql_escape_string($usr['name']).'\', \''.$usr['clusterstat'].'\', \''.$ts.'\', \''.mysql_escape_string($title).'\', \''.mysql_escape_string($text).'\', \''.mysql_escape_string($boxid).'\')';
    db_query($sql);
    
    header('Location: cboard.php?sid='.$sid.'&mode=board&rnd='.random_string());
    
  }
  else simple_message('Bitte einen Text für den Beitrag eingeben!');
  break;
  
  case 'newthreadform': // ------------------------ NEW THREAD FORM ---------------------------------
  
  createlayout_top('HackTheNet - Cluster-Board - Beitrag erstellen');
  echo '<div class="content" id="cluster-board">'.LF.'<h2>Cluster-Board</h2>'.LF.'<div id="cluster-board-newthread">'.LF.'<h3>Beitrag erstellen</h3>'."\n";
  showform();
  echo '</div>'.LF.'</div>'."\n";
  createlayout_bottom();
  
  break;
  
  case 'addreply': // ------------------------ ADD REPLY ---------------------------------
  
  $ts=time();
  $title=trim($_POST['title']);
  $text=$_POST['text'];
  $thread=(double)$_REQUEST[threadid];
  
  if($text!='') 
  {
    
    if($title=='') $title='(Beitrag ohne Titel)';
    if(strlen($title)>255) $subject=substr($title,0,255);
    $title=formatText($title);
    $text=formatText($text);
    
    $sql='INSERT INTO cboards VALUES (\''.mysql_escape_string($cix).'\', \'0\', \''.mysql_escape_string($thread).'\', \''.$usrid.'\', \''.mysql_escape_string($usr['name']).'\', \''.mysql_escape_string($usr['clusterstat']).'\', \''.mysql_escape_string($ts).'\', \''.mysql_escape_string($title).'\', \''.mysql_escape_string($text).'\', \'-1\')';
    db_query($sql);
    
    header('Location: cboard.php?sid='.$sid.'&mode=board&rnd='.random_string());
    
  }
  else simple_message('Bitte einen Text für den Beitrag eingeben!');
  
  break;
  
  case 'showthread': // ------------------------ SHOW THREAD ---------------------------------
  
  createlayout_top('HackTheNet - Cluster-Board - Beitrag');
  echo '<div class="content" id="cluster-board-post">'."\n";
  echo '<h2>Cluster-Board</h2>'."\n";
  
  $id=(double)$_REQUEST['threadid'];
  
  function show_beitrag($data) 
  {
    global $STYLESHEET,$DATADIR,$sid;
    #$sender=getuser($data[user]);
    if($data[relative]==-1) echo '<h3>Beitrag: '.$data['subject'].'</h3><br />'."\n";
    $cstat=cscodetostring($data['user_cstat']);
    $time=nicetime($data['time']);
    echo '<table>
    <tr><th>'.$data['subject'].' von <a href="user.php?a=info&user='.$data['user'].'&sid='.$sid.'">'.$data['user_name'].'</a>
    ('.$cstat.'), '.$time.'</th></tr>
    <tr valign="top"><td>'.$data['content'].'</td></tr>
    </table><br />';
  }
  
  $result=db_query('SELECT * FROM cboards WHERE thread LIKE \''.mysql_escape_string($id).'\' AND cluster LIKE \''.mysql_escape_string($cix).'\'');
  if(mysql_num_rows($result)>0) 
  {
    //settype($a,'array');
    $a=array();
    show_beitrag(mysql_fetch_assoc($result));
    $result=db_query('SELECT * FROM cboards WHERE relative LIKE \''.mysql_escape_string($id).'\' ORDER BY time ASC;');
    while($data=mysql_fetch_assoc($result))
    {
      #array_unshift($a,$data);
      show_beitrag($data);
    }
    foreach($a as $item) show_beitrag($item);
  }
  else exit;
  
  echo '<br /><br />'.LF.'<div id="cluster-board-post-reply">'.LF.'<h3>Antwort schreiben</h3>'."\n";
  showform('addreply');
  echo '</div>';
  echo '<p><a href="cboard.php?sid='.$sid.'&m=board">Zurück</a></p>'."\n";
  
  echo '</div>'."\n";
  createlayout_bottom();
  
  break;
  
}


function LoadSmilies() 
{
  // ------------------------ LOAD SMILIES ---------------------------------
  $smilies = array();
  $smilies[] = array('file'=>'angry.gif', 'symbol'=>'&gt;:(');
  $smilies[] = array('file'=>'biggrin.gif', 'symbol'=>':D');
  $smilies[] = array('file'=>'cheesy.gif', 'symbol'=>':lol:');
  $smilies[] = array('file'=>'confused.gif', 'symbol'=>'%-)');
  $smilies[] = array('file'=>'cool.gif', 'symbol'=>'8-)');
  $smilies[] = array('file'=>'dead.gif', 'symbol'=>'X-(');
  $smilies[] = array('file'=>'eek.gif', 'symbol'=>':-O');
  $smilies[] = array('file'=>'glubsch.gif', 'symbol'=>':glubsch:');
  $smilies[] = array('file'=>'king.gif', 'symbol'=>':king:');
  $smilies[] = array('file'=>'sad.gif', 'symbol'=>':-(');
  $smilies[] = array('file'=>'sleep.gif', 'symbol'=>':sleep:');
  $smilies[] = array('file'=>'smile.gif', 'symbol'=>':-)');
  $smilies[] = array('file'=>'tongue.gif', 'symbol'=>':-P');
  $smilies[] = array('file'=>'rolleyes.gif', 'symbol'=>':rolleyes:');
  $smilies[] = array('file'=>'wink.gif', 'symbol'=>';-)');
  $smilies[] = array('file'=>'twisted.gif', 'symbol'=>':evil:');
  $smilies[] = array('file'=>'mg.gif', 'symbol'=>':mg:');
  return $smilies;
}

function formatText($s) 
{
  // ------------------------ FORMAT TEXT ---------------------------------
  global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $sid,$usrid,$pcid,$cix;
  
  $text=$s;
  $text=htmlentities($text);
  $text=nl2br($text);
  
  #$text=preg_replace('/[code](.*?)[\\/code]/is', '<pre>\\1</pre>', $text);
  $text=str_replace('  ',' ',$text);
  $a=LoadSmilies();
  for($i=0;$i<count($a);$i++) 
  {
    $text=str_replace(htmlentities($a[$i][symbol]),'<img src="smilies/'.$a[$i]['file'].'" align=middle border=0>',$text);
  }
  #$text=replace_uri($text);
  
  return $text;
}

function showform($action='addthread') 
{
  // ---------------------------- SHOW FORM -----------------------
  global $STYLESHEET, $REMOTE_FILES_DIR, $DATADIR, $sid,$usrid,$pcid,$usr,$cluster;
  
  if($action=='addreply') $xval='Antwort'; else $xval='';
  echo '<form method="post" name="formular" action="cboard.php?sid='.$sid.'&amp;a='.$action.'">
  <table>
  <tr>
  <th>Titel:</th>
  <td><input name="title" maxlength="40" value="'.$xval.'" /></td>
  </tr>
  ';
  if($action=='addthread') echo '<tr>'.LF.'<th>Ordner:</th>'.LF.'<td><select name="box"><option value="0">1 ('.$cluster['box1'].')</option><option value="1">2 ('.$cluster['box2'].')</option><option value="2">3 ('.$cluster['box3'].')</option></select></td></tr>';
  
  echo '<tr><th>Dein Beitrag:</th>
  <td><textarea name="text" cols="50" rows="5" onkeyup="setCaret(this)" onmouseup="setCaret(this)" onchange="setCaret(this)">';
  if(trim($_REQUEST['text'])!='') $t=trim($_REQUEST['text']); elseif($usr['sig_board']!='') $t="\n\n\n\n".$usr['sig_board'];
  echo stripslashes($t);
  echo '</textarea><br />';
  
  $a=LoadSmilies();
  for($i=0; $i<count($a); $i++) 
  {
    echo '<a href="javascript:smily(\''.htmlentities($a[$i]['symbol']).'\');"><img src="smilies/'.$a[$i]['file'].'" title="'.$a[$i]['symbol'].'" alt="Smilie" border="0" /></a> ';
  }
  
  echo '</td></tr>
  <tr id="cluster-board-newthread-confirm">
  <td colspan="2"><input type="submit" value="Beitrag abschicken" /></td>
  </tr>
  </table>';
  if(isset($_REQUEST['threadid'])) echo '<input name="threadid" type=hidden value="'.$_REQUEST['threadid'].'">';
  echo '</form>';
}

?>