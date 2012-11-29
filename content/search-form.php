<form action="<?php echo $action;?>" method="get"><table cellpadding="2" cellspacing="0">
	<tr><td><?php _e("Category");?>:</td><td> 
	<?php 
	$query="SELECT friendlyName,name,(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent) FROM ".TABLE_PREFIX."categories C order by idCategoryParent";
	sqlOptionGroup($query,"category",$currentCategory);
	?></td></tr>
	<?php if (NEED_OFFER){?>
	<tr><td><?php _e("Type");?>:</td><td>
		<select id="type" name="type">
			<option value="<?php echo TYPE_OFFER;?>"><?php _e("offer");?></option>
			<option value="<?php echo TYPE_NEED;?>"><?php _e("need");?></option>
		</select>
	</td></tr>
	<?php }?>
    <?php if (LOCATION){?>
	<tr><td><?php _e("Location");?>:</td><td>
    	<?php 
    	global $location;
	$query="SELECT idLocation,name,(select name from ".TABLE_PREFIX."locations where idLocation=C.idLocationParent) 
					FROM ".TABLE_PREFIX."locations C order by idLocationParent,idLocation";
        sqlOptionGroup($query,"location",$location);
    	?>
	</td></tr>
    <?php }?>  
    <tr><td><?php _e("Place");?>:</td><td><input type="text" name="place" value="<?php echo cG("place");?>" /></td></tr>
	<tr><td><?php _e("Title");?>:</td><td><input type="text" name="title" value="<?php echo cG("title");?>" /></td></tr>
	<tr><td><?php _e("Description");?>:</td><td><input type="text" name="desc" value="<?php echo cG("desc");?>" /></td></tr>
	<tr><td><?php _e("Price");?>:</td><td><input type="text" name="price" value="<?php echo cG("price");?>" /></td></tr>
	<tr><td><?php _e("Sort");?>:</td>
		<td>
			<select name="sort">
				<option></option>
				<option value="price-desc" <?php if(cG("sort")=="price-desc")  echo "selected=selected";?> ><?php _e("Price");?> - <?php _e("Desc");?></option>
				<option value="price-asc" <?php if(cG("sort")=="price-asc")  echo "selected=selected";?> ><?php _e("Price");?> - <?php _e("Asc");?></option>
			</select>
		</td></tr>
	<tr><td>&nbsp;</td><td><input type="submit" value="<?php _e("Search");?>" /></td></tr>
	</table></form>