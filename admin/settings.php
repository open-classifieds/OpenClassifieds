<?php
require_once('access.php');
require_once('header.php');

if ($_POST){//if there's post action
		
	//Themes options configs saving
	if (oc::$_POST['THEME_OPTIONS'] == 1)
	{
		
		//generationg the config.php
		$config_content = "<?php\n//Open Classifieds Theme ".THEME." Configuration ".date("d/m/Y G:i:s")."\n";
			foreach  ($_POST AS $key => $value){
				if ($key!="submit" && $key!='THEME_OPTIONS'){
					if ($value=="TRUE") $config_content.="define('$key',true);\n";
					elseif ($value=="FALSE") $config_content.="define('$key',false);\n";
					else $config_content.="define('$key','".str_replace("\n",'',stripslashes($value))."');\n";
				}
			}
			$config_content.="?>";//	echo $config_content;			
			
			//writting the config.php
			if (!oc::fwrite(SITE_ROOT.'themes/'.THEME.'/config.php', $config_content))
			{
				$msg=T_("The configuration file")." 'config.php' ".T_("is not writable").". ".T_("Change its permissions, then try again").".";
			}else redirect($_POST['SITE_URL']."/admin/settings.php?msg=".T_('Theme Updated'));
							
			echo '<div class="alert alert-error">'.$msg.'</div>';
			
	}
	else
	{
	    //print_r($_POST["SIDEBAR"]);
	    $_POST["SIDEBAR"]=implode(",",$_POST["SIDEBAR"]);// sidebar
	    
		if ($_POST["ADVERT_TOP"]) {
			$_POST["ADVERT_TOP"] = str_replace("\n", "", $_POST["ADVERT_TOP"]);
			$_POST["ADVERT_TOP"] = stripslashes($_POST["ADVERT_TOP"]);
		}
		
		if ($_POST["ADVERT_SIDEBAR"]) {
			$_POST["ADVERT_SIDEBAR"] = str_replace("\n", "", $_POST["ADVERT_SIDEBAR"]);
			$_POST["ADVERT_SIDEBAR"] = stripslashes($_POST["ADVERT_SIDEBAR"]);
		}
	   
	    $succeed=false;
	    //generationg the config.php
		$config_content = "<?php\n//Open Classifieds Installation Config ".date("d/m/Y G:i:s")."\n";
		foreach  ($_POST AS $key => $value){
				if ($key!="submit"){
				    if ($value=="TRUE") $config_content.="define('$key',true);\n";	
				    elseif ($value=="FALSE") $config_content.="define('$key',false);\n";	
					else $config_content.="define('$key','$value');\n";		
				}
		}
		$config_content.="?>";//	echo $config_content;
		
		//writting the config.php
		if (!oc::fwrite('../includes/config.php', $config_content))
		{
			$msg=T_("The configuration file")." '/includes/config.php' ".T_("is not writable").". ".T_("Change its permissions, then try again").".";
			$succeed=false;
		}else $succeed=true;
			
	    //succeded writting the config.php
	    if ($succeed){
	        //sitemap::generate();
	        //Ocaku change settings
	        /*if (OCAKU){
	        	$ocaku=new ocaku();
		        $data=array(
					'KEY'=>$_POST['OCAKU_KEY'],
					'siteName'=>$_POST['SITE_NAME'],
					'email'=>$_POST['NOTIFY_EMAIL'],
					'language'=>$_POST["LANGUAGE"]
				 );
				 $ocaku->editSite($data);
	        }
	        //end ocaku change settings*/
	        
	    	if (SITE_URL!=$_POST['SITE_URL']) regenerateRobots($_POST['SITE_URL']);
	    	
	        //changing the language generating new .htaccess
	    	if (LANGUAGE!=$_POST["LANGUAGE"]){
	    		//we need to redirect so the new language is loaded
		        redirect($_POST['SITE_URL']."/admin/htaccess.php");
		    }//end if language
			else { 
				//we redirect for the new changes see them
			    redirect($_POST['SITE_URL']."/admin/settings.php?msg=Updated");
			    die();
			}
			
	    }
	    else echo $msg;
	}

}//end post

if (cG("msg")!="") echo '<div class="alert alert-success">'.cG("msg").'</div>';
?>
<script  type="text/javascript">
function moveUp(selectId) {
	var selectList = document.getElementById(selectId);
	var selectOptions = selectList.getElementsByTagName('option');
	for (var i = 1; i < selectOptions.length; i++) {
		var opt = selectOptions[i];
		if (opt.selected) {
			selectList.removeChild(opt);
			selectList.insertBefore(opt, selectOptions[i - 1]);
		}
       }
}
function moveDown(selectId) {
	var selectList = document.getElementById(selectId);
	var selectOptions = selectList.getElementsByTagName('option');
	for (var i = selectOptions.length - 2; i >= 0; i--) {
		var opt = selectOptions[i];
		if (opt.selected) {
		   var nextOpt = selectOptions[i + 1];
		   opt = selectList.removeChild(opt);
		   nextOpt = selectList.replaceChild(opt, nextOpt);
		   selectList.insertBefore(nextOpt, opt);
		}
       }
}
function swapElement(fromList,toList){
    var selectOptions = document.getElementById(fromList);
    for (var i = 0; i < selectOptions.length; i++) {
        var opt = selectOptions[i];
        if (opt.selected) {
            document.getElementById(fromList).removeChild(opt);
            document.getElementById(toList).appendChild(opt);
            i--;
        }
    }
}
function selectAllOptions(selStr)
{
    var selObj = document.getElementById(selStr);
    for (var i=0; i<selObj.options.length; i++) {
        selObj.options[i].selected = true;
    }
}

