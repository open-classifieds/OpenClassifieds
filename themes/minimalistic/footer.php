</div>
	
<div id="right">
	<div class="box">
		<script type="text/javascript">google_ad_client = "pub-9818256176049741";google_ad_slot = "4162447127";google_ad_width = 250;google_ad_height = 250;</script><script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
	</div>
	<?php getSideBar("<div class='box'>","</div>");?>
</div>

<?php if (SAMBA){?>
<script type="text/javascript">google_ad_client = "pub-9818256176049741";google_ad_slot = "5864321500"; google_ad_width = 728;google_ad_height = 15;</script><script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
<?php }?>

<div id="footer">
  &copy; 
<?php if (SAMBA){?>
	<!-- Open Classifieds License. To remove please buy professional vesion here: http://j.mp/ocdownload  -->
	<a href="http://open-classifieds.com" title="Open Source PHP Classifieds">Open Classifieds</a> 2009 - 
<?php } else {?>
	<a href="<?php echo SITE_URL;?>"><?php echo SITE_NAME;?></a>
<?php }?>

<?php echo date('Y')?>
</div>
