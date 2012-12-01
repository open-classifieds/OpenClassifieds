<?php
////////////////////////////////////////////////////////////
//Common Functions
////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////
function ToHtml($string){//replaces for special things
	$string = str_replace ("<br>","<br />", $string);//problem with new lines
	$string = str_replace ("&nbsp;"," ", $string);//problem with spaces
	$string = str_replace ("href=","rel=\"nofollow\" href=", $string);	//nofollow
	return $string;
}

////////////////////////////////////////////////////////////
function checkCSRF($key=''){//trying to prevent CSRF attacks
	//referer limitation
	/*
	$referer = substr($_SERVER['HTTP_REFERER'],0,strlen(SITE_URL));
	
	if ( $referer != '' && $referer!=SITE_URL ){//echo $referer.'---'.SITE_URL;
		return false;//invalid referer!!!
	}
	*/
    
	//correct referer or empty sent by browser, checkign the form
	if ( (!empty($_SESSION['token_'.$key])) && (!empty($_POST['token_'.$key])) ) {//echo $_SESSION['token_'.$key].'---'.$_POST['token_'.$key];
		if ($_SESSION['token_'.$key] == $_POST['token_'.$key]) {//same token session than form
		   return true;
		}	
	}
	
	return false;
}
////////////////////////////////////////////////////////////
function createCSRF($key){//create an input with a token that we check later to prevent CSRF
	//$key variable allows us to have more than 1 form per page and to have more than 1 tab opened with different items
	$token = md5($key.uniqid(rand(), true).ADMIN_PWD);//unique form token
	$_SESSION['token_'.$key] = $token;
	echo '<input type="hidden" name="token_'.$key.'" value="'.$token.'">';
}
////////////////////////////////////////////////////////////
function encode_str ($input){//converts the input into Ascii HTML, to ofuscate a bit
    for ($i = 0; $i < strlen($input); $i++) {
        $output .= "&#".ord($input[$i]).';';
    }
    //$output = htmlspecialchars($output);//uncomment to escape sepecial chars
    return $output;
}
////////////////////////////////////////////////////////////
function remove_querystring_var($key) {
    $arrquery = explode("&", $_SERVER["QUERY_STRING"]);
    
    foreach ($arrquery as $query_value) {
        $valor = substr($query_value, strpos($query_value, "=") + 1);
        $chave = substr($query_value, 0, strpos($query_value, "="));
        $querystring[$chave] = $valor;
    }
    
    unset($querystring[$key]);
    
    foreach ($querystring as $query_key => $query_value) {
        $query[] = "{$query_key}={$query_value}";
    }

    $query = implode("&", $query);

    return $query;
}

