<?php
////////////////////////////////////////////////////////////
function getPrice($amount){//returns the price for the item in the correct format
	return str_replace(array("AMOUNT","CURRENCY"),array($amount,CURRENCY),CURRENCY_FORMAT);
	//return $amount;
}
////////////////////////////////////////////////////////////
function isSpam($name,$email,$comment){//return if something is spam or not using akismet, and checking the spam list
	$ocdb=phpMyDB::GetInstance();
	$res=$ocdb->getValue("SELECT idPost FROM ".TABLE_PREFIX."posts p where isAvailable=2  and email='$email' LIMIT 1");//check spam tags
	if ($res==false){//nothing found
		if (AKISMET!=""){
			$akismet = new Akismet(SITE_URL ,AKISMET);//change this! or use defines with that name!
			$akismet->setCommentAuthor($name);
			$akismet->setCommentAuthorEmail($email);
			$akismet->setCommentContent($comment);
			return $akismet->isCommentSpam();
		}
		else return false;//we return is not spam since we do not have the api :(
	}
	else return true;//ohohoho SPAMMER!

}
////////////////////////////////////////////////////////////
function isInSpamList($ip){//return is was taged as spam (in /manage is where we tag)
	$ocdb=phpMyDB::GetInstance();
	$res=$ocdb->getValue("SELECT idPost FROM ".TABLE_PREFIX."posts p where isAvailable=2 and ip='$ip' LIMIT 1");

	if(!empty($res)) return true;//we had tagged him before as spammer
	elseif (empty($res) && SPAM_COUNTRY){
	    $geoip=geoIP();
	    $countries = explode(',',SPAM_COUNTRIES);
	    if ( in_array($geoip['country'],$countries))  return true;//ohohoho SPAMMER!
	    else return false;//nothing found
	}
	else return false;
}
////////////////////////////////////////////////////////////
function check_images_form(){//get values by reference to allow change them. Used in new item and manage item
    //image check
    $image_check=1;
    if (MAX_IMG_NUM>0){	//image upload active if there's more than 1
		$types=split(",",IMG_TYPES);//creating array with the allowed types print_r ($types);

		for ($i=1;$i<=MAX_IMG_NUM && is_numeric($image_check);$i++){//loop for all the elements in the form

		    if (file_exists($_FILES["pic$i"]['tmp_name'])){//only for uploaded files

			    $imageInfo = getimagesize($_FILES["pic$i"]["tmp_name"]);
			    $file_mime = strtolower(substr(strrchr($imageInfo["mime"], "/"), 1 ));//image mime
			    $file_ext  = strtolower(substr(strrchr($_FILES["pic$i"]["name"], "."), 1 ));//image extension

			    if ($_FILES["pic$i"]['size'] > MAX_IMG_SIZE) {//control the size
				     $image_check=T_("Picture")." $i ".T_("Upload pictures max file size").' '.(MAX_IMG_SIZE/1048576).' Mb';
			    }
			    elseif (!in_array($file_mime,$types) || !in_array($file_ext,$types)){//the size is right checking type and extension
				     $image_check=T_("Picture")." $i no ".T_("format")." ".IMG_TYPES;
			    }//end else

			    $image_check++;

			}//end if existing file
		}//end loop
	}//end image check
	return $image_check;
}
////////////////////////////////////////////////////////////
function upload_images_form($idPost,$title,$date=0){//upload image files from the form.  Used in new item and manage item
    //images upload and resize
	if (MAX_IMG_NUM>0){

	//create dir for the images
		if ($date!=0) {
		    $date = standarizeDate($date);
	        $date = explode('-',$date);
		}
		//else $imgDir=date("Y").'/'.date("m").'/'.date("d").'/'.$idPost; //no date	
		else{// we do not have the date for the post retrieving from DB
			$ocdb=phpMyDB::GetInstance();
			$date=setDate($ocdb->getValue("select insertDate from ".TABLE_PREFIX."posts where idPost=$idPost Limit 1"));
			$date = standarizeDate($date);
			//d($date);
			$date=explode('-',$date);
		}
		
		if (count($date)==3){ //there's a date where needs to be uploaded
			$imgDir=$date[2].'/'.$date[1].'/'.$date[0].'/'.$idPost;
		}

		$up_path=IMG_UPLOAD_DIR.$imgDir;
		//d($up_path);
		if (is_dir(IMG_UPLOAD_DIR.$imgDir)) oc::remove_resource(IMG_UPLOAD_DIR.$imgDir);
			
		umask(0000);
		mkdir($up_path, 0755,true);//create folder for item

		$needFolder=false;//to know if it's needed the folder
		//upload images
		for ($i=1;$i<=MAX_IMG_NUM;$i++){
		    if (file_exists($_FILES["pic$i"]['tmp_name'])){//only for uploaded files
			    $file_name = $_FILES["pic$i"]['name'];
			    $file_name = friendly_url($title).'_'.$i.strtolower(substr($file_name, strrpos($file_name, '.')));
			    $up_file=$up_path."/".$_FILES["pic$i"]['name'];

			    if (move_uploaded_file($_FILES["pic$i"]['tmp_name'],$up_file)){ //file uploaded
			    	//resize image to web standard			    	
					$resizeObj = new resize($up_file);
					$resizeObj->resizeImage(IMG_RESIZE, IMG_RESIZE, 'auto');
					$resizeObj->saveImage($up_path."/".$file_name, 85);
					//create thumb, and scale it no matter what
					$resizeObj = new resize($up_path."/".$file_name);
					$resizeObj->resizeImage(IMG_RESIZE_THUMB, IMG_RESIZE_THUMB, 'crop');
					$resizeObj->saveImage($up_path."/thumb_$file_name", 75);
			    	
				    @unlink($up_file);//delete old file
				    $needFolder=true;
			    }
			}//end if file exists
		}
		if (!$needFolder) @rmdir($up_path);//the folder is not needed no files uploaded
	}
	//end images
}
////////////////////////////////////////////////////////////
function getPostImages($idPost,$date,$just_one=false,$thumb=false){
	$no_pic=SITE_URL."/images/no_pic.png";
	$date = standarizeDate($date);
	$date=explode('-',$date);
	if (count($date)==3){//is_date
		$types=split(",",IMG_TYPES);//creating array with the allowed images types

		$imgUrl=SITE_URL.IMG_UPLOAD;//url for the image
		$imgPath=IMG_UPLOAD_DIR;//path of the image

		$imgDir=$date[2].'/'.$date[1].'/'.$date[0].'/'.$idPost.'/';	//$imgDir=$idPost.'/';

		$files = scandir($imgPath.$imgDir);
		foreach($files as $img){//searching for images
			$file_ext  = strtolower(substr(strrchr($img, "."), 1 ));//get file ext
			if (in_array($file_ext,$types))$images[]=$img;//we only keep images with allowed ext
		}
		//print_r($images);
		if (count($images)>0){//there's at least 1 image
			foreach($images as $img){

				$is_thumb=(substr($img,0,6)=='thumb_');

				if ($just_one){//we want just one image
					if (!$thumb && !$is_thumb) return $imgUrl.$imgDir.$img;//first image match
					elseif($thumb && $is_thumb) return $imgUrl.$imgDir.$img;//first thumb match
				}
				else{//we want all the images
					if (!$thumb && !$is_thumb) {//images and thumbs
						$r_images[]=array($imgUrl.$imgDir.$img,$imgUrl.$imgDir.'thumb_'.$img);//images array
					}
					elseif($thumb && $is_thumb){//only thumbs
						$r_images[]=$imgUrl.$imgDir.$img;//thumbs array
					}
				}

			}
		}
		elseif($thumb) return $no_pic;//nothing in the folder

		return $r_images;
	}//no date :(
	else return $no_pic;
}

