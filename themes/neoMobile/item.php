<h1><a title="<?php echo $itemTitle; ?>" href="<?php echo $_SERVER["REQUEST_URI"];?>">
			<?php echo $itemTitle; ?> <?if ($itemPrice!=0) echo " - ".getPrice($itemPrice);?></a>
</h1>
	<div class="item">
	<p>
		<b><?php echo _("Publish Date");?>:</b> <?php echo setDate($itemDate);?> <?php echo substr($itemDate,strlen($itemDate)-8);?><?php echo SEPARATOR;?>
        <b><?php echo _("Contact name");?>:</b> 
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
        <b><?php echo _("Location");?>:</b> <?php echo getLocationName($itemLocation); ?><?php echo SEPARATOR;?>
        <?php }?>
		<?php if ($itemPlace!=""){?>
			<b><?php echo _("Place");?>:</b> 
			<?php if (MAP_KEY!=""){?>
				<a title="Map <?php echo $itemPlace;?>" href="<?php echo SITE_URL."/".mapURL()."?address=".$itemPlace;?>" rel="gb_page_center[640, 480]"><?php echo $itemPlace;?></a>
			<?php } else echo $itemPlace;?>
			<?php echo SEPARATOR;?> 
		<?php }?>
		<?php if (COUNT_POSTS) echo "$itemViews "._("times displayed").SEPARATOR;?>
		<?php if (DISQUS!=""){ ?><a href="<?php echo $_SERVER["REQUEST_URI"];?>#disqus_thread"><?php echo _("Comments");?></a><?php echo SEPARATOR;?> <?php }?>
	</p>	
	</div>
<?php if (MAX_IMG_NUM>0){?>
		<div id="item">
			<?php 
			if ($itemImages) foreach($itemImages as $img){
				echo '<a href="'.$img[0].'" title="'.$itemTitle.' '._("Picture").'" ">
				 		<img class="thumb" src="'.$img[1].'" title="'.$itemTitle.' '._("Picture").'" alt="'.$itemTitle.' '._("Picture").'" /></a>';
			}
			?>
		</div>
	<?php }?>
<p><?php echo $itemDescription;?></p>
	<?php if ($itemAvailable==1){?>
	<b><?php echo _("Contact");?> <?php echo $itemName.': '.$itemTitle;?></b>
	<div id="contactmail">
		<?php if ($itemPhone!=""){?><b><?php echo _("Phone");?>:</b> <?php echo encode_str($itemPhone); ?><?php }?>
		<form method="post" action="" id="contactItem" onsubmit="return checkForm(this);">
		<p>
		<?php echo _("Your Name");?>*:<br />
		<input id="name" name="name" type="text" value="<?php echo cP("name");?>" maxlength="75" onblur="validateText(this);"  onkeypress="return isAlphaKey(event);" lang="false"  /><br />
		<?php echo _("Email");?>*:<br />
		<input id="email" name="email" type="text" value="<?php echo cP("email");?>" maxlength="120" onblur="validateEmail(this);" lang="false"  /><br />
		<?php echo _("Message");?>*:<br />
		<textarea rows="10" cols="79" name="msg" id="msg" onblur="validateText(this);"  lang="false"><?php echo strip_tags(stripslashes($_POST['msg']));?></textarea><br />
		<?php if (CAPTCHA){?>
            Captcha*:<br />
        	<?php echo captcha::image_tag('contact_'.$idItem);?><br />
            <input id="captcha" name="captcha" type="text"  onblur="validateText(this);"  lang="false" />
        <?php }?>
		<br />
		<br />
		<input type="hidden" name="contact" value="1" />
        <?php createCSRF('contact_'.$idItem);?>
		<input type="submit" id="submit" value="<?php echo _("Contact");?>" />
		</p>
		</form> 
	</div>
	<?php } else echo "<div id='sysmessage'>"._("This Ad is no longer available")."</div>";?>
