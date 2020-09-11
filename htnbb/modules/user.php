<?php

/**
 * user.php 
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.0.1
 **/

if(!defined('IN_BB')) die('Hacking attempt!');

/**
 * user module data
 **/
class htnbb_module_user extends i_htnbb_module
{
  
  public function __construct()
  {
    $this->m_id = 'user';
  }
  
  public function &set_page($pageid)
  {
    switch($pageid)
    {
    case 'register':          $this->current_page = new htn_module_page($this, $pageid, 'Account anlegen'); break;
    case 'activateaccount':   $this->current_page = new htn_module_page($this, $pageid, 'Account aktivieren');
                              $this->m_virtual_url_rewrite_params = array('key');
                              break;
    case 'profile':           $this->current_page = new htn_module_page($this, $pageid, 'Account-Optionen'); break;
    case 'lostpassword':      $this->current_page = new htn_module_page($this, $pageid, 'Neues Passwort zuschicken lassen'); break;
    case 'info':              $this->current_page = new htn_module_page($this, $pageid, 'Benutzer-Info');
                              $this->m_virtual_url_rewrite_params = array('user_id');
                              break;
    case 'mlist':             $this->current_page = new htn_module_page($this, $pageid, 'Mitgliederliste'); break;
    default: $this->current_page = false;
    }
    return $this->current_page;
  }
  
  /**
   * 
   **/
  public function register()
  {
    global $user_logged_in, $error_occured, $error_msg, $ignore_errors;
    
    if($user_logged_in) exit;
    
    $nick = '';
    $email = '';
    
    if(getifset($_GET, 'sent') == 1)
    {
      $nick = trim($_POST['nick']);
      $email = trim($_POST['email']);
      if(!check_string($nick, 'username')) handle_error('Bitte einen gültigen Benutzernamen eingeben und dabei auf Sonderzeichen weitgehend verzichten.');
      if(strlen($nick) < 3) handle_error('Mindestlänge für Benutzernamen: 3 Zeichen');
      if(!check_string($email, 'email')) handle_error('Bitte eine gültige Email-Adresse eingeben.');
      
      if(db_get_first_row('SELECT COUNT(*) FROM users WHERE name = \'' . prepare_string_for_query($nick) . '\'') > 0)
      {
        handle_error('Einen Benutzer mit diesem Namen gibt es bereits.');
      }
      
      if(!$error_occured)
      {
        include 'core/initial_password_generator.php';
        $data = array('nick' => $nick, 'email' => $email, 'password' => generate_initial_password());
        
        $key = random_string(12);
        
        $mail_body = 'Hallo ' . $nick . ', ' . LF . ' du hast dir einen Account im '.BOARD_TITLE.' angelegt.' . LF . LF .
          'Bevor du den Account nutzen kannst, musst du ihn aktivieren. Rufe dazu die folgende Adresse auf: ' .
          'http://' . $_SERVER['HTTP_HOST'] . bb_url('user', array('activateaccount', $key)) . LF . LF .
          'Hier außerdem deine Zugangsdaten: ' . LF . 'Benutzername: ' . $nick . LF . 'Passwort: ' . $data['password'] . LF . LF .
          'Das Passwort kannst du später in den Optionen jederzeit ändern.' . LF . LF;
        
        db_query('INSERT INTO `temp` SET `key` = \'' . prepare_string_for_query($key) . '\', `value` = \'' . prepare_string_for_query(serialize($data)) . '\'');
        
        $ignore_errors = true;
        // bool mail ( string to, string subject, string message [, string additional_headers [, string additional_parameters]])
        $success = @mail($data['email'], 'Dein ' . BOARD_TITLE . '-Account', $mail_body, FROM_MAIL_HEADER);
        $ignore_errors = false;
        
        if(!$success)
        {
          #message_die(nl2br($mail_body));
          message_die('Die eMail konnte nicht verschickt werden, da ein Fehler auftrat!');
        }
        else
        {
          message_die('Bitte rufe jetzt deine Emails ab und aktiviere deinen neuen Account im HTN-Forum.', 'info', 'Information');
        }
        
      }
    }
    
    include(SHOW_TEMPLATE_FILE);
  }
  