//jsTabs by neo22s
function getElementsByClassDustin(searchClass,node,tag) { //by http://www.dustindiaz.com/getelementsbyclass/
    var classElements = new Array();
    if ( node == null ) node = document;
    if ( tag == null )  tag = '*';
    var els = node.getElementsByTagName(tag);
    var elsLen = els.length;
    var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
    for (i = 0, j = 0; i < elsLen; i++) {
            if ( pattern.test(els[i].className) ) {
                    classElements[j] = els[i];
                    j++;
            }
    }
    return classElements;
}
function getElementsByClass(searchClass,node,tag) { 
	//if is Netscape we use the native procedure
	if (navigator.appName=="Netscape") return document.getElementsByClassName(searchClass);
	else return getElementsByClassDustin(searchClass,node,tag);
}

function showSetting(id)
{
	//hidding all the settings
	closeAllSettings();

	//show the one we want
	show(id);
}

function closeAllSettings()
{
	//hidding all the settings
	var set = getElementsByClass("settingsTable",document.getElementById('install'),"div");//all the boxes
	for (i in set) hide(set[i].id);
}

function openAllSettings()
{
	//showing all the settings
	var set = getElementsByClass("settingsTable",document.getElementById('install'),"div");//all the boxes
	for (i in set) show(set[i].id);
}
</script>

<div class="page-header">
	<h1><?php _e("Settings");?> v<?php echo VERSION;?></h1>	
</div>

<form class="well" id="install" action="settings.php" method="post" onsubmit="selectAllOptions('selected');">

	<input class="btn btn-primary" type="submit" name="submit" id="submit" value="<?php _e("Save Settings");?>" />
    <button class="btn btn-success" onclick="openAllSettings();return false;"><?php _e("Open all");?></button> 
	<button class="btn btn-warning" onclick="closeAllSettings();return false;"><?php _e("Close all");?></button>
	<hr>
	
<div class="settingsTitle" onclick="showSetting('bconf');"><h3><?php _e("Basic Configuration");?></h3></div>
<div class="settingsTable" id="bconf">
<fieldset>
<p>
	<label><?php _e("Site Name");?>:</label>
	<input  type="text" name="SITE_NAME" value="<?php echo SITE_NAME;?>" class="span4" lang="false" onblur="validateText(this);" />
</p>
<p>
	<label><?php _e("Site Description");?>:</label>
	<input  type="text" name="SITE_DESCRIPTION" value="<?php echo SITE_DESCRIPTION;?>" class="span4" lang="false" />
</p>
<p>
	<label><?php _e("Site URL");?>:</label>
	<input  type="text" name="SITE_URL" value="<?php echo SITE_URL;?>" class="span4" lang="false" />
</p>
<p>
	<label><?php _e("Site Full Path");?>:</label>
	<input  type="text" name="SITE_ROOT" value="<?php echo SITE_ROOT;?>" class="span4" lang="false" />
	<span class="help-block"><?php _e("IMPORTANT: Path in the server");?></span>
</p>
<p>
	<label><?php _e("Notifications Email");?>:</label>
	<input  type="text" name="NOTIFY_EMAIL"  value="<?php echo NOTIFY_EMAIL;?>" class="span4" lang="false" onblur="validateEmail(this);"/>
</p>
<p>
	<label><?php _e("Language");?>:</label>
	<select name="LANGUAGE" >
	    <option value="<?php echo LANGUAGE;?>"><?php echo LANGUAGE;?></option>
	    <option value="en_EN">en_EN</option>
	    <?php
	    $languages = scandir("../languages");
	    foreach ($languages as $lang) {
		    
		    if( strpos($lang,'.')==false && $lang!='.' && $lang!='..' && $lang!=LANGUAGE){
			    echo "<option value=\"$lang\">$lang</option>";
		    }
	    }
	    ?>
	</select>
</p>
<p>
	<label><?php _e("Locale Extension");?>:</label>
	<input  type="text" name="LOCALE_EXT"  value="<?php echo LOCALE_EXT;?>" class="span1" />
</p>
<p>
	<label><?php _e("Time Zone");?>:</label>
	<?php echo get_select_timezones('TIMEZONE',date_default_timezone_get())?>
</p>
<p>
	<label><?php _e("Administrator Login");?>:</label>
	<input type="text" name="ADMIN" value="<?php echo ADMIN;?>" class="span4" lang="false" onblur="validateText(this);" />
</p>
<p>
	<label><?php _e("Administrator Password");?>:</label>
	<input type="password" name="ADMIN_PWD" value="<?php echo ADMIN_PWD;?>" class="span4" />	
</p>
</fieldset>
</div>



<div class="settingsTitle" onclick="showSetting('dbconf');"><h3><?php _e("Database Configuration");?></h3></div>
<div class="settingsTable" id="dbconf">
<fieldset>
<p>
	<label><?php _e("Host Name");?>:</label>
	<input type="text" name="DB_HOST" value="<?php echo DB_HOST;?>" class="span4" lang="false" onblur="validateText(this);" />
