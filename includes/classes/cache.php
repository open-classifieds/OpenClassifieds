<?php
/**
 * Cache wrapper class for filecache, memcache, APC, Xcache and eaccelerator
 *
 * @package     Open Classifieds
 * @subpackage  Core
 * @category    Cache
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 * 
 */

class Cache {
	private $cache_params;//extra params for external caches like path or connection option memcached
	private $cache_expire;//seconds that the cache expires
	private $cache_type;//type of cache to use
	private $cache_external; //external instance of cache, can be fileCache or memcache
	private static $instance;//Instance of this class
	    
    /**
     * Always returns an instance
     * @param $type of cache to be loaded
     * @param $exp_time when expires the cache
     * @param $params extra params to load the cache
     */
    public static function get_instance($type='filecache',$exp_time=3600,$params='cache/')
    {
        if (!isset(self::$instance))//doesn't exists the isntance
        {
        	 self::$instance = new self($type,$exp_time,$params);//goes to the constructor
        }
        return self::$instance;
    }
    
	/**
	 * cache constructor, optional expiring time and cache path
	 * @param $type
	 * @param $exp_time
	 * @param $params
	 */
	private function __construct($type,$exp_time,$params)
	{
		$this->cache_expire=$exp_time;
		$this->cache_params=$params;
		$this->set_cache_type($type);
		log::add('cache::construct | '.$type.'-->'.$this->cache_type.' | '.$exp_time.' | '.$params);
	}
	
	public function __destruct() 
	{
		unset($this->cache_external);
	}
	
	/**
	 * Prevent users to clone the instance
	 */ 
    public function __clone(){
        $this->cache_error('Clone is not allowed.');
    }
	
	/**
	 * deletes all the cache 
	 * @return cache specific return for action
	 */
	public function clear()
	{
	    log::add('cache::clear | '.$this->cache_type);
		switch($this->cache_type)
		{
			case 'eaccelerator':
		    	@eaccelerator_clean();
                return @eaccelerator_clear();
	        break;

		    case 'apc':
		    	return apc_clear_cache('user');
			break;

		    case 'xcache':
	    		return xcache_clear_cache(XC_TYPE_VAR, 0);
		   	break;

		    case 'memcache':
		    	return @$this->cache_external->flush();
	        break;
	        
	        case 'filecache':
		     	return $this->cache_external->deleteCache();
	        break;
		}	
	}
	
	/**
	 * writes or reads the cache
	 * @param $key to read
	 * @param $value to write
	 * @param $ttl time expire
	 * @return mixed
	 */	
    public static function cache($key, $value=NULL,$ttl=NULL)
	{
		if ($value!=NULL)//wants to write
		{
		    log::add('cache::cache | action write key: '.$key);
			if (empty($ttl))
			{
		    	$ttl=self::$instance->cache_expire;
			} 
			return self::$instance->put($key, $value,$ttl);
		}
		else//reading value
		{
		    log::add('cache::cache | action read key: '.$key);
		    return self::$instance->get($key);
		}
	}
	
	
	/**
	 * creates new key
	 * @param $key to store
	 * @param $data value to be writen
	 * @param $ttl expire time
	 * @return cache specific return for action
	 */
	private function put($key,$data,$ttl=NULL )
	{
	    log::add('cache::put | '.$key);
		if ($ttl==NULL) $ttl=$this->cache_expire;
		switch($this->cache_type)
		{
			case 'eaccelerator':
		    	return eaccelerator_put($key, serialize($data), $ttl);
	        break;

		    case 'apc':
		    	return apc_store($key, $data, $ttl);
			break;

		    case 'xcache':
	    		return xcache_set($key, serialize($data), $ttl);
		   	break;

		    case 'memcache':
                $data=serialize($data);
		    	if (!$this->cache_external->replace($key, $data, false, $ttl))//key exists just 'refresh' it
		    	{
		    	    return $this->cache_external->set($key, $data, false, $ttl);  //new key
		    	}
	        break;
	        
	        case 'filecache':
		     	return $this->cache_external->cache($key,$data);
	        break;
		}	
    }
    
