<div id="contenedor">
<script type="text/javascript" src="<?php echo SITE_URL.'/themes/'.THEME;?>/jsTabs.js"></script>
<div id="header">
<div class="topr_box">

      <div class="topr_content"><a title="<?php _e("Publish a new Ad");?>" href="<?php echo SITE_URL.newURL();?>"><?php _e("Publish a new Ad"); ?></a></div>
      <div class="topr_bottom">
        <div></div>
      </div>
    </div>
<h1><?php echo SITE_NAME; ?></h1>

<div id="header_ad"></div>

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
   <div class="clear"></div>
</div>

<div id="content">
<div id="left">
