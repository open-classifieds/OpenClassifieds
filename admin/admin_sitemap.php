<?php
require_once('access.php');
require_once('header.php');
?>
<div class="page-header">
	<h1><?php _e("Sitemap Generator");?></h1>	
</div>

<a class="btn btn-primary" href="admin_sitemap.php?action=renew" onClick="return confirm('<?php _e("Are you sure");?>?');"><?php _e("Sitemap");?> <?php echo round((time()-filemtime(SITEMAP_FILE))/60,1);?> <?php _e("minutes");?></a>
<a class="btn" target="_blank" href="<?php echo SITE_URL;?>/sitemap.xml.gz"><?php _e("Open Sitemap");?></a>

<?php 
if (cG("action")=="renew") {
	$sitemap=sitemap::generate();
	echo "<textarea class='span9' rows=10>$sitemap</textarea>";
}
?>

<?php
require_once('footer.php');
?>