</p>
<p>
	<label><?php _e("Database Username");?>:</label>
	<input type="text" name="DB_USER"  value="<?php echo DB_USER;?>" class="span4" lang="false" onblur="validateText(this);" />
</p>
<p>
	<label><?php _e("Database Password");?>:</label>
	<input type="password" name="DB_PASS" value="<?php echo DB_PASS;?>" class="span4" />	
</p>
<p>
	<label><?php _e("Database Name");?>:</label>
	<input type="text" name="DB_NAME" value="<?php echo DB_NAME;?>" class="span4" lang="false" onblur="validateText(this);" />
</p>
<p>
	<label><?php _e("Database Charset");?>:</label>
	<input type="text" name="DB_CHARSET" value="<?php echo DB_CHARSET;?>" class="span4" lang="false" onblur="validateText(this);" />
	<span class="help-block"><?php _e("IMPORTANT: If you change this be sure you change it in the database structure and maybe you need to change it in the HTML Charset as well");?>.</span>
</p>
<p>
	<label><?php _e("Table Prefix");?>:</label>
	<input type="text" name="TABLE_PREFIX" value="<?php echo TABLE_PREFIX;?>" class="span4" />
	<span class="help-block"><?php _e("Multiple installations in one database if you give each a unique prefix. Only numbers, letters, and underscores");?>.</span>
</p>
<p>
	<label><?php _e("Type Offer");?>:</label>
	<input type="text" name="TYPE_OFFER" value="<?php echo TYPE_OFFER;?>" lang="false" class="span1" />
	<span class="help-block"><?php _e("Offer type value on database");?>.</span>
</p>
<p>
	<label><?php _e("Type Need");?>:</label>
	<input type="text" name="TYPE_NEED" value="<?php echo TYPE_NEED;?>" lang="false" class="span1" />
	<span class="help-block"><?php _e("Need type value on database");?>.</span>
</p>
</fieldset>
</div>



<div class="settingsTitle" onclick="showSetting('iniconf');"><h3><?php _e("Initial Settings");?></h3></div>
<div class="settingsTable" id="iniconf">
<fieldset>
<p>
	<label><?php _e("HTML Charset");?>:</label>
	<input  type="text" name="CHARSET" value="<?php echo CHARSET;?>" lang="false" class="span4" />
	<span class="help-block"><?php _e("IMPORTANT: maybe you need to change it in your database Charset as well");?>. <a href="http://www.w3.org/International/O-charset-list.html"><?php _e("List");?></a>.</span>
</p>
<p>
	<label><?php _e("Date Format");?>:</label>
	<select name="DATE_FORMAT" >
	<option <?php if(DATE_FORMAT=='dd-mm-yyyy')  echo "selected=selected";?> >dd-mm-yyyy</option>
	<option <?php if(DATE_FORMAT=='yyyy-mm-dd')  echo "selected=selected";?> >yyyy-mm-dd</option>
	<option <?php if(DATE_FORMAT=='mm-dd-yyyy')  echo "selected=selected";?> >mm-dd-yyyy</option>
	</select>
	<span class="help-block"><?php _e("Use a date format");?>.</span>
</p>
<p>
	<label><?php _e("Location");?>:</label>
	<select name="LOCATION" >
	<option <?php if(LOCATION)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!LOCATION)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Enables location features");?>.</span>
</p>
<p>
	<label><?php _e("Location Root");?>:</label>
	<input type="text" name="LOCATION_ROOT" value="<?php echo LOCATION_ROOT;?>" class="span4" />
	<span class="help-block"><?php _e("The root of all location. For example: Country name");?>.</span>
</p>
<p>
	<label><?php _e("Logon to Post");?>:</label>
	<select name="LOGON_TO_POST" >
	<option <?php if(LOGON_TO_POST)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!LOGON_TO_POST)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Require log on to post");?>.</span>
</p>
<p>
	<label><?php _e("Extra filters");?>:</label>
	<select name="NEED_OFFER" >
	<option <?php if(NEED_OFFER)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!NEED_OFFER)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Activates the need/offer filter");?>.</span>
</p>
<p>
	<label><?php _e("Posts counter");?>:</label>
	<select name="COUNT_POSTS" >
	<option <?php if(COUNT_POSTS)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!COUNT_POSTS)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Count the visitors per post");?>.</span>
</p>
<p>
	<label><?php _e("Posts in parent");?>:</label>
	<select name="PARENT_POSTS" >
	<option <?php if(PARENT_POSTS)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!PARENT_POSTS)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e('Allow to post in parent categories');?>.</span>
</p>
<p>
	<label><?php _e("Confirm Post");?>:</label>
	<select name="CONFIRM_POST" >
	<option <?php if(CONFIRM_POST)  echo 'selected="selected"';?> >TRUE</option>
	<option <?php if(!CONFIRM_POST)  echo 'selected="selected"';?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Require email confirmation to activate post");?></span>
</p>
<p>
	<label><?php _e("Moderate Post");?>:</label>
	<select name="MODERATE_POST" >
	<option <?php if(MODERATE_POST)  echo 'selected="selected"';?> >TRUE</option>
	<option <?php if(!MODERATE_POST)  echo 'selected="selected"';?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("After confirmed wait for aproval");?> - <a href="listing.php?show=moderate"><?php _e("Moderation list");?></a></span>
