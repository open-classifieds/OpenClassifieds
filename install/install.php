<?php

//there was a submit from index.php
if ($_POST)
{
	$install = true;
	$error_msg = '';
	
///////////////////////////////////////////////////////
	//check DB connection
	$link = mysql_connect($_POST["DB_HOST"], $_POST["DB_USER"], $_POST["DB_PASS"]);
	if (!$link) {
		$error_msg.= T_("Cannot connect to server").' '. $_POST["DB_HOST"].' '. mysql_error();
		$install = false;
	}
	
	if ($link && $install) {
        if ($_POST["DB_NAME"]){
            $dbcheck = mysql_select_db($_POST["DB_NAME"]);
            if (!$dbcheck){
            	 $error_msg.= T_("Database name").': ' . mysql_error();
            	 $install = false;
           }
        } else {
    		$error_msg.= '<p>'.T_("No database name was given").'. '.T_("Available databases").":</p>\n";
    		$db_list = @mysql_query("SHOW DATABASES");

    		$error_msg.= "<pre>\n";
		if (!$db_list) {
			$error_msg.= T_('Invalid query'). ":\n" . mysql_error();
		} else {
			while ($row = mysql_fetch_assoc($db_list)) {
				$error_msg.= $row['Database'] . "\n";
			}
		}
    		$error_msg.= "</pre>\n";
            	$install = false;
        }
	}

///////////////////////////////////////////////////////
	//install DB
	if ($install)
	{
		$TABLE_PREFIX=$_POST["TABLE_PREFIX"];
	    $DB_CHARSET=$_POST["DB_CHARSET"];
	    mysql_select_db($_POST["DB_NAME"]);//selecting the db
	    mysql_query('SET NAMES '.$DB_CHARSET);
	    include("sql.php");//dump tables
	    mysql_close();
	}
    
///////////////////////////////////////////////////////
	//ocaku register
	if ($install)
	{
		if ($_POST["OCAKU"] == 1)
	    {	        	    
	        //ocaku register new site!
	        $ocaku=new ocaku();
	        $data=array(
	        					'siteName'=>$_POST["SITE_NAME"],
	        					'siteUrl'=>$_POST["SITE_URL"],
	        					'email'=>$_POST["NOTIFY_EMAIL"],
	        					'language'=>substr($_POST["LANGUAGE"],0,2)
	        );
	        $apiKey=$ocaku->newSite($data);
	        unset($ocaku);
	        //end ocaku register
	    }	
	    else $apiKey="";
	}

///////////////////////////////////////////////////////
	//create config.php
	if ($install)
	{
		$config_content = "<?php\n//Open Classifieds v ".VERSION.' '.T_("Config").' '.date("d/m/Y G:i:s")."\n";
		foreach  ($_POST AS $key => $value){
			$config_content.="define('$key','$value');\n";		
		}
		$config_content.="define('CHARSET','UTF-8');
define('LOCALE_EXT','');
define('DEFAULT_THEME', 'minimalistic');
define('THEME_SELECTOR',false);
define('THEME_MOBILE',false);
define('ITEMS_PER_PAGE',10);
define('DISPLAY_PAGES',10);
define('FRIENDLY_URL', true);
define('COUNT_POSTS', true);
define('HTML_EDITOR', true);
define('DATE_FORMAT', 'dd-mm-yyyy');
define('MIN_SEARCH_CHAR',4);
define('PASSWORD_SIZE',8);
define('SEPARATOR',' | ');
define('SIDEBAR_ORIG','account,item_tools,new,search,locations,categories_cloud,infolinks,comments,advertisement,donate,popular,links,theme,translator,rss');
define('SIDEBAR','new,account,item_tools,search,locations,infolinks,advertisement,donate,popular');
define('ADVERT_TOP','<script type=\"text/javascript\">google_ad_client = \"pub-9818256176049741\";google_ad_slot = \"5864321500\"; google_ad_width = 728;google_ad_height = 15;</script><script type=\"text/javascript\" src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\"></script>');
define('ADVERT_SIDEBAR','<script type=\"text/javascript\">google_ad_client = \"pub-9818256176049741\";google_ad_slot = \"4162447127\";google_ad_width = 250;google_ad_height = 250;</script><script type=\"text/javascript\" src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\"></script>');
define('CURRENCY','&dollar;');
define('CURRENCY_FORMAT','AMOUNTCURRENCY');
define('MAX_IMG_SIZE', 4194304);
define('IMG_UPLOAD', '/images/');
define('IMG_UPLOAD_DIR', SITE_ROOT.IMG_UPLOAD);
define('MAX_IMG_NUM',4);
define('IMG_TYPES','gif,jpeg,jpg,png');
define('IMG_RESIZE',900);
define('IMG_RESIZE_THUMB',100);
define('RSS_ITEMS',15);
define('RSS_IMAGES',true);
define('SITEMAP_FILE',SITE_ROOT.'/sitemap.xml.gz');
define('SITEMAP_EXPIRE',86400);
define('SITEMAP_DEL_ON_POST',true);
define('SITEMAP_DEL_ON_CAT',true);
define('SITEMAP_PING',false);
define('TYPE_OFFER',0);
define('TYPE_NEED',1);
define('CACHE_ACTIVE',true);
define('CACHE_TYPE','fileCache');
define('CACHE_DATA_FILE',SITE_ROOT.'/cache/');
define('CACHE_EXPIRE',86400);
define('CACHE_DEL_ON_POST',true);
define('CACHE_DEL_ON_CAT',true);
define('ANALYTICS','');
define('AKISMET','');
define('MAP_KEY','');
define('MAP_INI_POINT','');
define('DISQUS','');
define('GMAIL',false);
define('GMAIL_USER','');
define('GMAIL_PASS','');
define('VIDEO',false);
define('LOCATION',false);
define('LOCATION_ROOT','');
define('SMTP_HOST','');
define('SMTP_PORT','');
define('SMTP_AUTH',false);
define('SMTP_USER','');
define('SMTP_PASS','');
define('LOGON_TO_POST',false);
define('OCAKU_KEY','".$apiKey."');
define('ALLOWED_HTML_TAGS','<b><i><u><div><center><blockquote><li><ul><a><p><br><br />');
define('SITE_DESCRIPTION','');
define('PARENT_POSTS',true);
define('CONFIRM_POST',false);
define('MODERATE_POST',false);
define('EXPIRE_POST',0);
define('RSS_SIDEBAR_URL','http://open-classifieds.com/feed');
define('RSS_SIDEBAR_NAME','Open Classifieds');
define('RSS_SIDEBAR_COUNT','5');
define('PAYPAL_ACTIVE',false);
define('PAYPAL_ACCOUNT','paypal@open-classifieds.com');
define('PAYPAL_AMOUNT','2');
define('PAYPAL_CURRENCY','USD');
define('PAYPAL_SANDBOX',false);
define('PAYPAL_AMOUNT_CATEGORY',false);
define('CAPTCHA',true);
define('SPAM_COUNTRY',false);
define('SPAM_COUNTRIES','');
define('NEED_OFFER',false);
\n?>";//	echo $config_content;
	
		if (!oc::fwrite('../includes/config.php', $config_content))
		{
			$error_msg=T_("The configuration file")." '/includes/config.php' ".T_("is not writable").". ".T_("Change its permissions, then try again").".";
			$install=false;
		}
	}

///////////////////////////////////////////////////////
	//create robots.txt
	if ($install)
	{
		if (!regenerateRobots($_POST["SITE_URL"]))
		{
			$error_msg = T_("Cannot write to the configuration file")." '/robots.txt'";
			$install=false;
		}
	}
	
///////////////////////////////////////////////////////
	//create htaccess
	if ($install)
	{		
		if (!regenerateHtaccess($_POST["SITE_URL"]))
		{
			$error_msg = T_("Cannot write to the configuration file")." '/.htaccess'";
			$install=false;
		}
	}
	
///////////////////////////////////////////////////////
	//all good!
	if ($install) unlink("../install.lock");//prevents from performing a new install
	
	//not succesful installation, let them know what was wrong
	if (!$install && !empty($error_msg)) {
		echo '<div class="alert alert-error">'.$error_msg.'</div>';
	}
	else{
		//let them know installation suceed
		?>
		<div class="alert alert-success"><?php echo T_("Congratulations").". ".T_("Installation done");?></div>
		<div class="hero-unit">
			<h1><?php _e('Installation done');?></h1>
			<p>
				<?php echo _e("Please now erase the folder");?> <code>/install/</code><br>
			
				<a class="btn btn-success btn-large" href="<?php echo $_POST['SITE_URL'];?>"><?php _e("Go to Your Website");?></a>
				
				<a class="btn btn-warning btn-large" href="<?php echo $_POST['SITE_URL'];?>/admin"">Admin</a> 
				<span class="help-block">user: <?php echo $_POST["ADMIN"];?> pass: <?php echo $_POST["ADMIN_PWD"];?></span>
				<hr>
				<a class="btn btn-primary btn-large" href="http://j.mp/ocdonate"><?php _e("Make a donation");?></a>
				<?php _e("We really appreciate it");?>.
			</p>
		</div>
		<?php
		include 'footer.php'; die();
	}
	
}