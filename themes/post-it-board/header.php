<div id="header">
    <h1><a href="<?php echo SITE_URL; ?>"><?php echo SITE_NAME; ?></a></h1>
    <p>
        <?php generateMenu($selectedCategory,"<b>","</b>".SEPARATOR);?><br />
        <?php if (isset($currentCategory)) generateSubMenu($idCategoryParent,$categoryParent,$currentCategory);
              else generatePopularCategories(); ?>
        <br/>&nbsp;
    </p>
</div>
    
    
	<div id="content">
	<?php if (!isset($resultSearch)) { echo '<div class="page">'; }?>
