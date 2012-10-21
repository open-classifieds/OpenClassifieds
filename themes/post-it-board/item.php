<script type="text/javascript" src="<?php echo SITE_URL;?>/content/greybox/AJS.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/content/greybox/gb_scripts.js"></script>
	<h1><a title="<?php echo $itemTitle; ?>" href="<?php echo $_SERVER["REQUEST_URI"];?>">
		<?php echo $itemTitle; ?> <?if ($itemPrice!=0) echo " - ".getPrice($itemPrice);?></a>
	</h1>
<div class="item">
        <b><?php _e("Publish Date");?>:</b> <?php echo setDate($itemDate);?> <?php echo substr($itemDate,strlen($itemDate)-8);?><?php echo SEPARATOR;?>
        <b><?php _e("Contact name");?>:</b> 
        <?php
        $account=new Account($itemEmail);
        if ($account->exists){ ?>
        <a href="<?php echo SITE_URL."/".accountPostsURL($itemType,$currentCategory,$itemEmail);?>" target="_blank"><?php echo $itemName; ?></a>
        <?php 
        } else {
            echo $itemName;
        } ?>
        <?php echo SEPARATOR;?>
        <?php if ($itemLocation!="0"){?>
        <b><?php _e("Location");?>:</b> <?php echo getLocationName($itemLocation); ?><?php echo SEPARATOR;?>
        <?php }?>
	    <?php if ($itemPlace!=""){?>
		    <b><?php _e("Place");?>:</b> 
		    <?php if (MAP_KEY!=""){?>
			    <a title="Map <?php echo $itemPlace;?>" href="<?php echo SITE_URL."/".mapURL().".?address=".$itemPlace;?>" rel="gb_page_center[640, 480]"><?php echo $itemPlace;?></a>
		    <?php } else echo $itemPlace;?>
		    <?php echo SEPARATOR;?> 
	    <?php }?>
	    <?php if (COUNT_POSTS) echo "$itemViews ".T_("times displayed").SEPARATOR;?>
	    <?php if (DISQUS!=""){ ?><a href="<?php echo $_SERVER["REQUEST_URI"];?>#disqus_thread">Comments</a><?php echo SEPARATOR;?> <?php }?>
</div>
<?php if (MAX_IMG_NUM>0){?>
	<div id="item">
		<?php 
		foreach($itemImages as $img){
			echo '<a href="'.$img[0].'" title="'.$itemTitle.' '._e('Picture').'" rel="gb_imageset['.$idItem.']">
			 		<img class="thumb" src="'.$img[1].'" title="'.$itemTitle.' '._e('Picture').'" alt="'.$itemTitle.' '._e('Picture').'" /></a>';
		}
		?>
	</div>
<?php }?>
<p>
	<?php echo $itemDescription;?>
	<br /><br />

    <!-- AddThis Button BEGIN -->
    <div class="addthis_toolbox addthis_default_style">
    <a href="http://www.addthis.com/bookmark.php?v=250" class="addthis_button_compact"><?php _e("Share");?></a>
    <a class="addthis_button_facebook"></a>
    <a class="addthis_button_myspace"></a>
    <a class="addthis_button_google"></a>
    <a class="addthis_button_twitter"></a>
    <a class="addthis_button_print"></a>
    <a class="addthis_button_email"></a>
    <a href="<?php echo SITE_URL."/".contactURL();?>?subject=<?php _e('Report bad use or Spam');?>: <?php echo $itemName." (".$idItem.")";?>"><?php _e('Report bad use or Spam');?></a>
    </div>
    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
    <!-- AddThis Button END -->

</p>
<?php if (DISQUS!=""){ ?>
	<?php if (DEBUG){ ?><script type="text/javascript"> var disqus_developer = 1;</script><?php } ?>

<div id="disqus_thread"></div><script type="text/javascript" src="http://disqus.com/forums/<?php echo DISQUS;?>/embed.js"></script>
<noscript><a href="http://disqus.com/forums/<?php echo DISQUS;?>/?url=ref">View the discussion thread.</a></noscript>
<script type="text/javascript">
//<![CDATA[
(function() {
	var links = document.getElementsByTagName('a');
	var query = '?';
	for(var i = 0; i < links.length; i++) {
	if(links[i].href.indexOf('#disqus_thread') >= 0) {
		query += 'url' + i + '=' + encodeURIComponent(links[i].href) + '&';
	}
	}
	document.write('<script charset="utf-8" type="text/javascript" src="http://disqus.com/forums/<?php echo DISQUS;?>/get_num_replies.js' + query + '"></' + 'script>');
})();
//]]>
</script>
<?php } ?>
</div>