</p>
<p>
	<label><?php _e("Expire posts");?>:</label>
	<input  type="text" name="EXPIRE_POST" value="<?php echo EXPIRE_POST;?>" lang="false" class="span1" />
	<span class="help-block"><?php _e("Days that the post will expire, only numbers. 0 days = no expire");?></span>
</p>
<p>
	<label><?php _e("Minimal search phrase length");?>:</label>
	<input  type="text" name="MIN_SEARCH_CHAR" value="<?php echo MIN_SEARCH_CHAR;?>" lang="false" class="span1" />
	<span class="help-block"><?php _e("Search less than this number will not be performed");?>.</span>
</p>
<p>
	<label><?php _e("Currency");?>:</label>
	<input type="text" name="CURRENCY" value="<?php echo CURRENCY;?>" lang="false" class="span1" />
	<span class="help-block"><?php _e("Price currency");?>.</span>
</p>
<p>
	<label><?php _e("Currency Format");?>:</label>
	<select name="CURRENCY_FORMAT" >
	<option <?php if(CURRENCY_FORMAT=='AMOUNTCURRENCY')  echo 'selected="selected"';?> value="AMOUNTCURRENCY" ><?php echo '100'.CURRENCY;?></option>
	<option <?php if(CURRENCY_FORMAT=='CURRENCYAMOUNT')  echo 'selected="selected"';?> value="CURRENCYAMOUNT" ><?php echo CURRENCY.'100';?></option>
	</select>
	<span class="help-block"><?php _e("Format to display price");?>.</span>
</p>
</fieldset>
</div>



<div class="settingsTitle" onclick="showSetting('look');"><h3><?php _e("Look and Feel");?></h3></div>
<div class="settingsTable" id="look">
<fieldset>
<p>
	<label><?php _e("Default Theme");?>:</label>
	<select name="DEFAULT_THEME" >
	<option value="<?php echo DEFAULT_THEME;?>"><?php echo DEFAULT_THEME;?></option>
	<?php
	$themes = scandir("../themes");
	foreach ($themes as $theme) {
		if($theme!="" && $theme!=DEFAULT_THEME && $theme!="." && $theme!=".." && $theme!="wordcloud.css"){
			echo "<option value=\"$theme\">".$theme."</option>";
		}
	}
	?>
	</select>
	<?php if (SAMBA){?>
	<span class="help-block"><?php _e("For more themes please go to");?> <a href="http://www.open-classifieds.com/">Open Classifieds</a></span>
	<?php }?>
</p>
<p>
	<label><?php _e("Theme Selector");?>:</label>
	<select name="THEME_SELECTOR" >
	<option <?php if(THEME_SELECTOR)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!THEME_SELECTOR)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("If you enable this you allow the user to select theme");?>.</span>
</p>
<p>
	<label><?php _e("Mobile Theme");?>:</label>
	<select name="THEME_MOBILE" >
	<option <?php if(THEME_MOBILE)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!THEME_MOBILE)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Displays the mobile version of your site  to mobile devices if its enabled, (uses neoMobile theme)");?>.</span>
</p>
<p>
	<label><?php _e("Items per Page");?>:</label>
	<input type="text" name="ITEMS_PER_PAGE" value="<?php echo ITEMS_PER_PAGE;?>" lang="false" class="span1" />
	<span class="help-block"><?php _e("Only numbers");?>.</span>
</p>
<p>
	<label><?php _e("Pages to display");?>:</label>
	<input type="text" name="DISPLAY_PAGES" value="<?php echo DISPLAY_PAGES;?>" lang="false" class="span1" />
	<span class="help-block"><?php _e("How many pages are displayed");?>. <?php _e("Only numbers");?>.</span>
</p>
<p>
	<label><?php _e("HTML Editor");?>:</label>
	<select name="HTML_EDITOR" >
	<option <?php if(HTML_EDITOR)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!HTML_EDITOR)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Disable nicEdit HTML editor in the post");?>.</span>
</p>
<p>
	<label><?php _e("Allowed Tags");?>:</label>
	<input type="text" name="ALLOWED_HTML_TAGS" value="<?php echo ALLOWED_HTML_TAGS;?>" class="span4" />
	<span class="help-block"><?php _e("This tags are allowed on submit post");?>.</span>
</p>
<p>
	<label><?php _e("HTML Separator");?>:</label>
	<input type="text" name="SEPARATOR" value="<?php echo SEPARATOR;?>" lang="false" class="span1" />
	<span class="help-block"><?php _e("Separator used in a few places");?></span>
</p>
<p>
	<label><?php _e("Captcha");?>:</label>
	<select name="CAPTCHA" >
	<option <?php if(CAPTCHA)  echo 'selected="selected"';?> >TRUE</option>
	<option <?php if(!CAPTCHA)  echo 'selected="selected"';?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Requires a math captcha before submitting any form");?></span>
</p>
<p>
	<label><?php _e("Friendly URL's");?>:</label>
	<select name="FRIENDLY_URL" >
	<option <?php if(FRIENDLY_URL)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!FRIENDLY_URL)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Disabled does not use the .htaccess, and the URLS will not look SEO friendly");?></span>
</p>
</fieldset>
</div>



<div class="settingsTitle" onclick="showSetting('advert');"><h3><?php _e("Adsense and Advertising");?></h3></div>
<div class="settingsTable" id="advert">
<fieldset>
<p>
	<?php _e("In this section you can paste your code of AdSense or any other provider and it will show in the website");?>
