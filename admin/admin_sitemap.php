<?php
require_once('access.php');
require_once('header.php');
?>
<div class="page-header">
	<h1><?php _e("Sitemap Generator");?> &amp; robots.txt</h1>
</div>

<a class="btn btn-primary" href="admin_sitemap.php?action=renew" onclick="return confirm('<?php _e("Are you sure");?>?');"><?php _e("Generate Sitemap");?> <?php echo round((time()-filemtime(SITEMAP_FILE))/60,1);?> <?php _e("minutes");?></a>
<a class="btn" target="_blank" href="<?php echo SITE_URL;?>/sitemap.xml.gz"><?php _e("Open Sitemap");?></a>
<br /><br />

<?php
if (cG("action")=="renew") {
	if (($sitemap=sitemap::generate())!==false) {
		echo "<textarea class='span9' rows=10>$sitemap</textarea>";
	} else {
		echo '<div class="alert alert-error">'.T_("Cannot write to the configuration file")." '".SITEMAP_FILE."'".'</div>';
	}
}
?>
<br /><br />

<a class="btn btn-primary" href="admin_sitemap.php?action=robots_gen" onclick="return confirm('<?php _e("Are you sure");?>?');"><?php _e("Regenerate robots.txt");?></a>
<a class="btn" target="_blank" href="<?php echo SITE_URL;?>/robots.txt"><?php _e("Open robots.txt");?></a>
<br /><br />

<?php
// Get the default content that will be displayed in the textarea
if (cG("action")=="robots_gen") {
	if (($robots_content=regenerateRobots(SITE_URL))!==false) {
		echo '<div class="alert alert-success">'.T_("File successfully regenerated and written").'</div>';
	} else {
		echo '<div class="alert alert-error">'.T_("Cannot write to the configuration file")." '".SITE_ROOT."/robots.txt'".'</div>';
		$robots_content=''; // to replace the 'false' value
	}
}
elseif (cP("action")=="robots_save") {
	$robots_content = str_replace(array("\r\n", "\r"), "\n", $_POST["robots_content"]);
}
else $robots_content = oc::fread('../robots.txt');
?>

<form method="post" action="admin_sitemap.php">
<textarea id="robots_content" name="robots_content" class="span9" rows="10"><?php echo $robots_content;?></textarea>
<input type="hidden" name="action" value="robots_save">
<input type="submit" value="<?php _e("Save file");?> /robots.txt" class="btn btn-success" onclick="return confirm('<?php _e("Are you sure");?>?');">
</form>

<?php
if (cP("action")=="robots_save") {

	if ( oc::fwrite('../robots.txt', $robots_content) ) {
		echo '<div class="alert alert-success">'.T_("File successfully written").'</div>';
	} else {
		echo '<div class="alert alert-error">'.T_("Cannot write to the configuration file")." '".SITE_ROOT."/robots.txt'".'</div>';
	}
}
?>

<?php
require_once('footer.php');
?>