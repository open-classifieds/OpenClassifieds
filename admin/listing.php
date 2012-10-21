<?php
require_once('access.php');
require_once('header.php');

//save query string in session to return with proper parameters
$_SESSION['ADMIN_QUERY_STRING'] = remove_querystring_var('rd');

?>
<div class="page-header">
	<h1><?php _e("Classified Ads");?></h1>
	<?php if (cG('show')=='moderate'){?>
		<button class="btn disabled pull-right"><?php _e("Moderation list");?></button>
	<?php }else{?>
		<a class="btn pull-right" href="listing.php?show=moderate"><?php _e("Moderation list");?></a>
	<?php }?> 
	<?php if (cG('show')=='spam'){?>
		<button class="btn disabled pull-right"><?php _e("Spam");?></button>
	<?php }else{?>
		<a class="btn pull-right" href="listing.php?show=spam"><?php _e("Spam");?></a>
	<?php }?> 				
</div>

<?php
//show return action message
$rd=cG("rd");

switch($rd) {
    case 'edit':
        echo "<div class='alert alert-success'>".T_("Your Ad was successfully updated")."</div>";
        break;
    case 'activate':
        echo "<div class='alert alert-success'>".T_("Your Ad was successfully activated")."</div>";
        break;
    case 'deactivate':
        echo "<div class='alert alert-success'>".T_("Your Ad was successfully deactivated")."</div>";
        break;
    case 'spam':
        echo "<div class='alert alert-success'>".T_("Spam reported")."</div>";
        break;
    case 'delete':
        echo "<div class='alert alert-success'>".T_("Your Ad was successfully deleted")."</div>";
        break;
    default:
        break;
}
?>


<table class="table table-bordered">
	<thead>
		<tr>
			<th><?php _e("Name");?></th>
			<th><?php _e("Category");?></th>
	        <?php if (COUNT_POSTS){?>
			<th><?php _e("Hits");?></th>
	        <?php }?>
			<th><?php _e("Date");?></th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
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
				$postPassword=$row['password'];
				$insertDate=setDate($row['insertDate']);
				$postUrl=itemURL($idPost,$fcategory,$postTypeName,$postTitle,$fCategoryParent);
		
				if (COUNT_POSTS) {
					$itemViews=$ocdb->getValue("SELECT count(idPost) FROM ".TABLE_PREFIX."postshits where idPost=$idPost","none");
				}
    ?>
	<tr>
		<td><a title="<?php echo $postTitle." ".$postTypeName." ".$category;?>" href="<?php echo SITE_URL.$postUrl;?>" >
			<?php echo substr($postTitle,0,35);?>...</a></td>
        <td><?php echo '<a href="listing.php?category='.$fcategory.'" title="'.$category.' '.$fCategoryParent.'">'.$category.'</a>';?></td>
        <?php if (COUNT_POSTS){?>
        <td><?php echo $itemViews;?></td>
        <?php }?>
        <td><?php echo $insertDate;?></td>
        <td class="action">
        	<a class="btn btn-primary" href="<?php echo itemManageURL();?>?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=edit">
            	<i class="icon-edit icon-white"></i>
            </a>
            <?php if (cG('show')==''){?>
            <a class="btn btn-warning" onclick="return confirm('<?php _e("Deactivate");?>?');" href="<?php echo itemManageURL();?>?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=deactivate">
            	<i class="icon-remove icon-white"></i>
            </a> 
            <a class="btn btn-warning" onclick="return confirm('<?php _e("Spam");?>?');" href="<?php echo itemManageURL();?>?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=spam">
            	<i class="icon-fire icon-white"></i>
            </a>
            <?php }else{?>
            <a class="btn btn-success" onclick="return confirm('<?php _e("Activate");?>?');" href="<?php echo itemManageURL();?>?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=activate">
            	<i class="icon-ok icon-white"></i>
            </a> 
            <?php }?>
            <a class="btn btn-danger" onclick="return confirm('<?php _e("Delete");?>?');" 
            	href="<?php echo itemManageURL();?>?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=delete">
            	<i class="icon-trash icon-white"></i>
            </a>
        </td>
        	
	</tr>
	<?php 
			}
		}//end if check there's results
		else echo "<p>".T_("Nothing found")."</p>";
    ?>
    </tbody>
</table>

<?php if (cG('show')==''){?>
<div class="pagination">
<?php //page numbers
    if ($total_pages>1){
        //if is a search
        if (strlen(cG("s"))>=MIN_SEARCH_CHAR) $search="&s=".cG("s");
        
        $pag_title=$html_title." ".T_("Page")." ";
        
        $pag_url="/admin/listing.php";

        //getting the url
        if(strlen(cG("s"))>=MIN_SEARCH_CHAR){//home with search
            $pag_url.='?s='.cG("s").'&category='.$currentCategory.'&page=';
        }
        elseif ($advs){//advanced search
            $pag_url.="?category=$currentCategory&type=".cG("type")."&title=".cG("title")."&desc=".cG("desc")."&price=".cG("price")."&place=".cG("place")."&sort=".cG("sort")."&page=";
        }
        elseif (isset($type)){ //only set type in the home
            $pag_url.='?type='.$type.'&page=';
        }
        elseif (isset($currentCategory)){//category
            $pag_url.='?category='.$currentCategory.'&page=';
        }
        else {
           $pag_url.='?page=';
        }
        //////////////////////////////////
    
    	if ($page>1){
			echo "<li><a title='$pag_title' href='".SITE_URL.$pag_url."1'><i class='icon-step-backward'></i></a></li>";//First
			echo "<li><a title='".T_("Previous")." $pag_title".($page-1)."' href='".SITE_URL.$pag_url.($page-1)."'><i class='icon-backward'></i></a></li>";//previous
		}
		//pages loop
		for ($i = $page; $i <= $total_pages && $i<=($page+DISPLAY_PAGES); $i++) {//
		//for ($i = 1; $i <= $total_pages; $i++) {
	        if ($i == $page) echo "<li class='active'><a title='$pag_title$i' href='".SITE_URL."$pag_url$i'>$i</a></li>";//print the link
	        else echo "<li><a title='$pag_title$i' href='".SITE_URL."$pag_url$i'>$i</a></li>";//print the link
	    }
	    
     	if ($page<$total_pages){
		   	echo "<li><a href='".SITE_URL.$pag_url.($page+1)."' title='".T_("Next")." $pag_title".($page+1)."' ><i class='icon-forward'></i></a></li>";//next
		   	echo  "<li><a title='$pag_title$total_pages' href='".SITE_URL."$pag_url$total_pages'><i class='icon-step-forward'></i></a></li>";//End
	    }
    }	
?>
</div>
<?php }?>

<?php
require_once('footer.php');
?>