<?php
////////////////////////////////////////////////////////////
//Sidebar generator
////////////////////////////////////////////////////////////

function getSideBar($beg,$end){//generates the sidebar reading from the config.php
	$widgets=explode(",",SIDEBAR);
	foreach ($widgets as $widget){
        $widget="sb_".$widget;
	    echo $widget($beg,$end);
	}
}

//////////////////////////////////////////////////////
//Side bar functions. ALL OF THEM MUST START ON sb_FUNCTION_NAME, to add them in the config file just write FUNCTION_NAME,
/////////////////////////////////////////////////////

function sb_new($beg,$end){//add new
	return $beg.'<b><a title="'.T_("Publish a new Ad").'" href="'.SITE_URL.newURL().'">'.T_("Publish a new Ad").'</a></b>'.$end;
}
////////////////////////////////////////////////////////////
function sb_search($beg,$end){//serach form
	global $categoryName,$idCategory,$currentCategory,$type,$location;
		if (cG("s")=="") $ws=T_("Search")."...";
		else $ws=cG("s");
		$search= "<form method=\"get\" action=\"".SITE_URL."\">
			<p><input name=\"s\" id=\"s\" maxlength=\"15\" title=\"".T_("Search")."\"
				onblur=\"this.value=(this.value=='') ? '$ws' : this.value;\" 
				onfocus=\"this.value=(this.value=='$ws') ? '' : this.value;\" 
				value=\"$ws\" type=\"text\" /></p>";
		
		if(isset($categoryName)) $search.='<p><input type="hidden" name="category" value="'.$currentCategory.'" /></p>';
        if(isset($location)) $search.='<p><input type="hidden" name="location" value="'.getLocationFriendlyName($location).'" /></p>';
		
		$search.='</form>';
		
		$search.=advancedSearchURL();
		      		
	return $beg.$search.$end;
}
////////////////////////////////////////////////////////////
function sb_locations($beg,$end){//locations list (state or city)
    if (LOCATION){
        global $location;//,$currentCategory,$selectedCategory;
           
        if (isset($location)) {
            $currentlocation = getLocationName($location);
            $locationparent = getLocationParent($location);
        } else $locationparent = 0;
        
        $locationcontent = "<h4>".T_("Location")."</h4>";
        
        if ($locationparent != 0) $locationcontent .= "<h4><a href=\"".SITE_URL.catURL($currentCategory,$selectedCategory,getLocationFriendlyName($locationparent))."\">".getLocationName($locationparent)."</a> / $currentlocation</h4>";
        elseif (isset($location)) {
          $locationroot = LOCATION_ROOT;
          if ($locationroot == "") $locationroot = T_("Home");
          $locationcontent .= "<h4><a href=\"".SITE_URL.catURL($currentCategory,$selectedCategory,$_unused_)."\">$locationroot</a> / $currentlocation</h4>";
        }
        
        if (!isset($location) || $location=='' ) $location = 0;
        $ocdb=phpMyDB::GetInstance();
        $query = "select idLocation, name, friendlyName from ".TABLE_PREFIX."locations where idLocationParent=$location order by name";
        $result=$ocdb->getRows($query);
        
        $i = 0;
    	$q = count($result);
    	$z = round($q/2);
    
        foreach ($result as $location_row ) {
            if ($i==0 or $i==$z) $locationcontent .= "<div class=\"columns\"><ul>";
            
            $locationcontent .= "<li><a href=\"".SITE_URL.catURL($currentCategory,$selectedCategory,$location_row["friendlyName"])."\">".$location_row["name"]."</a></li>";
            
            if ($i==($z-1) or $i==($q-1)) $locationcontent .= "</ul></div>";
    
            $i++;
        }
        
        $locationcontent .= "<div class=\"clear\" /></div>";
        
        return $beg.$locationcontent.$end;
    }
}
////////////////////////////////////////////////////////////
function sb_infolinks($beg,$end){//site stats info and tools linsk rss map..
	global $idCategory,$currentCategory,$type,$location;
	$info = '<b>'.T_("Total Ads").':</b> '.totalAds($idCategory).SEPARATOR
		.' <b>'.T_("Views").':</b> '.totalViews($idCategory).SEPARATOR
		.' <b><a href="'.rssURL().'">RSS</a></b>';
		 if (MAP_KEY!="") $info.=SEPARATOR.'<b><a href="'.SITE_URL.'/'.mapURL().'?category='.$currentCategory.'&amp;type='.$type.'">'.T_("Map").'</a></b>';
   return $beg.$info.$end;
}
////////////////////////////////////////////////////////////
function sb_donate($beg,$end){//donation
	return $beg.'<h4>'.T_("Recommended").'</h4><br />Please donate to help developing this software. No matter how much, even small amounts are very welcome.
<a href="http://j.mp/ocdonate" target="_blank">
<img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" alt="" />
</a> Thanks. <br /><br /> To erase this please go to: Admin->Settings->Look and Feel->Widget Sidebar->donate.'.$end;
}
////////////////////////////////////////////////////////////
function sb_advertisement($beg,$end){//advertisement
	return $beg.ADVERT_SIDEBAR.$end;
}
////////////////////////////////////////////////////////////
function sb_popular($beg,$end){//popular items
	if (COUNT_POSTS){
		global $categoryName,$idCategory;
		$ret=$beg."<h4>".T_("Most popular")." $categoryName:</h4>";
		$ret.=generatePopularItems(7,5,$idCategory);
		$ret.="*".T_("Last Week").$end;
		return $ret;
	}
}
////////////////////////////////////////////////////////////
function sb_item_tools($beg,$end){//utils for admin
	global $idItem,$itemPassword;
	if(isset($idItem)&&isset($_SESSION['admin'])){
		echo $beg;?>
		<h4><?php _e("Classifieds tools");?>:</h4>
		<ul>
			<li><a href="<?php echo itemManageURL();?>?post=<?php echo $idItem;?>&amp;pwd=<?php echo $itemPassword;?>&amp;action=edit">
				<?php _e("Edit");?></a>
			</li>
			<li><a onClick="return confirm('<?php _e("Activate");?>?');" 
				href="<?php echo itemManageURL();?>?post=<?php echo $idItem;?>&amp;pwd=<?php echo $itemPassword;?>&amp;action=activate">
				<?php _e("Activate");?></a>
			</li>
			<li><a onClick="return confirm('<?php _e("Deactivate");?>?');" 
				href="<?php echo itemManageURL();?>?post=<?php echo $idItem;?>&amp;pwd=<?php echo $itemPassword;?>&amp;action=deactivate">
				<?php _e("Deactivate");?></a>
			</li>
			<li>	<a onClick="return confirm('<?php _e("Spam");?>?');"
					href="<?php echo itemManageURL();?>?post=<?php echo $idItem;?>&amp;pwd=<?php echo $itemPassword;?>&amp;action=spam">
						<?php _e("Spam");?></a>
			</li>
			<li><a onClick="return confirm('<?php _e("Delete");?>?');"
				href="<?php echo itemManageURL();?>?post=<?php echo $idItem;?>&amp;pwd=<?php echo $itemPassword;?>&amp;action=delete">
				<?php _e("Delete");?></a>
			</li>
			<li><a href="<?php echo SITE_URL;?>/admin/logout.php"><?php _e("Logout");?></a>
			</li>
		</ul>
	<?php 
		echo $end;
	}
}
////////////////////////////////////////////////////////////
function sb_links($beg,$end){//links sitemap
		echo $beg;
		
	?>
		<h4><?php _e("Menu");?>:</h4>
		<ul>
		    <?php if(FRIENDLY_URL) {?>
			    <li><a href="<?php echo SITE_URL."/".u(T_("Advanced Search"));?>.htm"><?php _e("Advanced Search");?></a></li>
			    <li><a href="<?php echo SITE_URL."/".u(T_("Sitemap"));?>.htm"><?php _e("Sitemap");?></a></li>   
			    <li><a href="<?php echo SITE_URL."/".u(T_("Privacy Policy"));?>.htm"><?php _e("Privacy Policy");?></a></li>
		    <?php }else { ?>
		        <li><a href="<?php echo SITE_URL;?>/content/search.php"><?php _e("Advanced Search");?></a></li>
		        <li><a href="<?php echo SITE_URL;?>/content/site-map.php"><?php _e("Sitemap");?></a></li>
			    <li><a href="<?php echo SITE_URL;?>/content/privacy.php"><?php _e("Privacy Policy");?></a></li>
		    <?php } ?>
		    <li><a href="<?php echo SITE_URL."/".contactURL();?>"><?php _e("Contact");?></a></li>
		    <li><a href="<?php echo SITE_URL;?>/admin/"><?php _e("Administrator");?></a></li>
		</ul>
	<?php 
	echo $end;
}

