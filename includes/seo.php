<?php
////////////////////////////////////////////////////////////
//SEO generator
////////////////////////////////////////////////////////////
//metas implemented in header.php

$html_title ='';
$html_description = '';

//item
if (isset($idItem)) {
	//title
		$html_title=$itemTitle;
		if (isset($itemType)) $html_title.=' '.ucwords(getTypeName($itemType));
        if ($itemLocation!="") $html_title.=" ".getLocationName($itemLocation);
		if ($itemPlace!="") $html_title.=" ".$itemPlace;
		if ($itemPrice!=0) $html_title.="  ".getPrice($itemPrice);
		
		//to display the category in the title uncomment this:
		/*$html_title.=SEPARATOR;
		if ($categoryParent!=0) $html_title.=$selectedCategoryName. " ";
		$html_title.=$categoryName.SEPARATOR;*/
		
	//end title
	$html_description=$itemDescription;	
}
//Smart 404
elseif(strpos($_SERVER["SCRIPT_NAME"], "404.php")>0){
	$html_title.=u($_SERVER["REQUEST_URI"]).SEPARATOR.SITE_NAME;
	$html_description=$html_title;	
}
//for new item
elseif(strpos($_SERVER["SCRIPT_NAME"], "item-new.php")>0){
	 //title
		$html_title=T_("Publish a new Ad").SEPARATOR;
		if (isset($categoryName)){//new item with category selected
			if ($categoryParent!=0) $html_title.=$selectedCategoryName. " ";
			$html_title.=$categoryName.SEPARATOR;
		}
		//else $html_title.=getCategories().SEPARATOR;
		$html_title.=SITE_NAME;
	//end title
	$html_description=$html_title;	
}
//categories
elseif (isset($categoryName)) {
	//title
		//$html_title=T_("Classifieds")." ";
		if (isset($type)) $html_title.=ucwords(getTypeName($type)).SEPARATOR;
		$html_title.=$categoryName.SEPARATOR;
		if ($categoryParent!=0) $html_title.=$selectedCategoryName.SEPARATOR;
        if (isset($location)) $html_title.=getLocationName($location).SEPARATOR;
        $html_title.=SITE_NAME;//.SEPARATOR;	
		
	//end title
	$html_description=$categoryDescription;	

}
//locations
elseif (isset($location)) {
  //title
    //$html_title=T_("Classifieds")." ";
    if (isset($type)) $html_title.=ucwords(getTypeName($type)).SEPARATOR;
    $html_title.=getLocationName($location).SEPARATOR;
    $html_title.=SITE_NAME;//.SEPARATOR;  
    //end title
  $html_description=$html_title; 
}
elseif ((strlen(cG("title"))>=MIN_SEARCH_CHAR) || strlen(cG("s"))>=MIN_SEARCH_CHAR){//search
    if (cG("title")!="")$html_title.=cG("title").' ';
    if (cG("s")!="")$html_title.=cG("s").' ';
    if (cG("place")!="")$html_title.=cG("place").' ';
    //$html_title.=SITE_NAME;
	$html_description=$html_title;	
}
//home and RSS home
elseif ( isHome()) {
	//title
		//$html_title=T_("Classifieds")." ";
		if (isset($type)) $html_title.=ucwords(getTypeName($type)).SEPARATOR;
		if (isset($location)) $html_title.=getLocationName($location).SEPARATOR;
		//$html_title.=getCategories().SEPARATOR;
		$html_title.=SITE_NAME;
	//end title
	if (SITE_DESCRIPTION!='') $html_description=SITE_DESCRIPTION;
	else $html_description=$html_title;	
}
//search form
elseif(strpos($_SERVER["SCRIPT_NAME"], "search.php")>0){
	$html_title.=T_("Advanced Search").SEPARATOR.SITE_NAME;
	$html_description=$html_title;	
}
//contact form
elseif(strpos($_SERVER["SCRIPT_NAME"], "contact.php")>0){
	$html_title.=T_("Contact").SEPARATOR.SITE_NAME;
	$html_description=$html_title;	
}
//sitemap
elseif(strpos($_SERVER["SCRIPT_NAME"], "site-map.php")>0){
	$html_title.=T_("Sitemap").SEPARATOR.SITE_NAME;
	$html_description=$html_title;	
}
//privacy
elseif(strpos($_SERVER["SCRIPT_NAME"], "privacy.php")>0){
	$html_title.=T_("Privacy Policy").SEPARATOR.SITE_NAME;
	$html_description=$html_title;	
}

