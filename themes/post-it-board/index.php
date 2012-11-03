    <?php if ($advs){
         echo '<div class="adv">';
         advancedSearchForm();
         echo '</div>';}?>
	
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
		$insertDate=setDate($row['insertDate']);
		if ($row["hasImages"]==1){
			$postImage=getPostImages($idPost,$insertDate,true,true);
		}
		else $postImage=getPostImages(true,true,true,true);//there's no image
		$postUrl=itemURL($idPost,$fcategory,$postTypeName,$postTitle,$fCategoryParent);
		
		?>
		<div class="postit">
		    <h3><a title="<?php echo $postTitle." ".$postTypeName." ".$fcategory;?>" href="<?php echo SITE_URL.$postUrl;?>" >
					<?php echo $postTitle;?> 
					<?php if ($postPrice!=0) echo " - ".getPrice($postPrice);?>
				</a></h3>
			<?php if (MAX_IMG_NUM>0){?>
				<a title="<?php echo $postTitle." ".$postTypeName." ".$fcategory;?>" href="<?php echo SITE_URL.$postUrl;?>" >
					<img  title="<?php echo $postTitle." ".$postTypeName." ".$fcategory;?>"  alt="<?php echo $postTitle." ".$postTypeName." ".$fcategory;?>"  src="<?php echo $postImage;?>" />
				</a>
			<?php }?>
			<p class="postit_p">
            <?php echo $insertDate;?>
			<?php if(isset($_SESSION['admin'])){?><br />
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
else echo "<p>".T_("Nothing found")."</p>";
?>

	<div id="pagination">
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
				echo "<span class='number'><a title='$pag_title' href='".SITE_URL.$pag_url."1'><<</a></span>";//First
				echo "<span class='number'><a title='".T_("Previous")." $pag_title".($page-1)."' href='".SITE_URL.$pag_url.($page-1)."'><</a></span>";//previous
			}
			//pages loop
			for ($i = $page; $i <= $total_pages && $i<=($page+DISPLAY_PAGES); $i++) {//for ($i = 1; $i <= $total_pages; $i++) {
		        if ($i == $page) echo "<span class='number'>$i</span>";//not printing link current page
		        else echo "<span class='number'><a title='$pag_title$i' href='".SITE_URL."$pag_url$i'>$i</a></span>";//print the link
		    }
		    
		    if ($page<$total_pages){
		    	echo "<span class='number'><a href='".SITE_URL.$pag_url.($page+1)."' title='".T_("Next")." $pag_title".($page+1)."' >></a></span>";//next
		    	echo  "<span class='number'><a title='$pag_title$total_pages' href='".SITE_URL."$pag_url$total_pages'>>></a></span>";//End
		    }
		}	
	?>
	</div>
        
        
	    
	  


	