////////////////////////////////////////////////////////////
function deletePostImages($idPost,$date=0){
	if ($date!=0) {
	    $date = standarizeDate($date);
	    $dateD=explode('-',$date);
	}
	else $dateD=0;
	
	if (count($dateD)!=3){// we do not have the date for the post retrieving from DB
		$ocdb=phpMyDB::GetInstance();
		$date=setDate($ocdb->getValue("select insertDate from ".TABLE_PREFIX."posts where idPost=$idPost Limit 1"));
		$dateD=explode('-',$date);
	}

    if (count($dateD)==3){
    	$imgPath=IMG_UPLOAD_DIR.$dateD[2].'/'.$dateD[1].'/'.$dateD[0].'/'.$idPost;//path images
    	if (is_dir($imgPath)) oc::remove_resource($imgPath);//delete
    }

    return $date;//we return the date to reuse in other places
}

////////////////////////////////////////////////////////////
function mediaPostDesc($the_content){//from a description add the media
//using http://www.robertbuzink.nl/journal/2006/11/23/youtube-brackets-wordpress-plugin/
    if (VIDEO){
        $stag = "[youtube=http://www.youtube.com/watch?v=";
        $etag = "]";
        $spos = strpos($the_content, $stag);
        if ($spos !== false){
            $epos = strpos($the_content, $etag, $spos);
            $spose = $spos + strlen($stag);
            $slen = $epos - $spose;
            $file  = substr($the_content, $spose, $slen);
			//youtube
            $tags = '<object width="425" height="350">
                    <param name="movie" value="'.$file.'"></param>
                    <param name="wmode" value="transparent" ></param>
                    <embed src="http://www.youtube.com/v/'. $file.'" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed>
                    </object>';
            $new_content = substr($the_content,0,$spos);
            $new_content .= $tags;
            $new_content .= substr($the_content,($epos+1));

            if ($epos+1 < strlen($the_content)) {//reciproco
                $new_content = mediaPostDesc($new_content);
            }
            return $new_content;
        }
        else return $the_content;
    }
    else return $the_content;
}

