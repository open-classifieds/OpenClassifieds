<?php
/**
 * Logging and profiling class
 * 
 * Usage:
 * log::error_reporting(TRUE/FALSE);//sets error reporting and faltal error handling
 * log::add('some message');//stores time and memory usage also starts the log if was not initialized
 * echo log::show_logs('HTML');//displays the logs
 * 
 * @package     Open Classifieds
 * @subpackage  Core
 * @category    Helper
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 */
class log{
    
    private static $log_array;//log storage
    public  static $start_time=NULL;
    public  static $stop_time=NULL;
    
    /**
     * Ads a log entry
     * @param string $msg to be added
     */
    public static function add($msg=NULL)
    {
        //timer not started
        if(self::$start_time===NULL)
        {
            self::start_timer();
        }
        
        //adding new log element
        if (DEBUG)
        {
            $current_memory  = self::show_memory('mb',5);
            $current_time    = self::getm();
            
            if (count(self::$log_array)>1)
            {//case theres another log
                $used_memory     = end(self::$log_array);
                $used_memory     = $current_memory - $used_memory['current_memory'];
                $used_time       = end(self::$log_array);
                $used_time       = $current_time - $used_time['current_time'];
            }
            else
            {//first time here
                $used_memory  = 0;
                $used_time    = 0;
            }
            
            self::$log_array[] = array('name'          => self::get_caller_method(),
                                       'current_time'  => $current_time,
                                       'used_time'     => $used_time,
                                       'current_memory'=> $current_memory,
                                       'used_memory'   => $used_memory,
                                       'message' 	   => $msg//.'--'.self::get_cpu_usage()
                                       );
        }
    }
    
    /**
	 * 
	 *@return function name from where was called
	 *
	 */
	public static function get_caller_method() 
    { 
        $traces = debug_backtrace(); 
    
        if (isset($traces[2])) 
        { 
            return $traces[2]['class'].'::'.$traces[2]['function']; 
        } 
    
        return FALSE; 
    } 
    
    /**
     * Returns the entire log depends the type
     * @param string $return_type
     * @return mixed
     */
    public static function show_logs($return_type='HTML')
    {
        //not in debug mode so we return nothing.
        if (!DEBUG)
        {
            return FALSE;
        }
        log::add($return_type);
        switch ($return_type)
        {
            case 'HTML':
                //requires the view log
                /*$v=new View('log');
                $v->log_array=self::$log_array;
                $v->render();*/
            	oc::load_file(SITE_ROOT.'/content/profiler.php');
                break;
            case 'DUMP':
                var_dump(self::$log_array);
                break;
            case 'ARRAY':
            default:
                return self::$log_array;
        }
      
    }
      
    
    /**
     * returns a summary of the framework usage
     * @return string
     */
    public static function summary()
    {
        $db=phpMyDB::GetInstance();
        $ret='Page generated the '.date('d M Y H:i:s').' in '.self::show_timer(3).'s. Total queries: '.$db->query_counter.'.';
        if (DEBUG)//all the info
        {
            $ret.=' Total cached queries: '.$db->query_cache_counter.'. Memory usage: '.self::show_memory('mb',5).'mb.';
        }
        self::stop_timer();
        if (DEBUG)
        {
            return $ret;
        }
        else
        {
            return '<!--'.$ret.'-->';
        }
    }
 
	/**
	 * sets the error system handling
	 * @param string $debug boolean
	 */
    public static function error_reporting($debug=NULL)
    {        
        set_error_handler('log::error_handler');
        register_shutdown_function('log::fatal_error'); 
        
        //error display
        if (!$debug)
        {//do not display any error message
            error_reporting(0);
            ini_set('display_errors','off');
        }
        else
        {//displays error messages and debug
            error_reporting(E_ALL | E_STRICT);
            ini_set('display_errors','on');
        }
    }
    
