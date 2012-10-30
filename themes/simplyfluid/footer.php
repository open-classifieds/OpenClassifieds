</div>
	<div id="right">
		<?php getSideBar("","<br />");?>
	</div>
</div>

<div id="footer">
	<p>
  &copy; 
<?php if (SAMBA){?>
	<!-- Open Classifieds License. To remove please buy professional vesion here: http://j.mp/ocdownload  -->
	<a href="http://open-classifieds.com" title="Open Source PHP Classifieds">Open Classifieds</a> 2009 - 
<?php } else {?>
	<a href="<?php echo SITE_URL;?>"><?php echo SITE_NAME;?></a>
<?php }?>

<?php echo date('Y')?>
- 
Theme <a rel="nofollow" href="http://www.freecsstemplates.org" title="SimplyFluid">SimplyFluid</a>
</p>
</div>
