<script type="text/javascript" src="<?php echo SITE_URL.'/themes/'.THEME;?>/jsTabs.js"></script>

<div id="wrapper">
<div id="innerwrapper">

	<div id="header">
			<?php 
			if (cG("s")=="") $ws=T_('Search')."...";
			else $ws=cG("s");
			$searchf= "<form method=\"get\" action=\"".SITE_URL."\">
				<input name=\"s\" id=\"s\" maxlength=\"15\" title=\"".T_('Search')."\"
					onblur=\"this.value=(this.value=='') ? '$ws' : this.value;\" 
					onfocus=\"this.value=(this.value=='$ws') ? '' : this.value;\" 
					value=\"$ws\" type=\"text\" />";
			
			if(isset($categoryName)) $searchf.='<input type="hidden" name="category" value="'.$categoryName.'" />';
				
			$searchf.='</form>';
			echo $searchf;
			?>
			
			
			<h1><a title="<?php echo getCategories().SEPARATOR.SITE_NAME;?>" href="<?php echo SITE_URL;?>"><?php echo SITE_NAME; ?></a></h1>
			<h2><a title="<?php _e("Publish a new Ad");?>" href="<?php echo SITE_URL.newURL();?>"><?php _e("Publish a new Ad");?></a></h2>  
			<ul id="nav">
				<?php generateMenuJS($selectedCategory,"<li>","</li>");?>
			</ul>
			<ul id="subnav">
				<?php generateSubMenuJS($idCategoryParent,$categoryParent,$currentCategory);  ?>
			</ul>
	
	</div>
		
	<div id="sidebar">
		<b><?php _e("Filter");?></b>: <?php generatePostType($currentCategory,$type); ?>
		<?php getSideBar("","");?>
	</div>
	
	<div id="sidebarright">
		<h2>Links</h2>
	</div>
		
	<div id="content">