////////////////////////////////////////////////////////////
function confirmPost($post_id,$post_password){//confirm a post
	$ocdb=phpMyDB::GetInstance();
	//update table
	$ocdb->update(TABLE_PREFIX."posts","isConfirmed=1","idPost=$post_id and password='$post_password'");
	
	//redirect to the item
	$query="select title,type,friendlyName,password,c.name cname,p.description,p.name,p.price,hasImages,p.insertDate,p.idLocation,p.place,email,
	        (select friendlyName from ".TABLE_PREFIX."categories where idCategory=c.idCategoryParent limit 1) parent
			    from ".TABLE_PREFIX."posts p 
			    inner join ".TABLE_PREFIX."categories c
			    on c.idCategory=p.idCategory
			where idPost=$post_id and password='$post_password' and isConfirmed=1 Limit 1";
	$result=$ocdb->query($query);
	if (mysql_num_rows($result)){
		$row=mysql_fetch_assoc($result);
		$title=$row["title"];
		$email=$row["email"];
		$postTitle=friendly_url($title);
		$postTypeName=getTypeName($row["type"]);
		$fcategory=$row["friendlyName"];
		$parent=$row["parent"];
			
		$postUrl=itemURL($post_id,$fcategory,$postTypeName,$postTitle,$parent);
		
		if(FRIENDLY_URL) $linkext='/manage/';
    	else $linkext='/content/item-manage.php';
    	
	    $bodyHTML='NEW ad '.SITE_URL.$postUrl.'<br />
		<a href="'.SITE_URL.$linkext.'?post='.$post_id.'&amp;pwd='.$post_password.'&amp;action=edit">'.T_("Edit").'</a>'.SEPARATOR.'
		<a href="'.SITE_URL.$linkext.'?post='.$post_id.'&amp;pwd='.$post_password.'&amp;action=deactivate">'.T_("Deactivate").'</a>'.SEPARATOR.'
		<a href="'.SITE_URL.$linkext.'?post='.$post_id.'&amp;pwd='.$post_password.'&amp;action=spam">'.T_("Spam").'</a>'.SEPARATOR.'
		<a href="'.SITE_URL.$linkext.'?post='.$post_id.'&amp;pwd='.$post_password.'&amp;action=delete">'.T_("Delete").'</a>';
		sendEmail(NOTIFY_EMAIL,"NEW ad in ".SITE_URL,$bodyHTML);//email to the NOTIFY_EMAIL	
			
		if (CACHE_DEL_ON_POST) deleteCache();//delete cache on post if is activated
		if (SITEMAP_DEL_ON_POST) sitemap::generate();//new item generate sitemap
		
		//only if you pay using paypal
		if (PAYPAL_ACTIVE){
			 //generate the email to send to the client
			if(FRIENDLY_URL) {
			    $linkDeactivate=SITE_URL."/manage/?post=$post_id&pwd=$post_password&action=deactivate";
			    $linkEdit=SITE_URL."/manage/?post=$post_id&pwd=$post_password&action=edit";
			}
			else{
			    $linkDeactivate=SITE_URL."/content/item-manage.php?post=$post_id&pwd=$post_password&action=deactivate";
			    $linkEdit=SITE_URL."/content/item-manage.php?post=$post_id&pwd=$post_password&action=edit";
			}			
	        $message="<p>".T_("If you want to see your Ad click here").":
						<a href='".SITE_URL.$postUrl."'>$postTitle</a><br />".
	        			"<p>".T_("If you want to edit your Ad click here").":
						<a href='$linkEdit'>$linkEdit</a><br />".
						T_("If this Ad is no longer available please click here").":
						<a href='$linkDeactivate'>$linkDeactivate</a></p>";
	
	         $array_content[]=array("ACCOUNT", T_("User"));
	         $array_content[]=array("MESSAGE", $message);
	         $bodyHTML=buildEmailBodyHTML($array_content);
	         
	         //email to payer
	         sendEmail($email,$title." ". $_SERVER['SERVER_NAME'],$bodyHTML);
		}
		/*
		//ocaku insert new
		if (OCAKU){
			$ocaku=new ocaku();
			
			if ($row["hasImages"]==1){//images
				$itemImages=getPostImages($post_id,setDate($row["insertDate"]));//getting the images
				$numImages=count($itemImages);
				if ($numImages>0) $imagePost=$itemImages[0][1];//thumb
				else $imagePost='';
			}
			
			if (LOCATION) $oplace=getLocationName($row["idLocation"]);
			else  $oplace=$row["place"];
			
			$data=array(
				'KEY'=>OCAKU_KEY,
				'idPostInClass'=>$post_id,
				'Category'=>$row["cname"],
				'Place'=>$oplace,
				'URL'=>SITE_URL.$postUrl,
				'type'=>$postTypeName,
				'title'=>$title,
				'description'=>$row["description"],
				'name'=>$row["name"],
				'price'=>$row["price"],
				'currency'=>CURRENCY,
				'language'=>substr(LANGUAGE,0,2),
				'image'=>$imagePost,
				'num_images'=>$numImages
				);
				
			$ocaku->newPost($data);
			unset($ocaku);
		}
		//end ocaku		 */
		
		//if(!PAYPAL_ACTIVE){
			alert(T_("Your Ad was successfully activated, thank you"));
			redirect(SITE_URL.$postUrl);
		//}
	}
}

