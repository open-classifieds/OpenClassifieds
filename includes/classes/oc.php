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
    	$var = mb_strtolower($var,CHARSET);
        $var = str_replace(array('http://', 'https://', 'www.'), '', $var);
        $var = oc::replace_accents($var);
        $var = self::utf8_uri_encode($var,100);
        $var = preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('','-',''),$var);
        log::add();
    	return $var;
    }
     

    /**
     * Encode the Unicode values to be used in the URI.
     *
     * @see WordPress function formatting.php
     * @param string $utf8_string
     * @param int $length Max length of the string
     * @return string String with Unicode encoded for URI.
     */
    public static function utf8_uri_encode( $utf8_string, $length = 0 ) {
        $unicode = '';
        $values = array();
        $num_octets = 1;
        $unicode_length = 0;

        $string_length = strlen( $utf8_string );
        for ($i = 0; $i < $string_length; $i++ ) {

            $value = ord( $utf8_string[ $i ] );

            if ( $value < 128 ) {
                if ( $length && ( $unicode_length >= $length ) )
                    break;
                $unicode .= chr($value);
                $unicode_length++;
            } else {
                if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;

                $values[] = $value;

                if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
                    break;
                if ( count( $values ) == $num_octets ) {
                    if ($num_octets == 3) {
                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
                        $unicode_length += 9;
                    } else {
                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
                        $unicode_length += 6;
                    }

                    $values = array();
                    $num_octets = 1;
                }
            }
        }

        return $unicode;
    }

    /**
     * Checks to see if a string is utf8 encoded.
     *
     * NOTE: This function checks for 5-Byte sequences, UTF8
     *       has Bytes Sequences with a maximum length of 4.
     *
     * @author bmorel at ssi dot fr (modified)
     * @see WordPress function formatting.php
     *
     * @param string $str The string to be checked
     * @return bool True if $str fits a UTF-8 model, false otherwise.
     */
    public static function seems_utf8($str) {
        $length = strlen($str);
        for ($i=0; $i < $length; $i++) {
            $c = ord($str[$i]);
            if ($c < 0x80) $n = 0; # 0bbbbbbb
            elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
            elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
            elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
            elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
            elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
            else return false; # Does not match any model
            for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
                if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
                    return false;
            }
        }
        return true;
    }


    /**
     * Converts all accent characters to ASCII characters.
     *
     * If there are no accent characters, then the string given is just returned.
     *
     * @see WordPress function formatting.php
     *
     * @param string $string Text that might have accent characters
     * @return string Filtered string with replaced "nice" characters.
     */
    public static function replace_accents($string) {
        if ( !preg_match('/[\x80-\xff]/', $string) )
            return $string;

        if (self::seems_utf8($string)) {
            $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(194).chr(170) => 'a', chr(194).chr(186) => 'o',
            chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
            chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
            chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
            chr(195).chr(134) => 'AE',chr(195).chr(135) => 'C',
            chr(195).chr(136) => 'E', chr(195).chr(137) => 'E',
            chr(195).chr(138) => 'E', chr(195).chr(139) => 'E',
            chr(195).chr(140) => 'I', chr(195).chr(141) => 'I',
            chr(195).chr(142) => 'I', chr(195).chr(143) => 'I',
            chr(195).chr(144) => 'D', chr(195).chr(145) => 'N',
            chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
            chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
            chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
            chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
            chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
            chr(195).chr(158) => 'TH',chr(195).chr(159) => 's',
            chr(195).chr(160) => 'a', chr(195).chr(161) => 'a',
            chr(195).chr(162) => 'a', chr(195).chr(163) => 'a',
            chr(195).chr(164) => 'a', chr(195).chr(165) => 'a',
            chr(195).chr(166) => 'ae',chr(195).chr(167) => 'c',
            chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
            chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
            chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
            chr(195).chr(176) => 'd', chr(195).chr(177) => 'n',
            chr(195).chr(178) => 'o', chr(195).chr(179) => 'o',
            chr(195).chr(180) => 'o', chr(195).chr(181) => 'o',
            chr(195).chr(182) => 'o', chr(195).chr(184) => 'o',
            chr(195).chr(185) => 'u', chr(195).chr(186) => 'u',
            chr(195).chr(187) => 'u', chr(195).chr(188) => 'u',
            chr(195).chr(189) => 'y', chr(195).chr(190) => 'th',
            chr(195).chr(191) => 'y', chr(195).chr(152) => 'O',
            // Decompositions for Latin Extended-A
            chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
            chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
            chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
            chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
            chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
            chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
            chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
            chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
            chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
            chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
            chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
            chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
            chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
            chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
            chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
            chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
            chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
            chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
            chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
            chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
            chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
            chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
            chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
            chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
            chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
            chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
            chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
            chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
            chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
            chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
            chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
            chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
            chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
            chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
            chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
            chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
            chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
            chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
            chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
            chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
            chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
            chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
            chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
            chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
            chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
            chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
            chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
            chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
            chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
            chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
            chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
            chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
            chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
            chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
            chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
            chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
            chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
            chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
            chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
            chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
            chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
            chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
            chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
            chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
            // Decompositions for Latin Extended-B
            chr(200).chr(152) => 'S', chr(200).chr(153) => 's',
            chr(200).chr(154) => 'T', chr(200).chr(155) => 't',
            // Euro Sign
            chr(226).chr(130).chr(172) => 'E',
            // GBP (Pound) Sign
            chr(194).chr(163) => '',
            // Vowels with diacritic (Vietnamese)
            // unmarked
            chr(198).chr(160) => 'O', chr(198).chr(161) => 'o',
            chr(198).chr(175) => 'U', chr(198).chr(176) => 'u',
            // grave accent
            chr(225).chr(186).chr(166) => 'A', chr(225).chr(186).chr(167) => 'a',
            chr(225).chr(186).chr(176) => 'A', chr(225).chr(186).chr(177) => 'a',
            chr(225).chr(187).chr(128) => 'E', chr(225).chr(187).chr(129) => 'e',
            chr(225).chr(187).chr(146) => 'O', chr(225).chr(187).chr(147) => 'o',
            chr(225).chr(187).chr(156) => 'O', chr(225).chr(187).chr(157) => 'o',
            chr(225).chr(187).chr(170) => 'U', chr(225).chr(187).chr(171) => 'u',
            chr(225).chr(187).chr(178) => 'Y', chr(225).chr(187).chr(179) => 'y',
            // hook
            chr(225).chr(186).chr(162) => 'A', chr(225).chr(186).chr(163) => 'a',
            chr(225).chr(186).chr(168) => 'A', chr(225).chr(186).chr(169) => 'a',
            chr(225).chr(186).chr(178) => 'A', chr(225).chr(186).chr(179) => 'a',
            chr(225).chr(186).chr(186) => 'E', chr(225).chr(186).chr(187) => 'e',
            chr(225).chr(187).chr(130) => 'E', chr(225).chr(187).chr(131) => 'e',
            chr(225).chr(187).chr(136) => 'I', chr(225).chr(187).chr(137) => 'i',
            chr(225).chr(187).chr(142) => 'O', chr(225).chr(187).chr(143) => 'o',
            chr(225).chr(187).chr(148) => 'O', chr(225).chr(187).chr(149) => 'o',
            chr(225).chr(187).chr(158) => 'O', chr(225).chr(187).chr(159) => 'o',
            chr(225).chr(187).chr(166) => 'U', chr(225).chr(187).chr(167) => 'u',
            chr(225).chr(187).chr(172) => 'U', chr(225).chr(187).chr(173) => 'u',
            chr(225).chr(187).chr(182) => 'Y', chr(225).chr(187).chr(183) => 'y',
            // tilde
            chr(225).chr(186).chr(170) => 'A', chr(225).chr(186).chr(171) => 'a',
            chr(225).chr(186).chr(180) => 'A', chr(225).chr(186).chr(181) => 'a',
            chr(225).chr(186).chr(188) => 'E', chr(225).chr(186).chr(189) => 'e',
            chr(225).chr(187).chr(132) => 'E', chr(225).chr(187).chr(133) => 'e',
            chr(225).chr(187).chr(150) => 'O', chr(225).chr(187).chr(151) => 'o',
            chr(225).chr(187).chr(160) => 'O', chr(225).chr(187).chr(161) => 'o',
            chr(225).chr(187).chr(174) => 'U', chr(225).chr(187).chr(175) => 'u',
            chr(225).chr(187).chr(184) => 'Y', chr(225).chr(187).chr(185) => 'y',
            // acute accent
            chr(225).chr(186).chr(164) => 'A', chr(225).chr(186).chr(165) => 'a',
            chr(225).chr(186).chr(174) => 'A', chr(225).chr(186).chr(175) => 'a',
            chr(225).chr(186).chr(190) => 'E', chr(225).chr(186).chr(191) => 'e',
            chr(225).chr(187).chr(144) => 'O', chr(225).chr(187).chr(145) => 'o',
            chr(225).chr(187).chr(154) => 'O', chr(225).chr(187).chr(155) => 'o',
            chr(225).chr(187).chr(168) => 'U', chr(225).chr(187).chr(169) => 'u',
            // dot below
            chr(225).chr(186).chr(160) => 'A', chr(225).chr(186).chr(161) => 'a',
            chr(225).chr(186).chr(172) => 'A', chr(225).chr(186).chr(173) => 'a',
            chr(225).chr(186).chr(182) => 'A', chr(225).chr(186).chr(183) => 'a',
            chr(225).chr(186).chr(184) => 'E', chr(225).chr(186).chr(185) => 'e',
            chr(225).chr(187).chr(134) => 'E', chr(225).chr(187).chr(135) => 'e',
            chr(225).chr(187).chr(138) => 'I', chr(225).chr(187).chr(139) => 'i',
            chr(225).chr(187).chr(140) => 'O', chr(225).chr(187).chr(141) => 'o',
            chr(225).chr(187).chr(152) => 'O', chr(225).chr(187).chr(153) => 'o',
            chr(225).chr(187).chr(162) => 'O', chr(225).chr(187).chr(163) => 'o',
            chr(225).chr(187).chr(164) => 'U', chr(225).chr(187).chr(165) => 'u',
            chr(225).chr(187).chr(176) => 'U', chr(225).chr(187).chr(177) => 'u',
            chr(225).chr(187).chr(180) => 'Y', chr(225).chr(187).chr(181) => 'y',
            );

            $string = strtr($string, $chars);
        } else {
            // Assume ISO-8859-1 if not UTF-8
            $chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
                .chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
                .chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
                .chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
                .chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
                .chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
                .chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
                .chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
                .chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
                .chr(252).chr(253).chr(255);

            $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

            $string = strtr($string, $chars['in'], $chars['out']);
            $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
            $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
            $string = str_replace($double_chars['in'], $double_chars['out'], $string);
        }

        return $string;
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