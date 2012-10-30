<?php 
$style=" id='selected' ";//for the selected item
$theme_color=$_COOKIE['edit_80_color'];
if ($theme_color=="") $theme_color="blue.css";
?>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/themes/edit_80/<?php echo $theme_color;?>" id="css_color" />
<script language="JavaScript">
	function changeCss(id_css,new_css){
		document.getElementById(id_css).href = '<?php echo SITE_URL;?>/themes/edit_80/'+new_css;	
		setCookie("edit_80_color",new_css,365)
	}
	function setCookie(c_name,value,expiredays){
		var exdate=new Date();
		exdate.setDate(exdate.getDate()+expiredays);
		document.cookie=c_name+ "=" +escape(value)+
		((expiredays==null) ? "" : ";expires="+exdate.toGMTString()+";path=/");
	}
</script>
  <div id="main">
    <div id="links_container">
      <div id="logo">
      	<h1><?php echo SITE_NAME ?></h1>
      </div>
      <div id="links">
        <!-- **** COLOR LINKS HERE **** -->
	<a href="#" onclick="changeCss('css_color','blue.css');return false;"><span class="blue">blue</span></a> | 
	<a href="#" onclick="changeCss('css_color','green.css');return false;"><span class="green">green</span></a> |         		
	<a href="#" onclick="changeCss('css_color','orange.css');return false;"><span class="orange">orange</span></a> | 
	<a href="#" onclick="changeCss('css_color','purple.css');return false;"><span class="purple">purple</span></a>
			
      </div>
    </div>
    <div id="menu">
      <ul>
        <li><a href="<?php echo SITE_URL;?>"><?php _e("Home");?></a> </li>	   
		   <?php generateMenu($selectedCategory,"<li>","</li>");?>  
      </ul>
    </div>
    <div id="content">
      <div id="column1">
      		<?php getSideBar("<div class=\"sidebaritem\">","</div>");?>
      </div>
      <div id="column2">
       <?php 
 	if (isset($currentCategory)){//only if there's a category we create submenu
	 	generateSubMenu($idCategoryParent,$categoryParent,$currentCategory);				
	}
	else generatePopularCategories();//they did not choose a category, showing the popular categories	
   ?><br /><br />
