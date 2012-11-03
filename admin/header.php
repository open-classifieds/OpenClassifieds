<?php
////////////////////////////////////////////////////////////
//Common header for admin
////////////////////////////////////////////////////////////
require_once('../includes/bootstrap.php');
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="<?php echo substr(LANGUAGE,0,2);?>"> <!--<![endif]-->
<head>
	<meta charset="<?php echo CHARSET;?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php _e("Administration").' | '.SITE_NAME;?></title>
    <meta name="keywords" content="" >
    <meta name="description" content="" >
    <meta name="copyright" content="<?php echo (SAMBA)? 'Open Classifieds':SITE_NAME;?> <?php echo VERSION;?>" >
	<meta name="author" content="<?php echo (SAMBA)? 'Open Classifieds':SITE_NAME;?>">
	<meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>    <![endif]-->

    <link id="css_color" type="text/css" href="<?php echo SITE_URL;?>/admin/css/bootstrap.min.css" rel="stylesheet" media="screen" />

    <link id="css_color" type="text/css" href="<?php echo SITE_URL;?>/admin/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen" />
    	
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
       .settingsTitle{
      	cursor:pointer;
      }
      .settingsTable{
      	display: none;
      }
    </style>	
	<script type="text/javascript" src="<?php echo SITE_URL;?>/content/js/common.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		
  </head>

  <body>
  
	<div class="navbar navbar-fixed-top navbar-inverse">
<div class="navbar-inner">
<div class="container"><a class="brand"
	href="<?php echo SITE_URL;?>/admin/"><?php _e("Administration");?></a>
<div class="nav-collapse">
<ul class="nav">
	<li><a href="<?php echo SITE_URL;?>"><i class="icon-home icon-white"></i> <?php _e("Home");?></a></li>
	<?php
		if(strpos($_SERVER["REQUEST_URI"], "login.php")<=0){//do not display for login?>
	<li <?php if(strpos($_SERVER["REQUEST_URI"], "listing.php") !== false){?>class="active"<?php }?>>
		<a href="listing.php" title="<?php _e("Listings");?>" ><i class="icon-th-list icon-white"></i> <?php _e("Listings");?></a>
	</li>
	<li <?php if(strpos($_SERVER["REQUEST_URI"], "categories.php") !== false){?>class="active"<?php }?>>
		<a href="categories.php" title="<?php _e("Categories");?>" ><i class="icon-tags icon-white"></i> <?php _e("Categories");?></a>
	</li>
	<?php if (LOCATION){?>
		<li <?php if(strpos($_SERVER["REQUEST_URI"], "locations.php") !== false){?>class="active"<?php }?>>
			<a href="locations.php" title="<?php _e("Locations");?>" ><i class="icon-map-marker icon-white"></i></a>
		</li>
	<?php }?>
	<?php if (LOGON_TO_POST){?>
		<li <?php if(strpos($_SERVER["REQUEST_URI"], "accounts.php") !== false){?>class="active"<?php }?>>
			<a href="accounts.php" title="<?php _e("Accounts");?>" ><i class="icon-user icon-white"></i></a>
		</li>
	<?php }?>
	<li <?php if(strpos($_SERVER["REQUEST_URI"], "stats.php") !== false){?>class="active"<?php }?>>
		<a href="stats.php" title="<?php _e("Site Statistics");?>" ><i class="icon-align-left icon-white"></i></a>
	</li>
	<li <?php if(strpos($_SERVER["REQUEST_URI"], "settings.php") !== false){?>class="active"<?php }?>>
		<a href="settings.php" title="<?php _e("Settings");?>" ><i class="icon-cog icon-white"></i></a>
	</li>
</ul>

<div class="btn-group pull-right">
	<a class="btn btn-success" href="logout.php">
		<i class="icon-off icon-white"></i> <?php _e("Logout");?>
	</a>
</div>
<?php } else echo '</ul>'?>

</div>
<!--/.nav-collapse --></div>
</div>
</div>    <div class="container">
	    <div class="row">
	    
	    <?php
		if(strpos($_SERVER["REQUEST_URI"], "login.php")<=0){//do not display for login?>
		<div class="span3">
	<div class="well sidebar-nav">
		<ul class="nav nav-list">
			<li class="nav-header"><?php _e("Administration");?></li>
			<li <?php if(strpos($_SERVER["REQUEST_URI"], "listing.php") !== false){?>class="active"<?php }?>>
				<a href="listing.php" title="<?php _e("Listings");?>" ><?php _e("Listings");?></a>
			</li>
			<li <?php if(strpos($_SERVER["REQUEST_URI"], "categories.php") !== false){?>class="active"<?php }?>>
				<a href="categories.php" title="<?php _e("Categories");?>" ><?php _e("Categories");?></a>
			</li>
			<li <?php if(strpos($_SERVER["REQUEST_URI"], "locations.php") !== false){?>class="active"<?php }?>>
				<a href="locations.php" title="<?php _e("Locations");?>" ><?php _e("Locations");?></a>
			</li>
			<li <?php if(strpos($_SERVER["REQUEST_URI"], "accounts.php") !== false){?>class="active"<?php }?>>
				<a href="accounts.php" title="<?php _e("Accounts");?>" ><?php _e("Accounts");?></a>
			</li>
			
			<li class="nav-header"><?php _e("Settings");?></li>
			<li <?php if(strpos($_SERVER["REQUEST_URI"], "settings.php") !== false){?>class="active"<?php }?>>
				<a href="settings.php" title="<?php _e("Settings");?>" ><?php _e("Settings");?></a>
			</li>
			<li <?php if(strpos($_SERVER["REQUEST_URI"], "stats.php") !== false){?>class="active"<?php }?>>
				<a href="stats.php" title="<?php _e("Site Statistics");?>" ><?php _e("Site Statistics");?></a>
			</li>
			<li  <?php if(strpos($_SERVER["REQUEST_URI"], "optimize.php") !== false){?>class="active"<?php }?>>
				<a href="optimize.php" ><?php _e("Tools to Optimize");?></a>
			</li>
            <li  <?php if(strpos($_SERVER["REQUEST_URI"], "admin_sitemap.php") !== false){?>class="active"<?php }?>>
				<a href="admin_sitemap.php"><?php _e("Sitemap");?> &amp; robots.txt</a>
			</li>
			<li class="divider"></li>
			<?php if (SAMBA){?>
			<li class="nav-header">Open Classifieds</li>
			<li><a href="http://open-classifieds.com/themes/">Themes</a></li>
			<li><a href="http://open-classifieds.com/support/">Support</a></li>
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
<!--/span-->	<?php } ?>
			<div class="span9">