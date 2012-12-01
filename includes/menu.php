<?php
////////////////////////////////////////////////////////////
//MENU Functions generation
////////////////////////////////////////////////////////////
function generateMenu($selectedCategory,$begin,$end){//tabbed top menu, param, the selected category
	global $style;
	$ocdb=phpMyDB::GetInstance();
	
	$query="SELECT name,friendlyName from ".TABLE_PREFIX."categories where idCategoryParent=0 order by `order`";
	$result=$ocdb->getRows($query);

	foreach ($result as $category ) {
		$name=$category["name"];
		$fcategory=$category["friendlyName"];
		if ($name!=""&&$fcategory!=""){
			$url=catURL($fcategory);	
			if ($selectedCategory==$fcategory) $astyle=$style;//selected category
			else $astyle="";
			$Menu.=$begin."<a $astyle title=\"$name\" href=\"".SITE_URL."$url\">$name</a>".$end;
		}
	} 
	echo $Menu;//home menu
}
////////////////////////////////////////////////////////////
function generateSubMenu($idCategoryParent,$categoryParent,$currentCategory){//generates thes submenu for a category
	
	$ocdb=phpMyDB::GetInstance();
	
	if ($categoryParent!=0) $subCategory=$categoryParent; //if it's a subcategory
 	else { //its a category
 		if (!$idCategoryParent) $idCategoryParent=0;//if doesnt exist the category
 		$subCategory=$idCategoryParent;
 	}


	$query="SELECT name,friendlyName, (select friendlyName from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent limit 1) parent, idCategoryParent
	         from ".TABLE_PREFIX."categories C where idCategoryParent=$subCategory order by `order`";
	$result=$ocdb->getRows($query);
	if($result){
	    $subMenu="<b>".T_("Categories")."</b>";
	    foreach ( $result as $row ) {	
			    $name=$row['name'];
			    $fcategory=$row['friendlyName'];
			    $parent=$row['parent'];
			
			    if ($name!=""){
				    $url=catURL($fcategory,$parent);
				    $subMenu.=SEPARATOR;
				    if ($currentCategory==$fcategory) $subMenu.=  "<b>";//for the selectd item
				    $subMenu.="<a $astyle title=\"$name\" href=\"".SITE_URL."$url\">$name</a>";
				    if ($currentCategory==$fcategory) $subMenu.=  "</b>";
			    }
		    }
        
	    echo $subMenu;
	}
}
////////////////////////////////////////////////////////////
function generatePopularCategories(){//popular categories displayed in the home
	$ocdb=phpMyDB::GetInstance();

	$query="select c.idCategory,c.friendlyName,c.name,count(c.idCategory) cont , (select friendlyName from ".TABLE_PREFIX."categories where idCategory=c.idCategoryParent limit 1) parent
						from ".TABLE_PREFIX."categories c
						inner join ".TABLE_PREFIX."posts p
					on p.idCategory=c.idCategory
			group by c.idCategory,c.friendlyName,c.name
			order by cont desc,c.name Limit 7";//where idCategoryParent!=0	
	$result=$ocdb->getRows($query);
	
	$popularCategories="<b>".T_("Popular")."</b>";
	foreach ( $result as $category ) {
		$name=$category["name"];
		$fcategory=$category["friendlyName"];
		$cont=$category["cont"];
		$parent=$category["parent"];
	
		if ($name!=""){
			$url=catURL($fcategory,$parent);
			$popularCategories.=SEPARATOR."<a title=\"$name $cont\" href=\"".SITE_URL."$url\">$name</a>";
		}
	} 
	echo $popularCategories;
	
}
////////////////////////////////////////////////////////////
function generatePostType($currentCategory,$type){ //shows types in the right side
    global $selectedCategory;
    
    $offerUrl = typeURL(TYPE_OFFER,$currentCategory);
    $needUrl = typeURL(TYPE_NEED,$currentCategory);
    
	$allUrl = catURL($currentCategory,$selectedCategory);
    
	if ($type==TYPE_OFFER&&isset($type)) echo "<b>".ucwords(T_("offer"))."</b>";
	else echo "<a title=\"".T_("offer")." $currentCategory\" href=\"".SITE_URL."$offerUrl\">".ucwords(T_("offer"))."</a>";
	
	if ($type==TYPE_NEED&&isset($type)) echo SEPARATOR."<b>".ucwords(T_("need"))."</b>";
	else echo SEPARATOR."<a title=\"".T_("need")." $currentCategory\" href=\"".SITE_URL."$needUrl\">".ucwords(T_("need"))."</a>";
	
	if (isset($type)) echo SEPARATOR."<a href=\"".SITE_URL."$allUrl\">".T_("All")."</a>";
	else echo SEPARATOR."<b>".T_("All")."</b>";
}
////////////////////////////////////////////////////////////
function getCategories(){//return the categories for the title, used in SEO
	$ocdb=phpMyDB::GetInstance();
	$query="SELECT name from ".TABLE_PREFIX."categories where idCategoryParent=0  order by `order` Limit 5";
	
	$result =	$ocdb->getRows($query);
	$cat=array();
	foreach ( $result as $row ){
		 array_push($cat,$row['name']);
	}
	return implode(", ",$cat);
}


/////////////////////////////////////////////////////////////
// Functions with returning data
////////////////////////////////////////////////////////////