	/**
	 * get cache for the given key
	 * @param $key
	 * @return value from $key
	 */
	private function get($key)
	{
		switch($this->cache_type)
		{
			case 'eaccelerator':
		    	$data =  @unserialize(eaccelerator_get($key));
	        break;

		    case 'apc':
		    	$data =  apc_fetch($key);
			break;

		    case 'xcache':
	    		$data =  @unserialize(xcache_get($key));
		   	break;

		    case 'memcache':
		    	$data = @unserialize($this->cache_external->get($key));
	        break;
	        
	        case 'filecache':
		     	$data = $this->cache_external->cache($key);
	        break;
		}	
		log::add('cache::get | '.$key);
		return $data;
 	}
 	
 	/**
 	 * delete key from cache 
 	 * @param $key
 	 * @return cache specific return for action
 	 */
 	public function delete($key)
 	{
 	    log::add('cache::delete | '.$key);
 	    switch($this->cache_type)
 	    {
			case 'eaccelerator':
		    	return eaccelerator_rm($key);
	        break;

		    case 'apc':
		    	return apc_delete($key);
			break;

		    case 'xcache':
	    		return xcache_unset($key);
		   	break;

		    case 'memcache':
		    	return $this->cache_external->delete($key);
	        break;
	        
	        case 'filecache':
		     	return $this->cache_external->delete($key);
	        break;
		}	
 	
 	}
    /*
    * Overloading for the Application variables and automatically cached
    */
 	public function __set($name, $value) 
 	{
 		$this->put($name, $value, $this->cache_expire);
    }

    public function __get($name) 
    {
        return $this->get($name);
    }

    public function __isset($key) //@todo check this carefully
    {
        return ($this->get($key)!==NULL)?TRUE:FALSE;
    }

    public function __unset($name) 
    {
        $this->delete($name);
    }
	//end overloads
	
    /**
     * get cache type
     * @return string cache type 
     */
	public function get_cache_type()
	{
	    return $this->cache_type;
	}	
	
	/**
	 * sets the cache if its installed if not triggers error
	 * @param $type of cache to load
	 * 
 	 */
	public function set_cache_type($type)
	{
	    $this->cache_type=strtolower($type);
		
		switch($this->cache_type)
		{
			case 'eaccelerator':
		    	if (function_exists('eaccelerator_get')) $this->cache_type = 'eaccelerator';
		    	else $this->cache_error('eaccelerator not found');  	
	        break;

		    case 'apc':
		    	if (function_exists('apc_fetch')) $this->cache_type = 'apc' ;
		    	else $this->cache_error('APC not found');  
			break;

		    case 'xcache':
	    		if (function_exists('xcache_get')) $this->cache_type = 'xcache' ;
	    		else $this->cache_error('Xcache not found'); 
		   	break;

		    case 'memcache':
		    	if (class_exists('Memcache')) $this->init_memcache();
		    	else $this->cache_error('memcache not found'); 
	        break;
	        
	        case 'filecache':
		     	if (class_exists('filecache'))$this->init_filecache();
		     	else $this->cache_error('fileCache not found'); 
	        break;
	        
	        case 'auto'://try to auto select a cache system
		    	/*if     (function_exists('eaccelerator_get'))$this->cache_type = 'eaccelerator';                                       
				elseif (function_exists('apc_fetch'))    	$this->cache_type = 'apc' ;                                     
				elseif (function_exists('xcache_get'))  	$this->cache_type = 'xcache' ;                                        
				elseif (class_exists('Memcache'))			$this->init_memcache();
				elseif (class_exists('fileCache'))			$this->init_filecache();
				else $this->cache_error('not any compatible cache was found');*/
	        break;
	        
	        default://not any cache selected or wrong one selected
	        	if (isset($type)) $msg='Unrecognized cache type selected <b>'.$type.'</b>';
	        	else $msg='Not any cache type selected';
	        	$this->cache_error($msg);  	
	        break;
		}
		log::add('cache::set_cache_type | '.$this->cache_type);
	}	
	
