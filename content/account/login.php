<?php
require_once('../header.php');


if (file_exists(SITE_ROOT.'/themes/'.THEME.'/account/login.php')){//account login from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/account/login.php'); 
}
else{//not found in theme

$account = Account::createBySession();
if ($account->exists) redirect(accountURL());

?>
<h2><?php _e("Login")?></h2>
<?php
if ($_POST){//&& checkCSRF('login_user')
    $email = cP('email');
    $password = cP('password');
    $rememberme = cP('rememberme');
    if ($rememberme == "1") $rememberme = true;
    else $rememberme = false;
    
    $account = new Account($email);
        if ($account->logOn($password,$rememberme,"ocEmail")){
            redirect(accountURL());
        }
        else {
            if (!$account->exists) echo "<div id='sysmessage'>".T_("Account not found")."</div>";//account not found by email
            elseif (!$account->status_password) echo "<div id='sysmessage'>".T_("Wrong password")."</div>";//wrong password
            elseif (!$account->active) echo "<div id='sysmessage'>".T_("Account is disabled")."</div>";//account is disabled
    }
} else {
    $email = $_COOKIE["ocEmail"];
    if ($email!="") $rememberme = "1";
}
?>
<div>
<form id="loginForm" name="loginForm" action="" method="post" onsubmit="return checkForm(this);">
	<p><label for="email"><?php _e("Email")?>:<br />
    <input type="text" name="email" id="email" maxlength="145" value="<?php echo $email;?>" onblur="validateEmail(this);" lang="false" /></label></p>
	<p><label for="password"><?php _e("Password")?>:<br />
    <input type="password" name="password" id="password" maxlength="<?php echo PASSWORD_SIZE; ?>" onblur="validateText(this);" lang="false" /></label></p>
	<p><label for="rememberme"><input type="checkbox" name="rememberme" id="rememberme" value="1" <?php if ($rememberme === true) echo 'checked="checked"'; ?> style="width: 10px;" /><small><?php _e("Remember me on this computer");?></small></label></p>
	<p><input name="submit" id="submit" type="submit" value="<?php _e("Submit")?>" /></p>
    <br />
	<p><?php echo '<a href="'.accountRecoverPasswordURL().'">'.T_("Forgot My Password").'</a>';?></p>
</form>
</div>
<br />
<h3><?php _e("If you do not have an account");?>: <a href="<?php echo accountRegisterURL();?>"><?php _e("Register");?></a></h3>
<?php
}//if else

require_once('../footer.php');
?>