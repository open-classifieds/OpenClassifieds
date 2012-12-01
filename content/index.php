<?php
require_once('header.php');

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/index.php')){//index from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/index.php'); 
}
else{//default not found in theme
	
?>

	<?php if ($advs) echo '<div class="category">'.advancedSearchForm().'</div>';?>
	
	<?php if(isset($categoryName)&&isset($categoryDescription)){
    if (isset($location)) $locationtitle = " - ".getLocationName($location);
    ?>
	<div class="category">
	    <h1><?php echo $categoryName.$locationtitle;?></h1> 
		<p>
			 <?php echo $categoryDescription;?>
			 <?php if(!PARENT_POSTS){?><a title="<?php _e("Post Ad in").' '.$categoryName;?>" href="<?php echo SITE_URL.newURL();?>"><?php _e("Post Ad in").' '.$categoryName;?></a><?php }?> 
		</p>
	</div>
	<?php }?>

<div class="item">
<?php 
	if ($resultSearch){
	foreach ( $resultSearch as $row ){
		$idPost=$row['idPost'];
		$postType=$row['type'];
		$postTypeName=getTypeName($postType);
		$postTitle=$row['title'];
		$postPrice=$row['price'];
		$postDesc= substr(strip_tags(html_entity_decode($row['description'], ENT_QUOTES, CHARSET)), 0, 200)."...";
		$category=$row['category'];//real category name
		$fcategory=$row['fcategory'];//frienfly name category
		$idCategoryParent=$row['idCategoryParent'];
		$fCategoryParent=$row['parent'];
		$postPassword=$row['password'];
		
		if ($insertDate!=setDate($row['insertDate'])){
			$insertDate=setDate($row['insertDate']);
			echo "<h3>".$insertDate."</h3>";
		}
		
		if ($row["hasImages"]==1){
			$postImage=getPostImages($idPost,$insertDate,true,true);
		}
		else $postImage=getPostImages(true,true,true,true);//there's no image
		
		
		$postUrl=itemURL($idPost,$fcategory,$postTypeName,$postTitle,$fCategoryParent);
		 	
		?>
		<div class="post" style="float:left;">
			<?php if (MAX_IMG_NUM>0){?>
				<img style="float:left;margin-right:10px;" class="thumb" title="<?php echo $postTitle." ".$postTypeName." ".$fcategory;?>"  alt="<?php echo $postTitle." ".$postTypeName." ".$fcategory;?>"  src="<?php echo $postImage;?>" />
			<?php }?>
			<h2><a title="<?php echo $postTitle." ".$postTypeName." ".$fcategory;?>" href="<?php echo SITE_URL.$postUrl;?>" ><?php echo $postTitle;?></a></h2>
			<br />		
			<p>
			    <?php echo $postTypeName;?>
                <?php echo '<a href="'.SITE_URL.catURL($fcategory,$fCategoryParent).'" title="'.$category.' '.$fCategoryParent.'">'.$category.'</a>';?>
			    <a title="<?php echo $postTitle." ".$postTypeName." ".$category;?>" href="<?php echo SITE_URL.$postUrl;?>" >
			        <?php echo $postTitle;?> 	
				</a>
				<?php if ($postPrice!=0) echo " - ".getPrice($postPrice);?>
		    </p>
			<p><?php echo $postDesc;?></p>
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
				
		</div>
		<?php 
	}
}//end if check there's results
else echo "<p>".T_("Nothing found")."</p>";
?>
</div>
	
	<div class="item">&nbsp;<br />
	<?php //page numbers
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

<?php
}//if else

require_once('footer.php');
?>
