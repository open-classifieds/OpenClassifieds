<?php
/**
 * Core functions helper
 *
 * @package     Open Classifieds
 * @subpackage  Core
 * @category    Helper
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 */
class oc{ 
        
    /**
     * Core start up
     */
    public static function start()
    {
        spl_autoload_register('oc::autoload'); // custom autoload   
        register_shutdown_function('oc::end'); //what to do at the end of the script
        
        log::error_reporting(DEBUG);
        log::add();    

        //init session
        oc::ob_start();
        
        //don't start it in the admin since it's already been started
        if (!session_id()) session_start();
        
    }
    
    /*
     * Core finish execution function
     */
    public static function end()
    {
        log::add();
        //saving to cache
        //oc::page_cache(Router::$cache,'save');//@todo
        log::show_logs('HTML');  
        //flush content to browser
        ob_end_flush();
    }
    
    /**
     * Loads the given class
     * @param $class
     * @return boolean found
     */
    public static function autoload($class)
    {        
        
        $file_class=SITE_ROOT.'/includes/classes/'.strtolower($class).'.php';
		//die($file_class);
	    if(oc::load_file($file_class)) 
	    {
	    	return TRUE;
	    }
		else
		{
			log::add('ERROR: Class '.$class.' not found: '.$file_class);
			return FALSE;
		} 
        
    }
    
    /**
     * includes a file to the system
     * @param string $file
     * @param boolean $verify_exists if set false we don't check the file exists and we add it
     */
    public static function load_file($file,$verify_exists=TRUE)
    {
        //check if file exists
        if ($verify_exists==TRUE)
        {
            if(!file_exists($file))
            {
                log::add('FAILED | file '.$file);
                return FALSE;
            }
        }
        
        @require ($file);
        
        log::add('SUCCESS | verify: '.(int)$verify_exists.' | file '.$file);
        
        return TRUE;
    }
    
    /**
     * 
     * Clean all the request for the APP to prevent any injection
     * We preserve the original values from the Requests
     * Later on always to get params  oc::$_POST['inputaname']; or cP('inputname');
     */
    public static $_POST;
    public static $_GET;
    public static $_COOKIE;
    
    public static function clean_request()
    { 
        self::$_POST   = array_map('oc::filter_data', $_POST);
    	self::$_GET    = array_map('oc::filter_data', $_GET);
    	self::$_COOKIE = array_map('oc::filter_data', $_COOKIE);
    	log::add();
    }

    /**
     * filters the vars recursive
     * @param unknown_type $data
     * @return mixed string cleaned or recursive callback
     */
    public static function filter_data($data)
    {
    	return (is_array($data)) ?  array_map('oc::filter_data', $data) : oc::clean($data);
    }

    /**
     * string cleaner, to prevent any kind of injection
     * @param string $var
     * @return string variable cleaned
     */
    public static function clean($var)
    {
    	$var = oc::nl2br($var);//removing nl
    	if(get_magic_quotes_gpc())
    	{
    	    $var = stripslashes($var); //removes slashes
    	}
    	
        $var = mysql_real_escape_string($var); //sql injection
    	/*if(DB::isloaded())//@todo
    	{
    	   
    	} */
    	
    	return strip_tags($var,ALLOWED_HTML_TAGS);//whitelist of html tags
    }
    
    /**
     * improved version of nl2br since that one doesnt work really good
     * @param string $var
     * @return string without line returns
     */
    public static function nl2br($var)
    {
    	return str_replace(array('\\r\\n','\r\\n','r\\n','\r\n', '\n', '\r'), '<br />', nl2br($var));
    }

    /**
     * simple header redirect
     * @param string $url to redirect
     */
    public static function redirect($url)
    {
        header('Location: '.$url);//redirect header
        die();
    }
    
