<?php
require_once('../header.php');

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/account/index.php')){//account index from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/account/index.php'); 
}
else{//not found in theme

$account = Account::createBySession();
if ($account->exists){
    $name = $account->name;
    $email = $account->email;
} else redirect(accountLoginURL());


?>
<h2><?php _e("Welcome").' '.$name?></h2>
<br/>
<p><?php _e("My Account");?><?php echo SEPARATOR;?><?php echo '<a href="'.accountSettingsURL().'">'.T_("Settings").'</a>';?><?php echo SEPARATOR;?><?php echo '<a href="'.accountLogoutURL().'">'.T_("Logout").'</a>';?></p>
<br />
<h3><?php _e("My Classified Ads");?></h3>
<div class="item">
<?php
$query="select idPost,title,type,friendlyName,password,isConfirmed,isAvailable,
        (select friendlyName from ".TABLE_PREFIX."categories where idCategory=c.idCategoryParent limit 1) parent
		    from ".TABLE_PREFIX."posts p 
		    inner join ".TABLE_PREFIX."categories c
		    on c.idCategory=p.idCategory
            where p.email = '$email'
            order by title";

$result=$ocdb->query($query);
if (mysql_num_rows($result)){
	while ($row=mysql_fetch_assoc($result)){
	    $post_id=$row["idPost"];
    	$title=$row["title"];
    	$postTitle=friendly_url($title);
    	$postTypeName=getTypeName($row["type"]);
    	$fcategory=$row["friendlyName"];
    	$parent=$row["parent"];
    	$postPassword=$row["password"];
        $confirmed=$row["isConfirmed"];
        $active=$row["isAvailable"];
   		
    	$postUrl=itemURL($post_id,$fcategory,$postTypeName,$postTitle,$parent);
    	
    	if(FRIENDLY_URL) $linkext='/manage/';
    	else $linkext='/content/item-manage.php';
    	
        echo '<p><strong><a href="'.SITE_URL.$postUrl.'" target="_blank">'.$title.'</a></strong><br />';
        if ($confirmed){
            echo '<a href="'.SITE_URL.$linkext.'?post='.$post_id.'&amp;pwd='.$postPassword.'&amp;action=edit" target="_blank">'.T_("Edit").'</a>'.SEPARATOR.'';
            if ($active) echo '<a href="'.SITE_URL.$linkext.'?post='.$post_id.'&amp;pwd='.$postPassword.'&amp;action=deactivate" target="_blank">'.T_("Deactivate").'</a>'.SEPARATOR.'';
            else echo '<a href="'.SITE_URL.$linkext.'?post='.$post_id.'&amp;pwd='.$postPassword.'&amp;action=activate" target="_blank">'.T_("Activate").'</a>'.SEPARATOR.'';
        	echo '<a href="'.SITE_URL.$linkext.'?post='.$post_id.'&amp;pwd='.$postPassword.'&amp;action=delete" target="_blank">'.T_("Delete").'</a>';
        } else echo '<a href="'.SITE_URL.$linkext.'?post='.$post_id.'&amp;pwd='.$postPassword.'&amp;action=confirm" target="_blank">'.T_("Confirm").'</a>';
    	echo '</p>';
    }
}
?>
</div>
<?php
}//if else

require_once('../footer.php');
?>