<?php $style=" id='nav_selected' ";//for the selected item?>
<div id="header">
	<h1><?php echo SITE_NAME ?></h1>
</div>

<div id="menu">
	<ul>
	<li><a 	href="<?php echo SITE_URL;?>"><?php _e("Home");?></a></li>
   <?php generateMenu($selectedCategory,"<li>","</li>");?>
   <?php if(!PARENT_POSTS){?><li><a  <?php  if(strpos($_SERVER["REQUEST_URI"], u(T_("Publish a new Ad")))>0) echo $style;?>  
   		href="<?php echo SITE_URL.newURL();?>"><?php _e("Publish a new Ad");?></a></li><?php }?>
	</ul>
</div>
<div id="content">
	<div id="left">
	 <?php 
 	if (isset($currentCategory)){//only if there's a category we create submenu
	 	generateSubMenu($idCategoryParent,$categoryParent,$currentCategory);				
	}
	else generatePopularCategories();//they did not choose a category, showing the popular categories	
   ?>