////////////////////////////////////////////////////////////
function sb_comments($beg,$end){//disqus comments
	if (DISQUS!=""){
		return $beg .'<script type="text/javascript" src="http://disqus.com/forums/'.DISQUS.'/combination_widget.js?num_items=5&hide_mods=0&color=blue&default_tab=recent&excerpt_length=200"></script>'.$end;
	}
}

////////////////////////////////////////////////////////////
function sb_translator($beg,$end){//google translate
    $lang = LANGUAGE;
	return $beg.'<div id="google_translate_element"></div><script type="text/javascript">
	function googleTranslateElementInit() {
	new google.translate.TranslateElement({pageLanguage: \''.$lang.'\'}, \'google_translate_element\');
	}</script><script src="http://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" type="text/javascript"></script>'.$end;
}

///////////////////////////////////////////////////////////
function sb_theme($beg,$end){//theme selector
	if (THEME_SELECTOR){
	    echo $beg;?>
	    <b onclick="openClose('theme_sel');" style="cursor:pointer;"><?php echo THEME;?></b>
	    <div id="theme_sel" style="display:none;"><ul>
		<?php
		$themes = scandir(SITE_ROOT."/themes");
		foreach ($themes as $theme) {
			if($theme!="" && $theme!=THEME && $theme!="." && $theme!=".." && $theme!="wordcloud.css"){
				echo '<li><a href="'.SITE_URL.'/?theme='.$theme.'">'.$theme.'</a></li>';
			}
		}
	    echo "</ul></div>" . $end;
	}
}
////////////////////////////////////////////////////////////
function sb_categories_cloud($beg,$end){// popular categories
	global $categoryName;
	if(!isset($categoryName)){ 
		echo $beg."<h4>".T_("Categories")."</h4><br />";
		generateTagPopularCategories();
		echo $end;
	}
}
////////////////////////////////////////////////////////////
function sb_account($beg,$end){
	if (LOGON_TO_POST){
		$account = Account::createBySession();
		if ($account->exists){
			$ret='<h4>'.T_("Welcome").' '.$account->name.'</h4>';
		    $ret.= '<ul><li><a href="'.accountURL().'">'.T_("My Account").'</a></li>';
		    $ret.= '<li><a href="'.accountSettingsURL().'">'.T_("Settings").'</a></li>';
		    $ret.= '<li><a href="'.accountLogoutURL().'">'.T_("Logout").'</a></li></ul>';
		   
		}
		else{
		    $ret='<h4>'.T_("Account").'</h4>';
		    $ret.= '<ul><li><a href="'.accountLoginURL().'">'.T_("Login").'</a></li>';
		    $ret.= '<li><a href="'.accountRegisterURL().'">'.T_("Register").'</a></li></ul>';
		}		
		return $beg.$ret.$end;
	}
	
}

////////////////////////////////////////////////////////////
function sb_rss($beg,$end){
	$ret = '<h4>'.RSS_SIDEBAR_NAME.'</h4>';
	$ret.= '<ul>'.rssReader(RSS_SIDEBAR_URL,RSS_SIDEBAR_COUNT,CACHE_ACTIVE,'<li>','</li>').'</ul>';
	return $beg.$ret.$end;
}


?>