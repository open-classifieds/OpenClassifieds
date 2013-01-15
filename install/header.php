<?php 
//prevents from new install to be done
if(!file_exists('../install.lock')) die('Installation seems to be done');

if ( !defined('__DIR__') ) define('__DIR__', dirname(__FILE__));

define('SITE_ROOT',substr(__DIR__,0,-8));
//die(SITE_ROOT);
define('VERSION','1.8.5');
define('DEBUG', false);
define('CHARSET', 'UTF-8');

require_once('../includes/classes/oc.php');
require_once('../includes/common.php');//adding common library
oc::start();
require_once('../includes/seo.php');

if (isset($_POST["LANGUAGE"])) $locale_language=$_POST["LANGUAGE"];
elseif (isset($_GET["LANGUAGE"])) $locale_language=$_GET["LANGUAGE"];
else  $locale_language='en_EN';
i18n::load($locale_language,'messages','../languages/',CHARSET);

 // Try to guess installation path
    $suggest_path = substr(__FILE__,0,-18);
    $suggest_path = str_replace("\\","/",$suggest_path);

    // Try to guess installation URL
    $suggest_url = 'http://'.$_SERVER["SERVER_NAME"];
    if ($_SERVER["SERVER_PORT"] != "80") $suggest_url = $suggest_url.":".$_SERVER["SERVER_PORT"];
    //getting the folder, erasing the install
    $suggest_url .=str_replace('/install/index.php','', $_SERVER["SCRIPT_NAME"]);
 
    function hostingAd()
    {
        if (SAMBA){
        ?>
        <div class="alert alert-info">We have 100% compatible hosting, starting from $3 montlhy.
    	    <a class="btn btn-info" href="http://open-classifieds.com/hosting/">
    	        <i class="icon-ok icon-white"></i> Host now!
    	    </a>
	    </div>
        <?php }
    }
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en>"> <!--<![endif]-->
<head>
	<meta charset="utf8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo ucfirst(T_("installation"));?></title>
    <meta name="keywords" content="" >
    <meta name="description" content="" >
    <meta name="copyright" content="<?php echo (SAMBA)? 'Open Classifieds':SITE_NAME;?> <?php echo VERSION;?>" >
	<meta name="author" content="<?php echo (SAMBA)? 'Open Classifieds':SITE_NAME;?>">
	<meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>    <![endif]-->
    
    <link type="text/css" href="../admin/css/bootstrap.min.css" rel="stylesheet" media="screen" />	
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
    </style>
    
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>	
	<script type="text/javascript" src="../content/js/common.js"></script>	
	
	<script type="text/javascript">
			function redirectLang(cvalue){
				window.location = '<?php echo $_SERVER["SCRIPT_NAME"];?>?LANGUAGE='+cvalue;
			}
		</script>
  </head>

  <body>
  
	<div class="navbar navbar-fixed-top navbar-inverse">
<div class="navbar-inner">
<div class="container"><a class="brand"><?php echo ucfirst(T_("installation"));?></a>
<div class="nav-collapse">

<?php if (SAMBA){?>
<div class="btn-group pull-right">
	<a class="btn btn-primary" href="http://open-classifieds.com/download/">
		<i class="icon-shopping-cart icon-white"></i> We install it for you, Buy now!
	</a>
</div>
<?php }?>

