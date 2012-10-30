<?php
require_once('../includes/bootstrap.php');

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/feed-rss.php')){//feed-rss from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/feed-rss.php'); 
}
else{//not found in theme

header("Content-type: text/xml");
?>
<rss version="2.0"  xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
<title>RSS - <?php echo $html_title;?></title>
<link><?php echo SITE_URL;?>/</link>
<description>RSS - <?php echo $html_description." ".$currentCategory." ".getTypeName($type);?></description>
<pubDate><?php echo date(DATE_RFC822);?></pubDate>
<atom10:link xmlns:atom10="http://www.w3.org/2005/Atom" rel="self" href="<?php echo SITE_URL;?>/rss/" type="application/rss+xml" />
<?php 
	if ($resultRSS){
	    foreach ( $resultRSS as $row ){
		    $idPost=$row['idPost'];
		    $postType=$row['type'];
		    $postTypeName=getTypeName($postType);
		    $postTitle=html_entity_decode($row['title'],ENT_COMPAT,CHARSET);
		    $postPrice=$row['price'];
		    $postPlace=$row['place'];
		    if (LOCATION) $postLocation = getLocationName($row['idLocation']);
		    else $postLocation='';
		    $postDesc= str_replace ("&nbsp;"," ",strip_tags(html_entity_decode($row['description'],ENT_COMPAT,CHARSET)));
		    $category=$row['category'];//real category name
		    $fcategory=$row['friendlyName'];//frienfly name category
		    $idCategoryParent=$row['idCategoryParent'];
		    $fCategoryParent=$row['parent'];
		    $insertDate=date(DATE_RFC822,strtotime($row['insertDate']));
		    		    
		    if ($row["hasImages"]==1){
				$postImage=getPostImages($idPost,setdate($row['insertDate']),true,true);
			}
			else $postImage="";//there's no image
			
		    $categoryUrl=$fcategory;
			
		    $postUrl=itemURL($idPost,$fcategory,$postTypeName,$postTitle,$fCategoryParent);
		
		    ?>
		    <item>
			    <title><![CDATA[<?php echo ucwords($postTypeName)." - ".$postTitle;?><?php  if ($postLocation!='' && LOCATION) echo ' - '.$postLocation;?>]]></title>
			    <link><?php echo SITE_URL.$postUrl;?></link>
			    <guid><?php echo SITE_URL.$postUrl;?></guid>
			    <description><![CDATA[
			    <?php if ($postImage!="" && RSS_IMAGES) echo "<img src=\"".$postImage."\" width=\"100\" height=\"75\" />";?>
			    <?php echo $postDesc;?>]]>
			    </description>
			    <pubDate><?php echo $insertDate;?></pubDate>
			    <address><?php echo $postPlace;?></address>
		    </item>
		    <?php 
	    }
	}
$ocdb->closeDB();
?>
 </channel>
</rss>

<?php

}//if else

?>
