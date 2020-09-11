<?php


class Dbpdo {
	
	private $uid=dbuid; 		#Benutzename
	private $pw=dbPw;			#Password
	private $serv=dbServ;		#Datenbank Server
	private $dbname=dbname;		#Datenbank Name
	
	private $db_obj = null;  			#Datenbank Objekt PDO
	public $error;						#Fehler Varibale
	
	public function __construct() {

		$db="mysql:dbname=".$this->dbname.";host=".$this->serv;  #mysql:dbname=Datenbankname;host=localhost
		
		try{
			 $this->db_obj = new PDO($db, $this->uid, $this->pw);
		}
		catch (PDOException $e)
        {
            $this->error='Fehler beim Öffnen der Datenbank: ' . $e->getMessage();
            echo $this->error; #Fehler ausgabe
        } 
			
	 }// Ende construct
	
	public function get_db()
    {
         return $this->db_obj;
    }  
	
	
	
}