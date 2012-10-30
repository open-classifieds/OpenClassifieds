<?php
/**
 * i18n class
 *
 * @package     JAF
 * @subpackage  Core
 * @category    Helper
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 */

class i18n{
    
    /**
     * Loads the gettext dropin for the locales
     * @param string $locale
     * @param string $charset
     */
    public static function load($locale=NULL,$binddomain=NULL,$locale_path=NULL,$charset=NULL,$timezone=NULL)
    {
        /**
         * Set the default time zone.
         *
         * @see  http://php.net/timezones
         */
    	if ($timezone!==NULL)
        	date_default_timezone_set($timezone);
        
        //LOCALES
        mb_internal_encoding($charset);
        mb_http_output($charset);
        mb_http_input($charset);
        mb_language('uni');
        mb_regex_encoding($charset);
        
        //gettext override
        oc::load_file(SITE_ROOT.'/includes/gettext/gettext.inc',FALSE);
    
        if ( !function_exists('_') )
        {//check if gettext exists if not use dropin
            T_setlocale(LC_MESSAGES, $locale);
            bindtextdomain($binddomain,$locale_path);
            bind_textdomain_codeset($binddomain, $charset);
            textdomain($binddomain);
            log::add('i18n::load dropin locale: '.$locale.' charset: '.$charset);
        }
        else
        {//gettext exists using fallback in case locale doesn't exists
            T_setlocale(LC_MESSAGES, $locale);
            T_bindtextdomain($binddomain,$locale_path);
            T_bind_textdomain_codeset($binddomain, $charset);
            T_textdomain($binddomain);
            log::add('i18n::load locale: '.$locale.' charset: '.$charset);
        }
        //end language locales
    }
}

/**
 * Echoes a text and tries to translate it
 * @param string $text
 */
function _e($text)
{
    if (function_exists('T_'))
    {    
        echo T_($text);
    }
    else
    {
        echo $text;
    }
}

/**
 * 
 * returns the firendly word with html parsed
 * @param string $word
 */
function u($word)
{
	return oc::friendly_url(html_entity_decode($word,ENT_QUOTES,CHARSET));
}