<?php
	if ($_POST){//try to login
	    require_once('../includes/bootstrap.php');//loading functions
	    if (ADMIN==cP('user') && ADMIN_PWD==cP('pwd') && checkCSRF('login_admin')){//it's the same as in config.php?
			$_SESSION['admin']=cP('user');//setting the session
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
