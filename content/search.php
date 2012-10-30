<?php
require_once('header.php');

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/search.php')){//search from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/search.php'); 
}
else{//not found in theme

?>
<h2><?php _e("Advanced Search");?></h2>
<div class="item">
<?php advancedSearchForm();?>
</div>
<?php
}//if else

require_once('footer.php');
?>