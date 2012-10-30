<?php
/*
 * Name:	account
 * URL:		http:/openclassifieds.com/
 * Version:	v0.1
 * Date:	04/27/2010
 * Author:	Arnaldo Hidalgo
 * Support: http:/openclassifieds.com/forums/
 * License: GPL v3
 * Notes:	account class
 */

/////////////////////class account

class Account {
    public $id = null;
    public $name = null;
	public $email = null;
    public $location = null;
    public $active = 0;
    public $exists = false;
    public $status_password = false;

    //constructor   
    function __construct($email){
        if ($email != "")
        {
            $ocdb=phpMyDB::GetInstance();
    
    		$query = "SELECT idAccount, name, idLocation, active FROM ".TABLE_PREFIX."accounts
    		WHERE
    		email = '".$email."'
    		LIMIT 1";
    
    		$result=$ocdb->query($query);
            
    		if (mysql_num_rows($result))
            {
    		    $this->email = $email;
                
    			$row=mysql_fetch_assoc($result);
                
    			$this->id = $row['idAccount'];
                $this->name = $row['name'];
                $this->location = $row['idLocation'];
                $this->active = $row['active'];
                
                $this->exists = true;
            } else $this->exists = false;
        } else $this->exists = false;
    }
    
    public static function createById($id){ //construct by id
        $account = new Account("");
        
        if (is_numeric($id))
        {
            $ocdb=phpMyDB::GetInstance();
            
            $query = "SELECT idAccount,name,email,idLocation,active FROM ".TABLE_PREFIX."accounts
    		WHERE
    		idAccount = ".$id."
    		LIMIT 1";
    
    		$result=$ocdb->query($query);
            
    		if (mysql_num_rows($result))
            {               
    			$row=mysql_fetch_assoc($result);
            
    			$account->id = $id;
                $account->name = $row['name'];                
                $account->email = $row["email"];
                $account->location = $row['idLocation'];
                $account->active = $row['active'];
                $account->exists = true;
                
            } else $account->exists = false;
            
        } else $account->exists = false;
        
        return $account;
    }
    
    public static function createBySession(){ //construct by session
        $account = new Account("");
        
        $id = $_SESSION["ocAccount"];
        if (is_numeric($id))
        {
            $ocdb=phpMyDB::GetInstance();
            
            $query = "SELECT idAccount,name,email,idLocation,active FROM ".TABLE_PREFIX."accounts
    		WHERE
    		idAccount = ".$id."
    		LIMIT 1";
    
    		$result=$ocdb->query($query);
            
    		if (mysql_num_rows($result))
            {               
    			$row=mysql_fetch_assoc($result);
            
    			$account->id = $id;
                $account->name = $row['name'];                
                $account->email = $row["email"];
                $account->location = $row['idLocation'];
                $account->active = $row['active'];
                $account->exists = true;
                
            } else $account->exists = false;
            
        } else $account->exists = false;
        
        return $account;
    }
    
    //Register new account
	public function Register($name,$email,$password)
	{
	   if (!$this->exists)
       {
    		$ocdb=phpMyDB::GetInstance();
    		
            $token = $this->generateActivationToken();
            
            $ocdb->insert(TABLE_PREFIX."accounts (name,email,password,activationToken)","'$name','$email','$password','$token'");

            $this->id = $ocdb->getLastID();
            $this->name = $name;
            $this->email = $email;
            $this->active = 0;
            $this->exists = true;
            
            return true;
            
        } else return false;
	}
    
    //Activate account by token
	public function Activate($token)
	{
	    if ($this->exists)
        {
    		$ocdb=phpMyDB::GetInstance();
    		
    		$query = "SELECT idAccount FROM ".TABLE_PREFIX."accounts
    		WHERE
    		(activationToken = '".$token."') AND (idAccount = ".$this->id.")";
    
    		$result=$ocdb->query($query);
    		if (mysql_num_rows($result))
            {            
                $query = "UPDATE ".TABLE_PREFIX."accounts
    			SET active = 1
    			WHERE
    			idAccount = ".$this->id."";
    	
    	        $ocdb->query($query);
                
                return true;
            } else return false;
        } else return false;
    }
    
