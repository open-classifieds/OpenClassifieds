<div id="header"><h1><a href="<?php echo SITE_URL; ?>"><?php echo SITE_NAME; ?></a></h1></div>
<div class="item"><?php generateMenu($selectedCategory,"",SEPARATOR);?></div>
<div class="item"><?php generateSubMenu($idCategoryParent,$categoryParent,$currentCategory);  ?></div>
<div id="content">