////////////////////////////////////////////////////////////
function deactivatePost($post_id,$post_password,$msg=TRUE){//deactivate a post
	$ocdb=phpMyDB::GetInstance();
	
	$ocdb->update(TABLE_PREFIX."posts","isAvailable=0","idPost=$post_id and password='$post_password'");
		if (CACHE_DEL_ON_POST) deleteCache();//delete cache

        if ($msg==TRUE)
        {
	        //show confirmation message or return to admin listing
	        if (isset($_SESSION['admin']) && isset($_SESSION['ADMIN_QUERY_STRING'])){
	            $pag_url=SITE_URL."/admin/listing.php?rd=deactivate&".$_SESSION['ADMIN_QUERY_STRING'];
	            redirect($pag_url);//redirect to the admin listing
	        } else
			  echo "<div id='sysmessage'>".T_("Your Ad was successfully deactivated")."</div>";
		}
}


////////////////////////////////////////////////////////////
function activatePost($post_id,$post_password,$msg=TRUE){//activate a post
	$ocdb=phpMyDB::GetInstance();
	
	//move images to new folder, since we change the insertDate.
	
		$newDate = date('Y-m-d H:i:s');
	
		//get old date, creating orig folder
		$date=setDate($ocdb->getValue("select insertDate from ".TABLE_PREFIX."posts where idPost=$post_id AND hasImages =1 Limit 1"));
		
		//if different date then change it...
		if ($date != setDate($newDate))
		{
			$date=explode('-',$date);
			
			//there's a date so there's image
			if (count($date)==3){ 
				$imgDir=$date[2].'/'.$date[1].'/'.$date[0].'/'.$post_id;
				
				//set new images folder
				$dateN = explode('-',setDate($newDate));
				$imgDirN=$dateN[2].'/'.$dateN[1].'/'.$dateN[0].'/'.$post_id;
				
				//move images
				oc::move(IMG_UPLOAD_DIR.$imgDir,IMG_UPLOAD_DIR.$imgDirN);	
				
				oc::remove_resource(IMG_UPLOAD_DIR.$imgDir);
				
			}	
		}
		
		//activating the post, changing the insertdate
		$ocdb->update(TABLE_PREFIX."posts","isAvailable=1,isConfirmed=1,insertDate=NOW()","idPost=$post_id and password='$post_password'");
			
		if (CACHE_DEL_ON_POST) deleteCache();//delete cache
		
		if ($msg==TRUE)
        {
	        //show confirmation message or return to admin listing
	        if (isset($_SESSION['admin']) && isset($_SESSION['ADMIN_QUERY_STRING'])){
	            $pag_url=SITE_URL."/admin/listing.php?rd=activate&".$_SESSION['ADMIN_QUERY_STRING'];
	                
	            redirect($pag_url);//redirect to the admin listing
	        } else
			  echo "<div id='sysmessage'>".T_("Your Ad was successfully activated")."</div>";
		}
}

