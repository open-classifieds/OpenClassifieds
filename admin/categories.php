<?php
require_once('access.php');
require_once('header.php');

function catSlug($name,$id=""){ //try to prevent duplicated categories
	$ocdb=phpMyDB::GetInstance();
	$name=friendly_url($name);

	if (is_numeric($id)) $query="SELECT friendlyName FROM ".TABLE_PREFIX."categories where (friendlyName='$name') and (idCategory <> $id) limit 1";
	else $query="SELECT friendlyName FROM ".TABLE_PREFIX."categories where (friendlyName='$name') limit 1";
	$res=$ocdb->getValue($query,"none");

	if ($res!=false){//exists try adding with parent id
		$name.='-'.cP("cparent");  
		$res=$ocdb->getValue("SELECT friendlyName FROM ".TABLE_PREFIX."locations where friendlyName='$name' limit 1","none");//now with the new cat name
	}

	if ($res==false) return $name;
	else return false;	
}

//actions
$filter ='';
if (cP("action")!=""||cG("action")!=""){
	$action=cG("action");
	if ($action=="")$action=cP("action");

	if (!is_numeric($_POST['cparent']))  oc::$_POST['cparent']=0;
	
	if ($action=="new"){
		$nameSlug=catSlug(cP("cname"));
		if ($nameSlug!=false){  //no exists insert
			$ocdb->insert(TABLE_PREFIX."categories (name,friendlyName,`order`,idCategoryParent,description,price)",
			"'".cP("cname")."','$nameSlug',".cP("corder").",".cP("cparent").",'".cP("cdesc")."',".cP("cprice"));
		}
		else _e("Category already exists");

	}
	elseif ($action=="delete"){

		if ( is_numeric(cG('pcid')) && is_numeric(cG('cid')) ){//move posts
			$ocdb->update(TABLE_PREFIX.'posts',array('idCategory'=>cG('pcid')),'idCategory='.cG('cid'));
			$countposts=0;
		}
		else{//counting how many posts
			$query = "select count(*) from ".TABLE_PREFIX."posts where idCategory=".cG("cid");
			$countposts=$ocdb->getValue($query);
		}
		
		if ( is_numeric(cG('mcid')) && is_numeric(cG('cid')) ){//move category parent
			$ocdb->update(TABLE_PREFIX.'categories',array('idCategoryParent'=>cG('mcid')),'idCategoryParent='.cG('cid'));
			$siblings=0;
		}
		else{
			$query = "select count(*) from ".TABLE_PREFIX."categories where idCategoryParent=".cG("cid");
			$siblings=$ocdb->getValue($query);
		}

		if ($siblings>0){
			echo '<b>'.T_('There´s categories that belong to the selected category').'</b><br />';
			echo '<form  class="well form-inline" action="categories.php" method="get" >'.$siblings.' '.T_('Move them to').':';
				$query="SELECT idCategory,name FROM ".TABLE_PREFIX."categories C where idCategoryParent=0 and idCategory!=".cG('cid')." order by  `order`";
				sqlOption($query,"mcid","0",T_("Home"));
			echo '<input type="hidden" name="cid" value="'.cG("cid").'" />
				<input type="hidden" name="action" value="delete" />
				<input type="submit" class="btn" /></form>';	
		}
		elseif ($countposts>0){
			echo '<b>'.T_('There´s posts that belong to the selected category').'</b><br />';
			echo '<form class="well form-inline" action="categories.php" method="get" >'.$countposts.' '.T_('Move them to').':';
				$query="SELECT idCategory,name,(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent) FROM ".TABLE_PREFIX."categories C where idCategory!=".cG('cid')." order by idCategoryParent, `order`";
				sqlOption($query,"pcid","0",T_("Home"));
			echo '<input type="hidden" name="cid" value="'.cG("cid").'" />
				<input type="hidden" name="action" value="delete" />
				<input type="submit" class="btn" /></form>';
			
		}
		else $ocdb->delete(TABLE_PREFIX."categories","idCategory=".cG("cid"));
		//echo "Deleted";
	}
	elseif ($action=="edit"){
		$nameSlug=catSlug(cP("cname"),cP("cid"));
		if ($nameSlug!=false){  //no exists update	
			$query="update ".TABLE_PREFIX."categories set name='".cP("cname")."',friendlyName='$nameSlug'
					,`order`=".cP("corder").",idCategoryParent=".cP("cparent").",description='".cP("cdesc")."',price=".cP("cprice")." 
					where idCategory=".cP("cid");
			$ocdb->query($query);
		}
		else _e("Category already exists");
		//echo "Edit: $query";
	}
	elseif ($action=="filter" && is_numeric(cG("cid"))){
		$filter = ' and idCategoryParent='.cG("cid");
	}

	if (CACHE_DEL_ON_CAT) deleteCache();//delete cache on category if is activated
	if (SITEMAP_DEL_ON_CAT) sitemap::generate();//new/update cat generate sitemap
}
?>
<script type="text/javascript">
	function newCategory(){
		d = document.category;
		d.cid.value = "";
		d.cname.value = "";
		d.corder.value = "";
		d.cparent.value = "";
		d.cdesc.value = "";
		d.cprice.value = "0";
		d.action.value ="new";
		d.submitCat.value ="<?php _e("New Category");?>";
		document.getElementById("form-tab").innerHTML ="<?php _e("New Category");?>";
		show("formCat");
		location.href = "#formCat";
	}	
	function editCategory(cid, corder,cparent,cprice){
		d = document.category;
		d.cid.value = cid;
		d.cname.value = document.getElementById('name-'+cid).innerHTML;
		d.corder.value = corder;
		d.cparent.value = cparent;
		d.cdesc.value = document.getElementById('desc-'+cid).innerHTML;//cdesc;
		d.cprice.value = cprice;
		d.action.value ="edit";
		d.submitCat.value ="<?php _e("Edit Category");?>";
		document.getElementById("form-tab").innerHTML ="<?php _e("Edit Category");?>";
		show("formCat");
		location.href = "#formCat";
	}	
	function deleteCategory(category){
		if (confirm('<?php _e("Delete Category");?> "'+document.getElementById('name-'+category).innerHTML+'"?'))
		window.location = "categories.php?action=delete&cid=" + category;
	}
