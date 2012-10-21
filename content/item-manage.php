<?php
require_once('header.php');

if (cG("pwd")&&is_numeric(cG("post"))){//delete ,activate or deactivate
	$action=cG("action");
	$post_password=cG("pwd");
	$post_id=cG("post");
	
	if ($action=="confirm"){//confirm a new post, if paypal or moderation enabled you can't confirm from here
		$query="select c.price from ".TABLE_PREFIX."categories c
			inner join ".TABLE_PREFIX."posts p
			on c.idCategory=p.idCategory
			where p.idPost=$post_id and p.password='$post_password' Limit 1";
		$categoryPrice = $ocdb->getValue($query);

		$confirm = true;
		if (PAYPAL_ACTIVE)
			if ((float)$categoryPrice != 0)
				$confirm = false;
				
		if (MODERATE_POST) {
			echo "<div id='sysmessage'>".T_("In few hours your advertisement will be displayed.")."</div>";
			$confirm =false;
		}
		
		if ($confirm)
			confirmPost($post_id,$post_password);
	}	
	elseif ($action=="deactivate"){
		deactivatePost($post_id,$post_password);
	}
	elseif ($action=="activate"){
		if (MODERATE_POST && !isset($_SESSION['admin']) ) { //if moderation is set  we don't allow to activate unless you are the admin
			echo "<div id='sysmessage'>".T_("In few hours your advertisement will be displayed.")."</div>";
		}
		else		activatePost($post_id,$post_password);
	}
	elseif ($action=="delete"&&(isset($_SESSION['admin']) || isset($_SESSION['ocAccount']))){
		deletePost($post_id,$post_password);
	}
	elseif ($action=="spam"&&isset($_SESSION['admin'])){//only for admin mark as spam
		spamPost($post_id,$post_password);
	}
	elseif ($action=="edit"){//edit post
		if ($_POST && checkCSRF('edititem')){//update post
			editPost($post_id,$post_password);
		}

		$query="select p.*,friendlyName,c.name cname,p.description,
		        (select friendlyName from ".TABLE_PREFIX."categories where idCategory=c.idCategoryParent limit 1) parent
				    from ".TABLE_PREFIX."posts p 
				    inner join ".TABLE_PREFIX."categories c
				    on c.idCategory=p.idCategory
				where idPost=$post_id and password='$post_password' and isAvailable!=2 Limit 1";
		$result=$ocdb->query($query);
		if (mysql_num_rows($result)){
			$row=mysql_fetch_assoc($result);
			
            if (VIDEO){
                $description_with_video=$row['description'];
                $descriptionParts=explode('[youtube=',$description_with_video);
                $videoParts=explode(']',$descriptionParts[1]);
                $row['description']=$descriptionParts[0];
                $row["video"]=$videoParts[0];
            }       
            
			if($row['isConfirmed']!=1) {//the ad is not confirmed!
				$linkConfirm=SITE_URL."/manage/?post=$post_id&pwd=$post_password&action=confirm";
				echo "<b><a href='$linkConfirm'>".T_("To confirm your Ad click here")."</a></b><br />";
			}
			
			if($row['isAvailable']==1){//able to deactivate it
				$linkDeactivate=SITE_URL."/manage/?post=$post_id&pwd=$post_password&action=deactivate";
				echo "<a href='$linkDeactivate'>".T_("If this Ad is no longer available please click here")."</a>";
			}
			else {//activate it
				$linkActivate=SITE_URL."/manage/?post=$post_id&pwd=$post_password&action=activate";
				echo "<a href='$linkActivate'>".T_("Activate")."</a>";
			}
			
			$postTitle=$row["title"];
			$postTitleF=friendly_url($postTitle);
			$postTypeName=getTypeName($row["type"]);
			$fcategory=$row["friendlyName"];
			$parent=$row["parent"];
			$insertDate=setDate($row['insertDate']);
			
			$postUrl=itemURL($post_id,$fcategory,$postTypeName,$postTitleF,$parent);	

			 //ocaku update post
			 /*
			if (OCAKU && $_POST && $action=="edit" ){
				$ocaku=new ocaku();
				
				if ($row["hasImages"]==1){//images
					$itemImages=getPostImages($post_id,$insertDate);//getting the images
					$numImages=count($itemImages);
					if ($numImages>0) $imagePost=$itemImages[0][1];//thumb
					else $imagePost='';
				}
				
				if (LOCATION) $oplace=getLocationName(cP("location"));
				else  $oplace=cP("place");
				
				$data=array(
					'KEY'=>OCAKU_KEY,
					'idPostInClass'=>$post_id,
					'Category'=>$row["cname"],
					'Place'=>$oplace,
					'URL'=>SITE_URL.$postUrl,
					'type'=>$postTypeName,
					'title'=>$postTitle,
					'description'=>$row["description"],
					'name'=>$row["name"],
					'price'=>$row["price"],
					'currency'=>CURRENCY,
					'language'=>substr(LANGUAGE,0,2),
					'image'=>$imagePost,
					'num_images'=>$numImages
					);
				$ocaku->updatePost($data);
				unset($ocaku);
			}
			//end ocaku */
			
    if (file_exists(SITE_ROOT.'/themes/'.THEME.'/item-manage.php')){//item-manage from the theme!
    	require_once(SITE_ROOT.'/themes/'.THEME.'/item-manage.php'); 
    }
    else{//not found in theme
			?>
<h3><?php _e("Edit Ad");?>: <a target="_blank" href="<?php echo SITE_URL.$postUrl;?>"><?php echo $postTitle;?></a></h3>
<form action="" method="post" onsubmit="return checkForm(this);" enctype="multipart/form-data">
    <?php _e("Category");?>:<br />
	<?php 
	if (PAYPAL_ACTIVE && PAYPAL_AMOUNT_CATEGORY)
		echo "<strong>".$row["cname"]."</strong><input type=\"hidden\" name=\"category\" value=\"".$row["idCategory"]."\" /><br/>";
	else
	{
		$query="SELECT idCategory,name,(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent) FROM ".TABLE_PREFIX."categories C order by idCategoryParent, `order`";
		sqlOptionGroup($query,"category",$row["idCategory"]);
	}
	?>
	<br />
	<?php _e("Title");?>*:<br />
	<input style="width:520px;" id="title" name="title" type="text" value="<?php echo $postTitle;?>" size="61" maxlength="120" onblur="validateText(this);"  lang="false" />
	<input style="width:50px;" id="price" name="price" type="text" size="3" value="<?php echo $row["price"];?>" maxlength="8"  onkeypress="return isNumberKey(event);"   /><?php echo CURRENCY;?><br />
	<br />
	<?php _e("Description");?>*:<br />
	<?php if (HTML_EDITOR){?>
        <script>!window.jQuery && document.write(unescape('%3Cscript src="http://code.jquery.com/jquery-1.8.2.min.js"%3E%3C/script%3E'))</script>
        <link rel="stylesheet" href="<?php echo SITE_URL; ?>/content/js/sceditor.css" type="text/css" media="all" />
        <script type="text/javascript"    src="<?php echo SITE_URL; ?>/content/js/jquery.sceditor.min.js"></script>
        <textarea rows="10" cols="73" name="description" id="description"><?php echo stripslashes($row['description']);?></textarea>
        <script>
            $(document).ready(function() {
                $('textarea[name=description]').sceditor({
                    toolbar: "bold,italic,underline|bulletlist,orderedlist|left,center,right|email,link,unlink",
                    resizeEnabled: "true"
                });
            });
        </script>

	<?php }else{?>
		<textarea rows="10" cols="73" name="description" id="description" onblur="validateText(this);"  lang="false"><?php echo strip_tags($row['description']);?></textarea>
    <?php }?>
    <br />
	<?php if (NEED_OFFER){?>
	<?php _e("Type");?>:<br />
	<select id="type" name="type">
		<option value="<?php echo TYPE_OFFER;?>" <?php if($row['type']==TYPE_OFFER)echo 'selected="selected"';?> ><?php _e("offer");?></option>
		<option value="<?php echo TYPE_NEED;?>"  <?php if($row['type']==TYPE_NEED)echo 'selected="selected"';?> ><?php _e("need");?></option>
	</select>
	<?php }?>
	<br />
    <?php if (LOCATION){?>
    <?php _e("Location");?>:<br />
	<?php 
    $query="SELECT idLocation,name,(select name from ".TABLE_PREFIX."locations where idLocation=C.idLocationParent) FROM ".TABLE_PREFIX."locations C order by idLocationParent, idLocation";
	echo sqlOptionGroup($query,"location",$row["idLocation"]);
	?>
    <?php }?>
    <br />
    <?php _e("Place");?>:<br />
	<?php if (MAP_KEY==""){//not google maps?>
	<input id="place" name="place" type="text" value="<?php echo $row["place"];?>" size="69" maxlength="120" /><br />
	<?php }
	else{//google maps
		if ($_POST["place"]!="") $m_value=$_POST["place"];
		elseif($row["place"]!="") $m_value=$row["place"];
		else $m_value=MAP_INI_POINT;
	?>
	<input id="place" name="place" type="text" value="<?php echo $m_value;?>" onblur="showAddress(this.value);" size="69" maxlength="120" /><br />
	<div id="map" style="width: 100%; height: 200px;"></div>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo MAP_KEY;?>" type="text/javascript"></script>
	<script type="text/javascript">var init_street="<?php echo MAP_INI_POINT;?>";</script>
	<script type="text/javascript" src="<?php echo SITE_URL;?>/content/js/map.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL;?>/content/js/mapSmall.js"></script>
	<script type="text/javascript">showAddress("<?php echo $m_value;?>");</script>
	<?php }?>
	<br />
    <?php if (VIDEO){?>
    <span style="cursor:pointer;" onclick="youtubePrompt();"><?php _e("YouTube video");?></span>: <br />
    <input id="video" name="video" type="text" value="<?php echo $row["video"];?>" onclick="youtubePrompt();" size="40" />
    <div id="youtubeVideo"></div>
    <br />
    <?php } ?>
	<?php _e("Your Name");?>*:<br />
	<input id="name" name="name" type="text" value="<?php echo $row["name"];?>" maxlength="75" onblur="validateText(this);"  lang="false"  /><br />
	<?php _e("Your Phone (published)");?>:<br />
	<input id="phone" name="phone" type="text" value="<?php echo $row["phone"];?>" maxlength="11"/><br />
	<?php if (MAX_IMG_NUM>0){
	echo "<br />".T_("Upload pictures max file size").": ".(MAX_IMG_SIZE/1000000)."Mb ".T_("format")." ".IMG_TYPES."<br />";
	echo "<input type='hidden' name='MAX_FILE_SIZE' value='".MAX_IMG_SIZE."' />";
	echo "<b>".T_("These images will be permanently removed if you upload new ones")."</b><br />";?>
	<?php 
		$images=getPostImages($post_id,$insertDate);
		foreach($images as $img){
			echo '<a href="'.$img[0].'" title="'.$itemTitle.' '.T_("Picture").'" target="_blank">
			 		<img class="thumb" src="'.$img[1].'" title="'.$itemTitle.' '.T_("Picture").'" alt="'.$itemTitle.' '.T_("Picture").'" /></a>';
		}
		for ($i=1;$i<=MAX_IMG_NUM;$i++){
			?><br /><label><?php _e("Picture");?> <?php echo $i?>:</label><input type="file" name="pic<?php echo $i?>" id="pic<?php echo $i?>" value="<?php echo $_POST["pic".$i];?>" /><?php
		 }
	 }?>
	<br /><br />
	<?php if (CAPTCHA){?>
    Captcha*:<br />
	<img alt="captcha" src="<?php echo captcha::url('edititem');?>"><br />
    <input id="captcha" name="captcha" type="text"  onblur="validateText(this);"  lang="false" /><br />
    <?php }?>
	<?php createCSRF('edititem');?>
	<input type="submit" id="submit" value="<?php _e("Update");?>" />
</form>		
<?php 
    }//if theme
		}
		else _e("Nothing found");//nothing returned for that item	
			
	}
	
	
}


require_once('footer.php');
?>