////////////////////////////////////////////////////////////
function sqlOption($sql,$name,$option,$empty=""){//generates a select tag with the values specified on the sql, 2nd parameter name for the combo, , 3rd value selected if there's
	$ocdb=phpMyDB::GetInstance();
	$sel="";
	$result =$ocdb->query($sql);//1 value needs to be the ID, second the Name, if there's more doens't work
	$sqloption= "<select name=\"".$name."\" id=\"".$name."\">
	  <option>".$empty."</option>";
	while ($row=mysql_fetch_assoc($result)){
		$first=mysql_field_name($result, 0);
		$second=mysql_field_name($result, 1);

			if ($option==$row[$first]) { $sel="selected=selected";}
			$sqloption=$sqloption .  "<option ".$sel." value='".$row[$first]."'>" .$row[$second]. "</option>";
			$sel="";
	}
		$sqloption=$sqloption . "</select>";
		echo $sqloption;
}
////////////////////////////////////////////////////////////
function sqlOptionGroup($sql,$name,$option,$empty=""){//generates a select tag with the values specified on the sql, 2nd parameter name for the combo, , 3rd value selected if there's
	$ocdb=phpMyDB::GetInstance();
	$result =$ocdb->query($sql);//1 value needs to be the ID, second the Name, 3rd is the group
	//echo $sql;
	$sel="";
	$sqloption= "<select name=\"".$name."\" id=\"".$name."\" onChange=\"validateNumber(this);\" lang=\"false\">
    <option>".$empty."</option>";
	$lastLabel = "";
	while ($row=mysql_fetch_assoc($result)){
		$first=mysql_field_name($result, 0);
		$second=mysql_field_name($result, 1);
		$third= mysql_field_name($result,2);

		if($lastLabel!=$row[$third]){
			if($lastLabel!=""){
				$sqloption.="</optgroup>";
			}
			$sqloption.="<optgroup label='$row[$third]'>";
			$lastLabel = $row[$third];
		}

			if ($option==$row[$first]) { $sel="selected=selected";}
			$sqloption=$sqloption .  "<option ".$sel." value='".$row[$first]."'>" .$row[$second]. "</option>";
			$sel="";
	}
		$sqloption.="</optgroup>";
		$sqloption=$sqloption . "</select>";
		echo $sqloption;
}
////////////////////////////////////////////////////////////
function sqlOptionGroupScript($sql,$name,$option,$empty="",$script=""){//generates a select tag with the values specified on the sql, 2nd parameter name for the combo, , 3rd value selected if there's... add script
	$ocdb=phpMyDB::GetInstance();
	$result =$ocdb->query($sql);//1 value needs to be the ID, second the Name, 3rd is the group
	//echo $sql;
	$sel="";
	$sqloption= "<select name=\"".$name."\" id=\"".$name."\" ".$script." lang=\"false\">
    <option>".$empty."</option>";
	$lastLabel = "";
	while ($row=mysql_fetch_assoc($result)){
		$first=mysql_field_name($result, 0);
		$second=mysql_field_name($result, 1);
		$third= mysql_field_name($result,2);

		if($lastLabel!=$row[$third]){
			if($lastLabel!=""){
				$sqloption.="</optgroup>";
			}
			$sqloption.="<optgroup label='$row[$third]'>";
			$lastLabel = $row[$third];
		}

			if ($option==$row[$first]) { $sel="selected=selected";}
			$sqloption=$sqloption .  "<option ".$sel." value='".$row[$first]."'>" .$row[$second]. "</option>";
			$sel="";
	}
		$sqloption.="</optgroup>";
		$sqloption=$sqloption . "</select>";
		echo $sqloption;
}
////////////////////////////////////////////////////////////
function generatePassword ($length = PASSWORD_SIZE){
	  // start with a blank password
	  $password = "";
	  // define possible characters
	  $possible = "0123456789abcdefghijklmnopqrstuvwxyz";
	  // set up a counter
	  $i = 0;
	  // add random characters to $password until $length is reached
	  while ($i < $length) {
		// pick a random character from the possible ones
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		// we do not want this character if it's already in the password
		if (!strstr($password, $char)) {
		  $password .= $char;
		  $i++;
		}
	  }
	  // done!
	  return $password;
}
////////////////////////////////////////////////////////////
function setDate($L_date,$L_dateFormat=DATE_FORMAT){//sets a date in a format
	if(strlen($L_date)>0){
		$L_arrTemp = explode(" ",$L_date);
		$L_strDate = $L_arrTemp[0]; // 2007-07-21 year month day
		$L_arrDate = explode("-",$L_strDate);// split date
		$L_strYear =  $L_arrDate[0];
		$L_strMonth = $L_arrDate[1];
		$L_strDay = $L_arrDate[2];

		if($L_dateFormat == 'yyyy-mm-dd'){//default
		    return $L_arrTemp[0];
        }
		elseif($L_dateFormat == "dd-mm-yyyy"){//day month year
			$returnDate = $L_strDay."-".$L_strMonth."-".$L_strYear;
			return $returnDate;
		}
		elseif($L_dateFormat == "mm-dd-yyyy"){//month day year
			$returnDate = $L_strMonth."-".$L_strDay."-".$L_strYear;
			return $returnDate;
		}
		else
			return $L_date; // or false ?
	}
	else return false;
}
////////////////////////////////////////////////////////////
function getTypeName($type){//get the type name
	if ($type==TYPE_OFFER) $type=T_("offer");
	else $type=T_("need");

	return $type;
}
////////////////////////////////////////////////////////////
function getTypeNum($type){//get the type in number
	if ($type==T_("offer")){
		$type=TYPE_OFFER;
	}
	else $type=TYPE_NEED;

	return $type;
}
////////////////////////////////////////////////////////////
function getLocationName($location){//get the location name
    if (isset($location)&&is_numeric($location)) {
        $ocdb=phpMyDB::GetInstance();
        $query="select name from ".TABLE_PREFIX."locations where idLocation=$location Limit 1";
		return $ocdb->getValue($query);
	} else return "";//nothing returned for that item
}
////////////////////////////////////////////////////////////
function getLocationFriendlyName($location){//get the location name
    if (isset($location)&&is_numeric($location)) {
        $ocdb=phpMyDB::GetInstance();
        $query="select friendlyName from ".TABLE_PREFIX."locations where idLocation=$location Limit 1";
		return $ocdb->getValue($query);
	} else return "";//nothing returned for that item
}
////////////////////////////////////////////////////////////
function getLocationParent($location){//get the location name
    if (isset($location)&&is_numeric($location)) {
        $ocdb=phpMyDB::GetInstance();
        $query="select idLocationParent from ".TABLE_PREFIX."locations where idLocation=$location Limit 1";
		$result=$ocdb->getValue($query);
		if (is_numeric($result)) return $result;
		else return 0;//nothing returned for that item
	} else return 0;//nothing returned for that item
}
////////////////////////////////////////////////////////////
function getLocationNum($location){//get the location in number
    if (isset($location)) {
        $ocdb=phpMyDB::GetInstance();
        $query="select idLocation from ".TABLE_PREFIX."locations where lower(friendlyName)='".friendly_url($location)."' Limit 1";
		$result=$ocdb->getValue($query);
		if (is_numeric($result)) return $result;
		else return 0;//nothing returned for that item
	} else return 0;//nothing returned for that item
}
////////////////////////////////////////////////////////////
function getCategoryFriendlyName($category){//get the category friendly name
    if (isset($category)&&is_numeric($category)) {
        $ocdb=phpMyDB::GetInstance();
        $query="select friendlyName from ".TABLE_PREFIX."categories where idCategory=$category Limit 1";
		return $ocdb->getValue($query);
	} else return "";//nothing returned for that item
}
////////////////////////////////////////////////////////////
function buildEmailBodyHTML($var_array){
    $filename = SITE_ROOT.'/content/email/'.LANGUAGE.'/template.html';
    if (!file_exists($filename))
        $filename = SITE_ROOT.'/content/email/en_EN/template.html';

    $fd = fopen ($filename, "r");
    $mailcontent = fread ($fd, filesize ($filename));

    foreach ($var_array as $key=>$value)
    {
    $mailcontent = str_replace("%$value[0]%", $value[1],$mailcontent);
    }

    $array_content[]=array("DATE", Date("l F d, Y"));
    $array_content[]=array("SITE_NAME", SITE_NAME);
    $array_content[]=array("SITE_URL", SITE_URL);

    foreach ($array_content as $key=>$value)
    {
    $mailcontent = str_replace("%$value[0]%", $value[1],$mailcontent);
    }

    $mailcontent = stripslashes($mailcontent);

    fclose ($fd);

    return $mailcontent;
}
////////////////////////////////////////////////////////////
function sendEmail($to,$subject,$body){//send email using smtp from gmail
	sendEmailComplete($to,$subject,$body,NOTIFY_EMAIL,SITE_NAME);
}
////////////////////////////////////////////////////////////
function sendEmailComplete($to,$subject,$body,$reply,$replyName){//send email using smtp from gmail
	$mail             = new PHPMailer();

    //SMTP HOST config
	if (SMTP_HOST!=""){
	    $mail->IsSMTP();
		$mail->Host       = SMTP_HOST;              // sets custom SMTP server
    }

    //SMTP PORT config
	if (SMTP_PORT!=""){
		$mail->Port       = SMTP_PORT;              // set a custom SMTP port
    }

	//SMTP AUTH config
	if (SMTP_AUTH==true){
		$mail->SMTPAuth   = true;                   // enable SMTP authentication
		$mail->Username   = SMTP_USER;              // SMTP username
		$mail->Password   = SMTP_PASS;              // SMTP password
    }

	//GMAIL config
	if (GMAIL==true){
	    $mail->IsSMTP();
		$mail->SMTPAuth   = true;                   // enable SMTP authentication
		$mail->SMTPSecure = "ssl";                  // sets the prefix to the server
		$mail->Host       = "smtp.gmail.com";       // sets GMAIL as the SMTP server
		$mail->Port       = 465;                    // set the SMTP port for the GMAIL server
		$mail->Username   = GMAIL_USER;                     // GMAIL username
		$mail->Password   = GMAIL_PASS;                     // GMAIL password
    }

	$mail->From       = NOTIFY_EMAIL;
	$mail->FromName   = "no-reply ".SITE_NAME;
	$mail->Subject    = $subject;
	$mail->MsgHTML($body);

	$mail->AddReplyTo($reply,$replyName);//they answer here
	$mail->AddAddress($to,$to);
	$mail->IsHTML(true); // send as HTML

	if(!$mail->Send()) {//to see if we return a message or a value bolean
	  echo "Mailer Error: " . $mail->ErrorInfo;
	} else return false;
	 // echo "Message sent! $to";
}