</script>

<div class="page-header">
	<h1><?php _e("Categories"); ?></h1>
	<button class="btn btn-primary pull-right" onclick="newCategory();return false;">
		<i class="icon-pencil icon-white"></i>
		<?php _e("New Category");?>
	</button>		
</div>

<?php _e("Manage your website categories");?>
<form  class="well form-inline" action="categories.php" method="get" >
<?php 
	$query="SELECT idCategory,name FROM ".TABLE_PREFIX."categories C where idCategoryParent=0  order by  `order`";
	sqlOption($query,"cid",cG("cid"),T_("Home"));
	echo'<input type="hidden" name="action" value="filter" /><input type="submit" class="btn" value="'.T_('Filter').'" />';	
?>
</form>

<div id="formCat" style="display:none;">
	<h2 id="form-tab"></h2>
	<form class="well" name="category" action="categories.php" method="post" onsubmit="return checkForm(this);">
		<fieldset>
			<p>
				<label><?php _e("Category name");?></label>
				<input class="span4" name="cname" type="text" class="text-long" lang="false" onblur="validateText(this);" xml:lang="false" />
			</p>                          
			<p>
            	<label><?php _e("Order");?></label>
                <input class="span1" name="corder" type="text" class="text-small" lang="false"  onblur="validateNumber(this);" onkeypress="return isNumberKey(event);" value="" maxlength="5" xml:lang="false" />
			</p>
			<p>
            	<label><?php _e("Parent");?></label>
                <?php sqlOption("select idCategory,name from ".TABLE_PREFIX."categories where idCategoryParent=0","cparent","0",T_("Home"));?>
        	</p>
			<p>
            	<label><?php _e("Description");?></label>
                <textarea class="span4" rows="10" name="cdesc" ></textarea>
        	</p>
			<p>
            	<label><?php echo T_("Price")." (".PAYPAL_CURRENCY.")";?></label>
                <input  class="span1" name="cprice" type="text" class="text-small" lang="false"  onblur="validateNumber(this);" onkeypress="return isNumberKey(event);" value="" maxlength="5" xml:lang="false" />
			</p>
			<input id="submitCat" type="submit" value="" class="btn btn-primary" />
			<input type="submit" value="<?php _e("Cancel");?>" class="btn" onclick="hide('formCat');return false;" />
			<input type="hidden" name="cid" value="" />
			<input type="hidden" name="action" value="" />
		</fieldset>
	</form>
</div>


<table class="table table-bordered">
	<thead>
		<tr>
			<th><?php _e("Categories");?></th>
			<th><?php _e("Order");?></th>
			<th><?php _e("Parent");?></th>
			<th><?php echo T_("Price")." (".PAYPAL_CURRENCY.")";?></th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<?php 
		$result = $ocdb->query("SELECT *,(select name from ".TABLE_PREFIX."categories  where idCategory=C.idCategoryParent) ParentName 
								FROM ".TABLE_PREFIX."categories C where 6=6 ".$filter." order by  idCategoryParent,`order`");
		$row_count = 0;
		while ($row = mysql_fetch_array($result)){
			$name = $row["name"] ;
			$desc = $row["description"];
			$order =  $row["order"];
			$idCategory=$row["idCategory"];
			$parent=$row["idCategoryParent"];
			$parentName=$row["ParentName"];
			if ($parentName=="") $parentName="Home";
			$cprice=$row["price"];
	?>
	<tr>      
		<td><?php echo $name;?></td>
		<td><?php echo $order;?></td>
		<td><?php echo $parentName;?></td>
		<td><?php echo $cprice;?></td>
		<td>
			<button  class="btn btn-primary" onclick="editCategory('<?php echo $idCategory; ?>', '<?php echo $order;?>','<?php echo $parent;?>','<?php echo $cprice;?>');return false;">
				<i class="icon-edit icon-white"></i>
			</button> 
			
			<button class="btn btn-danger" onclick="deleteCategory('<?php echo $idCategory;?>');return false;" >
				<i class="icon-trash icon-white"></i>
			</button>
			
		<div style="display:none;" id="name-<?php echo $idCategory; ?>"><?php echo $name;?></div>
		<div style="display:none;" id="desc-<?php echo $idCategory; ?>"><?php echo $desc;?></div>
		</td>
	</tr>
	<?php } ?>
</table>


<?php
require_once('footer.php');
?>