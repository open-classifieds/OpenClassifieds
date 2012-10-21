<?php
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