	/**
	 * get instance of the memcache class
	 * 
	 */
	private function init_memcache()
	{
    	if (is_array($this->cache_params))
    	{
    		$this->cache_type = 'memcache';
    		$this->cache_external = new Memcache;
    		foreach ($this->cache_params as $server) 
    		{
    			$server['port'] = isset($server['port']) ? (int) $server['port'] : ini_get('memcache.default_port'); 
            	$server['persistent'] = isset($server['persistent']) ? (bool) $server['persistent'] : true; 
    			$this->cache_external->addServer($server['host'], $server['port'], $server['persistent']);
    		}
    	}
    	else
    	{
    	    $this->cache_error('memcache needs an array, example: 
    				Cache::get_instance(\'memcache\',30,array(array(\'host\'=>\'localhost\')));');  
    	} 
    	log::add('cache::init_memcache');
    }
    
    /**
     * get instance of the filecache class
     * 
     */
 	private function init_filecache()
 	{
    	$this->cache_type = 'filecache';
    	$this->cache_external = fileCache::get_instance($this->cache_expire,$this->cache_params);
    	log::add('cache::init_filecache');
    }
    
    /**
     * returns the available cache
     * @param $return_format
     */
	public function get_available_cache($return_format='html')
	{
		$avCaches	= array();
		$avCaches[] = array('eaccelerator', function_exists('eaccelerator_get'));                                       
		$avCaches[] = array('apc',          function_exists('apc_fetch')) ;                                     
		$avCaches[] = array('xcache',       function_exists('xcache_get'));                                        
		$avCaches[] = array('memcache',     class_exists('Memcache'));
		$avCaches[] = array('fileCache',    class_exists('fileCache'));
		
		log::add('cache::get_available_cache | '.print_r($avCaches,1));
		
		if ($return_format=='html')
		{
			$ret='<ul>';
			foreach ($avCaches as $c)
			{
				$ret.='<li>'.$c[0].' - ';
				if ($c[1]) $ret.='Found/Compatible';
				else $ret.='Not Found/Incompatible';
				$ret.='</li>';
			}
			return $ret.'</ul>';
		}
		else 
		{
		    return $avCaches;
		}	
	}
	
	/**
	 * triggers an error
	 * @param $msg
	 */
    private function cache_error($msg)
    {
    	trigger_error('<br /><b>wrapperCache error</b>: '.$msg.
	        		'<br />Choose a supported cache from list:'.$this->get_available_cache(), E_USER_ERROR);
    }
}

/**
 * fileCache class, caches variables in standalone files if value is too long or uses unique file for small ones
 *
 * @package     php-cache
 * @subpackage  Addon
 * @category    Cache
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 * @version:	0.1
 * @date		19/08/2011
 */

class fileCache {
	private $cache_path;//path for the cache
	private $cache_expire;//seconds that the cache expires
	private $application=array();//application object like in ASP
 	private $application_file;//file for the application object
 	private $application_write=FALSE;//if application write is TRUE means there was changes and we need to write the app file
	private $start_time=0;//application start time
 	private $content_size=64;//this is the max size can be used in APP cache if bigger writes independent file
	private static $instance;//Instance of this class
	    
    /**
     * Always returns an instance
     * @param integer $exp_time
     * @param string $path
     */
    public static function get_instance($exp_time=3600,$path='cache/')
    {
        if (!isset(self::$instance))
        {//doesn't exists the isntance
        	 self::$instance = new self($exp_time,$path);//goes to the constructor
        }
        return self::$instance;
    }
    
	/**
	 * cache constructor, optional expiring time and cache path
	 * @param integer $exp_time
	 * @param string $path
	 */
	private function __construct($exp_time=3600,$path='cache/')
	{
	    $this->start_time=microtime(TRUE);//time starts
		$this->cache_expire=$exp_time;
		if ( ! is_writable($path) ) trigger_error('Path not writable:'.$path,E_USER_ERROR);
		else $this->cache_path=$path;
		$this->APP_start();//starting application cache
	}
	
