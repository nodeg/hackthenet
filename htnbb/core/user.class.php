<?php
/**
 * core/user.class.php.
 * Die User-Klasse!
 * @author      Ingmar
 * @copyright   HackTheNet-Team (www.hackthenet.org)
 **/

if(!defined('IN_BB')) die('Hacking attempt!');

final class user
{
  private $data = array();
  private $changed_data_elements = array();
  
  protected function __construct($data)
  {
    $this->data = $data;
  }
  
  public static function load($val, $by = 'id')
  {
    $r = db_query('SELECT * FROM users WHERE ' . prepare_string_for_query($by) . ' = \'' . prepare_string_for_query($val) . '\' LIMIT 1');
    
    if($r->num_rows != 1)
    {
      return false;
    }
    
    return new user($r->fetch_assoc());
    
    $r->free();
  }
  
  public function __get($property)
  {
    if(isset($this->data[$property]))
    {
      return $this->data[$property];
    }
    
    switch($property)
    {
      
      default:
      trigger_error('trying to read unknown property (user::' . $property . ')');
    }
  }
  
  public function __set($property, $new_value)
  {
    if(isset($this->data[$property]))
    {
      $this->data[$property] = $new_value;
      $this->changed_data_elements[$property] = 1;
      return true;
    }
    
    switch($property)
    {
      
      default:
      trigger_error('trying to write unknown property (user::' . $property . ')');
    }
  }
  
  public function set_val($key, $val = NULL, $force = false, $add = false)
  {
    if(!isset($this->data[$key])) return false;
    
    if($val === NULL) $val = $this->$key;
    
    $tmp = ($add ? $this->$key + $val : $val);
    if($force == false && $this->$key === $tmp) return;
    
    $this->$key = $tmp;
    
    db_query('UPDATE users SET ' . prepare_string_for_query($key) . ' = \'' . prepare_string_for_query($val) . '\' '.
      'WHERE id=' . (int)$this->id . ' LIMIT 1');
  }
  
  public function save()
  {
    $sql = 'UPDATE users SET ';
    
    foreach($this->data as $key => $val)
    {
      if(!isset($this->changed_data_elements[$key])) continue;
      $sql .= '`' . $key . '` = \'' . prepare_string_for_query($val) . '\', ';
    }
    
    $sql = trim($sql, ' ,');
    
    db_query($sql . ' WHERE id = ' . (int)$this->id . ' LIMIT 1');
  }
  
}

?>