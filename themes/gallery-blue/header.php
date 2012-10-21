
	<!-- Header -->
	<div id="header">
	
		<!-- Your gallery name  -->
		<h1><?php echo SITE_NAME ?></h1>
		<!-- Your gallery name end -->
		
			<!-- Your slogan -->
			<h2><?php echo $categoryName." ".T_("Classifieds"); ?></h2>
			<!-- Your slogan end -->
	
		<!-- Menu -->
		<ul id="menu">
			<li><a href="<?php echo SITE_URL;?>"><?php _e("Home");?></a></li>
                    <?php generateMenu($selectedCategory,"<li>","</li>");?>
		</ul>
		<!-- Menu end -->

	</div>
	<!-- Header end -->
        <div id="submenucat">
                               <?php 
 	if (isset($currentCategory)){//only if there's a category we create submenu
	 	generateSubMenu($idCategoryParent,$categoryParent,$currentCategory);				
	}
	else generatePopularCategories();//they did not choose a category, showing the popular categories	
   ?>

        </div>	
<hr class="noscreen" />

<div id="skip-menu"></div>
	
	<!-- Content box -->
	<div id="content-box">
		<div id="content-box-in">
		
			<!-- Content left -->
			<div id="content-box-in-left">
				<div id="content-box-in-left-in">