	public function __destruct()
	{
    	log::add('destruct');
		$this->APP_write();//on destruct we write if needed
	}
	
	// Prevent users to clone the instance
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
	
	/**
	 * deletes cache from folder
	 * @param integer $older_than time to delete
	 */
	public function deleteCache($older_than=NULL)
	{
	    if (!is_numeric($older_than)) $older_than=0;
		
	    $this->removeResource($this->cache_path,$older_than);
	    if( !is_dir($this->cache_path) )//just in case cache dir is deleted
	    {
	        umask(0000);
            mkdir($this->cache_path, 0755,TRUE);
	    }
	    unset($this->application);
	    $this->APP_start($this->cache_path);
		log::add('deleted all cache');
	}
	
	/**
	 * writes or reads the cache
	 * @param string $key
	 * @param mixed $value
	 * @return mixed value
	 */
	public function cache($key, $value=NULL)
	{
		if ($value!==NULL)
		{//wants to write
			if (strlen(serialize($value)) > $this->content_size )
			{//write independent file it's a big result
			    log::add('cache function write in file key:'. $key);
				$this->put($key, $value);
			}
			else 
			{
			    log::add('cache function write in APP key:'. $key);
			    $this->APP($key,$value);//write in the APP cache
			}
		}
		else{//reading value
			if ( $this->APP($key)!==NULL )
			{
			    log::add('cache function read APP key:'. $key);
			    return $this->APP($key);//returns from app cache
			}
			else 
			{
			    log::add('cache function read file key:'. $key);
			    return $this->get($key);//returns from file cache
			}
		}
	}
	
	/**
	 * deletes a key from cache
	 * @param string $name
	 */
	public function delete($name)
	{
		if ( $this->APP($name)!==NULL )
		{//unset the APP var
    	    log::add('unset APP key:'. $name);
    		unset($this->application[md5($name)]);
        	$this->application_write=TRUE;//says that we have changes to later save the APP
    	}
		elseif ( file_exists($this->fileName($name)) )
		{//unlink filename
		    log::add('unset File key:'. $name);
			unlink($this->fileName($name));			
		}
	}
	
	// Overloading for the variables and automatically cached
	 	public function __set($name, $value) 
	 	{
	 		$this->cache($name, $value);
	    }
	
	    public function __get($name) 
	    {
	        return $this->cache($name);
	    }
	
	    public function __isset($name) 
	    {
	        return ($this->get($name)!==NULL)?TRUE:FALSE;
	    }
	
	    public function __unset($name) 
	    {//echo "Unsetting '$name'\n";
	    	$this->delete($name);
	    }
	//end overloads
	
	//////////Cache for files individually///////////////////
	
		/**
		 * creates new cache files with the given data
		 * @param string $key
		 * @param mixed $data
		 */
	    private function put($key, $data)
		{
			if ( $this->get($key)!= $data )
			{//only write if it's different
				$values = serialize($data);

				$filename = $this->fileName($key);
                $cache_dir=$this->cache_path.substr($filename,0,2).'/';
                if (!is_dir($cache_dir))
                {//creating the directory
		            umask(0000);
                    mkdir($cache_dir, 0755,TRUE);
		        }
                $filename = $cache_dir.$filename;
                oc::fwrite($filename, $values);
                
			}//end if different
		}
		