	/**
	 * error_handler
	 * @param string $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param string $errline
	 */
	public static function error_handler($errno, $errstr, $errfile, $errline)
    {
        $error_details = ' ['.$errno. '] '.$errstr.' error on line ' .$errline. ' 
        				in file ' .$errfile. ' - PHP ' . PHP_VERSION . ' (' . PHP_OS . ')<br />\n';
        
        switch ($errno) {
            case E_USER_ERROR:
            	if ($errstr == '(SQL)')// handling an sql error
            	{
            	    $error_type = 'SQL';
                    $error_details = SQLMESSAGE.' '.$error_details;
                } 
                else 
              {
                    $error_type = 'CRITICAL';
                }		
        
                $msg = 'OC: '. $error_type.$error_details;
        		error_log($msg, 0);
        		    
           		if (DEBUG)
           		{
           		    echo $msg; 
           		} 
        		elseif (!DEBUG)
        		{
        		    error_log($msg, 1, NOTIFY_EMAIL);
        		}
        		
                exit(1);
                break;
        
            case E_USER_WARNING:
               	    $error_type = 'ERROR WARNING';   
                break;
        
            case E_USER_NOTICE:
                    $error_type = 'NOTICE';   
                break;
        
           default:
                    $error_type = 'UNDEFINED';   
                break; 
        }
        
        log::add($error_type.$error_details);
        
        return true;
    }
    
    /**
     * Handles fatal error (shutdown functionS)
     */ 
    public static function fatal_error() 
    { 
        $last_error = error_get_last(); 
        
        if( $last_error['type'] == 1 || 
            $last_error['type'] == 4 || 
            $last_error['type'] == 16 ||
            $last_error['type'] == 64 || 
            $last_error['type'] == 256 || 
            $last_error['type'] == 4096) 
        { 
            log::error_handler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
        } 
    }  
	
 	/*
     * returns the current CPU usage
     * @return float cpu usage
     */
    public static function get_cpu_usage()
    {
        if (function_exists(sys_getloadavg))
        {
        	$load=sys_getloadavg();
        	return $load[0];
        }
        else
        {
        	$content = file_get_contents('/proc/loadavg');
        	$loadavg = explode(' ', $content);
        	return $loadavg[0] + 0;
        }
        
    }
    
    /**
     * NOTE, this class has been refactored to be part of the OC core.
     * 
    *Benchmark class, it's a good solution for testing your web applications and scripts!
    *also it can be a permanent tool you can integrate into your systems that will indicate the performance of your app/script etc.
    * Test Time and memory consumption!
    *@author David Constantine Kurushin
    *@link http://www.zend.com/en/store/education/certification/authenticate.php/ClientCandidateID/ZEND015209/RegistrationID/238001337
    *@version 10.12.2010
    *@package Benchmark
    *@copyright Copyright (c) 2010, David Constantine Kurushin
     */
    	
	/**
	*Private static method returns the current microtime
	*@staticvar stime holds the starting time
	*@return full microtime 
	 */
	private static function getm()
	{
		return array_sum(explode(" ",microtime()));
	}

	/**
	*Public static method the starting time
	*@static
	 */
	public static function start_timer()
	{
		self::$start_time=self::getm();
    }

	/**
	*Private static method sets the ending time
	*@static
	 */
	public static function stop_timer()
	{
	    self::$stop_time=self::getm();
	}

	/**
	*Public static method,
	*Calculate the time elapsed from starting to stopping in second
	*@static
	*@param integer $round set how much numbers you want after Zero, default is 10
	*@return double the time elapsed from starting to stopping in seconds
	 */
	public static function show_timer($round=10)
	{
		if(self::$start_time===NULL)
		{
		    trigger_error('log::show_timer timer need to be started',E_USER_ERROR);
		}
		else
		{
		    self::$stop_time=self::getm();
		}
			
		return number_format( (self::$stop_time - self::$start_time) , $round, '.', '');
	}
	
	/**
	*Public static method,
	*Calculate the memory that used/alocated to your script
	*@static
	*@param String $string you should choose the format you want, 'mb'/'kb'/'bytes' default if bytes!
	*@param integer $round set how much numbers you want after Zero, default is 3
	*@return double amount of memory your script consume
	 */
	public static function show_memory($string ='bytes', $round=3)
	{
		$result=NULL;
		switch($string)
		{
			case 'mb': $result = round(memory_get_usage()/1048576,$round);
			break;
			case 'kb': $result = round(memory_get_usage()/1024, $round);
			break;
			default: $result = memory_get_usage();
			break;
		}
		return $result;
	}
}