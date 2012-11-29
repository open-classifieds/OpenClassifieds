<?php
if ($advs){//advanced search form
	echo '<div>';
	    advancedSearchForm();
	echo '</div>';
}


if (!isHome()){
?>
<div id="listings">
<?php 
	if (isset($location)) $locationtitle = " - ".getLocationName($location);
	if (isset($categoryName)) echo '<h1>'.$categoryName.$locationtitle.'</h1>';

	if ($resultSearch){
		foreach ( $resultSearch as $row ){
			$idPost=$row['idPost'];
			$postType=$row['type'];
			$postTypeName=getTypeName($postType);
			$postTitle=$row['title'];
			$postPrice=$row['price'];
			$postDesc = mb_substr(strip_tags(html_entity_decode($row['description'], ENT_QUOTES, CHARSET)), 0, 200, CHARSET)."...";
			$category=$row['category'];//real category name
			$fcategory=$row['fcategory'];//frienfly name category
			$idCategoryParent=$row['idCategoryParent'];
			$fCategoryParent=$row['parent'];
			$postImage=$row['image'];
			$postPassword=$row['password'];
			$insertDate=setDate($row['insertDate']);
			$postUrl=itemURL($idPost,$fcategory,$postTypeName,$postTitle,$fCategoryParent);
			if ($row["hasImages"]==1){
				$postImage=getPostImages($idPost,$insertDate,true,true);
			}
			else $postImage=getPostImages(true,true,true,true);//there's no image
			?>
			<div class="post">
			
			    <?php if (MAX_IMG_NUM>0){?>	
						<img  title="<?php echo $postTitle." ".$postTypeName." ".$category;?>"  alt="<?php echo $postTitle." ".$postTypeName." ".$category;?>"  src="<?php echo $postImage;?>" class="post-img" />
				<?php }?>
				
			    <h2><a title="<?php echo $postTitle." ".$postTypeName." ".$category;?>" href="<?php echo SITE_URL.$postUrl;?>"  rel="bookmark" >
						<?php echo $postTitle;?></a></h2>
			    
			     <div class="post-detail">
	                <p><?php if ($postPrice!=0) echo '<span class="post-price">'.getPrice($postPrice).'</span> — ';?><span class="post-cat"><?php echo '<a href="'.SITE_URL.catURL($fcategory,$fCategoryParent).'" title="'.$category.' '.$fCategoryParent.'">'.$category.'</a>';?></span> — <span class="post-date"><?php echo $insertDate;?></span></p>
	             </div>
	             
	          <p class="post-desc"><?php echo $postDesc;?></p>
	          
	          <?php if(isset($_SESSION['admin'])){?>
					<br />
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
	          <div class="clear"></div>
	        </div>    
			    		
			<?php 
		}
	}//end if check there's results
else echo "<p>".T_("Still no ads in this category.")."</p>";
?>
</div>
	

	<div class="pagination">
	 <div class="wp-pagenavi">
	<?php //page numbers echo $_SERVER["REQUEST_URI"];
		if ($total_pages>1){
			
			//if is a search
			if (strlen(cG("s"))>=MIN_SEARCH_CHAR) $search="&s=".cG("s");
			
			$pag_title=$html_title." ".T_("Page")." ";

			//getting the url
			if(cG("s")!=''){//home with search
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
				echo "<a title='$pag_title' href='".SITE_URL.$pag_url."1'>&lt;&lt;</a>";//First
				echo "<a title='".T_("Previous")." $pag_title".($page-1)."' href='".SITE_URL.$pag_url.($page-1)."'>&lt;</a>";//previous
			}
			//pages loop
			for ($i = $page; $i <= $total_pages && $i<=($page+DISPLAY_PAGES); $i++) {//for ($i = 1; $i <= $total_pages; $i++) {
		        if ($i == $page) echo "<span class='current'>$i</span>";//not printing link current page
		        else echo "<a class='page' title='$pag_title$i' href='".SITE_URL."$pag_url$i'>$i</a>";//print the link
		    }
		    
		    if ($page<$total_pages){
		    	echo "<a href='".SITE_URL.$pag_url.($page+1)."' title='".T_("Next")." $pag_title".($page+1)."' >&gt;</a>";//next
		    	echo  "<a title='$pag_title$total_pages' href='".SITE_URL."$pag_url$total_pages'>&gt;&gt;</a>";//End
		    }
		}	
	?>
	</div>
	</div>
   
<?php    
}//if not home
else {//home page carousel and categories?>
     
     <h4 class="slideh"><?php _e("Recents Ads"); ?></h4>
      <div id="slider">
        <div class="prev"><img src="<?php echo SITE_URL;?>/themes/hostpel/images/prev.gif" alt="<?php _e('Previous picture');?>" width="19" height="19" /></div>
        <div class="slider">
          <ul>  
            <?php
            if ($resultSearch){
            	foreach ( $resultSearch as $row ){
	                $idPost=$row['idPost'];
		            $postType=$row['type'];
		            $postTypeName=getTypeName($postType);
		            $postTitle=$row['title'];
		            $category=$row['category'];//real category name
		            $fcategory=$row['fcategory'];//frienfly name category
		            $idCategoryParent=$row['idCategoryParent'];
		            $fCategoryParent=$row['parent'];
		            $postImage=$row['image'];
		  			$insertDate=setDate($row['insertDate']);
		  			if ($row["hasImages"]==1){
						$postImage=getPostImages($idPost,$insertDate,true,true);
					}
					else $postImage=getPostImages(true,true,true,true);//there's no image
		
		            $postUrl=itemURL($idPost,$fcategory,$postTypeName,$postTitle,$fCategoryParent);?>
	                <li>
				        <?php if (MAX_IMG_NUM>0){?>	
				        <a title="<?php echo $postTitle." ".$postTypeName." ".$category;?>" href="<?php echo SITE_URL.$postUrl;?>" >
						    <img  title="<?php echo $postTitle." ".$postTypeName." ".$category;?>"  alt="<?php echo $postTitle." ".$postTypeName." ".$category;?>"  src="<?php echo $postImage;?>"  /></a>
						<?php }?>
	                    <a title="<?php echo $postTitle." ".$postTypeName." ".$category;?>" href="<?php echo SITE_URL.$postUrl;?>" >
			    		<?php echo $postTitle;?></a>
	                </li>
            <?php }
            }?>
          </ul>
        </div>
        <div class="next"><img src="<?php echo SITE_URL;?>/themes/hostpel/images/next.gif" alt="<?php _e('Next picture');?>" width="19" height="19" /></div>
        <div class="clear"></div>
      </div>
    
      <div id="frontpage_cats">
        <?php echo getCategoriesList();?>
        <div class="clear"></div>
      </div>

     <?php
     }
?>

