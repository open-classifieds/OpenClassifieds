<?php
	if ($_POST){//try to login
	    require_once('../includes/bootstrap.php');//loading functions

	    $rememberme = (cP('rememberme') == '1');

	    if (ADMIN==cP('user') && ADMIN_PWD==cP('pwd') && checkCSRF('login_admin')){//it's the same as in config.php?
			$_SESSION['admin']=cP('user');//setting the session

			if ($rememberme)
            {
            	//1 month logged in
                setcookie('oc_admin',md5(ADMIN+ADMIN_PWD), time()+60*60*24*30);
            } 
            else  setcookie('oc_admin','', time()-3600);

			redirect('index.php');
		}//else echo "MEC!!";	
	}
	
	require_once('header.php');
?>
<h1><?php _e("Administration Login");?></h1>
<form class="well" action="login.php" method="post" onsubmit="return checkForm(this);" >
	<fieldset>
	
		<div class="control-group">
            <label class="control-label"><?php _e("User");?></label>
            <div class="controls">
            	<input name="user" type="text" class="text-long" onblur="validateText(this);" lang="false" value=""  />
            </div>
        </div>
 
 		<div class="control-group">
            <label class="control-label"><?php _e("Password");?></label>
            <div class="controls">
          	  <input name="pwd" type="password" class="text-long" onblur="validateText(this);"  lang="false" value="" />
            </div>
            <label class="checkbox">
	        	<input type="checkbox" name="rememberme" id="rememberme" value="1" <?php if ($rememberme) echo 'checked="checked"'; ?> />
				<?php _e("Remember me on this computer");?>
	        </label>
        </div>
        
        <?php createCSRF('login_admin');?>
		<div class="form-actions">
            <button type="submit" class="btn btn-primary"><?php _e("Submit");?></button>
            <a class="btn" href="<?php echo SITE_URL;?>"><?php _e('Return');?></a> 
        </div>

	</fieldset>
</form>

<?php
require_once('footer.php');
?>
