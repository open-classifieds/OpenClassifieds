<?php

/*
 * Name:	phpMyDB
 * URL:		http://neo22s.com/phpmydb/
 * Version:	v1.3
 * Date:	12/07/2012
 * Author:	Chema Garrido
 * License: GPL v3
 * Notes:	Mysql Object with cache integrated
 */

class phpMyDB {//requires wrapper cache class to use caching
	private $dbh;//data base handler
	public $query_counter=0;//count queries
	private $query_cache_status=false; //cache deactivated by default
	public $query_cache_counter=0;//count cached queries
	private $insert_last_id;//last insert ID for mysql_insert_id()
    private static $instance;//Instance of this class
    
	    // Always returns only one instance
	    public static function GetInstance($dbuser='', $dbpass='', $dbname='', $dbhost='',$dbcharset='utf8',$dbtimezone='',$dbconnectiontype='default'){
	        if (!isset(self::$instance)){//doesn't exists the isntance
	        	 self::$instance = new self($dbuser, $dbpass, $dbname, $dbhost,$dbcharset,$dbtimezone,$dbconnectiontype);//goes to the constructor
	        }
	        return self::$instance;
	    }
	    
	 	// Prevent users to clone the instance
	    public function __clone(){
	       $this->print_error('Clone is not allowed.');
	    }
	    
