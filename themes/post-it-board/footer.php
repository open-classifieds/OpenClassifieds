<?php if (!isset($resultSearch)) { echo '</div>'; }?>
</div>   
	     
    <div id="sidebar">
    
        <div class="box">
             <?php if(isset($categoryName)&&isset($categoryDescription)){ 
             if (isset($location)) $locationtitle = " - ".getLocationName($location);
             ?>
		        <p>
			        <b><?php echo $categoryName.$locationtitle;?>:</b> 
			         <?php echo $categoryDescription; ?>
			         <?php if(!PARENT_POSTS){?><p><?php echo '<a title="'.T_("Publish a new Ad").'" href="'.SITE_URL.newURL().'">'.T_("Publish a new Ad").'</a>';?></p><?php }?>			         
		        </p>
	        
	        <?php }?>
	        <?php if (NEED_OFFER){?>
            <br/>
            <b><?php _e('Filter');?></b>:
		    <?php generatePostType($currentCategory,$type); ?>
		    <?php }?>
        </div>
        
        <?php if(isset($idItem)){ ?>
	     <div class="box">
	        <?php if ($itemAvailable==1){?>
			<h3 style="cursor:pointer;" onclick="openClose('contactmail');"><?php _e("Contact");?> <?php echo $itemName.': '.$itemTitle;?></h3>
			<div id="contactmail" >
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
				    <textarea rows="10" cols="79" name="msg" id="msg" onblur="validateText(this);"  lang="false"><?php echo strip_tags(stripslashes($_POST['msg']));?></textarea><br />
				</p>
				<?php if (CAPTCHA){?>
                    Captcha*:<br />
                	<img alt="captcha" src="<?php echo captcha::url('contact_'.$idItem);?>"><br />
                    <input id="captcha" name="captcha" type="text"  onblur="validateText(this);"  lang="false" />
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
	        
	    </div>
        <?php }?>
        
        
	    <?php getSideBar("<div class='box'>","</div>");?>
	    
	</div>
	
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