</p>
<p>
	<label><?php _e("Top Advertisement");?>:</label>
	<textarea  name="ADVERT_TOP"  rows=5 class="span8"><?php echo ADVERT_TOP;?></textarea>
    <span class="help-block"><?php _e("HTML advertisement that appears in the top of the website");?>.</span>
</p>
<p>
	<label><?php _e("Widget Advertisement");?>:</label>
	<textarea  name="ADVERT_SIDEBAR"  rows=5 class="span8"><?php echo ADVERT_SIDEBAR;?></textarea>
	<span class="help-block"><?php _e("HTML advertisement that appears in the sidebar");?>.</span>
</p>
</fieldset>
</div>



<div class="settingsTitle" onclick="showSetting('sidebarconf');"><h3><?php _e("Sidebar");?></h3></div>
<input type="hidden" name="SIDEBAR_ORIG" value="<?php echo SIDEBAR_ORIG;?>" />
<div class="settingsTable" id="sidebarconf">
<fieldset>
<p>
	<table class="span7">
    <tr>
	    <td width="45%" >
	        <strong><?php _e("Available Widgets");?></strong><br />
			<select id="available" size="15" multiple="multiple" class="span3">
			<?php 
			$default_sidebar=explode(",",SIDEBAR_ORIG);
			$sidebar=explode(",",SIDEBAR);
			foreach($default_sidebar as $widget){
				if (!in_array($widget, $sidebar)) echo '<option>'.$widget.'</option>';
			}
			?>
			</select>
	    </td>
	    <td width="5%" valign="center">			
			<button  class="btn btn-success" onclick="swapElement('selected','available');return false;" >
				<i class="icon-arrow-left icon-white"></i></button>
			<button  class="btn btn-success" onclick="swapElement('available','selected');return false;">
				<i class="icon-arrow-right icon-white"></i></button>
			<button  class="btn btn-primary" onclick=" moveUp('selected');return false;" >
				<i class="icon-arrow-up  icon-white"></i></button>
			<button  class="btn btn-primary" onclick="moveDown('selected');return false;">
				<i class="icon-arrow-down icon-white"></i></button>
			  
	    </td>
	    <td width="50%">
	        <strong><?php _e("Your Sidebar");?></strong><br />
			<select id="selected" name="SIDEBAR[]" size="15" multiple="multiple" class="span3">
			<?php 
			foreach($sidebar as $widget){
				echo '<option>'.$widget.'</option>';
			}
			?>
			</select>
	    </td>
    </tr>
	</table>
	<div style="clear:both;"></div>
</p>
<p>
	<label><?php _e("RSS sidebar URL");?>:</label>
	<input type="text" name="RSS_SIDEBAR_URL" value="<?php echo RSS_SIDEBAR_URL;?>" lang="false" class="span4" />
</p>
<p>
	<label><?php _e('RSS sidebar Name');?>:</label>
	<input type="text" name="RSS_SIDEBAR_NAME" value="<?php echo RSS_SIDEBAR_NAME;?>" lang="false" class="span4" />
</p>
<p>
	<label><?php _e("RSS sidebar Count");?>:</label>
	<input type="text" name="RSS_SIDEBAR_COUNT" value="<?php echo RSS_SIDEBAR_COUNT;?>" lang="false" class="span1" />
</p>
</fieldset>
</div>



<div class="settingsTitle" onclick="showSetting('smtpconf');"><h3><?php _e("Mail Server Configuration");?></h3></div>
<div class="settingsTable" id="smtpconf">
<fieldset>
<p>
	<label><?php _e("Host Name");?>:</label>
	<input type="text" name="SMTP_HOST" value="<?php echo SMTP_HOST;?>" class="span4" />
</p>
<p>
	<label><?php _e("Server Port");?>:</label>
	<input type="text" name="SMTP_PORT" value="<?php echo SMTP_PORT;?>" class="span4" />
	<span class="help-block"><?php _e("Leave blank to use default SMTP port");?>.</span>
</p>
<p>
	<label><?php _e("Authentication");?>:</label>
	<select name="SMTP_AUTH" >
	<option <?php if(SMTP_AUTH)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!SMTP_AUTH)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Enables SMTP authentication");?>.</span>
</p>
<p>
	<label><?php _e("Username");?>:</label>
	<input  type="text" name="SMTP_USER" value="<?php echo SMTP_USER;?>" class="span4" />
</p>
<p>
	<label><?php _e("Password");?>:</label>
	<input type="password" name="SMTP_PASS" value="<?php echo SMTP_PASS;?>" class="span4" />	
</p>
<p>
	<label>GMAIL:</label>
	<select name="GMAIL" >
	<option <?php if(GMAIL)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!GMAIL)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Uses GMAIL for SMTP, perfect if you do not have email server or you cannot manage to configure it");?>.</span>
</p>
<p>
	<label>GMAIL <?php _e("Username");?>:</label>
	<input type="text" name="GMAIL_USER" value="<?php echo GMAIL_USER;?>" class="span4" />
	<span class="help-block"><?php _e("Account Name");?>.</span>
</p>
<p>
	<label>GMAIL <?php _e("Password");?>:</label>
	<input type="password" name="GMAIL_PASS" value="<?php echo GMAIL_PASS;?>" class="span4" />
	<span class="help-block"><?php _e("Account Password");?>.</span>
