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
	<p>Just for $69.90, commercial license, premium support, free of ads, 13 premium themes and much more.</br>
		<a class="btn btn-primary btn-large" href="http://open-classifieds.com/download/"><i class=" icon-shopping-cart icon-white"></i> Buy now!</a>
	</p>
</div>

<table class="table ">
	<thead>
		<tr>
			<th width="50%"><a href="http://open-classifieds.com/about/blog/" target="_blank"><?php _e("Blog Updates");?></a></th>
			<th width="50%"><a href="http://open-classifieds.com/themes" target="_blank"><?php _e("Themes");?></a></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo '<ul>'.rssReader('http://open-classifieds.com/feed/',10,CACHE_ACTIVE,'<li>','</li>').'</ul>';?></td>
			<td>
				<ul>
	<li><a title="Demo Open Classifieds" href="http://open-classifieds.com/demo/?theme=kamaleon">Kamaleon</a></li>
	<li><a title="Demo Open Classifieds" href="http://open-classifieds.com/demo/?theme=responsive">Responsive</a></li>
	<li><a title="Demo Open Classifieds" href="http://open-classifieds.com/demo/?theme=moderndeluxe">Moderndeluxe</a></li>
	<li><a title="Demo Open Classifieds" href="http://open-classifieds.com/demo/?theme=yenii">yenii</a></li>
	<li><a title="Demo Open Classifieds" href="http://open-classifieds.com/demo/?theme=wpClassifieds">wpClassifieds</a></li>
	<li><a title="Demo Open Classifieds hostpel" href="http://open-classifieds.com/demo/?theme=hostpel">hostpel</a></li>
	<li><a title="Demo Open Classifieds Primitive" href="http://open-classifieds.com/demo/?theme=primitive">Primitive</a></li>
	<li><a title="Demo Open Classifieds Simply Fluid" href="http://open-classifieds.com/demo/?theme=simplyfluid">Simply Fluid</a></li>
	<li><a title="Demo Open Classifieds Edit_80" href="http://open-classifieds.com/demo/?theme=edit_80">Edit_80</a></li>
	<li><a title="Demo Open Classifieds anunciamex" href="http://open-classifieds.com/demo/?theme=anunciamex">anunciamex</a></li>
	<li><a title="Demo Open Classifieds aqueous" href="http://open-classifieds.com/demo/?theme=aqueous">aqueous</a></li>
	<li><a title="Demo Open Classifieds aqueous" href="http://open-classifieds.com/demo/?theme=gallery-blue">Gallery Blue</a></li>
	<li><a href="http://open-classifieds.com/demo/?theme=post-it-board">Post it Board</a></li>
	</ul>
			</td>
		</tr>
	</tbody>
</table>
<?php }?>

<?php
	require_once('footer.php');
?>