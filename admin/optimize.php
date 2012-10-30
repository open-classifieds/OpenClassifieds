<?php
require_once('access.php');
require_once('header.php');
?>
<div class="page-header">
	<h1><?php _e("Tools to Optimize");?></h1>	
</div>

<?php
if (cG("action")=="db"){
    $result  = $ocdb->query('SHOW TABLE STATUS FROM '. DB_NAME); 
    while ($row = mysql_fetch_array($result)) $tables[]=$row[0];  
    $tables=implode(", ",$tables); //echo $tables;
    $ocdb->query('OPTIMIZE TABLE '.$tables);
    echo "<div class='alert alert-success'>".T_("All tables found in the database were optimized").": $tables</div>";
}
elseif (cG("action")=="cache") {
	deleteCache();
	echo "<div class='alert alert-success'>".T_("Cache was deleted")."</div>";
}
elseif (cG("action")=="notconfirmed") {
	echo "<div class='alert alert-success'>".T_("Delete Ads not confirmed in 3 days")."</div>";
	$query="SELECT idPost,insertDate FROM ".TABLE_PREFIX."posts where isConfirmed=0  and TIMESTAMPDIFF(DAY,insertDate,now())>=3";
	$result =	$ocdb->query($query);
	while ($row=mysql_fetch_assoc($result))//delete posible images and folders
	{	
		$idPost=$row['idPost'];
		$date=setDate($row['insertDate']);
		deletePostImages($idPost,$date);//delete images and folder for the ad
		//_e("Deleted")." $idPost<br />";
	}
	//delete from db
	$ocdb->delete(TABLE_PREFIX."posts","isConfirmed=0  and TIMESTAMPDIFF(DAY,insertDate,now())>=3");	
}
elseif (cG("action")=="spam") {
    echo "<div class='alert alert-success'>".T_("Deleted all Spam Ads")."</div>";
    $query="SELECT idPost,insertDate FROM ".TABLE_PREFIX."posts where isAvailable=2";
    $result =	$ocdb->query($query);
    while ($row=mysql_fetch_assoc($result))//delete posible images and folders
    {
        $idPost=$row['idPost'];
        $date=setDate($row['insertDate']);
        deletePostImages($idPost,$date);//delete images and folder for the ad
        //_e("Deleted")." $idPost<br />";
    }
    //delete from db
    $ocdb->delete(TABLE_PREFIX."posts","isAvailable=2");
}
?>
<ul>
    <li><a href="<?php echo SITE_URL;?>/admin/optimize.php?action=cache" onClick="return confirm('<?php _e("Are you sure");?>?');"><?php _e("Delete Cache");?></a></li>
    <li><a href="<?php echo SITE_URL;?>/admin/optimize.php?action=notconfirmed" onClick="return confirm('<?php _e("Are you sure");?>?');"><?php _e("Delete Ads not confirmed in 3 days");?></a></li>
    <li><a href="<?php echo SITE_URL;?>/admin/optimize.php?action=spam" onClick="return confirm('<?php _e("Are you sure");?>?');"><?php _e("Delete all Spam Ads");?></a></li>
    <li><a href="<?php echo SITE_URL;?>/admin/optimize.php?action=db" onClick="return confirm('<?php _e("Are you sure");?>?');"><?php _e("Optimize  all tables found in the database");?></a></li>
    <li><a href="<?php echo SITE_URL;?>/admin/phpinfo.php" ><?php _e("PHP Info")?></a></li>
</ul>
<?php
require_once('footer.php');
?>