</p>
</fieldset>
</div>



<div class="settingsTitle" onclick="showSetting('paypal');"><h3><?php _e("Paypal Configuration");?></h3></div>
<div class="settingsTable" id="paypal">
<fieldset>
<p>
	<label><?php _e("Paypal Active");?>:</label>
	<select name="PAYPAL_ACTIVE" >
	<option <?php if(PAYPAL_ACTIVE)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!PAYPAL_ACTIVE)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Sets on/off the pay to post mode");?>.</span>
</p>
<p>
	<label><?php _e("Paypal account");?>:</label>
	<input type="text" name="PAYPAL_ACCOUNT" value="<?php echo PAYPAL_ACCOUNT;?>" lang="false" class="span4" />
	<span class="help-block"><?php _e("Your paypal address, remember to activate IPN");?>.</span>
</p>
<p>
	<label><?php _e("Amount");?>:</label>
	<input type="text" name="PAYPAL_AMOUNT" value="<?php echo PAYPAL_AMOUNT;?>" lang="false" class="span1" />
	<span class="help-block"><?php _e("Amount you want the users to pay for posting.");?>.</span>
</p>
<p>
	<label><?php _e("Amount by category");?>:</label>
	<select name="PAYPAL_AMOUNT_CATEGORY" >
	<option <?php if(PAYPAL_AMOUNT_CATEGORY)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!PAYPAL_AMOUNT_CATEGORY)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Sets on/off the category amount override");?></span>
</p>
<p>
	<label><?php _e("Paypal currency");?>:</label>
	<select  name="PAYPAL_CURRENCY">
	<?php 
	$currency_codes=array(
	'Australian Dollars (A $)' => 'AUD',
	'Canadian Dollars (C $)' =>	'CAD',
	'Euros (€)' =>	'EUR',
	'Pounds Sterling (£)' =>	'GBP',
	'Yen (¥)' =>	'JPY',
	'U.S. Dollars ($)' =>	'USD',
	'New Zealand Dollar ($)' =>	'NZD',
	'Swiss Franc' =>	'CHF',
	'Hong Kong Dollar ($)' =>	'HKD',
	'Singapore Dollar ($)' =>	'SGD',
	'Swedish Krona' =>	'SEK',
	'Danish Krone' =>	'DKK',
	'Polish Zloty' =>	'PLN',
	'Norwegian Krone' =>	'NOK',
	'Hungarian Forint' =>	'HUF',
	'Czech Koruna' =>	'CZK',
	'Israeli Shekel' =>	'ILS',
	'Mexican Peso' =>	'MXN',
	'Brazilian Real (only for Brazilian users)' =>	'BRL',
	'Malaysian Ringgits (only for Malaysian users)' =>	'MYR',
	'Philippine Pesos' =>	'PHP',
	'Taiwan New Dollars' =>	'TWD',
	'Thai Baht' =>	'THB'
	);
	
	foreach($currency_codes as $k=>$v){
		if (PAYPAL_CURRENCY==$v) echo '<option value="'.$v.'" selected=selected >'.$k.'</option>';
		else echo '<option value="'.$v.'">'.$k.'</option>';
	}
	?>
	</select>
	<span class="help-block"><?php _e("Currency to get the payments.");?>.</span>
</p>
<p>
	<label><?php _e("Paypal sandbox");?>:</label>
	<select name="PAYPAL_SANDBOX" >
	<option <?php if(PAYPAL_SANDBOX)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!PAYPAL_SANDBOX)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Sets on/off the sanbox to test paypal payment");?>.</span>
</p>
</fieldset>
</div>



<div class="settingsTitle" onclick="showSetting('imgconf');"><h3><?php _e("Image Settings");?></h3></div>
<div class="settingsTable" id="imgconf">
<fieldset>
<p>
	<label><?php _e("Number of Images");?>:</label>
	<input type="text" name="MAX_IMG_NUM" value="<?php echo MAX_IMG_NUM;?>" lang="false" class="span1" />
	<span class="help-block"><?php _e("Number of images that can be posted, 0 disable all the images");?>.</span>
</p>
<p>
	<label><?php _e("Image Size");?>:</label>
	<input type="text" name="MAX_IMG_SIZE" value="<?php echo MAX_IMG_SIZE;?>" lang="false" class="span4" />b 
	<span class="help-block"><?php _e("Max image size allowed");?>.</span>
</p>
<p>
	<label><?php _e("Image Folder");?>:</label>
	<input type="text" name="IMG_UPLOAD" value="<?php echo IMG_UPLOAD;?>" lang="false" class="span4" />
	<span class="help-block"><?php _e("Image upload directory name");?>.</span>
</p>
<p>
	<label><?php _e("Image Full Path");?>:</label>
	<input type="text" name="IMG_UPLOAD_DIR" value="<?php echo IMG_UPLOAD_DIR;?>" lang="false" class="span4" />
	<span class="help-block"><?php _e("Full path where the images will be stored");?>.</span>
</p>
<p>
	<label><?php _e("Image Types");?>:</label>
	<input type="text" name="IMG_TYPES" value="<?php echo IMG_TYPES;?>" lang="false" class="span4" />
	<span class="help-block"><?php _e("Type of images allowed, separated by comma");?>.</span>
