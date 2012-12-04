<?php
require_once('header.php');

if (LOGON_TO_POST){
    $account = Account::createBySession();
    if ($account->exists){
        $name = $account->name;
        $email = $account->email;
    } 
    else redirect(accountLoginURL());
}

if (!isInSpamList(oc::get_ip())){//no spammer
	if ($_POST && checkCSRF('newitem') ){	
		newPost();
	}//if post else die('something went wrong');
	
	if (file_exists(SITE_ROOT.'/themes/'.THEME.'/item-new.php')){//item-new from the theme!
    	require_once(SITE_ROOT.'/themes/'.THEME.'/item-new.php'); 
    }
    else{//not found in theme
?>
<h3><?php _e("Publish a new Ad");?> <?php echo $categoryName;?></h3>
<form action="" method="post" onsubmit="return checkForm(this);" enctype="multipart/form-data">
	<?php _e("Category");?>:<br />

	<?php 
	if (is_numeric(cP("category"))) $selectedCategory=cP("category");
		else $selectedCategory=$idCategory;
		
	if (PAYPAL_ACTIVE)	{
		$script = "onchange=\"redirectCategory(this.value)\"";
		if (PAYPAL_AMOUNT_CATEGORY)	$extra_sql = "concat(name,' - ',price,' ".PAYPAL_CURRENCY."')";
		else $extra_sql = "concat(name,' - ',".PAYPAL_AMOUNT.",' ".PAYPAL_CURRENCY."')";
	}
	else
	{
		$script = "onChange=\"validateNumber(this);\"";
		$extra_sql = 'name';
	}

	if (PARENT_POSTS) $parent_posts = '';
	else $parent_posts = "where C.idCategoryParent!=0"; 
	
		$query="SELECT idCategory,$extra_sql name,
						(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent) 
						FROM ".TABLE_PREFIX."categories C 
				".$parent_posts."
				order by idCategoryParent, `order`";
	
	
		sqlOptionGroupScript($query,"category",$selectedCategory,"","class=\"span4\" ".$script);
	?>
	<br />
	<?php _e("Title");?>*:<br />
	<input style="width:520px;" id="title" name="title" type="text" value="<?php echo $_POST["title"];?>" maxlength="120" onblur="validateText(this);"  lang="false" />
	<input style="width:50px;" id="price" name="price" type="text" value="<?php echo $_POST["price"];?>" maxlength="8"  onkeypress="return isNumberKey(event);"   /><?php echo CURRENCY;?><br />
	<br />
	<?php _e("Description");?>*:<br />

	<?php if (HTML_EDITOR){?>
        <script>!window.jQuery && document.write(unescape('%3Cscript src="http://code.jquery.com/jquery-1.8.2.min.js"%3E%3C/script%3E'))</script>
		<link rel="stylesheet" href="<?php echo SITE_URL; ?>/content/js/sceditor.css" type="text/css" media="all" />
		<script type="text/javascript"    src="<?php echo SITE_URL; ?>/content/js/jquery.sceditor.min.js"></script>

		<textarea rows="10" cols="73" name="description" id="description"><?php echo stripslashes($_POST['description']);?></textarea>


        <script>
            $(document).ready(function() {
                $('textarea[name=description]').sceditor({
                    toolbar: "bold,italic,underline|bulletlist,orderedlist|left,center,right|email,link,unlink",
                    resizeEnabled: "true"
                });
            });
        </script>

	<?php }
	    else{?>
		<textarea rows="10" cols="73" name="description" id="description" onblur="validateText(this);"  lang="false"><?php echo strip_tags($_POST['description']);?></textarea><?php }?>
	<br />	
	<?php if (NEED_OFFER){?>
	<?php _e("Type");?>:<br />
	<select id="type" name="type">
		<option value="<?php echo TYPE_OFFER;?>"><?php _e("offer");?></option>
		<option value="<?php echo TYPE_NEED;?>"><?php _e("need");?></option>
	</select>
	<br />
	<?php }?>
	
	<?php if (LOCATION){?>
    <?php _e("Location");?>:<br />
	<?php 
    $query="SELECT idLocation,name,(select name from ".TABLE_PREFIX."locations where idLocation=C.idLocationParent) FROM ".TABLE_PREFIX."locations C order by idLocationParent, idLocation";
	sqlOptionGroup($query,"location",$location);
	?>
    <?php }?>
    <br />
	<?php _e("Place");?>:<br />
	<?php if (MAP_KEY==""){//not google maps?>
	<input id="place" name="place" type="text" value="<?php echo $_POST["place"];?>" size="69" maxlength="120" /><br />
	<?php }
	else{//google maps
		if ($_POST["place"]!="") $m_value=$_POST["place"];
		else $m_value=MAP_INI_POINT;
	?>
	<input id="place" name="place" type="text" value="<?php echo $m_value;?>" onblur="showAddress(this.value);" size="69" maxlength="120" /><br />
	<div id="map" style="width: 100%; height: 200px;"></div>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo MAP_KEY;?>" type="text/javascript"></script>
	<script type="text/javascript">var init_street="<?php echo MAP_INI_POINT;?>";</script>
	<script type="text/javascript" src="<?php echo SITE_URL;?>/content/js/map.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL;?>/content/js/mapSmall.js"></script>
	<?php }?>
    <br />
	<?php _e("Your Name");?>*:<br />
	<input id="name" name="name" type="text" value="<?php if ($_POST) echo $_POST["name"]; else echo $name;?>" maxlength="75" onblur="validateText(this);"  lang="false"  /><br />
    <?php if ($email!=""){?>
    <input id="email" name="email" type="hidden" value="<?php echo $email;?>" />
    <?php } else {
       echo T_("Email (not published)")."*:<br />";
    ?>
    <input id="email" name="email" type="text" value="<?php echo $_POST["email"];?>" maxlength="120" onblur="validateEmail(this);" lang="false"  /><br />
    <?php }?>
	<?php _e("Your Phone (published)");?>:<br />
	<input id="phone" name="phone" type="text" value="<?php echo $_POST["phone"];?>" maxlength="11" /><br />
	<?php if (VIDEO){?>
    	<span style="cursor:pointer;" onclick="youtubePrompt();"><?php _e("YouTube video");?></span>: <br />
    	<input id="video" name="video" type="text" value="<?php echo $_POST["video"];?>" onclick="youtubePrompt();" size="40" /><br />
    	<div id="youtubeVideo"></div>
	<?php } ?>
	<?php 
	if (MAX_IMG_NUM>0){
		echo "<input type='hidden' name='MAX_FILE_SIZE' value='".MAX_IMG_SIZE."' />";
		echo '<br />'.T_("Upload pictures max file size").': '.(MAX_IMG_SIZE/1048576).' Mb ' .T_("format").' '.IMG_TYPES.'<br />';
		for ($i=1;$i<=MAX_IMG_NUM;$i++){?>
			<label><?php _e("Picture");?> <?php echo $i?>:</label><input type="file" name="pic<?php echo $i?>" id="pic<?php echo $i?>" value="<?php echo $_POST["pic".$i];?>" /><br />
	<?php }
	}
	?>
	<br />
	<?php if (CAPTCHA){?>
    Captcha*:<br />
	<?php echo captcha::image_tag('newitem');?><br />
    <input id="captcha" name="captcha" type="text"  onblur="validateText(this);"  lang="false" /><br />
    <?php }?>
	<?php
	if (PAYPAL_ACTIVE){
		$amount = (float)PAYPAL_AMOUNT;
		if (PAYPAL_AMOUNT_CATEGORY)
			$amount = (float)$categoryPaypal_amount;
		
		if ($amount > 0){
			if (PAYPAL_AMOUNT_CATEGORY)
				echo "<input type=\"hidden\" name=\"cpaypal\" value=\"".$amount."\" />";
				
			echo T_('Price to post using Paypal: ').$amount.PAYPAL_CURRENCY.'<br />';
		}
		else
			echo T_('Price to post using Paypal: FREE ').'<br />';
	}?>
	<?php createCSRF('newitem');?>
	<input type="submit" id="submit" value="<?php _e("Post it!");?>" />
</form>

<?php

    }//if theme
    Paypal::js_functions();
}
else {//is spammer
	alert(T_("NO Spam!"));
	jsRedirect(SITE_URL);
}


require_once('footer.php');
?>