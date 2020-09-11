<?php

/**
 * admincp.php 
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 * @version     0.0.0.1
 **/

if(!defined('IN_BB')) die('Hacking attempt!');

/**
 * admincp module data
 **/
class htnbb_module_admincp extends i_htnbb_module
{
  
  public function __construct()
  {
    $this->m_id = 'admincp';
  }
  
  public function &set_page($pageid)
  {
    switch($pageid)
    {
    case 'overview':         $this->current_page = new htn_module_page($this, $pageid, 'Administration', true); break;
    case 'grouppermissions': $this->current_page = new htn_module_page($this, $pageid, 'Gruppen-Rechte', true); break;
    case 'groups':           $this->current_page = new htn_module_page($this, $pageid, 'Gruppen-Verwaltung', true); break;
    case 'upstrimg':         $this->current_page = new htn_module_page($this, $pageid, '', true); break;
    default: $this->current_page = false;
    }
    return $this->current_page;
  }
  
  /**
   * 
   **/
  public function overview()
  {
    global $usr;
    
    
    
    include(SHOW_TEMPLATE_FILE);
    
  }
  
  /**
   * 
   **/
  public function upstrimg()
  {
    $str = trim($_GET['str']);
    
    $h = imagefontheight(3) * strlen($str) + 30;
    $w = 6 + imagefontwidth(3);
    
    $img = imagecreate($w, $h / 2);
    
    $bg = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $bg);
    
    $font_clr = imagecolorallocate($img, 0, 0, 0);
    
    imagestringup($img, 3, 0, $h / 2 - 5, $str, $font_clr);
    
    header('Content-Type: image/png');
    imagepng($img);
  }
  
  /**
   * 
   **/
  public function groups()
  {
    global $usr;
    
    $r_groups = db_query('SELECT id, name, hidden, undeletable, rank_icon, rank_name FROM groups');
    
    if(getifset($_GET, 'save') == 1)
    {
      while($group = $r_groups->fetch_object())
      {
        $name = trim($_POST['g_' . $group->id . '_name']);
        $rank_name = trim($_POST['g_' . $group->id . '_rank_name']);
        $rank_icon = trim($_POST['g_' . $group->id . '_rank_icon']);
        $hidden = (getifset($_POST, 'g_' . $group->id . '_hidden') == '1' ? '1' : '0');
        $delete = (getifset($_POST, 'g_' . $group->id . '_delete') == '1' ? '1' : '0'); // Noch keine Funktion
        
        if(!empty($name))
        {
          db_query('UPDATE groups SET name=\'' . prepare_string_for_query($name) . '\', 
            rank_name=\'' . prepare_string_for_query($rank_name) . '\', rank_icon=\'' . prepare_string_for_query($rank_icon) . '\', 
            hidden=\'' . $hidden . '\' WHERE id = ' . $group->id . ' LIMIT 1');
        }
      }
      #$r_groups->data_seek(0);
      bb_redir('admincp', 'groups');
    }
    
    include(SHOW_TEMPLATE_FILE);
    
  }
  
  /**
   * 
   **/
  public function grouppermissions()
  {
    global $usr, $_permission_actions;
    
    $group_id = (int)$_GET['group'];
    
    $group = db_query_fetch('SELECT id, name FROM groups WHERE id = ' . $group_id . ' LIMIT 1');
    if(!$group) exit;
    
    $r_forums = db_query('SELECT id, name FROM forums ORDER BY name ASC');
    
    $r_permissions = db_query('SELECT id, forum_id, action FROM group_permissions WHERE group_id = ' . $group_id);
    
    $perms = array();
    
    while($forum = $r_forums->fetch_object())
    {
      foreach($_permission_actions as $action)
      {
        $perms[$forum->id][$action] = 0;
      }
    }
    $r_forums->data_seek(0);
    
    while($perm = $r_permissions->fetch_object())
    {
      $perms[$perm->forum_id][$perm->action] = 1;
    }
    
    if(getifset($_GET, 'save') == 1)
    {
      foreach($perms as $forum_id => $forum_perms)
      {
        foreach($forum_perms as $action => $old_val)
        {
          if(empty($action)) continue;
          $new_val = (int)getifset($_POST, 'p_' . $forum_id . '_' . $action, '0');
          if($new_val != 0) $new_val = 1;
          $old_val = (int)$old_val;
          
          if($new_val == 1 && $old_val == 0)
          {
            db_query('INSERT INTO group_permissions SET group_id = ' . $group_id . ', action = \'' . $action . '\', forum_id = ' . $forum_id);
          }
          elseif($new_val == 0 && $old_val == 1)
          {
            db_query('DELETE FROM group_permissions WHERE group_id = ' . $group_id . ' AND action = \'' . $action . '\' AND forum_id = ' . $forum_id . ' LIMIT 1');
          }
          
        }
      }
      bb_redir('admincp', 'grouppermissions', 'rnd=' . random_string(16) . '&group=' . $group_id);
      exit;
    }
    
    include(SHOW_TEMPLATE_FILE);
    
  }
  
}


?>