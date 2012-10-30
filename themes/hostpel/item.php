<script type="text/javascript" src="<?php echo SITE_URL;?>/content/greybox/AJS.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/content/greybox/gb_scripts.js"></script>
      <div class="single_area">
		<h1><?php echo $itemTitle; ?> <?php if ($itemPrice!=0) echo " - ".getPrice($itemPrice);?></h1>
        <p>
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
				    <a title="<?php echo  T_('Map').$itemPlace;?>" href="<?php echo SITE_URL."/".mapURL().".?address=".$itemPlace;?>" rel="gb_page_center[640, 480]"><?php echo $itemPlace;?></a>
			    <?php } else echo $itemPlace;?>
			    <?php echo SEPARATOR;?> 
		    <?php }?>
		    <?php if (COUNT_POSTS) echo "$itemViews ".T_("times displayed").SEPARATOR;?>
		    <?php if (DISQUS!=""){ ?><a href="<?php echo $_SERVER["REQUEST_URI"];?>#disqus_thread">Comments</a><?php echo SEPARATOR;?> <?php }?>
        </p>
        <?php if (MAX_IMG_NUM>0){?>
		<div id="pictures">
			<?php 
			foreach($itemImages as $img){
				echo '<a href="'.$img[0].'" title="'.$itemTitle.' '.T_('Picture').'" rel="gb_imageset['.$idItem.']">
				 		<img class="thumb" src="'.$img[1].'" title="'.$itemTitle.' '.T_('Picture').'" alt="'.$itemTitle.' '.T_('Picture').'" /></a>';
			}
			?>
			<div class="clear"></div>
		</div>
	    <?php }?>
    <div>
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
        <?php echo SEPARATOR;?><a href="<?php echo SITE_URL."/".contactURL();?>?subject=<?php _e("Report bad use or Spam");?>: <?php echo $itemName." (".$idItem.")";?>"><?php _e("Report bad use or Spam");?></a>
        </div>
        <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
        <!-- AddThis Button END -->
	</div>
  </div>
  <?php if ($itemAvailable==1){?>
	<h3 style="cursor:pointer;" onclick="openClose('contactmail');"><?php _e("Contact");?> <?php echo $itemName.': '.$itemTitle;?></h3>
	<div id="contactmail" class="contactform form" >
		<?php if ($itemPhone!=""){?><b><?php _e("Phone");?>:</b> <?php echo encode_str($itemPhone); ?><?php }?>
		<form method="post" action="" id="contactItem" onsubmit="return checkForm(this);">
		<p>
		    <label><small><?php _e("Name");?></small></label>*<br />
		    <input id="name" name="name" type="text" class="ico_person" value="<?php echo cP("name");?>" maxlength="75" onblur="validateText(this);"  onkeypress="return isAlphaKey(event);" lang="false"  /><br />
		</p>
		<p>
            <label><small><?php _e("Email");?></small></label>*<br />
		    <input id="email" name="email"  class="ico_mail" type="text" value="<?php echo cP("email");?>" maxlength="120" onblur="validateEmail(this);" lang="false"  /><br />
		</p>
		<p>
            <label><small><?php _e("Message");?></small></label>*<br />
		    <textarea rows="10" cols="50" name="msg" id="msg" onblur="validateText(this);"  lang="false"><?php echo strip_tags(stripslashes($_POST['msg']));?></textarea><br />
		</p>
		<?php if (CAPTCHA){?>
		<p>
            <label><small>Captcha*:</small></label> <br />
        	<img alt="captcha" src="<?php echo captcha::url('contact_'.$idItem);?>"><br />
            <input id="captcha" name="captcha" type="text"  onblur="validateText(this);"  lang="false" />
            <br />
            <br />
		</p>
		<?php }?>
        <p>
		<input type="hidden" name="contact" value="1" />
		<?php createCSRF('contact_'.$idItem);?>
		<input type="submit" id="submit" value="<?php _e("Contact");?>" />
		</p>
		</form> 
	</div>
	<?php } else echo "<div id='sysmessage'>".T_("This Ad is no longer available")."</div>";?>

	<span style="cursor:pointer;" onclick="openClose('remembermail');"> <?php _e("Send me an email with links to manage my Ad");?></span><br />
	<div style="display:none;" id="remembermail" >
		<form method="post" action="" id="remember" onsubmit="return checkForm(this);">
		<p>
        	<input type="hidden" name="remember" value="1" />
		<input onblur="this.value=(this.value=='') ? 'email' : this.value;"
				onfocus="this.value=(this.value=='email') ? '' : this.value;" 
		id="emailR" name="emailR" type="text" value="email" maxlength="120" onblur="validateEmail(this);" lang="false"  />
			<?php createCSRF('remember_'.$idItem);?>
			<input type="submit"  value="<?php _e("Remember");?>" />
        </p>
		</form> 
	</div>
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