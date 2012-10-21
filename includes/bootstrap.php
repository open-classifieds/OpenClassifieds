<?php
////////////////////////////////////////////////////////////
//APP bootstrap
////////////////////////////////////////////////////////////

//starts installation, you can erase this line to optimize code
if(file_exists('install.lock')) die(header('Location: install/'));

//Initial defines
define('VERSION','1.8.2');
//if you change this to true, returns error in the page instead of email, also enables debug from phpMyDB and disables disqus
define('DEBUG',false);

//config includes
require_once('config.php');//configuration file
require_once('classes/oc.php');

oc::start();

i18n::load(LANGUAGE.LOCALE_EXT,'messages',SITE_ROOT.'/languages/',CHARSET,TIMEZONE);

$ocdb = phpMyDB::GetInstance(DB_USER,DB_PASS,DB_NAME,DB_HOST,DB_CHARSET);//,DB_CHARSET,DB_TIMEZONE,'persistent'

//prevent attacks, hacks, injections, xss etc...
oc::clean_request();

//theme selector
oc::theme();
        
//start cache
Cache::get_instance(CACHE_TYPE,CACHE_EXPIRE,CACHE_DATA_FILE);

if (CACHE_ACTIVE && !isset($_SESSION['admin'])){
	$ocdb->setCache(true);//cache for DB
}
    
require_once('common.php');//common functions
require_once('item-common.php');//item common functions

require_once('controller.php');//loads the value of the items/categories  if there's
require_once('menu.php');//menu functions generation and some functions that returns stats
require_once('sidebar.php');//sidebar functions generation
require_once('seo.php');//metas for the html, title,description, keywords


//sitemap regeneration
Sitemap::renew();

//special functions from the theme if they exists
if (file_exists(SITE_ROOT.'/themes/'.THEME.'/functions.php')){
	require_once(SITE_ROOT.'/themes/'.THEME.'/functions.php'); 
}
?>