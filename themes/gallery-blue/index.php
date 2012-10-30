<?php if ($advs) echo '<div class="category">'.advancedSearchForm().'</div>';?>
	
	<?php if(isset($categoryName)&&isset($categoryDescription)){ 
	if (isset($location)) $locationtitle = " - ".getLocationName($location);
    ?>
	<div class="category">
		<p>
			<b><?php echo $categoryName.$locationtitle;?>:</b> 
			 <?php echo $categoryDescription;  ?>
		</p>
	</div>
	<?php }?>
	

<?php 
	if ($resultSearch){

	foreach ( $resultSearch as $row ){
		$idPost=$row['idPost'];
		$postType=$row['type'];
		$postTypeName=getTypeName($postType);
		$postTitle=substr($row['title'], 0, 15);
		$postPrice=$row['price'];
		$category=$row['category'];//real category name
		$fcategory=$row['fcategory'];//frienfly name category
		$idCategoryParent=$row['idCategoryParent'];
		$fCategoryParent=$row['parent'];
		$postImage=$row['image'];
		$postPassword=$row['password'];
		
		if ($insertDate!=setDate($row['insertDate'])){
			if ($insertDate!="") echo '<div class="cleaner">&nbsp;</div></div>';
			$insertDate=setDate($row['insertDate']);
			echo '<h3 class="line"><span>'.$insertDate.'</span></h3><div class="galerie">';
		}
		
		if ($row["hasImages"]==1){
			$postImage=getPostImages($idPost,$insertDate,true,true);
		}
		else $postImage=getPostImages(true,true,true,true);//there's no image
		
		$postUrl=itemURL($idPost,$fcategory,$postTypeName,$postTitle,$fCategoryParent);
		?>
		<div class="foto">
			<?php if (MAX_IMG_NUM>0){?>
				<a title="<?php echo $postTitle." ".$postTypeName." ".$fcategory;?>" href="<?php echo SITE_URL.$postUrl;?>" >
					<img  title="<?php echo $postTitle." ".$postTypeName." ".$fcategory;?>"  alt="<?php echo $postTitle." ".$postTypeName." ".$fcategory;?>"  src="<?php echo $postImage;?>" />
				</a>
			<?php }?>
			<p>
				<a title="<?php echo $postTitle." ".$postTypeName." ".$fcategory;?>" href="<?php echo SITE_URL.$postUrl;?>" >
					<?php echo $postTitle;?> 
					<?php if ($postPrice!=0) echo " - ".getPrice($postPrice);?>
				</a>
			<?php if(isset($_SESSION['admin'])){?>
				<?php echo SEPARATOR;?>
				<a href="<?php echo SITE_URL;?>/manage/?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=edit">
						<?php _e("Edit");?></a><?php echo SEPARATOR;?>
				<a onClick="return confirm('<?php _e("Deactivate");?>?');" 
					href="<?php echo SITE_URL;?>/manage/?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=deactivate">
						<?php _e("Deactivate");?></a><?php echo SEPARATOR;?>
				<a onClick="return confirm('<?php _e("Spam");?>?');"
					href="<?php echo SITE_URL;?>/manage/?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=spam">
						<?php _e("Spam");?></a><?php echo SEPARATOR;?>
				<a onClick="return confirm('<?php _e("Delete");?>?');"
					href="<?php echo SITE_URL;?>/manage/?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=delete">
						<?php _e("Delete");?></a>
			<?php }?>
			</p>
		</div>
		<?php 
	}
}//end if check there's results
else echo "<p>".T_("Nothing found")."</p>";?>
<div class="cleaner">&nbsp;</div>
</div>
	
	<div class="item">&nbsp;<br />
	<?php //page numbers echo $_SERVER["REQUEST_URI"];
		if ($total_pages>1){
			
				//if is a search
			if (strlen(cG("s"))>=MIN_SEARCH_CHAR) $search="&s=".cG("s");
			
			$pag_title=$html_title." ".T_("Page")." ";

			//getting the url
			if(strlen(cG("s"))>=MIN_SEARCH_CHAR){//home with search
				$pag_url='?s='.cG("s").'&category='.$currentCategory.'&page=';
			}
			elseif ($advs){//advanced search
				$pag_url="?category=$currentCategory&type=".cG("type")."&title=".cG("title")."&desc=".cG("desc")."&price=".cG("price")."&place=".cG("place")."&sort=".cG("sort")."&page=";
			}
			elseif (isset($type)){ //only set type in the home
				$pag_url=typeURL($type,$currentCategory).'&page=';
			}
			elseif (isset($currentCategory)){//category
				$pag_url=catURL($currentCategory,$selectedCategory);//only category
				if(!FRIENDLY_URL) $pag_url.='&page=';
			}
			elseif (isset($location)){//category
				$pag_url=catURL($currentCategory,$selectedCategory,$location);//only category
				if(!FRIENDLY_URL) $pag_url.='&page=';
			}
			else {
			    $pag_url="/";//home
			    if(!FRIENDLY_URL) $pag_url.='?page=';
			}
			//////////////////////////////////
		
			if ($page>1){
				echo "<a title='$pag_title' href='".SITE_URL.$pag_url."1'>&laquo;&laquo;</a>".SEPARATOR;//First
				echo "<a title='".T_("Previous")." $pag_title".($page-1)."' href='".SITE_URL.$pag_url.($page-1)."'>".T_("Previous")."</a>";//previous
			}
			//pages loop
			for ($i = $page; $i <= $total_pages && $i<=($page+DISPLAY_PAGES); $i++) {//for ($i = 1; $i <= $total_pages; $i++) {
		        if ($i == $page) echo SEPARATOR."<b>$i</b>";//not printing link current page
		        else echo SEPARATOR."<a title='$pag_title$i' href='".SITE_URL."$pag_url$i'>$i</a>";//print the link
		    }
		    
		    if ($page<$total_pages){
		    	echo SEPARATOR."<a href='".SITE_URL.$pag_url.($page+1)."' title='".T_("Next")." $pag_title".($page+1)."' >".T_("Next")."</a>";//next
		    	echo  SEPARATOR."<a title='$pag_title$total_pages' href='".SITE_URL."$pag_url$total_pages'>&raquo;&raquo;</a>";//End
		    }
		}	
	?>
	</div>