////////////////////////////////////////////////////////////
function spamPost($post_id,$post_password,$msg=TRUE){//flag post as spam
	$ocdb=phpMyDB::GetInstance();
	if (AKISMET!=""){//report akismet
			$query="select name,email,description,ip from ".TABLE_PREFIX."posts where idPost=$post_id and password='$post_password' Limit 1";
			$result=$ocdb->query($query);
			if (mysql_num_rows($result)){
				$row=mysql_fetch_assoc($result);
					$akismet = new Akismet(SITE_URL ,AKISMET);
					$akismet->setCommentAuthor($row["name"]);
					$akismet->setCommentAuthorEmail($row["email"]);
					$akismet->setCommentContent($row["description"]);
					$akismet->setUserIP($row["ip"]);//ip of the bastard!
					$akismet->submitSpam();
					$akismet->submitHam();
			}
		}
		
		$ocdb->update(TABLE_PREFIX."posts","isAvailable=2","idPost=$post_id and password='$post_password'");//set post as spam state 2
		deletePostImages($post_id);// delete the images cuz of spammer
		if (CACHE_DEL_ON_POST) deleteCache();//delete cache
		
		if ($msg==TRUE)
        {
	        //show confirmation message or return to admin listing
	        if (isset($_SESSION['admin']) && isset($_SESSION['ADMIN_QUERY_STRING'])){
	            $pag_url=SITE_URL."/admin/listing.php?rd=spam&".$_SESSION['ADMIN_QUERY_STRING'];
	                
	            redirect($pag_url);//redirect to the admin listing
	            die();
	        } else
			  echo "<div id='sysmessage'>".T_("Spam reported")."</div>";
		}
}

