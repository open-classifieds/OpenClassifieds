<?php
require_once('access.php');
require_once('header.php');

//save query string in session to return with proper parameters
$_SESSION['ADMIN_QUERY_STRING'] = remove_querystring_var('rd');


//perform multiple actions
if (cP('formaction')!=NULL)
{
    $action = cP('formaction');
 
    //get id's of the items to perform action
    $posts = array();
    foreach (oc::$_POST as $key=> $value) 
    {
        if ($value == 'on')
        {
            $key = explode('___', $key);
            $posts[] = array('id'  => (int)$key[0],
                             'pwd' => $key[1]);
        }
    }

    if (count($posts)>0)
    {
        //var_dump($posts);

        switch ($action) {
        case 'delete':
               foreach ($posts as $post) deletePost($post['id'],$post['pwd'],FALSE);
            break;
        
        case 'spam':
                foreach ($posts as $post) spamPost($post['id'],$post['pwd'],FALSE);
            break;

        case 'activate':
                foreach ($posts as $post) activatePost($post['id'],$post['pwd'],FALSE);
            break;
        case 'deactivate':
                foreach ($posts as $post) deactivatePost($post['id'],$post['pwd'],FALSE);
            break;
        }

        //redirect you to the listing to update the changes...
        redirect('listing.php?show='.cG('show'));
    }
   
}

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

<script type="text/javascript">
function checkUncheckAll(theElement) {
        var theForm = theElement.form, z = 0;
         for(z=0; z<theForm .length;z++){
         if(theForm[z].type == 'checkbox' && theForm[z].name != 'checkall'){
          theForm[z].checked = theElement.checked;
          }
        }
    }
function form_action(action,text)
{
    if (confirm(text)) {
        document.getElementById('formaction').value = action;
        return true;
    }

    return false;
    
}
</script>

<form name="listing" id="listing" method="post" action="" >
    <input type="hidden" name="formaction" id="formaction" value="" />

<table class="table table-bordered">
	<thead>
		<tr>
            <th><input type="checkbox"  name="checkall" id="checkall" onclick="checkUncheckAll(this);" ></th>
			<th><?php _e("Name");?></th>
			<th><?php _e("Category");?></th>
	        <?php if (COUNT_POSTS){?>
			<th><?php _e("Hits");?></th>
	        <?php }?>
			<th><?php _e("Date");?></th>
			<th>
                <?php if (cG('show')==''){?>
                <button title="<?php _e("Deactivate");?>" class="btn btn-warning" onclick="return form_action('deactivate','<?php _e("Deactivate");?>');">
                    <i class="icon-remove icon-white"></i>
                </button> 
                <?php }?>

                <?php if (cG('show')=='moderate' || cG('show')==''){?>
                <button title="<?php _e("Spam");?>" class="btn btn-warning" onclick="return form_action('spam','<?php _e("Spam");?>?');" >
                    <i class="icon-fire icon-white"></i>
                </button>
                <?php }?>

                <?php if (cG('show')=='spam' || cG('show')=='moderate'){?>
                <button title="<?php _e("Activate");?>" class="btn btn-success" onclick="return form_action('activate','<?php _e("Activate");?>?');" >
                    <i class="icon-ok icon-white"></i>
                </button> 
                <?php }?>

                <button title="<?php _e("Delete");?>" class="btn btn-danger" onclick="return form_action('delete','<?php _e("Delete");?>?');" >
                    <i class="icon-trash icon-white"></i>
                </button>

            </th>
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
        <td><input type="checkbox" name="<?php echo $idPost.'___'.$postPassword;?>"></td>
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
            <?php }?>

            <?php if (cG('show')=='moderate' || cG('show')==''){?>
            <a class="btn btn-warning" onclick="return confirm('<?php _e("Spam");?>?');" href="<?php echo itemManageURL();?>?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=spam">
            	<i class="icon-fire icon-white"></i>
            </a>
            <?php }?>

            <?php if (cG('show')=='spam' || cG('show')=='moderate'){?>
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
</form>


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
        
        //filter
        if (cG('show')!=NULL) $show='&show='.cG('show');
        

    	if ($page>1){
			echo "<li><a title='$pag_title' href='".SITE_URL.$pag_url."1$show'><i class='icon-step-backward'></i></a></li>";//First
			echo "<li><a title='".T_("Previous")." $pag_title".($page-1)."' href='".SITE_URL.$pag_url.($page-1).$show."'><i class='icon-backward'></i></a></li>";//previous
		}
		//pages loop
		for ($i = $page; $i <= $total_pages && $i<=($page+DISPLAY_PAGES); $i++) {//
		//for ($i = 1; $i <= $total_pages; $i++) {
	        if ($i == $page) echo "<li class='active'><a title='$pag_title$i' href='".SITE_URL."$pag_url$i$show'>$i</a></li>";//print the link
	        else echo "<li><a title='$pag_title$i' href='".SITE_URL."$pag_url$i$show'>$i</a></li>";//print the link
	    }
	    
     	if ($page<$total_pages){
		   	echo "<li><a href='".SITE_URL.$pag_url.($page+1)."' title='".T_("Next")." $pag_title".($page+1).$show."' ><i class='icon-forward'></i></a></li>";//next
		   	echo  "<li><a title='$pag_title$total_pages' href='".SITE_URL."$pag_url$total_pages$show'><i class='icon-step-forward'></i></a></li>";//End
	    }
    }	
?>
</div>


<?php
require_once('footer.php');
?>