</div>
<!--/.nav-collapse --></div>
</div>
</div>    <div class="container">
	    <div class="row">
	    
		<div class="span3">
	<div class="well sidebar-nav">
	
		<ul class="nav nav-list">
			<?php $succeed=true; $msg='';?>
			<li class="nav-header"><?php _e("Requirements");?></li>
			<li class="divider"></li>
			
			<li class="nav-header"><?php _e("Server software");?></li>
			
			<?php
				$php_compat     = version_compare( phpversion(), '5.2.4', '>=' );

				if( !$php_compat){
					$msg .= T_("Please upgrade")." PHP ".T_("in order to proceed");
					$succeed=false;
					$test = false;
				}
				else $test = true;
				?>
			<li><i class="icon-<?php if($test) echo "ok"; else echo "remove";?>"></i> 
				PHP <?php _e("version");?> 5.2.4+
			</li>
			
			<?php 
				if (!extension_loaded('mysql')){
				    $msg.= T_("Please install")." MySQL ".T_("in order to proceed");
				    $succeed=false;
					$test = false;
				}
				else $test = true;
			?>
			<li><i class="icon-<?php if($test) echo "ok"; else echo "remove";?>"></i> 
				MySQL 5.1+
			</li>
			
			<?php 
			if (!extension_loaded('curl')){
				$msg.= T_("Please install")." Curl ,".T_("it is strongly recommended to install it");
			    $succeed=true;
			    $test = false;
			}
			else $test = true;
			?>
			<li><i class="icon-<?php if($test) echo "ok"; else echo "remove";?>"></i> 
				CURL
			</li>
				
			<?php 
			if(function_exists('apache_get_modules'))
			{
			    if (in_array('mod_rewrite',apache_get_modules())) $test = true;
			    else {
			    	$msg = T_("Please install")." mod_rewrite ".T_("in order to proceed");
			    	$succeed=false;
			    	$test = false;
			    }
			}
			else {
				$msg = 'mod_rewrite '.T_("Not found").', '.T_("Cannot check if installed");
				$test = false;
			}
			?>
			<li><i class="icon-<?php if($test) echo "ok"; else echo "remove";?>"></i> 
				<?php _e("URL rewriting");?>
			</li>

			<?php 
			if ( !extension_loaded('gd') || !function_exists('gd_info') ){
    			$msg = T_("Please install")." GD ".T_("in order to proceed");
			    	$succeed=false;
			    	$test = false;
			}
			else $test = true;
			?>
			<li><i class="icon-<?php if($test) echo "ok"; else echo "remove";?>"></i> 
				<?php _e("Image support");?> 
			</li>
			
			<?php 
			if ( !extension_loaded('gettext') || !function_exists('_') ){
    			$msg.= 'Gettext '.T_("Not found").', '.T_("it is strongly recommended to install it");
				$succeed=true;
			    $test = false;
			}
			else $test = true;
			?>
			<li><i class="icon-<?php if($test) echo "ok"; else echo "remove";?>"></i> 
				<?php _e("Language support");?> 
			</li>
			<li class="divider"></li>
			<li class="nav-header"><?php _e("Writeable folders");?></li>
			<li><i class="icon-<?php if(!is_writable('../includes/config.php')) { echo "remove";$succeed=false;$msg.='../includes/config.php';} else echo "ok";?>"></i> 
				/includes/config.php
			</li>
			<li><i class="icon-<?php if(!is_writable('../.htaccess')) { echo "remove";$succeed=false;$msg.='../.htaccess';} else echo "ok";?>"></i> 
				/.htaccess
			</li>
			<li><i class="icon-<?php if(!is_writable('../robots.txt')) { echo "remove";$succeed=false;$msg.='../robots.txt';} else echo "ok";?>"></i> 
				/robots.txt
			</li>
			<li><i class="icon-<?php if(!is_writable('../sitemap.xml.gz')) { echo "remove";$succeed=false;$msg.='../sitemap.xml.gz';} else echo "ok";?>"></i> 
				/sitemap.xml.gz
			</li>
			<li><i class="icon-<?php if(!is_writable('../images')) { echo "remove";$succeed=false;$msg.='../images';} else echo "ok";?>"></i> 
				/images
			</li>
			<li><i class="icon-<?php if(!is_writable('../cache')) { echo "remove";$succeed=false;$msg.='../cache';} else echo "ok";?>"></i> 
				/cache
			</li>
			<li class="divider"></li>
			<li><a href="phpinfo.php"><?php _e("PHP Info");?></a></li>
			<li class="divider"></li>
			<?php if (SAMBA){?>
			<li class="nav-header">Open Classifieds</li>
			<li><a href="http://open-classifieds.com/themes/">Themes</a></li>
			<li><a href="http://open-classifieds.com/download/">Support</a></li>
			<li><a href="http://j.mp/ocdonate" target="_blank">
					<img src="http://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" alt="">
			</a></li>
			<li class="divider"></li>
			<?php }?>
		</ul>
		<?php if (SAMBA){?>
		<a href="https://twitter.com/openclassifieds"
				onclick="javascript:_gaq.push(['_trackEvent','outbound-widget','http://twitter.com']);"
				class="twitter-follow-button" data-show-count="false"
				data-size="large">Follow @openclassifieds</a><br />
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		<?php }?>
		
	</div>
	<!--/.well -->
</div>
<!--/span-->	
			<div class="span9">
