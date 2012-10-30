        
        </div><!--left-->
        <div id="sidebar">
                   <ul id="sidewid">
      <?php getSideBar("<li>","</li>");?>
      </ul>
    </div><!--sidebar-->
    </div><!--content-->
<div id="footer">
          <ul class="pages">
	    <?php if(FRIENDLY_URL) {?>
		    <li><a href="<?php echo SITE_URL."/".u(T_("Advanced Search"));?>.htm"><?php _e("Advanced Search");?></a></li>
		    <li><a href="<?php echo SITE_URL."/".u(T_("Sitemap"));?>.htm"><?php _e("Sitemap");?></a></li>   
		    <li><a href="<?php echo SITE_URL."/".u(T_("Privacy Policy"));?>.htm"><?php _e("Privacy Policy");?></a></li>
	    <?php }else { ?>
	        <li><a href="<?php echo SITE_URL;?>/content/search.php"><?php _e("Advanced Search");?></a></li>
	        <li><a href="<?php echo SITE_URL;?>/content/site-map.php"><?php _e("Sitemap");?></a></li>
		    <li><a href="<?php echo SITE_URL;?>/content/privacy.php"><?php _e("Privacy Policy");?></a></li>
	    <?php } ?>
	    <li><a href="<?php echo SITE_URL."/".contactURL();?>"><?php _e("Contact");?></a></li>
	    <li><a href="<?php echo SITE_URL.newURL();?>"><?php _e("Publish a new Ad");?></a></li>
	</ul>
    <p>
  &copy; 
<?php if (SAMBA){?>
	<!-- Open Classifieds License. To remove please buy professional vesion here: http://j.mp/ocdownload  -->
	<a href="http://open-classifieds.com" title="Open Source PHP Classifieds">Open Classifieds</a> 2009 - 
<?php } else {?>
	<a href="<?php echo SITE_URL;?>"><?php echo SITE_NAME;?></a>
<?php }?>

<?php echo date('Y')?>
</p>
</div><!--footer-->
</div><!--inner-->
</div><!--wrap-->

