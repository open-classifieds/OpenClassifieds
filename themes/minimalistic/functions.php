<?php
function generateMenuJS($selectedCategory,$begin,$end){//tabbed top menu, param, the selected category
	$ocdb=phpMyDB::GetInstance();
	
	$style='nav_selected';//for the selected item
	$nstyle='nav';//normal style
	
	//home
	 if (!isset($selectedCategory)) $astyle=$style;
	 else $astyle=$nstyle;
	 echo $begin.'<a id="nav0" onmouseover="ShowTab(0);" class="'.$astyle.'" href="'.SITE_URL.'">'.T_("Home").'</a>'.$end;
	
	
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
			$Menu.=$begin."<a id=\"nav$idCategory\" onmouseover=\"ShowTab($idCategory);\" class=\"$astyle\" title=\"$name\" href=\"".SITE_URL."$url\">$name</a>".$end;
			
		}
	} 
	echo $Menu;//home menu
}

function generateSubMenuJS($idCategoryParent,$categoryParent,$currentCategory){//generates thes submenu for a category
	
	$ocdb=phpMyDB::GetInstance();
	
	echo '<div class="sub" id="sub0"';
	if (isset($currentCategory)) echo ' style="display:none;" ';
	echo ">";
	generatePopularCategories();
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
	   			order by idCategoryParent, `order`";
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
				$subMenu.="><b>".$row['parent']."</b>";	
				$parent=$row['parent'];
			}
			
			if ($fcategory!=""){
				$url=catURL($fcategory,friendly_url($parent));	
				$subMenu.=SEPARATOR;
				if ($currentCategory==$fcategory) $subMenu.=  "<b>";//for the selectd item
				$subMenu.="<a $astyle title=\"$name\" href=\"".SITE_URL."$url\">$name</a>";
				if ($currentCategory==$fcategory) $subMenu.=  "</b>";
			}
		}
 
	if ($subMenu!="") $subMenu.="</div>";
	echo $subMenu;
}


?>