function generateTagPopularCategories(){//popular categories displayed in the home
	$ocdb=phpMyDB::GetInstance();
			
    $query="select c.idCategory,c.friendlyName,c.name,count(c.idCategory) cont , (select friendlyName from ".TABLE_PREFIX."categories where idCategory=c.idCategoryParent limit 1) parent
						from ".TABLE_PREFIX."categories c
						left join ".TABLE_PREFIX."posts p
					on p.idCategory=c.idCategory
			group by c.idCategory,c.friendlyName,c.name
			order by cont desc,c.name";
			
	$result =$ocdb->getRows($query);
		
	if ($result){
			$cloud_size=9;//max size in the css
			$cloud = new wordCloud();
			foreach ($result as $row){
				if(!isset($max_size))$max_size=$row['cont'];///max post number
				$size=($row['cont']*$cloud_size)/$max_size;//size for the cloud	
				$cloud->addWord(array('word' => $row['name'], 'size' => round($size,0), 'url' => catURL($row['friendlyName'],$row['parent'])));
			}
		  	$cloud->orderBy('word');	
		  	$myCloud=$cloud->showCloud('array');
		  	
			foreach ($myCloud as $cloudArray) {		
		        echo ' &nbsp; <a href="'.SITE_URL.$cloudArray['url'].'" title="'.$cloudArray['word'].'"  class="word size'.$cloudArray['range'].'">'.$cloudArray['word'].'</a> &nbsp;';
		     }		
	}
	  
}

////////////////////////////////////////////////////////////
function generatePopularItems($days=7,$limit=5,$idCategory){//displays the top X limit popular items from the last X days, possible by category
	$pop='';
	if (COUNT_POSTS){
		$ocdb=phpMyDB::GetInstance();
			
		if (isset($idCategory)){//if category is set we  filter by category
			$filter =" and (P.idCategory=$idCategory or P.idCategory in (select idCategory from ".TABLE_PREFIX."categories where idCategoryParent=$idCategory))";
		}

	    $query="select count(P.idPost) cont,P.idPost,P.title,P.type,P.idCategory,C.friendlyName,(select friendlyName from ".TABLE_PREFIX."categories where idCAtegory=C.idCategoryParent Limit 1) parent
                    from ".TABLE_PREFIX."categories C 
                    inner join  ".TABLE_PREFIX."posts P
					inner join ".TABLE_PREFIX."postshits H
					 on P.idPost=H.idPost and P.idCategory=C.idCategory
				            where P.isConfirmed=1 and P.isAvailable=1 and TIMESTAMPDIFF(DAY,H.hitTime,now())<=$days $filter
                group by P.idPost,P.title,P.idCategory,P.type
				order by 1 desc	Limit $limit";
		$result=$ocdb->getRows($query);
		
		$pop="<ul>";
		foreach ($result as $row ) {
			$idPost=$row["idPost"];
			$postTypeName=getTypeName($row["type"]);
			$fcategory=$row["friendlyName"];
			$parent=$row["parent"];
			$postTitle=$row["title"];
			$FpostTitle=friendly_url($postTitle);
			$hitsCount=$row["cont"];
			
			if (is_numeric($idPost)){
				$url=itemURL($idPost,$fcategory,$postTypeName,$FpostTitle,$parent);
				$pop.="<li><a title=\"$hitsCount $postTitle $postTypeName $fcategory\" href=\"".SITE_URL."$url\">$postTitle</a></li>";
			}
		}
		$pop.="</ul>";
	}
	return $pop;
}
////////////////////////////////////////////////////////////
function totalAds($idCategory=NULL,$days=0){//return the total number of posts
	$ocdb=phpMyDB::GetInstance();
		
	if (!isset($idCategory)) $idCategory="all";
	$filter ='';
	if ($idCategory!="all"){
		$filter =" and (P.idCategory=$idCategory or P.idCategory in (select idCategory from ".TABLE_PREFIX."categories where idCategoryParent=$idCategory))";
	}
	if(is_numeric($days) && $days>0){
		$filter =" and TIMESTAMPDIFF(DAY,P.insertDate,now())<=$days";
	}
	$query="SELECT count(idPost) from ".TABLE_PREFIX."posts P where P.isConfirmed=1  $filter";//and P.isAvailable=1
		
	return $ocdb->getValue($query);
}
////////////////////////////////////////////////////////////
function totalViews($idCategory=NULL,$days=0){//return the total number of posts views
	$ocdb=phpMyDB::GetInstance();
	if (!isset($idCategory)) $idCategory="all";
	$filter ='';
	if ($idCategory!="all"){
		$filter =" and (P.idCategory=$idCategory or P.idCategory in (select idCategory from ".TABLE_PREFIX."categories where idCategoryParent=$idCategory))";
	}
	if(is_numeric($days) && $days>0){
		$filter =" and TIMESTAMPDIFF(DAY,H.hitTime,now())<=$days";
	}	
	$query="SELECT count(P.idPost) FROM ".TABLE_PREFIX."posts P 
					inner join ".TABLE_PREFIX."postshits H 
			on P.idPost=H.idPost where P.isConfirmed=1   $filter";//and P.isAvailable=1
	return $ocdb->getValue($query);	
}

///////////////////////////////////////////////////////////
function isHome(){//return if we are in the home page
    global $idItem,$currentCategory,$type,$location;
    if ( !empty($idItem)||!empty($currentCategory)||
        !empty($type)|| cG("s")!=""||!empty($location)
         ){//|| $_SERVER['PHP_SELF']!='content/index.php'
        return false;
    }
    else return true;
}
?>