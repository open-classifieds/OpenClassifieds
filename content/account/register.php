<?php
require_once('../header.php');

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/account/register.php')){//account register from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/account/register.php'); 
}
else{//not found in theme

?>
<h2><?php _e("Register")?></h2>
<?php
$show_form = true;

if ($_POST && checkCSRF('register_user')){
    if(captcha::check('register'))	{
        $namer = cP('name');
        $email = cP('email');
        $password = cP('password');
        $password_confirmation = cP('password_confirmation');
        $agree_terms = cP('agree_terms');
    
        if ($agree_terms == "yes"){
            if (isEmail($email)){        
                if ($password == $password_confirmation){
                    $account = new Account($email);
                    if ($account->exists){
                        echo "<div id='sysmessage'>".T_("Account already exists")."</div>";
                    }
                    else {
                        if ($account->Register($namer,$email,$password)){
                            $token=$account->token();
                            
                            $url=accountRegisterURL();
                            if (strpos($url,"?")) $url.='&amp;account='.$email.'&amp;token='.$token.'&amp;action=confirm';
                            else $url.='?account='.$email.'&amp;token='.$token.'&amp;action=confirm';
                            
                            $message='<p>'.T_("Click the following link or copy and paste it into your browser address field to activate your account").'</p>
                        	<p><a href="'.$url.'">'.T_("Confirm account").'</a></p><p>'.$url.'</p>';
                            
                            $array_content[]=array("ACCOUNT", $namer);
                            $array_content[]=array("MESSAGE", $message);
                            
                            $bodyHTML=buildEmailBodyHTML($array_content);
                            
                        	sendEmail($email,T_("Confirm your account")." - ".SITE_NAME,$bodyHTML);//email registration confirm request
                            
                            $show_form = false;
                            echo "<div id='sysmessage'>".T_("Instructions to confirm your account has been sent").". ".T_("Please, check your email")."</div>";
                        } else _e("An unexpected error has occurred trying to register your account");
                    }
                } else echo "<div id='sysmessage'>".T_("Passwords do not match")."</div>";
            } else echo "<div id='sysmessage'>".T_("Wrong email")."</div>";
        } else echo "<div id='sysmessage'>".T_("Terms agreement is required")."</div>";
    } else echo "<div id='sysmessage'>".T_("Wrong captcha")."</div>";//wrong captcha
}

if (trim(cG('account'))!="" && trim(cG('token'))!="" && trim(cG('action'))=="confirm"){
    $show_form = false;
    
    $email = trim(cG('account'));
    $token = trim(cG('token'));
    
    $account = new Account($email);
    if ($account->exists){
        if ($account->Activate($token)){
            echo "<div id='sysmessage'>".T_("Your account has been succesfully confirmed")."</div>";
            
            $bodyHTML="<p>".T_("NEW account registered")."</p><br/>".T_("Email").": ".$account->email." - ".$account->signupTimeStamp();
        	sendEmail(NOTIFY_EMAIL,T_("NEW account")." - ".SITE_NAME,$bodyHTML);//email to the NOTIFY_EMAIL
            
            $account->logOn($account->password());
            
            echo '<p><a href="'.accountURL().'">'.T_("Welcome").' '.$account->name.'</a></p><br/>';
        } else echo "<div id='sysmessage'>".T_("An unexpected error has occurred trying to confirm your account")."</div>";
    } else echo "<div id='sysmessage'>".T_("Account not found")."</div>";
}

if ($show_form){
?>
<div>
<form id="registerForm" action="" onsubmit="return checkForm(this);" method="post">
    <p><label for="name"><?php _e("Name")?>:<br />
    <input type="text" id="name" name="name" value="<?php echo $namer;?>" maxlength="250" onblur="validateText(this);" lang="false" /></label></p>
    <p><label for="email"><?php _e("Email")?>:<br />
    <input type="text" id="email" name="email" value="<?php echo $email;?>" maxlength="145" onblur="validateEmail(this);" lang="false" /></label></p>
    <p><label for="password"><?php _e("Password")?>:<br />
    <input type="password" id="password" name="password" value="" onblur="validateText(this);" lang="false" /></label></p>
    <p><label for="password_confirmation"><?php _e("Confirm password")?>:<br />
    <input type="password" id="password_confirmation" value="" name="password_confirmation" onblur="validateText(this);" lang="false" /></label></p>
    <p><label for="agree_terms"><input type="checkbox" id="agree_terms" name="agree_terms" value="yes" style="width: 10px;" /> <?php _e("Accept")?> <a href="<?php echo termsURL();?>"><?php _e("Terms")?></a> - <?php echo SITE_NAME?></label></p>
    <br />
	<?php if (CAPTCHA){?>
    Captcha*:<br />
	<img alt="captcha" src="<?php echo captcha::url('register');?>"><br />
    <input id="captcha" name="captcha" type="text"  onblur="validateText(this);"  lang="false" />
    <?php }?>
    <?php createCSRF('register_user');?>
    <p><input name="submit" id="submit" type="submit" value="<?php _e("Submit")?>" /></p>
</form>
</div>
<?php
}

}//if else

require_once('../footer.php');
?>