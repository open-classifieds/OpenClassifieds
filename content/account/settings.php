<?php
require_once('../header.php');

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/account/settings.php')){//account settings from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/account/settings.php'); 
}
else{//not found in theme

$account = Account::createBySession();
if ($account->exists){
    $email = $account->email;
    $name = $account->name;
} 
else redirect(accountLoginURL());


?>
<h2><?php _e("Settings")?></h2><br/>
<p><?php echo '<a href="'.accountURL().'">'.T_("My Account").'</a>';?><?php echo SEPARATOR;?><?php _e("Settings");?><?php echo SEPARATOR;?><?php echo '<a href="'.accountLogoutURL().'">'.T_("Logout").'</a>';?></p><br />
<?php
if ($_POST){
    $name = cP('name');
    $password = cP('password');
    $password_confirmation = cP('password_confirmation');

    if (trim($password)!=""){
        if ($password != $password_confirmation) echo "<div id='sysmessage'>".T_("Passwords do not match")."</div>";
        else{
            $account->updateName($name);
            $account->updatePassword($password);
            
            echo "<div id='sysmessage'>".T_("Your account has been updated")."</div>";
        }
    } else {
        $account->updateName($name);
        
        echo "<div id='sysmessage'>".T_("Your account has been updated")."</div>";
    }
} else {
    $name = $account->name;
}
?>
<div>
<form id="settingsForm" action="" onsubmit="return checkForm(this);" method="post">
    <h3><?php echo $email;?></h3>
    <p><label for="name"><?php _e("Name")?>:<br />
    <input type="text" id="name" name="name" value="<?php echo $name;?>" maxlength="250" onblur="validateText(this);" lang="false" /></label></p>
    <p><label for="password"><?php _e("Password")?>:<br />
    <input type="password" id="password" name="password" value="" /></label></p>
    <p><label for="password_confirmation"><?php _e("Confirm password")?>:<br />
    <input type="password" id="password_confirmation" value="" name="password_confirmation" /></label></p>
    <br />
    <p><input name="submit" id="submit" type="submit" value="<?php _e("Submit")?>" /></p>
</form>
</div>
<?php
}//if else

require_once('../footer.php');
?>