////////////////////////////////////////////////////////////
function deletePost($post_id,$post_password,$msg=TRUE){//delete post
	$ocdb=phpMyDB::GetInstance();
	deletePostImages($post_id);//delete images! and folder
		$ocdb->delete(TABLE_PREFIX."posts","idPost=$post_id and password='$post_password'");
		if (CACHE_DEL_ON_POST) deleteCache();//delete cache

        if ($msg==TRUE)
        {
        	 //show confirmation message or return to admin listing
	        if (isset($_SESSION['admin']) && isset($_SESSION['ADMIN_QUERY_STRING'])){
	            $pag_url=SITE_URL."/admin/listing.php?rd=delete&".$_SESSION['ADMIN_QUERY_STRING'];
	                
	            redirect($pag_url);//redirect to the admin listing
	        } else
			  echo "<div id='sysmessage'>".T_("Your Ad was successfully deleted")."</div>";
        }
       
}

////////////////////////////////////////////////////////////
function newPost(){
	if(captcha::check('newitem'))	{
		if (isEmail(cP("email"))){//is email
			if(!isSpam(cP("name"),cP("email"),cP("description"))){//check if is spam!
				$ocdb=phpMyDB::GetInstance();
				$image_check=check_images_form();//echo $image_check;
				
				if (is_numeric($image_check)){//if the images were right, or not any image uploaded
				
					if (is_numeric(cP("price"))) $price=cP("price");
					else $price=0;
					//DB insert
					$post_password=generatePassword();					
					if (HTML_EDITOR) $desc=cPR("description");
					else $desc=cP("description");
					if (VIDEO && cp("video")!="" && strpos(cp("video"), "http://www.youtube.com/watch?v=")==0) $desc.='[youtube='.cp("video").']';//youtube video
					$title=cP("title");
					$email=cP("email");
                        if (cP("location")!="") $location = intval(cP("location"));
                        else $location=0;
					
                        if ($image_check>1) $hasImages=1;
                        else $hasImages=0;
                    if (cP("place")!='not found') $place=cP("place");
                    else $place='';
                    
                    $category = cP("category");
                    
                    if (!is_numeric($category) || $category == 0)
                    {
                        echo "<div id='sysmessage'>".T_('Select category')."</div>";
                        return false;
                    }
                    
                    
					$ocdb->insert(TABLE_PREFIX."posts (idCategory,type,title,description,price,idLocation,place,name,email,phone,password,ip,hasImages)","".
											$category.",".intval(cP("type")).",'$title','$desc',$price,$location,'".$place."','".cP("name")."','$email','".cP("phone")."','$post_password','".oc::get_ip()."',$hasImages");
					$idPost=$ocdb->getLastID();
					//end database insert
					
					if ($image_check>1) upload_images_form($idPost,$title);
					
					//if paypal active redirect the user to paypal and die
				  	if (PAYPAL_ACTIVE) {
						$cpaypal = cP("cpaypal");
						if (is_numeric($cpaypal)){
							if ((float)$cpaypal > 0) paypal::form($idPost,$cpaypal);
						}
						else{
							if ((float)PAYPAL_AMOUNT > 0) paypal::form($idPost);
						}
							
					}
                    		
					//EMAIL notify
					//generate the email to send to the client , we allow them to erase posts? mmmm
					if(FRIENDLY_URL) {
					    $linkConfirm=SITE_URL."/manage/?post=$idPost&pwd=$post_password&action=confirm";
					    $linkDeactivate=SITE_URL."/manage/?post=$idPost&pwd=$post_password&action=deactivate";
					    $linkEdit=SITE_URL."/manage/?post=$idPost&pwd=$post_password&action=edit";
					}
					else{
					    $linkConfirm=SITE_URL."/content/item-manage.php?post=$idPost&pwd=$post_password&action=confirm";
					    $linkDeactivate=SITE_URL."/content/item-manage.php?post=$idPost&pwd=$post_password&action=deactivate";
					    $linkEdit=SITE_URL."/content/item-manage.php?post=$idPost&pwd=$post_password&action=edit";
					}
					
                      
							
					if (!CONFIRM_POST){
		                $message="<p>".T_("If you want to edit your Ad click here").":
									<a href='$linkEdit'>$linkEdit</a><br />".
									T_("If this Ad is no longer available please click here").":
									<a href='$linkDeactivate'>$linkDeactivate</a></p>";
				    }
					else {
						$message="<p>".T_("To confirm your Ad click here").": ".
							"<a href='$linkConfirm'>$linkConfirm</a><br /><br />".
							T_("If you want to edit your Ad click here").":
							<a href='$linkEdit'>$linkEdit</a><br />".
							T_("If this Ad is no longer available please click here").":
							<a href='$linkDeactivate'>$linkDeactivate</a></p>";
	                }
                
                        
                        $array_content[]=array("ACCOUNT", T_("User"));
                        $array_content[]=array("MESSAGE", $message);
                        
                        $bodyHTML=buildEmailBodyHTML($array_content);
                        
                      
					if (!CONFIRM_POST){
						sendEmail($email,$title." - ". SITE_NAME,$bodyHTML);
					}
					else {
						_e("Thank you! Check your email to confirm the post");
						sendEmail($email,T_("Confirm")." ".$title." - ". SITE_NAME,$bodyHTML);
					}
					
					if (!CONFIRM_POST) jsRedirect($linkConfirm);
					else require_once('../content/footer.php');
					
					die();
				}
				else echo "<div id='sysmessage'>".$image_check."</div>";//end upload verification
			}//end akismet
			else {//is spam!
				echo "<div id='sysmessage'>".T_("Oops! Spam? If it was not spam, contact us")."</div>";
				require_once('../content/footer.php');
				exit;
			}
		}//email validation
		else echo "<div id='sysmessage'>".T_("Wrong email address")."</div>";//Wrong email address
	}//captcha validation
	else echo "<div id='sysmessage'>".T_("Wrong captcha")."</div>";//wrong captcha
}

