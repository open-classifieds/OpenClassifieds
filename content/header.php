<?php
////////////////////////////////////////////////////////////
//Common header for all the themes
////////////////////////////////////////////////////////////
//die(__DIR__);
if ( !defined('__DIR__') ) define('__DIR__', dirname(__FILE__));
require_once(__DIR__.'/../includes/bootstrap.php');//@todo

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/header-full.php')){//all the header
	require_once(SITE_ROOT.'/themes/'.THEME.'/header-full.php'); 
}
else{//not found in theme
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo substr(LANGUAGE,0,2);?>" lang="<?php echo substr(LANGUAGE,0,2);?>">
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
		<title><?php echo $html_title;?></title>
		<meta name="title" content="<?php echo $html_title;?>" />
		<meta name="description" content="<?php echo $html_description;?>" />
		<meta name="keywords" content="<?php echo $html_keywords;?>" />		
		<meta name="generator" content="<?php echo (SAMBA)? 'Open Classifieds':SITE_NAME;?> <?php echo VERSION;?>" />
		<link rel ="author" href="<?php echo SITE_URL;?>/humans.txt" />
		<link rel="shortcut icon" href="<?php echo SITE_URL;?>/favicon.ico" />
	<?php if (isset($currentCategory) || isset($type) || isset($location) ){?>
		<link rel="alternate" type="application/rss+xml" title="<?php _e("Latest Ads");?> 
		<?php echo ucwords($currentCategory);?> <?php echo ucwords(getTypeName($type));?> <?php echo getLocationName($location);?>"
		href="<?php echo rssURL().'?category='.$currentCategory.'&amp;type='.$type.'&amp;location='.$location;?>" />
	<?php }?>
		<link rel="alternate" type="application/rss+xml" title="<?php _e("Latest Ads");?>" href="<?php echo SITE_URL;?>/rss/" />
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/themes/<?php echo THEME;?>/style.css" media="screen" />
	<?php if (strpos(SIDEBAR,'categories_cloud')){?>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/themes/wordcloud.css" media="screen" />
	<?php }?>
	<?php if (isset($idItem)) {//only in the item the greybox?>
		<script type="text/javascript">var GB_ROOT_DIR = "<?php echo SITE_URL;?>/content/greybox/";</script>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/content/greybox/gb_styles.css" media="screen" />
	<?php }?>
		<script type="text/javascript" src="<?php echo SITE_URL;?>/content/js/common.js"></script>
	<?php if (ANALYTICS!=""){?>
        <script type="text/javascript">
          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', '<?php echo ANALYTICS;?>']);
          _gaq.push(['_trackPageview']);
          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
        </script>
    <?php }?>
</head>
<body>
<?php require_once(SITE_ROOT.'/themes/'.THEME.'/header.php');?>
<!--googleoff: index-->
<noscript>
	<div style="height:30px;border:3px solid #6699ff;text-align:center;font-weight: bold;padding-top:10px">
		Your browser does not support JavaScript!
	</div>
</noscript>
<!--googleon: index-->
<?php if (ADVERT_TOP!='') echo ADVERT_TOP;?>
<?php 
}
?>