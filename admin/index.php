<?php
require_once('access.php');
require_once('header.php');
?>

<div class="page-header">
	<h1><?php _e("Quick View");?></h1>
	<p>
	  <ul>
		  <li><?php _e("Version");?>: <?php echo VERSION;?></li>
		  <li><?php _e("Language");?>: <?php echo LANGUAGE;?></li>
		  <li><?php _e("Theme");?>: <?php echo THEME;?></li>
		  <li><?php echo T_("Total Ads").': '.totalAds();?></li>
		  <li><?php echo T_("Total Views").': '.totalViews();?></li>
	  </ul>
  	 	<a class="btn btn-primary" href="settings.php"><i class="icon-cog icon-white"></i> <?php _e('Settings')?></a>
   		<?php if (SAMBA){?><a class="btn btn-success" href="http://open-classifieds.com/themes">Get more themes</a><?php }?>
	</p>	
</div>

<?php if (SAMBA){?>
<div class="hero-unit">
	<h2>Need a professional Open Classifieds site?</h2>
	<p>Just for 50 EUR, commercial license, premium support, free of ads, 13 premium themes and much more.</br>
		<a class="btn btn-primary btn-large" href="http://open-classifieds.com/download/"><i class=" icon-shopping-cart icon-white"></i> Buy now!</a>
	</p>
</div>

<table class="table ">
	<thead>
		<tr>
			<th width="50%"><a href="http://open-classifieds.com/about/blog/" target="_blank"><?php _e("Blog Updates");?></a></th>
			<th width="50%"><a href="http://open-classifieds.com/forums/" target="_blank"><?php _e("Support Forum");?></a></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo '<ul>'.rssReader('http://open-classifieds.com/feed/',10,CACHE_ACTIVE,'<li>','</li>').'</ul>';?></td>
			<td><?php echo '<ul>'.rssReader('http://open-classifieds.com/forums/forum/support-2/feed/',10,CACHE_ACTIVE,'<li>','</li>').'</ul>';			?></td>
		</tr>
	</tbody>
</table>
<?php }?>

<?php
	require_once('footer.php');
?>