function editPost($post_id,$post_password){
	if(captcha::check('edititem'))	{//everything ok
		$ocdb=phpMyDB::GetInstance();
	    $image_check=check_images_form();//echo $image_check;
	    if (is_numeric($image_check)){//if the images were right, or not any image uploaded
		    if (is_numeric(cP("price"))) $price=cP("price");
		    else $price=0;
		    //DB update				
            if (HTML_EDITOR) {$desc=cPR("description"); if ($_POST['video']){$desc.='[youtube='.cPR("video").']';}}
            else {$desc=cP("description"); if ($_POST['video']){$desc.='[youtube='.cP("video").']';}}
               
		    if ($image_check>1) $hasImages= " ,hasImages=1";
		    
		    $title=cP("title");	
                    if (cP("location")!="") $location = cP("location");
                    else $location=0;
                    
                    $category = cP("category");
                    if (!is_numeric($category) || $category == 0)
                    {
                        echo "<div id='sysmessage'>".T_('Select category')."</div>";
                        return false;
                    }
                           
                    $param = "idCategory=".$category.",type=".intval(cP("type")).",
					    title='$title',description='$desc',price=$price,
					    place='".cP("place")."',name='".cP("name")."',
					    phone='".cP("phone")."',ip='".oc::get_ip()."'".$hasImages;
                    if (is_numeric(cP("location"))) $param .= ",idLocation=".intval(cP("location"));
		    $ocdb->update(TABLE_PREFIX."posts",$param,"idPost=$post_id and password='$post_password' Limit 1");
		    if (CACHE_DEL_ON_POST) deleteCache();//delete cache on post
		    //end database update
		
		    if ($image_check>1){//something to upload
			    $date=deletePostImages($post_id); //delete previous images
			    upload_images_form($post_id,$title,$date);//upload new ones
		    }
		    //end images	
	
                    //show confirmation message or return to admin listing
                    if (isset($_SESSION['admin']) && isset($_SESSION['ADMIN_QUERY_STRING'])){
                        $pag_url=SITE_URL."/admin/listing.php?rd=edit&".$_SESSION['ADMIN_QUERY_STRING'];
                            
                        redirect($pag_url);//redirect to the admin listing
                    } else
              echo "<div id='sysmessage'>".T_("Your Ad was successfully updated")."</div>";
		}//image check
	    else echo "<div id='sysmessage'>".$image_check."</div>";//end upload verification
	}//end captcha
    else echo "<div id='sysmessage'>".T_("Wrong captcha")."</div>";
}
?>