// common in all
if (isset($page)&&$page>1) $html_title.=SEPARATOR.T_("page")." ".$page;

//better SEO with phpSEO class http:/neo22s.com/phpseo
$seo = new phpSEO($html_title,CHARSET);
$html_keywords= $seo->getKeyWords(5);

$seo = new phpSEO($html_description,CHARSET);
$html_keywords.=", ". $seo->getKeyWords(15);
$html_description= $seo->getMetaDescription(160);
unset($seo);

///////FUNCTIONS////////////////////////////////////////////
////////////////////////////////////////////////////////////
function itemURL($idPost,$category,$type,$title,$subcat=""){//returns de url for the item, if you change this be aware that you need to change it in the .htaccess as well.
   if(FRIENDLY_URL){
        if ($subcat!="" && $category!=$subcat) $url='/'.$subcat.'/'.$category.'/'.friendly_url($title).'-'.$idPost.'.htm';
        else $url='/'.$category.'/'.friendly_url($title).'-'.$idPost.'.htm'; // old= "/$idPost/$type/$category/".friendly_url($title);
   }
   else $url="/content/item.php?item=$idPost&type=$type&category=$category&title=".friendly_url($title);//no friendly url activated
   return $url;
}
////////////////////////////////////////////////////////////
function catURL($category,$subcat="",$location=""){//returns de url for the category, if you change this be aware that you need to change it in the .htaccess as well.
   if (LOCATION){
       if (isset($location)) if ($location=="") global $location;
       
       $locationurl = "";
       if ($location!=""){
            if (is_numeric($location)){
                if(FRIENDLY_URL) $locationurl = getLocationFriendlyName($location)."/";
                else $locationurl = "&location=".getLocationFriendlyName($location);
            } else {
                if(FRIENDLY_URL) $locationurl = $location."/";
                else $locationurl = "&location=$location";
            }
            
            if ($category=="" && $subcat == "") $category = strtolower(T_("Classifieds"));
       }
       
       if(FRIENDLY_URL){
           if ($subcat!="" && $category!=$subcat)  $url='/'.$fix.$subcat.'/'.$category.'/'.$locationurl; 
           else {
                if ($category == strtolower(T_("Classifieds"))) $url='/'.$category.'/'.$locationurl;
                else {
                    if ($category!="") $url='/l/'.$category.'/'.$locationurl;
                    else $url= "/";
                }
           }
       }
       else {//no friendly url activated
            if ($category!="") $url='/content/index.php?category='.$category.$locationurl;
            else $url= "/";
       }
   } else {
       if(FRIENDLY_URL){
           if ($subcat!="" && $category!=$subcat)  $url='/'.$subcat.'/'.$category.'/'; 
           else  $url='/'.$category.'/';  
       }
       else $url='/content/index.php?category='.$category;//no friendly url activated
   }

   return $url;
}
////////////////////////////////////////////////////////////
function typeURL($type,$category){//returns de url for the type, if you change this be aware that you need to change it in the .htaccess as well.
   global $location;
   if (!empty($location)) {
       if (FRIENDLY_URL) $params = "/".getLocationFriendlyName($location)."/";
       else $params = "&location=".getLocationFriendlyName($location);
   }

   if (empty($category)) $category="all"; 
   if (FRIENDLY_URL) $url='/'.getTypeName($type).'/'.$category.$params;
   else $url='/content/index.php?category='.$category.'&type='.$type.$params;
   
   return $url;
}
////////////////////////////////////////////////////////////
function newURL(){//returns de url to post new item
   global $currentCategory, $location;
   if (!empty($currentCategory)) $params = "?category=$currentCategory";
   if (!empty($location)){
        if ($params=="") $params = "?location=".getLocationFriendlyName($location);
        else $params .= "&location=".getLocationFriendlyName($location);
   }
   
   if (FRIENDLY_URL) $url='/'.u(T_("Publish a new Ad")).'.htm'.$params;
   else $url='/content/item-new.php'.$params;
   return $url;
}
////////////////////////////////////////////////////////////
function mapURL(){//returns de url for the map
   if (FRIENDLY_URL) $url=u(T_("Map")).'.htm';
   else $url='content/map.php';
   return $url;
}
////////////////////////////////////////////////////////////
function contactURL(){//returns de url for the contact
   if (FRIENDLY_URL) $url=u(T_("Contact")).'.htm';
   else $url='content/contact.php';
   return $url;
}
////////////////////////////////////////////////////////////
function accountPostsURL($type,$category,$email){//returns de url for posts by account
   global $location;
   if (!empty($location)) {
       if (FRIENDLY_URL) $params = "/".getLocationFriendlyName($location)."/";
       else $params="&location=".getLocationFriendlyName($location);
   }
   
   $account=new Account($email);
   if ($account->exists){
        $params.="&contact=".$account->id;
   }
   
   if (empty($category)) $category="all"; 
   if (FRIENDLY_URL) $url=''.getTypeName($type).'/'.$category.$params;
   else $url='/content/index.php?category='.$category.'&type='.$type.$params;
   
   return $url;
}
////////////////////////////////////////////////////////////
function termsURL(){//returns de url for terms
   if (FRIENDLY_URL) $url=u(T_("terms")).'.htm';
   else $url='/content/terms.php';
   return $url;
}
////////////////////////////////////////////////////////////
function privacyPolicyURL(){//returns de url for Privacy Policy
   if (FRIENDLY_URL) $url=u(T_("Privacy Policy")).'.htm';
   else $url='/content/privacy.php';
   return $url;
}
////////////////////////////////////////////////////////////
function advancedSearchURL(){//returns de url for the advanced search
   global $currentCategory, $location;
   if (!empty($currentCategory)) $params = "?category=$currentCategory";
   if (!empty($location)){
        if ($params=="") $params = "?location=".getLocationFriendlyName($location);
        else $params .= "&location=".getLocationFriendlyName($location);
   }
   
   if(FRIENDLY_URL) $url='<a href="'.SITE_URL.'/'.u(T_("Advanced Search")).'.htm'.$params.'">'.T_("Advanced Search").'</a>';
   else $url='<a href="'.SITE_URL.'/content/search.php'.$params.'">'.T_("Advanced Search").'</a>';
   
   return $url;
}
////////////////////////////////////////////////////////////
function accountRegisterURL(){//returns de url for account register
   global $currentCategory, $location;
   if (!empty($currentCategory)) $params = "?category=$currentCategory";
   if (!empty($location)){
        if ($params=="") $params = "?location=".getLocationFriendlyName($location);
        else $params .= "&location=".getLocationFriendlyName($location);
   }
   
   if(FRIENDLY_URL) $url=SITE_URL.'/'.u(T_("Register new account")).'.htm'.$params;
   else $url=SITE_URL.'/content/account/register.php'.$params;
   
   return $url;
}
////////////////////////////////////////////////////////////
function accountLoginURL(){//returns de url for account logon
   global $currentCategory, $location;
   if (!empty($currentCategory)) $params = "?category=$currentCategory";
   if (!empty($location)){
        if ($params=="") $params = "?location=".getLocationFriendlyName($location);
        else $params .= "&location=".getLocationFriendlyName($location);
   }
   
   if(FRIENDLY_URL) $url=SITE_URL.'/'.u(T_("Login")).'.htm'.$params;
   else $url=SITE_URL.'/content/account/login.php'.$params;
   
   return $url;
}
////////////////////////////////////////////////////////////
function accountLogoutURL(){//returns de url for account logout
   if(FRIENDLY_URL) $url=SITE_URL.'/'.u(T_("Logout")).'.htm';
   else $url=SITE_URL.'/content/account/logout.php';
   
   return $url;
}
////////////////////////////////////////////////////////////
function accountRecoverPasswordURL(){//returns de url for account password recovery
   if(FRIENDLY_URL) $url=SITE_URL.'/'.u(T_("Forgot My Password")).'.htm';
   else $url=SITE_URL.'/content/account/recoverpassword.php';
   
   return $url;
}
////////////////////////////////////////////////////////////
function accountSettingsURL(){//returns de url for account settings
   if(FRIENDLY_URL) $url=SITE_URL.'/'.u(T_("Settings")).'.htm';
   else $url=SITE_URL.'/content/account/settings.php';
   
   return $url;
}
////////////////////////////////////////////////////////////
function accountURL(){//returns de url for account
   if(FRIENDLY_URL) $url=SITE_URL.'/'.u(T_("My Account")).'/';
   else $url=SITE_URL.'/content/account/';
   
   return $url;
}
////////////////////////////////////////////////////////////
function rssURL(){//returns de url for account
   if(FRIENDLY_URL) $url=SITE_URL.'/rss/';
   else $url=SITE_URL.'/content/feed-rss.php';
   
   return $url;
}

////////////////////////////////////////////////////////////
function itemManageURL(){//returns de url for account
   if(FRIENDLY_URL) $url=SITE_URL.'/manage/';
   else $url=SITE_URL.'/content/item-manage.php';
   
   return $url;
}


////////////////////////////////////////////////////////////
?>
