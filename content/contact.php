<?php
require_once('header.php');

if ($_POST && checkCSRF('contact')){//contact form
	if(captcha::check('contact'))	{
		if (isEmail(cP("email"))){//is email
			if(!isSpam(cP("name"),cP("email"),cP("msg"))){//check if is spam!
				//generate the email to send to the client that is contacted
				$subject=T_("Contact").SEPARATOR.cP("subject").SEPARATOR. $_SERVER['SERVER_NAME'];
				$body=cP("name")." (".cP("email").") ".T_("contacted you about the Ad") . "<br /><br />".cP("msg");
	
				sendEmailComplete(NOTIFY_EMAIL,$subject,$body,cP("email"),cP("name"));
				
				echo "<div id='sysmessage'>".T_("Message sent, thank you")."</div>";
			}//end akismet
			else echo "<div id='sysmessage'>".T_("Oops! Spam? If it was not spam, contact us")."</div>";
		}
		else echo "<div id='sysmessage'>".T_("Wrong email")."</div>";	
	}
	else echo "<div id='sysmessage'>".T_("Wrong captcha")."</div>";
}

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/contact.php')){//contact from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/contact.php'); 
}
else{//not found in theme
?>
<a href="<?php echo SITE_URL."/".contactURL()."?subject=".T_("Suggest new category");?>"><?php _e("Suggest new category");?></a>
<h3><?php _e("Contact");?></h3>
<form method="post" action="" id="contactItem" onsubmit="return checkForm(this);">
<p>
<?php _e("Your Name");?>*:<br />
<input id="name" name="name" type="text" value="<?php echo cP("name");?>" maxlength="75" onblur="validateText(this);"  onkeypress="return isAlphaKey(event);" lang="false"  /><br />
<?php _e("Email");?>*:<br />
<input id="email" name="email" type="text" value="<?php echo cP("email");?>" maxlength="120" onblur="validateEmail(this);" lang="false"  /><br />
<?php _e("Subject");?>*:<br />
<input id="subject" name="subject" type="text" value="<?php echo cP("subject");?><?php echo cG("subject");?>" maxlength="75" onblur="validateText(this);"  onkeypress="return isAlphaKey(event);" lang="false"  /><br />
<?php _e("Message");?>*:<br />
<textarea rows="10" cols="79" name="msg" id="msg" onblur="validateText(this);"  lang="false"><?php echo strip_tags(stripslashes($_POST['msg']));?></textarea><br />
<?php if (CAPTCHA){?>
    Captcha*:<br />
	<img alt="captcha" src="<?php echo captcha::url('contact');?>"><br />
    <input id="captcha" name="captcha" type="text"  onblur="validateText(this);"  lang="false" />
<?php }?>
<br />
<br />
<?php createCSRF('contact');?>
<input type="submit" id="submit" value="<?php _e("Contact");?>" />
</p>
</form>
<?php

}//if else

require_once('footer.php');
?>