  /**
   * 
   **/
  public function activateaccount()
  {
    global $user_logged_in, $db;
    
    if($user_logged_in) exit;
    
    $key = prepare_string_for_query($_GET['key']);
    
    $reg_dat = db_get_first_row('SELECT `value` FROM temp WHERE `key` = \'' . $key . '\' LIMIT 1');
    
    if(!$reg_dat)
    {
      message_die('Dieser Registrierungsschlüssel ist ungültig. Bitte starte den Registrierungsprozess von vorne.');
    }
    
    $reg_dat = unserialize($reg_dat);
      
    if(db_get_first_row('SELECT COUNT(*) FROM users WHERE name = \'' . prepare_string_for_query($reg_dat['nick']) . '\'') > 0)
    {
      handle_error('Einen Benutzer mit diesem Namen gibt es bereits.');
    }
    
    db_query('INSERT INTO users SET name = \'' . prepare_string_for_query($reg_dat['nick']) . '\', ' .
      'password = \'' . prepare_string_for_query(sha1($reg_dat['password'])) . '\', registered_time = ' . time() .
      ', email = \'' . prepare_string_for_query($reg_dat['email']) . '\', unread_post_data_last_update = ' . (time() - 7 * 86400));
    
    db_query('INSERT INTO user_groups SET user_id = ' . $db->insert_id . ', group_id = ' . MEMBER_GROUP . ', is_main_group = 1');
    
    db_query('DELETE FROM temp WHERE `key` = \'' . $key . '\' LIMIT 1');
    
    message_die('Dein Account ' . $reg_dat['nick'] . ' wurde aktiviert. Du kannst dich jetzt einloggen!', 'info', 'Information');
  }
  
  
  /**
   * 
   **/
  public function profile()
  {
    global $user_logged_in, $usr, $error_occured, $error_msg, $AVA_ALLOWED_MIME;
    
    if(!$user_logged_in) exit;
    
    $save_pw = (getifset($_GET, 'save_pw') == '1');
    
    if($save_pw)
    {
      $old_pw = sha1(trim($_POST['old_pw']));
      $new_pw = sha1(trim($_POST['new_pw1']));
      $new_pw_check = sha1(trim($_POST['new_pw2']));
      
      if($old_pw !== $usr->password)
      {
        handle_error('Das eingegebene alte Passwort ist falsch.');
      }
      
      if($new_pw !== $new_pw_check)
      {
        handle_error('Du hast dich bei der Eingabe des neuen Passwortes vertippt.');
      }
      
      if(strlen(trim($_POST['new_pw1'])) < 8)
      {
        handle_error('Das neue Passwort muss mindestens 8 Zeichen haben!');
      }
      
      if(!$error_occured)
      {
        $usr->set_val('password', $new_pw);
        setcookie(SAVE_LOGIN_COOKIE_NAME, $usr->name . '|' . sha1($usr->name . '<-|%|->' . $new_pw), time() + 86400 * 32, FORUM_BASEDIR.'/', $_SERVER['HTTP_HOST']);
      }
      
    }
    
    $editable_profile_rows = array(
      'residence' => array('type' => 'text', 'maxlen' => 32, 'caption' => 'Wohnort'),
      'jabber_id' => array('type' => 'email', 'maxlen' => 48, 'caption' => 'Jabber-ID', 'help' => 'siehe <a href="' . wiki_url('Jabber') . '">Jabber</a> im HTN-Wiki'),
      //'cluster' => array('type' => 'text', 'maxlen' => 16, 'caption' => 'Dein Cluster-Code'),
      'email' => array('type' => 'email', 'maxlen' => 32, 'caption' => 'Email-Adresse', 'needed' => 1),
      'email_public' => array('type' => 'bool', 'caption' => 'Email-Adresse', 'caption2' => 'Email-Adresse für andere Mitglieder sichtbar'),
      'invisible' => array('type' => 'bool', 'caption' => 'Online-Status', 'caption2' => 'Online-Status verstecken'),
      'scroll_menu' => array('type' => 'bool', 'caption' => 'Menü', 'caption2' => 'Menü mitscrollen lassen'),
    );
    
    $customtitle_allowed = (db_get_first_row('SELECT COUNT(*) FROM groups WHERE
      id IN (SELECT group_id FROM user_groups WHERE user_id = ' . $usr->id . ') AND allow_customtitle = 1') > 0);
    
    if($customtitle_allowed)
    {
      $editable_profile_rows['customtitle'] = array('type' => 'text', 'maxlen' => 50, 'caption' => 'Rang-Titel', 'blacklist' => array('admin', 'mode'));
    }
    
    $save_profile = (getifset($_GET, 'save_profile') == '1');
    
    if($save_profile)
    {
      
      foreach($editable_profile_rows as $name => $dat)
      {
        $new_val = trim(getifset($_POST, $name));
        switch($dat['type'])
        {
          case 'email':
          if(!empty($new_val) || getifset($dat, 'needed', 0) == 1)
          {
            if(!check_string($new_val, 'email'))
            {
              handle_error('Bitte ins Feld ' . $dat['caption'] . ' eine gültige Email-Adresse eintragen.');
            }
          }
          break;
          case 'bool':
          $new_val = ($new_val == '1' ? '1' : '0');
          break;
          case 'text':
          if(isset($dat['blacklist']))
          {
            foreach($dat['blacklist'] as $str)
            {
              if(stripos($new_val, $str) !== false)
              {
                handle_error('Im Feld ' . $dat['caption'] . ' befindet sich ein ungültiger Ausdruck.');
              }
            }
          }
          break;
        }
        
        if(!$error_occured)
        {
          $usr->$name = $new_val;
        }
      }
      
      if(!$error_occured)
      {
        $usr->save();
      }
      
    }
    
		$save_avatar = (getifset($_GET, 'save_avatar') == '1');
    
    if($save_avatar)
    {
      
    	#if($_FILES['new_ava']['error'] == 4)
    	if(empty($_FILES['new_ava']) || $_FILES['new_ava']['error'] == 4)
    	{
        #if(empty($_POST['usrimg_url']))
        #{
          if($usr->avatar)
            unlink('media/avatars/' . $usr->avatar);
  
          $usr->set_val('avatar', '');
        /*}
        else
        {
          $url = trim($_POST['usrimg_url']);
          if(!preg_match('~^http://25-hackthe\.net/_htn\.php/usrimg/show/\d/(\d+|\w+)/info\.png$~i', $url, $match))
          {
            handle_error('Die Benutzerinfobild-URL ist nicht gültig.');
          }
          else
          {
            $usr->set_val('avatar', $url);
          }
        }*/
    	}
    	else
    	{
    		$imagetypes 		 = array('GIF', 'JPG', 'PNG', 'SWF', 'PSD', 'BMP', 'TIFF', 'TIFF', 'JPC', 'JP2', 'JPX', 'JB2', 'SWC', 'IFF', 'WBMP', 'XBM');
    		$imageattributes = getimagesize($_FILES['new_ava']['tmp_name']);
        
        /* Returns an array with 4 elements.
          Index 0 contains the width of the image in pixels.
          Index 1 contains the height.
          Index 2 is a flag indicating the type of the image:
          1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(intel byte order),
          8 = TIFF(motorola byte order), 9 = JPC, 10 = JP2,
          11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF, 15 = WBMP, 16 = XBM.
        */
        
    		if(!in_array($imagetypes[($imageattributes[2]-1)], $AVA_ALLOWED_MIME))
    		{
    			handle_error( 'Nicht erlaubter Dateityp hochgeladen!' );
    		}
        
    		elseif($_FILES['new_ava']['size'] > AVA_MAX_SIZE)
    		{
    			handle_error( 'Der Avatar hat eine zu große Dateigröße!' );
    		}
    		
    		elseif($imageattributes[0] > AVA_MAX_WIDTH || $imageattributes[1] > AVA_MAX_HEIGHT)
    		{
    			handle_error( 'Avatarbilder dürfen höchstens ' . AVA_MAX_WIDTH . 'x' . AVA_MAX_HEIGHT . ' Pixel groß sein!' );
    		}
    		
    		else
    		{
    			if($usr->avatar)
    				unlink('media/avatars/' . $usr->avatar);
          
    			$ava_filename = $usr->id . strrchr($_FILES['new_ava']['name'], '.');
          
    			move_uploaded_file($_FILES['new_ava']['tmp_name'], 'media/avatars/' . $ava_filename);
    		
    			$usr->set_val('avatar', $ava_filename);
    		}
    	}
    	
    }
    
    include(SHOW_TEMPLATE_FILE);
  }
  
  
  /**
   * 
   **/
  public function lostpassword()
  {
    global $user_logged_in, $error_occured, $error_msg, $ignore_errors;
    
    $nick = trim(getifset($_POST, 'nick'));
    $email = trim(getifset($_POST, 'email'));
    
    if(getifset($_GET, 'sent') == 1)
    {
      $user = user::load($nick, 'name');
      
      if(!$user)
      {
        handle_error('Einen Account mit diesem Nickname gibt es nicht.');
      }
      elseif($user->email != $email)
      {
        handle_error('Die eingegebene Email-Adresse stimmt nicht mit der zu diesem Account gespeicherten überein.');
      }
      
      if(!$error_occured)
      {
        include 'core/initial_password_generator.php';

        $new_pw = generate_initial_password();
        
        $ignore_errors = true;
        $success = @mail($user->email, BOARD_TITLE . ': Neues Passwort', 'Hallo ' . $user->name . ',' . LF . LF .
          'Du hast ein neues Passwort angefordert. Hier kommt es:' . LF . LF . 'Benutzername: ' . $user->name . LF .
          'Passwort: ' . $new_pw . LF, FROM_MAIL_HEADER);
        $ignore_errors = false;
        
        if($success)
        {
          $user->set_val('password', sha1($new_pw));
          message_die('Rufe jetzt deine Emails ab, dort findest du dein neues Passwort.', 'info', 'Information');
        }
        else
        {
          message_die('Die eMail konnte nicht verschickt werden, da ein Fehler auftrat!');
        }
        
      }
    }
    
    include(SHOW_TEMPLATE_FILE);
  }
  
  /**
   * 
   **/
  public function info()
  {
    global $usr;
    $id = (int)$_GET['user_id'];
    
    $user = user::load($id);
    if(!$user) message_die('Diesen Benutzer gibt es nicht.');
    
    
    include(SHOW_TEMPLATE_FILE);
  }
  
  
  /**
   * 
   **/
  public function mlist()
  {
    global $user_logged_in, $db;
    
    if(!$user_logged_in) exit;
    
    $members_per_page = 50;
    
    $start = 0;
    $page = getifset($_GET, 'page', 1);
    
    $total_pages = ceil(db_get_first_row('SELECT COUNT(*) FROM users') / $members_per_page);
    
    if($page < 1 || $page > $total_pages) $page = 1;
    
    $start = ($page - 1) * $members_per_page;
    
    $r_members = db_query('SELECT id, name, registered_time, is_admin, (SELECT rank_name FROM groups WHERE id = (SELECT group_id FROM user_groups WHERE user_id = users.id LIMIT 1) LIMIT 1) AS rank_title FROM users ORDER BY registered_time ASC LIMIT ' . $start . ', ' . $members_per_page);
    
    include(SHOW_TEMPLATE_FILE);
  }
  
}


?>