    /**
     * generates a string ready to be in the URL / post slug
     * @param string string to replace
     * @return string prepared for the URL
     */
    public static function friendly_url($var)
    {
    	$var = mb_strtolower(oc::replace_accents($var),CHARSET);
        $var = str_replace(array('http://', 'https://', 'www.'), '', $var);//erase http/https and wwww, we do shorter the url
        $var = preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('','-',''),$var);
        log::add();
    	return $var;
    }
    
    /**
     * replace for accents catalan spanish and more
     * @param string to replace characters
     * @return string with characters replaced
     */
    public static function replace_accents($var)
    {
        $from = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $to   = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        $var = str_replace($from, $to,$var);
        log::add(); 
        return  $var;
    } 
    
    /**
     * check correct url formation
     * @return boolean
     */    
    public static function is_URL($url)
    {
    	return (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url) > 0)? TRUE:FALSE;
    }
    
    /**
     * session start
     */
    public static function ob_start()
    {
        if (extension_loaded('zlib') && !DEBUG) 
        {//check extension is loaded and debug enabled
            if(!ob_start('ob_gzhandler'))//start HTML compression, if not normal buffer input mode  
            {
                ob_start();
            } 
        }
        else //normal output in case could not load extension or debug mode
        {
             ob_start();
        }
        log::add();
    }
    
 	/**
     * caches the output of the given page
     * used in the bootstrap if cached enabled
     * @param boolean $cached says if we should cache the page or not
     * @param string $action
     */
    public static function page_cache($cache_expire=NULL,$action='start')
    {
        if (is_numeric($cache_expire)) 
        {
            log::add('cache: '.$cache_expire.' action: '.$action);
            //we use file cache since I think for an HTML page is the best storage, you can chage this of course ;)
            $cache = Cache::get_instance(CACHE_TYPE,$cache_expire,CACHE_CONFIG); 
            if ($action=='start')
            {
            	$html = $cache->cache(oc::get_current_URI());
            	if ($html !==NULL)
            	{
            	    die(gzuncompress($html));
            	} 
            }
            elseif($action=='save')
            {
                echo '<!--Page cached on '.date('d-m-Y-H:i:s').' expires on '.date('d-m-Y-H:i:s',time()+$cache_expire).' -->';//this is just a bit dirty @TODO
                $cache->cache(oc::get_current_URI(),gzcompress(ob_get_contents()));  
            }	
            unset($cache);
        }
        
    }
    
    /**
     * 
     * Get the real ip form the visitor.
     * @param boolean returns ip to long instead of string ip
     * @return string IP
     */
    public static function get_ip($to_long=FALSE)
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
     
        return ($to_long==TRUE)? ip2long($ip):$ip;
    }
    
    /**
     * gets the current URI 
     * @return string current URI
     */
    public static function get_current_URI()
    {
        return oc::get_domain().$_SERVER['REQUEST_URI'];
        //return SITE_URL.'/'.Router::$controller.'/'.Router::$action.'/'.implode('/',Router::$params);
    }
    
    /**
     * get the domain with or without protocol
     * @param boolean $url
     * return string domain
     */
    public static function get_domain($url=TRUE)
    {
        if ($url)
        {
            if (defined(SITE_URL))//allow to override the url
            {
                return SITE_URL;
            }
            //we try to guess the domain name
            else
            {
                return 'http://'.$_SERVER['SERVER_NAME'];   
            }
            
        }
        //only name
        else 
        {
            return $_SERVER['SERVER_NAME'];
        }
        
    }
    
	/**
     * checks if a call_back function name can be used
     * @param string $call_back function name
     * @return boolean
     */
    public static function is_callable($call_back)
    {
        if (function_exists($call_back))
        {
            return TRUE;
        }
        
        //for static methods, be aware this may be not the best way and we need to trust the developers
        if (strpos($call_back, '::'))
        {
            $m=explode('::',$call_back);
            if (method_exists($m[0], $m[1]))
            {
                return TRUE;
            }
        } 
       
       return FALSE;
    }
    
    /**
     * write to file
     * @param $filename fullpath file name
     * @param $content
     * @return boolean
     */
    public static function fwrite($filename,$content)
    {
        log::add('filename:'.$filename);
        $file = fopen($filename, 'w');
	    if ($file)
	    {//able to create the file
	        fwrite($file, $content);
	        fclose($file);
	        return TRUE;
	    }
	    return FALSE;   
    }//@TODO create intermediate directories if needed
    
    /**
     * read file content
     * @param $filename fullpath file name
     * @return $string or false if not found
     */
    public static function fread($filename)
    {
        log::add('filename:'.$filename);
        if (is_readable($filename))
        {
            $file = fopen($filename, 'r');
    	    if ($file)
    	    {//able to read the file
    	        $data = fread($file, filesize($filename));
    		    fclose($file);
    	        return $data;
    	    }
        }
	    return FALSE;   
    }
    
    
	/**
     * allows to delete a directory or file recursevely	
     * @param string path/file
     * @param integer filters the fiels to delte by age
     */
	public static function remove_resource($_target=NULL,$older_than=0) 
    {
        //file?
        if( is_file($_target) ) 
        {
            if( is_writable($_target)  && time() >= (filemtime($_target) + $older_than) ) 
            {
                
                if( @unlink($_target) ) 
                {
                    return TRUE;
                }
            }
            return FALSE;
        }
        //dir recursive
        if( is_dir($_target) ) 
        {
            if( is_writeable($_target) ) 
            {
                foreach( new DirectoryIterator($_target) as $_res ) 
                {
                    if( $_res->isDot() ) 
                    {
                        unset($_res);
                        continue;
                    }
                    if( $_res->isFile() ) 
                    {
                        self::remove_resource( $_res->getPathName() );
                    }

                    elseif( $_res->isDir() ) 
                    {
                        self::remove_resource( $_res->getRealPath() );
                    }
                    unset($_res);
                }
                if( @rmdir($_target) && time() >= (filemtime($_target) + $older_than) ) 
                {
                    return TRUE;
                }
            }
            return FALSE;
        }
    }
    
    
    /**
     * moves recursevily files from src to destination
     * @param string $src
     * @param string $dst
     */
    public static function move($src,$dst) {
    	$handle=opendir($src);                      // Opens source dir.
    	if (!is_dir($dst))
    	{
    		umask(0000);
    		mkdir($dst,0755,true);       // Make dest dir.
    	}
    	while ($file = readdir($handle)) 
    	{
    		if (($file!=".") and ($file!="..")) 
    		{       // Skips . and .. dirs
    			$srcm=$src."/".$file;
    			$dstm=$dst."/".$file;
    			if (is_dir($srcm)) 
    			{                      // If another dir is found
    				oc::move($srcm,$dstm);               // calls itself - recursive WTG
    			} else 
    			{
    				copy($srcm,$dstm);
    				unlink($srcm);                         // Is just a copy procedure is needed
    			}                                             // comment out this line
    		}
    	}
    	closedir($handle);
    	rmdir($src);                                     // and this one also :)
    }
    
    
    /**
     * gets the extension from a string
     * @param string $file
     * @return string extension name
     */
    public static function get_extension($file)
    {
        $dots = explode('.', $file);
        $extension = end($dots);
        return $extension;
    }
    
	/**
	 * 
	 * Detection function for mobile devices
	 * @return boolean
	 */
	public static function is_mobile()
	{
		if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) 
		{
		   return TRUE;
		}
		 
		if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
		   return TRUE;
		}    
		 
		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
		$mobile_agents = array(
		    'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
		    'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
		    'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
		    'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
		    'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
		    'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
		    'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
		    'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
		    'wapr','webc','winw','winw','xda','xda-');
		 
		if(in_array($mobile_ua,$mobile_agents)) return TRUE; 
		if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) return TRUE;
		
		return FALSE;
	}
	
	/**
	 * 
	 * theme selector, allows to change the theme
	 */
	public static function theme()
	{
		if (THEME_MOBILE && cG("mobile")!="0" && !isset($_SESSION['theme_mobile']) ){
		    $mobile=oc::is_mobile();
		}
		else{
		    $_SESSION['theme_mobile']=1;
		    $mobile=false;
		}
		//$mobile=true;
		
		if (!$mobile){
			if (THEME_SELECTOR){//see config.php
				if (cG("theme")!=""){//select them by request
					$theme= str_replace(array('..', '&', '\\', '//', ' '), '', cG("theme"));//for secure reasons we remove dots and slashes
					$theme_dir=SITE_ROOT."/themes/$theme";//directory for the theme
					if (is_dir($theme_dir)){//the folder exists
						define('THEME', $theme);
						$_SESSION['theme']=$theme;
						setcookie ("theme",$theme, time() + 3600*24*365, "/", ".".$_SERVER['SERVER_NAME']);
					}
					else define('THEME', DEFAULT_THEME);//default theme doesnt exists the theme :S
				}
				else if (isset($_SESSION['theme'])){//theme kept in the session
					define('THEME', $_SESSION['theme']);
				}
				else if (isset($_COOKIE['theme'])){//theme from the cookie
					define('THEME', $_COOKIE['theme']);
					$_SESSION['theme']=$_COOKIE['theme'];
				}
				else define('THEME', DEFAULT_THEME);//default theme
			}
			else  define('THEME', DEFAULT_THEME);//default theme
		}
		else define('THEME', 'neoMobile');//mobile version
	}
}

/**
 * shared common functions 
 */

    /**
     * request get alias
     * @param $name
     */
    function cG($name)
    {
    	return (isset(oc::$_GET[$name]))? oc::$_GET[$name]:NULL;
    }
    
    /**
     * request post alias
     * @param $name
     */
    function cP($name)
    {
    	return (isset(oc::$_POST[$name]))? oc::$_POST[$name]:NULL;
    }
    
    /**
     * request post with some tweaks
     * @param $name
     */
    function cPR($name)
    {
    	return (isset(oc::$_POST[$name]))? ToHtml(oc::$_POST[$name]):NULL;
	}

	/**
	 * alias post slug
	 * @param string $url
	 */
	function friendly_url($url)
	{
		return oc::friendly_url($url);
	}
    
    /**
     * 
     * die script and var dump
     * @param mixed $var
     */
    function d($var)
    {
    	die(var_dump($var));
    }