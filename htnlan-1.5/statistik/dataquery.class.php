<?
class dataquery{

	var $tmp = "";
	var $_sql = "";		// set querystring 
	var $_result = 0;	// set result
	var $_errno = 0;	// set errornumber
	var $_error = "";	// set errormessage

	function dataquery($sql)
	{
		$this->_sql = trim($sql);			
		$this->_result = mysql_query($this->_sql);	// make query
		if(!$this->_result)				
		{            
			$this->_errno = mysql_errno();		
			$this->_error = mysql_error();
		} 
	}
	
	function error()
	{
		$tmp = $this->_result;
		// Variable in boolean umwandeln
		$tmp = (bool)$tmp;
		// Variable invertieren
		$tmp = !$tmp;   
		// und zurückgeben
		return $tmp;
	}
	
	function getError()
	{
        	if($this->error()) {
			$str  = "Anfrage:\n".$this->_sql."\n";
			$str .= "Antwort:\n".$this->_error."\n";
			$str .= "Fehlercode: ".$this->_errno;
		} else {
			$str = "Kein Fehler aufgetreten.";
	        }
		return $str;
	}
	
	function fetch()
	{
		if($this->error()) {
			echo "Es trat ein Fehler auf. Bitte überprüfen sie ihr\n";
			echo "MySQL-Query.\n";
			$return = null;
		} else {
			$return = mysql_fetch_assoc($this->_result);        
		}
		return $return;
	}
	
	function numRows()
	{
		if($this->error()) {
			$return = -1;
	        } else {
			$return = mysql_num_rows($this->_result);
		}
		return $return;
	}
	
	function free()
	{
		mysql_free_result($this->_result);	// free ressources	
	}
}



?>