		// DB Constructor - connects to the server and selects a database
		private function __construct($dbuser, $dbpass, $dbname, $dbhost,$dbcharset,$dbtimezone,$dbconnectiontype){
			if ($dbconnectiontype=='persistent') $this->dbh = @mysql_pconnect($dbhost,$dbuser,$dbpass);
			else $this->dbh = @mysql_connect($dbhost,$dbuser,$dbpass);
			
			if (!$this->dbh){
				$this->print_error('<ol><li><b>Error establishing a database connection!</b>
									<li>Are you sure you have the correct user/password?
									<li>Are you sure that you have typed the correct hostname?
									<li>Are you sure that the database server is running?</ol>');
			}
			$this->selectDB($dbname);
			$this->query('SET NAMES '.$dbcharset);
			if (!empty($dbtimezone))$this->query('SET time_zone =  \''.$dbtimezone.'\'');
		}
		
		public function __destruct() {
		    $this->closeDB();
		}
		
		// Select a DB (if another one needs to be selected)
		public function selectDB($db){
			if ( !@mysql_select_db($db,$this->dbh)){
				$this->print_error('<ol><li><b>Error selecting database <u>'.$db.'</u>!
									</b><li>Are you sure it exists?
									<li>Are you sure there is a valid database connection?</ol>');
			}
		}
		
		// Closes DB connection
		public function closeDB(){
			if (isset($this->dbh)){
				mysql_close();
				unset($this->dbh);
			}
			log::add();
		}
		
		// Normal query
		public function query($query) {
			log::add($query);
			$this->query_counter++;
			$return_val=@ mysql_query($query) or $this->print_error('('.mysql_errno().') in line '.__LINE__.' error:'.mysql_error().' 
																	<br/>Query: '. $query.' <br/>File: '. $_SERVER['PHP_SELF'] );
			return $return_val;
		}
		
		///Select functions
		
		//normal select
		public function select($fields, $from, $where='') {  
			log::add('Function select');
			if (!empty($where)) $where = ' WHERE ' . $where;   
            $query = 'SELECT ' . $fields . ' FROM `' . $from . '`'. $where;  
            $result = $this->query($query);  
            return $result;  
        }  
        
        //insert into
		public function insert($into, $values) {  
			log::add('Function insert');
			$query = 'INSERT INTO ';
			
			if (is_array($values)){
				$fields='';
				$valuesf='';				
				foreach ($values as $f => $v){
					$fields.=$f.',';
					$valuesf.='\''.$v.'\',';
				}
				$query .=  $into .' ('.substr($fields,0,-1). ') VALUES(' . substr($valuesf,0,-1) . ')';  
			}
            else $query .= $into . ' VALUES(' . $values . ')';        
            
            if($this->query($query)) {
            	$this->setLastID(mysql_insert_id(),$this->dbh);
            	return $this->getLastID();//true;  //succed
            }
            else  return false;  //not succed    
        } 
        
        //delete from
        public function delete($from, $where='') {  
        	log::add('Function delete');
        	if (!empty($where)) $where = ' WHERE ' . $where;   
            $query = 'DELETE FROM ' . $from . $where;  
            if($this->query($query))   return true;  //succed
            else  return false;  //not succed        
         } 
         
        //update, aware! $value= column='test', name='test2' .....
        public function update($table,$values, $where='') {  
        	log::add('Function update');
        	if (is_array($values)){
				$valuesf='';				
				foreach ($values as $f => $v) $valuesf.= $f.'=\''.$v.'\',';
				$values = substr($valuesf,0,-1);  
			}
        	if (!empty($where)) $where = ' WHERE ' . $where;   
            $query = 'UPDATE '. $table. ' SET '.$values. $where;
            if($this->query($query))   return true;  //succed
            else  return false;  //not succed        
        } 
		
        /**
         * From a given query returns an array,  uses cache if enabled
         * @param string $query
         * @param string $type
         * @return array results
         */
		public function getRows($query,$type='assoc')
		{
			log::add('getRows | type: '. $type);	
			//get values from cache if enabled
			$values = ($this->query_cache_status)? Cache::cache(md5($query)):NULL;
			
			if ($values==NULL) //not value from cache found
			{
				$result=$this->query($query);
				if (mysql_num_rows($result)>0)
				{
					$values=array();
    				if ($type=='object')//@todo check this//if type is object and the cache is activated we use assoc since object can't be cached
    				{
    					$type='assoc';
    					log::add('Fetch mode changed to object, if cache is activated not possible to use.');
    				}
					switch ($type)
					{
						case 'assoc':
							while($row = mysql_fetch_assoc($result))  array_push($values, $row);  
						break;
						case  'row':
							while($row = mysql_fetch_row($result))	   array_push($values, $row);  
						break;
						case 'object':
							while($row = mysql_fetch_object($result))  array_push($values, $row);  
						break;
						case 'value':
							$row     = mysql_fetch_row($result);
							$values  = $row[0];//return value
						break;	
						default:
							$this->sql_error('Not recognized fetch mode: '.$type);
						break;
					} 
					
					if ($this->query_cache_status)//save cache
					{
						Cache::cache(md5($query), $values);
						//log::add('getRows | values saved in cache');
					}
				}
				else
				{
				    $values=NULL;
				    //log::add('getRows | query wth 0 rows');
				}
				mysql_free_result($result);//freeing memory
			}
			else//found in cache
			{
				$this->query_cache_counter++;
				//log::add('getRows | retrieved from cache query:'. $query);
			}
			return $values;
		}
		
		// return the 1st value from a field of a query
		public function getValue($query){
			return $this->getRows($query,'value');
		}
		
		private function setLastID($id){
			if (is_numeric($id)) $this->insert_last_id=$id;
			else $this->insert_last_id=false;//will return false in case can't retrieve last id
		}
		public function getLastID(){
			return $this->insert_last_id;
		}
		/////////////Tool functions
		
		//sets cache active or inactive
		public function setCache($state){		
			$this->query_cache_status = ($state===TRUE)?TRUE:FALSE;	
		}
		
		
		// Print SQL/DB error.
		private function print_error($str = ''){
			if ( empty($str) ) $str = mysql_error();
			// If there is an error then take note of it
			ocSqlError('<b>phpMyDB Error</b> <br />'.$str);
		}
		
		public function getQueryCounter($type='queries'){
			switch($type){
				case 'queries':
					return $this->query_counter;
				break;
				case 'cache':
					return $this->query_cache_counter;
				break;
			}			
		}
		
	
}

////////////////////////////////////////////////////////////
//@todo
function ocSqlError($ERROR){
    define("SQLMESSAGE", $ERROR);
    trigger_error("(SQL)", E_USER_ERROR);
}
?>