    //Logon
	function logOn($password,$remember=false,$rememberCookie="")
    {
        $ocdb=phpMyDB::GetInstance();
        
		$query = "SELECT password, active FROM ".TABLE_PREFIX."accounts
		WHERE
		email = '".$this->email."'
		LIMIT 1";

		$result=$ocdb->query($query);
		if (mysql_num_rows($result))
        {            
			$row=mysql_fetch_assoc($result);
            
            $this->exists = true;
            
            if ($row["password"]==$password)
            {
                $this->status_password = true;
                
                if ($row["active"]==1)
                {
                    $_SESSION["ocAccount"] = $this->id;
                    if ($remember)
                    {
                        if ($rememberCookie!="")
                        {
                            $expire=time()+60*60*24*30;
                            setcookie($rememberCookie, $this->email, $expire);
                        }
                    } else if ($rememberCookie!="") setcookie($rememberCookie, "", time()-3600);
                    
                    $this->active = 1;
                    
                    //update lastSigninDate
            		$query = "UPDATE ".TABLE_PREFIX."accounts
            			    SET
            				lastSigninDate = CURRENT_TIMESTAMP()
            				WHERE
            				idAccount = ".$this->id."";
            		$ocdb->query($query);
                    
                    return true;
                } else {
                    $this->active = 0;
                    return false;
                }
            } else {
                $this->status_password = false;
                return false;
            }
        } else {
            $this->exists = false;
            return false;
        }
    }
    
	//Logout
	public static function logOut()
    {
		if(isset($_SESSION["ocAccount"]))
        {
			$_SESSION["ocAccount"] = null;
			
			unset($_SESSION["ocAccount"]);
		}
	}

    //Return account's activation token
	public function token()
	{
		$ocdb=phpMyDB::GetInstance();
		
		$query = "SELECT
				activationToken
				FROM
				".TABLE_PREFIX."accounts
				WHERE
				idAccount = ".$this->id."";
		       
        $result=$ocdb->query($query);
        
		if (mysql_num_rows($result))
        {
			$row=mysql_fetch_assoc($result);
            $token = $row['activationToken'];
            
			return $token;
        } else return null;
	}
    
	//Return the timestamp when the account was registered
	public function signupTimeStamp()
	{
		$ocdb=phpMyDB::GetInstance();
		
		$query = "SELECT
				createdDate
				FROM
				".TABLE_PREFIX."accounts
				WHERE
				idAccount = ".$this->id."";
		
        $result=$ocdb->query($query);
        
		if (mysql_num_rows($result))
        {
			$row=mysql_fetch_assoc($result);
	
			return ($row['createdDate']);
        } else return null;
	}
	
    //Return account's passsword
	public function password()
	{
		$ocdb=phpMyDB::GetInstance();
		
		$query = "SELECT
				password
				FROM
				".TABLE_PREFIX."accounts
				WHERE
				idAccount = ".$this->id."";
		
        $result=$ocdb->query($query);
        
		if (mysql_num_rows($result))
        {
			$row=mysql_fetch_assoc($result);
	
			return ($row['password']);
        } else return null;
	}
    
	//Update an account's email
	public function updateName($name)
	{
		$ocdb=phpMyDB::GetInstance();
		
		$query = "UPDATE ".TABLE_PREFIX."accounts
				SET name = '".$name."'
                ,lastModifiedDate = CURRENT_TIMESTAMP()
				WHERE
				idAccount = ".$this->id."";
		
        $this->name = $name;
        
		return $ocdb->query($query);
	}
	
    //Update an account's password
	public function updatePassword($password)
	{
		$ocdb=phpMyDB::GetInstance();

		$query = "UPDATE ".TABLE_PREFIX."accounts
		       SET
			   password = '".$password."' 
               ,lastModifiedDate = CURRENT_TIMESTAMP()
			   WHERE
			   idAccount = ".$this->id."";
               
        return $ocdb->query($query);
	}
    
    //Helper functions
    
	//Function lostpass var if set will check for an active account.
	private function validateActivationToken($token,$lostpass=null)
	{
		$ocdb=phpMyDB::GetInstance();
		
		if($lostpass == null) 
		{	
			$query = "SELECT activationToken
					FROM ".TABLE_PREFIX."accounts
					WHERE active = 0
					AND
					activationToken ='".trim($token)."'
					LIMIT 1";
		} else {
			 $query = "SELECT activationToken
			 		FROM ".TABLE_PREFIX."accounts
					WHERE active = 1
					AND
					activationToken ='".trim($token)."' 
					LIMIT 1";
		}
		
		$result=$ocdb->query($query);
        
		if (mysql_num_rows($result)) return true;
		else return false;
	}
    
    //Generate an activation key 
	private function generateActivationToken()
	{
		$gen;
	
		do
		{
			$gen = md5(uniqid(mt_rand(), false));
		}
		while($this->validateActivationToken($gen));
	
		return $gen;
	}
}

?>