////////////////////////////////////////////////////////////
function isEmail($email){//check that the email is correct
	$pattern="/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/";
	return (preg_match($pattern, $email) > 0);
}

////////////////////////////////////////////////////////////
function redirect($url){//simple header redirect
	oc::redirect($url);
}
////////////////////////////////////////////////////////////
function jsRedirect($url){//simple JavaScript redirect
	echo "<script language='JavaScript' type='text/javascript'>location.href='$url';</script>";
	die();
}
////////////////////////////////////////////////////////////
function alert($msg){//simple JavaScript alert
	echo "<script language='JavaScript' type='text/javascript'>alert('$msg');</script>";
}

////////////////////////////////////////////////////////////
function deleteCache(){//delete cache
	Cache::get_instance()->clear();
}
////////////////////////////////////////////////////////////
function rssReader($url,$maxItems=15,$cache,$begin="",$end=""){//read RSS from the url and cache it
    $cache= (bool) $cache;
    if ($cache){
        $out = Cache::get_instance()->cache(md5($url));//getting values from cache
        if (!isset($out)) $out=false;
    }else $out=false;

    if (!$out) {	//no values in cache
        $rss = simplexml_load_file($url);
        $i=0;
        if($rss){
            $items = $rss->channel->item;
            foreach($items as $item){
                if($i==$maxItems){
                    if ($cache) Cache::get_instance()->cache(md5($url),$out);//save cache
                    return $out;
                }
                else $out.=$begin.'<a href="'.$item->link.'" target="_blank" >'.$item->title.'</a>'.$end;
                $i++;
            }//for each
        }//if rss
    }
    return $out;
}
////////////////////////////////////////////////////////////
function standarizeDate($date) {
	
	if (DATE_FORMAT=='dd-mm-yyyy'){//normal date
	    return $date;
	}
	elseif (DATE_FORMAT=='yyyy-mm-dd'){
	    $L_arrDate = explode('-',$date);// split date
		$L_strYear =  $L_arrDate[0];
		$L_strMonth = $L_arrDate[1];
		$L_strDay = $L_arrDate[2];
		$date=$L_arrDate[2].'-'.$L_arrDate[1].'-'.$L_arrDate[0];
	}
	elseif (DATE_FORMAT=='mm-dd-yyyy'){
	    $L_arrDate = explode('-',$date);// split date
		$L_strMonth = $L_arrDate[0];
		$L_strDay = $L_arrDate[1];
		$L_strYear =  $L_arrDate[2];
		$date=$L_arrDate[1].'-'.$L_arrDate[0].'-'.$L_arrDate[2];
	}
	//else//not any known format TODO
	 
	return $date;
	
}