</p>
<p>
	<label><?php _e("Image Resize");?>:</label>
	<input type="text" name="IMG_RESIZE" value="<?php echo IMG_RESIZE;?>" lang="false" class="span1" />px 
	<span class="help-block"><?php _e("Size of the images uploaded");?>.</span>
</p>
<p>
	<label><?php _e("Thumbs Resize");?>:</label>
	<input type="text" name="IMG_RESIZE_THUMB" value="<?php echo IMG_RESIZE_THUMB;?>" lang="false" class="span1" />px 
	<span class="help-block"><?php _e("Size of the thumbs generated");?>.</span>
</p>
</fieldset>
</div>



<div class="settingsTitle" onclick="showSetting('Sitemap');"><h3><?php _e("Rss & Sitemap");?></h3></div>
<div class="settingsTable" id="Sitemap">
<fieldset>
<p>
	<label><?php _e("RSS Items");?>:</label>
	<input type="text" name="RSS_ITEMS" value="<?php echo RSS_ITEMS;?>" lang="false" class="span1" />
	<span class="help-block"><?php _e("Number of items to display in RSS");?>.</span>
</p>
<p>
	<label><?php _e("RSS Images");?>:</label>
	<select name="RSS_IMAGES" >
	<option <?php if(RSS_IMAGES)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!RSS_IMAGES)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Display images in RSS");?>.</span>
</p>
<p>
	<label><?php _e("Sitemap File Path");?>:</label>
	<input type="text" name="SITEMAP_FILE" value="<?php echo SITEMAP_FILE;?>" lang="false" class="span4" />
	<span class="help-block"><?php _e("Path for the sitemap");?>.</span>
</p>
<p>
	<label><?php _e("Sitemap Expires");?>:</label>
	<input type="text" name="SITEMAP_EXPIRE" value="<?php echo SITEMAP_EXPIRE;?>" lang="false" class="span4" />seconds 
	<span class="help-block"><?php _e("Generates new sitemap after expire");?>.</span>
</p>
<p>
	<label><?php _e("Sitemap deleted on post");?>:</label>
	<select name="SITEMAP_DEL_ON_POST" >
	<option <?php if(SITEMAP_DEL_ON_POST)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!SITEMAP_DEL_ON_POST)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("On new post generate new sitemap");?>.</span>
</p>
<p>
	<label><?php _e("Sitemap on category");?>:</label>
	<select name="SITEMAP_DEL_ON_CAT" >
	<option <?php if(SITEMAP_DEL_ON_CAT)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!SITEMAP_DEL_ON_CAT)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("On new/updated category generate new sitemap");?>.</span>
</p>
<p>
	<label><?php _e("Sitemap ping to Google");?>:</label>
	<select name="SITEMAP_PING" >
	<option <?php if(SITEMAP_PING)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!SITEMAP_PING)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("On any update will ping Google about the changes, before activate register your site at");?> <a href="http://www.google.com/webmasters/tools/"><?php _e("Google Webmaster Tools");?></a></span>
</p>
</fieldset>
</div>



<div class="settingsTitle" onclick="showSetting('cache');"><h3><?php _e("Cache Configuration");?></h3></div>
<div class="settingsTable" id="cache">
<fieldset>
<p>
	<label><?php _e("Cache Active");?>:</label>
	<select name="CACHE_ACTIVE" >
	<option <?php if(CACHE_ACTIVE)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!CACHE_ACTIVE)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Sets on/off the cache system");?>.</span>
</p>
<p>
	<label><?php _e("Cache type");?>:</label>
	<select name="CACHE_TYPE" >
		<option <?php if(CACHE_TYPE=='auto')  echo "selected=selected";?> >auto</option>
		<?php 
		$cache_types= Cache::get_instance()->get_available_cache('array');
		foreach($cache_types as $c=>$v){
			if ($v[1]){
				if($v[0]!=CACHE_TYPE) echo '<option>'.$v[0].'</option>';
				else echo '<option selected=selected >'.$v[0].'</option>';
			}
		}
		?>
	</select>
	<span class="help-block"><?php _e("Sets the cache you want to use in the system");?>.</span>
</p>
<p>
	<label><?php _e("Cache File Path");?>:</label>
	<input type="text" name="CACHE_DATA_FILE" value="<?php echo CACHE_DATA_FILE;?>" lang="false" class="span4" />
	<span class="help-block"><?php _e("Path for the cache");?>.</span>
</p>
<p>
	<label><?php _e("Cache Expires");?>:</label>
	<input type="text" name="CACHE_EXPIRE" value="<?php echo CACHE_EXPIRE;?>" lang="false" class="span4" />s
</p>
<p>
	<label><?php _e("Cache deleted on post");?>:</label>
	<select name="CACHE_DEL_ON_POST" >
	<option <?php if(CACHE_DEL_ON_POST)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!CACHE_DEL_ON_POST)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("On new post generate deletes the cache");?>.</span>
</p>
<p>
	<label><?php _e("Cache deleted on category");?>:</label>
	<select name="CACHE_DEL_ON_CAT" >
	<option <?php if(CACHE_DEL_ON_CAT)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!CACHE_DEL_ON_CAT)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("On new/updated category deletes the cache");?>.</span>
</p>
</fieldset>
</div>



