<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL; ?>/themes/wpClassifieds/jsclass.js"></script>
<?php 
//getting the title in two parts/colors
$pos=strpos(SITE_NAME," ");
$firstH=substr(SITE_NAME,0,$pos);//first part of the site name in green
$secondH=substr(SITE_NAME,$pos);//second part of the name un blue
?>

<div class="container_12" id="wrap">
  <div class="grid_12" id="header">
    <div id="logo"> 
      <h4><a href="<?php echo SITE_URL; ?>" title="<?php echo SITE_NAME; ?>"><span class="firstH"><?php echo $firstH; ?></span><span class="secondH"><?php echo $secondH; ?></span></a></h4> 
      <p><?php echo '<a title="'.T_("Publish a new Ad").'" href="'.SITE_URL.newURL().'">'.T_("Publish a new Ad").'</a>';?></p>
      <div class="clear"></div>
    </div>
  </div>
  <div class="clear"></div>
  <div class="grid_12" id="top_dropdown">
    <ul id="nav">
    <?php generateMenuJS($selectedCategory);?>
    </ul>
  </div>
  <div class="clear"></div>
  <div class="grid_12" style="position:static;" id="top_cats">
    <?php generateSubMenuJS($idCategoryParent,$categoryParent,$currentCategory);  ?>
  </div>
  <div class="clear"></div>
  <div id="content">
     <div class="grid_12">
      <div class=" breadcrumb">
            <?php if(isset($categoryName)&&isset($categoryDescription)){ ?>
			    <?php echo $categoryDescription;?>
			    <a title="<?php _e("Post Ad in");?> <?php echo $categoryName;?>" href="<?php echo SITE_URL.newURL();?>"><?php _e("Post Ad in");?> <?php echo $categoryName;?></a> 
	        <?php }            
	            else echo strftime("%A %e %B %Y");
	        ?>
	        <?php if (NEED_OFFER){?>
            <div style="float:right;"><b><?php _e("Filter");?></b>:
		    <?php generatePostType($currentCategory,$type); ?>
		    </div>
		    <?php }?>
		</div>
    </div>
    <div class="clear"></div>
       <div class="grid_8" id="content_main">
   