////////////////////////////////////////////////////////////
// from MySQL to UNIX timestamp
function convert_datetime($str) {
	list($date, $time) = explode(' ', $str);
	list($year, $month, $day) = explode('-', $date);
	list($hour, $minute, $second) = explode(':', $time);
	
	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
	
	return $timestamp;
}

///////////////////functions used for the spam by country/////////////////////////////////////////
function get_tag($tag,$xml){
	preg_match_all('/<'.$tag.'>(.*)<\/'.$tag.'>$/imU',$xml,$match);
	return $match[1];
}
 
function valid_ip($ip){
	return ( ! preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
}
///return IP using hostip.info service 
function geoIP($stringIp=-1){
 
    if (!valid_ip($stringIp)) $stringIp = $_SERVER['REMOTE_ADDR'];
 
    if ($_COOKIE['geoip']){
        $geoip=unserialize($_COOKIE['geoip']);        
        if ($geoip['ip']==$stringIp) return $geoip;//only return if IP is the same if not continue
	}
 
	$url='http://api.hostip.info/?ip='.$stringIp;// Making an API call to Hostip:
	$xml = file_get_contents($url);//echo $url;
 
	$city = get_tag('gml:name',$xml);
	$city = strtolower ($city[1]);
 
	$countryName = get_tag('countryName',$xml);
	$countryName = strtolower ($countryName[0]);
 
	$countryAbbrev = get_tag('countryAbbrev',$xml);
	$countryAbbrev = strtolower ($countryAbbrev[0]);
 
	$geoip['ip']=$stringIp;
	$geoip['city']=$city;
	$geoip['country']=$countryName;
	$geoip['countryAb']=$countryAbbrev;
 
	setcookie('geoip',serialize($geoip), time()+60*60*24*15);// Setting a cookie with the data, which is set to expire in half month:
	return $geoip;
}

///timezones functions
function get_timezones()
{
    if (method_exists('DateTimeZone','listIdentifiers'))
    {
        $timezones = array();
        $timezone_identifiers = DateTimeZone::listIdentifiers();

        foreach( $timezone_identifiers as $value )
        {
            if ( preg_match( '/^(America|Antartica|Africa|Arctic|Asia|Atlantic|Australia|Europe|Indian|Pacific)\//', $value ) )
            {
                $ex=explode('/',$value);//obtain continent,city
                $city = isset($ex[2])? $ex[1].' - '.$ex[2]:$ex[1];//in case a timezone has more than one
                $timezones[$ex[0]][$value] = $city;
            }
        }
        return $timezones;
    }
    else//old php version
    {
        return FALSE;
    }
}



function get_select_timezones($select_name='TIMEZONE',$selected=NULL)
{
	$sel='';
    $timezones = get_timezones();
    $sel.='<select id="'.$select_name.'" name="'.$select_name.'">';
    foreach( $timezones as $continent=>$timezone )
    {
        $sel.= '<optgroup label="'.$continent.'">';
        foreach( $timezone as $city=>$cityname )
        {            
            if ($selected==$city)
            {
                $sel.= '<option selected="selected" value="'.$city.'">'.$cityname.'</option>';
            }
            else $sel.= '<option value="'.$city.'">'.$cityname.'</option>';
        }
        $sel.= '</optgroup>';
    }
    $sel.='</select>';

    return $sel;
}


////////////////////////////////////////////////////////////
function advancedSearchForm($admin=0){//used in  /search and in index.php when an advanced search is done
	global $currentCategory;
	if ($admin==1) $action=SITE_URL.'/admin/listing.php';
	else $action=SITE_URL;
	include '../content/search-form.php';
}

////////////////////////////////////////////////////////////
//creates a new htaccess with the current language, used in the settings and in the installation
function regenerateHtaccess($site_url)
{
	$array = parse_url($site_url);
	$rewritebase = $array['path'];
	if ($_SERVER["SERVER_PORT"]!="80") $rewritebase=str_replace(":".$_SERVER["SERVER_PORT"],"",$rewritebase);
	if ($rewritebase=="") $rewritebase="/";
	
	$offer=u(T_("Offer"));
	if ($offer=="") $offer="offer";
	
	$need=u(T_("Need"));
	if ($need=="") $need="need";
	
	$cat=u(T_("Category"));
	if ($cat=="") $cat="category";
	
	$typ=u(T_("Type"));
	if ($typ=="") $typ="type";
	
	$new=u(T_("Publish a new Ad"));
	if ($new=="") $new="new";
	
	$con=u(T_("Contact"));
	if ($con=="") $con="contact";
	
	$pol=u(T_("Privacy Policy"));
	if ($pol=="") $pol="policy";
	
	$sm=u(T_("Sitemap"));
	if ($sm=="") $sm="sitemap";
	
	$sch=u(T_("Advanced Search"));
	if ($sch=="") $sch="search";
	
	$gm=u(T_("Map"));
	if ($gm=="") $gm="map";
	
	$ads=u(T_("Classifieds"));
	if ($ads=="") $ads="ads";
	
	$alogin=u(T_("Login"));
	if ($alogin=="") $alogin="login";
	
	$alogout=u(T_("Logout"));
	if ($alogout=="") $alogout="logout";
	
	$aforgotpwd=u(T_("Forgot My Password"));
	if ($aforgotpwd=="") $aforgotpwd="forgot-password";
	
	$aconfig=u(T_("Settings"));
	if ($aconfig=="") $aconfig="settings";
	
	$account=u(T_("My Account"));
	if ($account=="") $account="my-account";
	
	$terms=u(T_("Terms"));
	if ($terms=="") $terms="terms";
	
	$new=u(T_("Publish a new Ad"));
	if ($new=="") $new="publish-a-new-ad-for-free";
	
	$aregister =u(T_("Register new account"));
	if ($aregister=="") $aregister="register";
	
	$htaccess_content = "ErrorDocument 404 ".$rewritebase."content/404.php
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteBase $rewritebase
RewriteRule ^([0-9]+)$ content/index.php?page=$1 [L]
RewriteRule ^install/$ install/index.php [L]
RewriteRule ^admin/$ admin/index.php [L]
RewriteRule ^rss/$ content/feed-rss.php [L]
RewriteRule ^manage/$ content/item-manage.php [L]
RewriteRule ^$new.htm content/item-new.php [L]
RewriteRule ^$con.htm content/contact.php [L]
RewriteRule ^$terms.htm content/terms.php [L]
RewriteRule ^$pol.htm content/privacy.php [L]
RewriteRule ^$sm.htm content/site-map.php [L]
RewriteRule ^$sch.htm content/search.php [L]
RewriteRule ^$gm.htm content/map.php [L]
RewriteRule ^$aregister.htm content/account/register.php [L]
RewriteRule ^$alogin.htm content/account/login.php [L]
RewriteRule ^$alogout.htm content/account/logout.php [L]
RewriteRule ^$aforgotpwd.htm content/account/recoverpassword.php [L]
RewriteRule ^$aconfig.htm content/account/settings.php [L]
RewriteRule ^$account/$ content/account/index.php [L]
RewriteRule ^$offer/(.+)/(.+)/$ content/index.php?category=$1&type=0&location=$2 [L]
RewriteRule ^$offer/(.+)$ content/index.php?category=$1&type=0  [L]
RewriteRule ^$need/(.+)/(.+)/$ content/index.php?category=$1&type=1&location=$2 [L]
RewriteRule ^$need/(.+)$ content/index.php?category=$1&type=1 [L]
RewriteRule ^$ads/(.+)/([0-9]+)$ content/index.php?location=$1&page=$2 [L]
RewriteRule ^$ads/(.+)/$ content/index.php?location=$1 [L]
RewriteRule ^(.+)/(.+)/(.+)/$ content/index.php?category=$2&location=$3 [L]
RewriteRule ^(.+)/(.+)/$ content/index.php?category=$2 [L]
RewriteRule ^$cat/(.+) $1/ [R=301,L]
RewriteRule ^(.+)/(.+)/(.+)/([0-9]+)$ content/index.php?category=$2&location=$3&page=$4 [L]
RewriteRule ^(.+)/$ content/index.php?category=$1 [L]
RewriteRule ^(.+)/(.+)/([0-9]+)$ content/index.php?category=$2&page=$3 [L]
RewriteRule ^(.+)/([0-9]+)$ content/index.php?category=$1&page=$2 [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.+)/(.+)/(.+)/(.+)$ /$3/$4-$1.htm [R=301,L]
RewriteRule ^(.+)/(.+)/(.+)-([0-9]+).htm$  content/item.php?category=$2&item=$4 [L]
RewriteRule ^(.+)/(.+)-([0-9]+).htm$  content/item.php?category=$1&item=$3 [L]
</IfModule>";

	return oc::fwrite('../.htaccess', $htaccess_content);

}

////////////////////////////////////////////////////////////
//creates the robots.txt file
//creates the robots.txt file & returns its content 
function regenerateRobots($site_url)
{
	$con=contactURL(); // from seo.php
	$robots_content = "User-agent: *
Allow: /images/*
Disallow: /includes/
Disallow: /admin/
Disallow: /cache/
Disallow: /install/
Disallow: /$con
Disallow: /?s=
Allow: /rss/
Sitemap: ".$site_url."/sitemap.xml.gz";

	if (oc::fwrite('../robots.txt', $robots_content))
		return $robots_content;
	else
		return false;

}
////////////////////////////////////////////////////////////
define('SAMBA', TRUE);
?>