		/**
		 * returns cache for the given key 
		 * @param string $key
		 * @return value / NULL if not found
		 */
		private function get($key)
		{
			$filename  = $this->fileName($key);            
            $cache_dir = $this->cache_path.substr($filename,0,2).'/';
            $filename  = $cache_dir.$filename;

			if (!file_exists($filename) || !is_readable($filename))
			{//can't read the cache
			    log::add('can\'t read key: '.$key.' file: '.$filename);
				return NULL;
			}
			
			if ( time() < (filemtime($filename) + $this->cache_expire) ) 
			{//cache for the key not expired
			    $data = oc::fread($filename);
		        if ($data)
		        {//able to open the file
		            log::add('reading key: '.$key.' file: '.$filename);
		            return unserialize($data);//return the values
		        }
		        else
		        {
		            log::add('unable to read key: '.$key.' file: '.$filename);
		            return NULL;
		        }
			}
			else
			{
			    log::add('expired key: '.$key.' file: '.$filename);
			    oc::remove_resource($filename);	
			    return NULL;//was expired you need to create new
			}
	 	}
		
	 	/**
	 	 * returns the filename for the cache
	 	 * @param string $key
	 	 * @return string
	 	 */
		private function fileName($key)
		{
			return md5($key);
		}
 	//////////END Cache for files individually///////////////////
 	
	//////////Cache for APP variables///////////////////
	
	 	/**
	 	 * load variables from the file
	 	 * @param $app_file name of the file is gonna be stored
	 	 */
		private function APP_start ($app_file='application')
		{
			$this->application_file=$app_file;		
			
		    if (file_exists($this->cache_path.$this->application_file))
		    { // if data file exists, load the cached variables
		        //erase the cache every X minutes
			    $app_time=filemtime($this->cache_path.$this->application_file)+$this->cache_expire;
			    if (time()>$app_time)
			    {
			        log::add('deleting APP file: '.$this->cache_path.$this->application_file);
			        unlink ($this->cache_path.$this->application_file);//erase the cache
			    }
			    else
			    {//not expired
			        $filesize=filesize($this->cache_path.$this->application_file);
	                if ($filesize>0)
	                {
	                    $data = oc::fread($this->cache_path.$this->application_file);
	                    if ($data)
	                    {
	                        log::add('reading APP file: '.$this->cache_path.$this->application_file);
        		            $this->application = unserialize($data);// build application variables from data file
	                    } //en if file could open
	                }//end if file size
			    
			    }     
	        }
	        else  
	        {//if the file does not exist we create it
	            log::add('creating APP file: '.$this->cache_path.$this->application_file);
	            oc::fwrite($this->cache_path.$this->application_file, '');
	        }
			 
		}
		
		/**
		 * write application data to file
		 */
		private function APP_write()
		{
			if ($this->application_write)
			{
			    $data = serialize($this->application);
			    $file = $this->cache_path.$this->application_file;
			    oc::fwrite($file,$data);			    
			}
		}
		
		/**
		 * returns the value form APP cache or stores it
		 * @param string $var
		 * @param mixed $value
		 */
		private function APP($var,$value=NULL){
			if ($value!==NULL)
			{//wants to write
				if (is_array($this->application))
				{
				    if ( array_key_exists(md5($var), $this->application) )
				    {//exist the value in the APP
					    $write=FALSE;//we don't need to wirte
					    if ($this->application[md5($var)]!=$value)$write=TRUE;//but exists and is different then we write
				    }
				    else $write=TRUE;//not set we write!
				}
				else $write=FALSE;
	
				if ($write)
				{
				    log::add('writting APP key:'.$var);
					$this->application[md5($var)]=$value;
					$this->application_write=TRUE;//says that we have changes to later save the APP
				}
			}
			else 
			{//reading	
				if ( !is_array($this->application) || ! array_key_exists(md5($var), $this->application) )
				{
				    log::add('nothing found for APP key:'.$var);
				    return NULL;//nothing found not in array
				}
				else
				{
                    log::add('reading APP key:'.$var);				
				    return $this->application[md5($var)];//return value
				}
			}
		}
    //////////End Cache for APP variables///////////////////
    
	/**
     * allows to delete a directory or file recursevely	
     * @param string path/file
     * @param integer filters the fiels to delte by age
     */
	public function removeResource($_target=NULL,$older_than=0) 
    {
        oc::remove_resource($_target,$older_than);
    }
}
