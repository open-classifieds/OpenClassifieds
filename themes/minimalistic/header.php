<script type="text/javascript" src="<?php echo SITE_URL.'/themes/'.THEME;?>/jsTabs.js"></script>
<div id="header">
<?php 
if (isset($idItem)||isset($currentCategory)||isset($type)){
    echo '<p>'.SITE_NAME.'</p>';
}
else  echo '<h1>'.SITE_NAME.'</h1>';?>

<div id="menu">
  <ul id="nav">
   <?php generateMenuJS($selectedCategory,"<li>","</li>");?>
  </ul>
 </div>
</div>
 
<div id="submenu">
	<div id="submenu_left">
   		<?php generateSubMenuJS($idCategoryParent,$categoryParent,$currentCategory);  ?>
   </div>
    <?php if (NEED_OFFER){?>
	<div id="submenu_type"><b><?php _e('Filter');?></b>:
		<?php generatePostType($currentCategory,$type); ?>
	</div>
	<?php }?>
</div>

<div id="content">
<div id="left">
