<?php  include 'header.php';?>


<?php if ($succeed){?>

<?php  include 'install.php';?>



<div class="page-header">
	<h1><?php _e("Welcome to");?> Open Classifieds <?php _e("installation");?></h1>
	<p>
		<?php _e("Welcome to the super easy and fast installation");?>. 
		<?php if (SAMBA){?>
			<?php _e("If you need any help please check");?> <a href="http://open-classifieds.com/support/" target="_blank">
			<?php _e("the forum");?></a>.
		<?php }?>
	</p>	
</div>

<?php if ($msg){?>
	<div class="alert alert-warning"><?php echo $msg;?></div>
<?php hostingAd();}?>

<form method="post" action="" class="well" onsubmit="return checkForm(this);">
<fieldset>

<div class="control-group">
	<label class="control-label"><?php _e("Site Language");?></label>
	<div class="controls">
       <select name="LANGUAGE" onchange="redirectLang(this.value);">

		<option value="en_EN">en_EN</option>
		    <?php
		    $languages = scandir("../languages");
		    foreach ($languages as $lang) {
			    
			    if( strpos($lang,'.')==false && $lang!='.' && $lang!='..' ){
				    if ($lang==$locale_language)  $sel= "selected=selected";
				    else $sel = "";
				    echo "<option $sel value=\"$lang\">$lang</option>";
			    }
		    }
		    ?>
		</select>
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php _e("Site URL");?>:</label>
	<div class="controls">
    <input  type="text" size="75" name="SITE_URL" value="<?php echo ($_POST['SITE_URL'])? $_POST['SITE_URL']: $suggest_url;?>" lang="false" onblur="validateText(this);" class="span6" />
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php _e("Suggested path");?>:</label>
	<div class="controls">
    <input  type="text" size="75" name="SITE_ROOT" value="<?php echo ($_POST['SITE_ROOT'])? $_POST['SITE_ROOT']: $suggest_path;?>" lang="false" onblur="validateText(this);" class="span6" />
    <span class="help-block"><?php _e("please check this carefully");?></span>
	</div>
</div>

<h2><?php _e('Database Configuration');?></h2>

<div class="control-group">
	<label class="control-label"><?php _e("Host name");?>:</label>
	<div class="controls">
	<input  type="text" name="DB_HOST" value="<?php echo ($_POST['DB_HOST'])? $_POST['DB_HOST']:'localhost';?>" lang="false" class="span6"  />
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php _e("User name");?>:</label>
	<div class="controls">
	<input  type="text" name="DB_USER"  value="<?php echo ($_POST['DB_USER'])? $_POST['DB_USER']:'root';?>" lang="false"  class="span6"   />
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php _e("Password");?>:</label>
	<div class="controls">
	<input type="password" name="DB_PASS" value="<?php echo $_POST['DB_PASS'];?>" class="span6" />		
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php _e("Database name");?>:</label>
	<div class="controls">
	<input type="text" name="DB_NAME" value="<?php echo ($_POST['DB_NAME'])? $_POST['DB_NAME']:'openclassifieds';?>" lang="false" class="span6"  />
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php _e("Database charset");?>:</label>
	<div class="controls">
	<input type="text" name="DB_CHARSET" value="<?php echo ($_POST['DB_CHARSET'])? $_POST['DB_CHARSET']:'utf8';?>" lang="false"  class="span6"   />
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php _e("Table prefix");?>:</label>
	<div class="controls">
	<input type="text" name="TABLE_PREFIX" value="<?php echo ($_POST['TABLE_PREFIX'])? $_POST['TABLE_PREFIX']:'oc_';?>" class="text-medium" />
	<span class="help-block"><?php _e("Allows multiple installations in one database if you give each one a unique prefix");?>. <?php _e("Only numbers, letters, and underscores");?>.</span>
	</div>
</div>


<div class="control-group">
	<label class="checkbox"><input type="checkbox" name="SAMPLE_DB"  value="1" /><?php _e("Sample data");?></label>
	<span class="help-block"><?php echo T_("Creates few sample categories and posts");?></span>
</div>

<h2><?php _e('Basic Configuration');?></h2>

<div class="control-group">
	<label class="control-label"><?php _e("Site Name");?>:</label>
	<div class="controls">
	<input  type="text" name="SITE_NAME" placeholder="<?php _e("Site Name");?>" value="<?php echo ($_POST['SITE_NAME'])? $_POST['SITE_NAME']:'';?>" lang="false" onblur="validateText(this);" class="span6" />
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php _e("Email address");?>:</label>
	<div class="controls">
	<input type="text" name="NOTIFY_EMAIL" placeholder="<?php _e("Email address");?>"  value="<?php echo ($_POST['NOTIFY_EMAIL'])? $_POST['NOTIFY_EMAIL']:'';?>" lang="false" onblur="validateEmail(this);" class="span6" />
	<span class="help-block"><?php _e("for notifications, and set as recipient in the emails sent from the site");?>.</span>
	</div>
</div>


<div class="control-group">
	<label class="control-label"><?php _e("Time Zone");?>:</label>
	<div class="controls">
	<?php echo get_select_timezones(TIMEZONE,$_POST['TIMEZONE']);?>
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php _e("Admin Name");?>:</label>
	<div class="controls">
	<input type="text" name="ADMIN" value="<?php echo ($_POST['ADMIN'])? $_POST['ADMIN']:'admin';?>" lang="false" onblur="validateText(this);" class="span6" />
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php _e("Admin Password");?>:</label>
	<div class="controls">
	<input type="password" name="ADMIN_PWD" value="<?php echo ($_POST['ADMIN_PWD'])? $_POST['ADMIN_PWD']:'';?>" class="span6" />	
	</div>
</div>

<div class="control-group">
	<label class="checkbox">
		<input type="checkbox" name="OCAKU" value="1" checked="checked" />
		<?php _e("Ocaku registration");?> <a target="_blank" href="http://ocacu.com/en/terms.html">
			<?php echo _e('Terms');?></a>
	</label>
	<span class="help-block"><?php _e("Allow site to be in Ocaku, classifieds community (recommended)");?></span>	
</div>

<?php if (SAMBA){?>
	<div class="control-group">
		<label class="checkbox"><input checked="checked" type="checkbox" id="terms" name="terms" value="1" />  <?php _e("I accept the license terms");?>. </label>
		<span class="help-block"><a href="http://www.gnu.org/licenses/gpl.txt" target="_blank">GPL v3</a>
		<?php _e("Please read the following license agreement and accept the terms to continue");?>
		</span>
	</div>
<?php }else{?>
	<input type="hidden" id="terms" name="terms" value="1" />
<?php }?>

<input type="submit" name="action" id="submit" value="<?php _e("Install");?>" class="btn btn-primary btn-large" />

</fieldset>
</form>

<?php 
}//if requirements succeed

else {?>

<div class="alert alert-error"><?php echo $msg;?></div>
<?php hostingAd(); }?>

<?php if (SAMBA){?>
<div class="hero-unit">
	<h2>Need professional help?</h2>
	<p>Just for 50 EUR, <code>Installation</code>, commercial license, premium support, 13 premium themes and much more.</br>
		<a class="btn btn-primary btn-large" href="http://open-classifieds.com/download/"><i class=" icon-shopping-cart icon-white"></i> Buy now!</a>
	</p>
</div>
<?php }?>


<?php include 'footer.php';?>
