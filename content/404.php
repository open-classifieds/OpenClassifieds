<?php require_once('header.php');

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/404.php')){//404 from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/404.php'); 
}
else{//not found in theme

echo "<h1>".u(urldecode($_SERVER["REQUEST_URI"]))."</h1>".T_("Nothing found");
?>
<br /> 
<br /> 
<b><?php _e("Advanced Search");?></b>
<div class="item">
<?php advancedSearchForm();?>
</div>
<?php

}//if else

require_once('footer.php');
?>