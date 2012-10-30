<?php
function generateMenuJS($selectedCategory){//tabbed top menu, param, the selected category
	$ocdb=phpMyDB::GetInstance();
	
	$style='default_page_item';//for the selected item
	$nstyle='page_item page-item';//normal style
	
	//home
	 if (!isset($selectedCategory)) $astyle=$style;
	 else $astyle=$nstyle;
	 echo '<li id="nav0" class="'.$astyle.'"><a onmouseover="ShowTab(0);" href="'.SITE_URL.'">'.T_("Home").'</a></li>';
	
	
	$query="SELECT name,friendlyName,idCategory from ".TABLE_PREFIX."categories where idCategoryParent=0 order by `order`";
	$result=$ocdb->getRows($query);
	
	foreach ($result as $category ) {
		$name=$category["name"];
		$fcategory=$category["friendlyName"];
		$idCategory=$category["idCategory"];
		if ($name!=""&&$fcategory!=""){
			$url=catURL($fcategory);	
			if ($selectedCategory==$fcategory) $astyle=$style;//selected category
			else $astyle=$nstyle;
			$Menu.="<li id=\"nav$idCategory\" class='".$astyle."'><a  onmouseover=\"ShowTab($idCategory);\" title=\"$name\" href=\"".SITE_URL."$url\">$name</a></li>";
			
		}
	} 
	echo $Menu;//home menu
}

function generateSubMenuJS($idCategoryParent,$categoryParent,$currentCategory){//generates thes submenu for a category
	
	$ocdb=phpMyDB::GetInstance();
	
	echo '<div class="sub" id="sub0"';
	if (isset($currentCategory)) echo ' style="display:none;" ';
	echo ">";
	generatePopularCategoriesJS();
	echo '</div>';
	
	if ($categoryParent!=0) $subCategory=$categoryParent; //if it's a subcategory
 	else { //its a category
 		if (!$idCategoryParent) $idCategoryParent=0;//if doesnt exist the category
 		$subCategory=$idCategoryParent;
 	}	
	$query="SELECT idCategory,name,friendlyName,
	   					(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent limit 1) parent, 
	   					idCategoryParent
	   					FROM ".TABLE_PREFIX."categories C 
	   			where idCategoryParent!=0 
	   			order by idCategoryParent,`order`";
	$result=$ocdb->getRows($query);
	
	$parent="";
	foreach ($result as $row ) {	
			$name=$row['name'];
			$fcategory=$row['friendlyName'];
			$CategoryParent=$row['idCategoryParent'];
			
			if ($parent!=$row['parent']&&$row['parent']!=""){
				if ($parent!='') $subMenu.='</div>';
				$subMenu.="<div class=\"sub\" id=\"sub$CategoryParent\""; 
				if ($CategoryParent!=$subCategory) $subMenu.=' style="display:none;" ';
				$subMenu.=">";	
				$parent=$row['parent'];
			}
			
			if ($fcategory!=""){
				$url=catURL($fcategory,friendly_url($parent));	
				//$subMenu.=SEPARATOR;
				if ($currentCategory==$fcategory) $subMenu.=  "<b>";//for the selectd item
				$subMenu.="<a $astyle title=\"$name\" href=\"".SITE_URL."$url\">$name</a>";
				if ($currentCategory==$fcategory) $subMenu.=  "</b>";
			}
		}
 
	if ($subMenu!="") $subMenu.="</div>";
	echo $subMenu;
}

function generatePopularCategoriesJS(){//popular categories displayed in the menu
	$ocdb=phpMyDB::GetInstance();

	$query="select c.idCategory,c.friendlyName,c.name,count(c.idCategory) cont , (select friendlyName from ".TABLE_PREFIX."categories where idCategory=c.idCategoryParent limit 1) parent
						from ".TABLE_PREFIX."categories c
						inner join ".TABLE_PREFIX."posts p
					on p.idCategory=c.idCategory
			group by c.idCategory,c.friendlyName,c.name
			order by cont desc,c.name Limit 7";//where idCategoryParent!=0	
	$result=$ocdb->getRows($query);
	
	//$popularCategories="<b>".T_("Popular")."</b>";
	foreach ( $result as $category ) {
		$name=$category["name"];
		$fcategory=$category["friendlyName"];
		$cont=$category["cont"];
		$parent=$category["parent"];
	
		if ($name!=""){
			$url=catURL($fcategory,$parent);
			$popularCategories.="<a title=\"$name $cont\" href=\"".SITE_URL."$url\">$name</a>";
		}
	} 
	echo $popularCategories;
	
}

function getCategoriesList(){//for the home
    $ocdb=phpMyDB::GetInstance();
    $query="SELECT name,friendlyName,idCategory from ".TABLE_PREFIX."categories where idCategoryParent=0 order by `order`";
    $result=$ocdb->getRows($query);
    
    $i = 0;
	$q = count($result);
	$z = round($q/3);

    foreach ($result as $category ) {
        $name=$category["name"];
        $fcategory=$category["friendlyName"];
        $idCat=$category["idCategory"];
        if ($name!=""&&$fcategory!=""){
            
            if ($i==0 or $i==$z) $list.= '<div class="cats_col1 cats_colums">';
		    elseif ($i==($z*2)) $list.= '<div class="cats_col2 cats_colums">';

	        $url=catURL($fcategory);	
	       
	        $list.= '<ul><li class="cathead"><a title="'.$name.'" href="'.SITE_URL.$url.'">'.$name.'</a></li>';
	        
	        //get sub cats category
	            $query="SELECT idCategory,name,friendlyName
   					FROM ".TABLE_PREFIX."categories C 
       			where idCategoryParent!=0  and idCategoryParent=$idCat
       			order by idCategoryParent, `order`";
                $result2=$ocdb->getRows($query);


                foreach ($result2 as $row ) {	
	                    $name2=$row['name'];
	                    $fcategory2=$row['friendlyName'];
	                    if ($fcategory!=""){
		                    $url=catURL($fcategory2,$fcategory);	              
		                    $list.= "<li><a title=\"$name2\" href=\"".SITE_URL."$url\">$name2</a></li>";
	                    }
                 }
	        //end get sub cats category
	        
	        $list.= '</ul>';
	        if ($i==($z-1) or $i==(($z*2)-1) or $i==($q-1)) $list.='</div>';
		    $i++;
        }	//if name        
    } //for  
   return $list;
}


?>