<div class="settingsTitle" onclick="showSetting('etools');"><h3><?php _e("External Tools");?></h3></div>
<div class="settingsTable" id="etools">
<fieldset>
<p>
	<label><a href="http://www.google.com/analytics/">Google Analytics</a>:</label>
	<input type="text" name="ANALYTICS" value="<?php echo ANALYTICS;?>" class="span4" />
	<span class="help-block"><?php _e("Code in the footer for tracking, for example: UA-4562297-13. Empty to disable");?>.</span>
</p>
<p>
	<label><a href="http://wordpress.com/api-keys/">Akismet KEY</a>:</label>
	<input type="text" name="AKISMET" value="<?php echo AKISMET;?>" class="span4" />
	<span class="help-block"><?php _e("Prevent spam");?>.</span>
</p>
<p>
	<label><?php _e("Advanced spam by country");?>:</label>
	<select name="SPAM_COUNTRY" >
	<option <?php if(SPAM_COUNTRY)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!SPAM_COUNTRY)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("If enabled check if country is in blacklist, using hostip.info service");?>.</span>
</p>
<p>
	<label><?php _e("Countries blacklist");?>:</label>
	<input type="text" name="SPAM_COUNTRIES" value="<?php echo SPAM_COUNTRIES;?>" class="span4" />
	<span class="help-block"><?php _e("Comma separated countries, this countries can't post content");?>.</span>
</p>
<p>
	<label><a href="http://code.google.com/apis/maps/signup.html">Google Maps KEY</a>:</label>
	<input type="text" name="MAP_KEY" value="<?php echo MAP_KEY;?>" class="span4" />
	<span class="help-block"><?php _e("Displays Google Maps with posts");?>.</span>
</p>
<p>
	<label><?php _e("Center Maps at");?>:</label>
	<input type="text" name="MAP_INI_POINT" value="<?php echo MAP_INI_POINT;?>" class="span4" />
	<span class="help-block"><?php _e("Map would be centered in this address");?>. <?php _e("For example");?>: Barcelona, Spain.</span>
</p>
<p>
	<label><?php _e("Comments");?>:</label>
	<input type="text" name="DISQUS" value="<?php echo DISQUS;?>" class="span4" />
	<span class="help-block"><?php _e("Account Name enables comments for posts threads with");?> <a href="http://disqus.com/comments/register/">Disqus</a>.</span>
</p>
<p>
	<label><?php _e("Video");?>:</label>
	<select name="VIDEO" >
	<option <?php if(VIDEO)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!VIDEO)  echo "selected=selected";?> >FALSE</option>
	</select>
	<span class="help-block"><?php _e("Allows YouTube videos on posts using [youtube=URLVIDEO]");?>.</span>
</p>
<p>
	<label><a href="http://ocaku.com/">Ocaku</a>:</label>
	<select name="OCAKU" >
	<option <?php if(OCAKU)  echo "selected=selected";?> >TRUE</option>
	<option <?php if(!OCAKU)  echo "selected=selected";?> >FALSE</option>
	</select><?php _e("Enable/Disable Ocaku Classifieds Community");?>
</p>
<p>
	<label><a href="http://api.ocaku.com/">Ocaku KEY</a>:</label>
	<input name="OCAKU_KEY" value="<?php echo OCAKU_KEY;?>" class="span4" />
    <span class="help-block"><?php _e("API key to use Ocaku. Do not lose this! if you reinstall copy paste here the key.");?> 
    <a href="rememberKEY.php" target="_blank"> REMEMBER KEY</a>.</span>
</p>
</fieldset>
</div>


<input type="hidden" name="PASSWORD_SIZE" value="<?php echo PASSWORD_SIZE;?>" />
	<div class="form-actions">
		<input class="btn btn-primary" type="submit" name="submit" id="submit" value="<?php _e("Save Settings");?>" />
        <button class="btn btn-success" onclick="openAllSettings();return false;"><?php _e("Open all");?></button> 
		<button class="btn btn-warning" onclick="closeAllSettings();return false;"><?php _e("Close all");?></button>
	</div>
</form>



<?php if (isset($theme_options)){?>
	<h2><?php echo THEME;?> <?php _e('Theme options');?></h2>
	<form class="well" id="theme_options" action="settings.php" method="post">
	
	<?php $defines = get_defined_constants();?>
	
	<?php foreach($theme_options as $option=>$v){?>
		<p>
			<label><?php echo $option;?></label>
			<?php if (is_array($v['values'])){?>
				<select name="THEME_OPTIONS_<?php echo $option;?>" >
					<?php foreach($v['values'] as $o){?>
						<option <?php echo ($defines['THEME_OPTIONS_'.$option]==$o)?' selected=selected ':'';?>><?php echo $o?></option>
					<?php };?>
				</select>
			<?php }
			    else{?>
				<input name="THEME_OPTIONS_<?php echo $option;?>" value="<?php echo htmlspecialchars((!isset($defines['THEME_OPTIONS_'.$option])) ? $v['values']:$defines['THEME_OPTIONS_'.$option], ENT_COMPAT | ENT_HTML401,CHARSET);?>" class="span4" />

			<?php }?>
			<span class="help-block"><?php _e($v['desc']);?></span>
		</p>
	<?php }?>
		
		<input type="hidden" name="THEME_OPTIONS" value="1" />
		<div class="form-actions">
			<input class="btn btn-primary" type="submit" name="submit" id="submit" value="<?php _e("Save");?>" />
		</div>
	</form>
<?php }?>



<?php
require_once('footer.php');
?>