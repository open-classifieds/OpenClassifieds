<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL; ?>/themes/hostpel/jsclass.js"></script>
<div id="wrap">
	<div id="inner">
    <div id="header">
    	<div id="logo">
        <a href="<?php echo SITE_URL; ?>" title="<?php echo SITE_NAME; ?>" ><img src="<?php echo SITE_URL; ?>/themes/hostpel/images/logo.gif" /></a>
        </div><!--logo-->
        <div id="post">
        	<a href="<?php echo SITE_URL; ?>/publish-a-new-ad.htm"><img src="<?php echo SITE_URL; ?>/themes/hostpel/images/post.gif" /></a>
        </div><!--post-->
    </div><!--header-->
    <div id="nav">
        <ul>
            <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li><a href="<?php echo SITE_URL.newURL();?>"><?php _e("Post An Ad");?></a></li>
            <li><a href="<?php echo SITE_URL; ?>/terms.htm">TOS</a></li>
            <?php if(FRIENDLY_URL) {?>
		    <li><a href="<?php echo SITE_URL."/".u(T_("Privacy Policy"));?>.htm"><?php _e("Privacy Policy");?></a></li>
	    <?php }else { ?>
		    <li><a href="<?php echo SITE_URL;?>/content/privacy.php"><?php _e("Privacy Policy");?></a></li>
	    <?php } ?>
            <li><a href="<?php echo SITE_URL."/".contactURL();?>">Contact</a></li>
        </ul>
        <div id="search">
            <form method="get" action="<?php echo SITE_URL; ?>">
                <input name="s" type="text" onblur="this.value=(this.value=='') ? 'Search ...' : this.value;"
				onfocus="this.value=(this.value=='Search ...') ? '' : this.value;" 
				value="Search ..." />
                <input type="submit" value="" />
            </form>
        </div><!--search-->
    </div><!--nav-->
    <div id="info">
    <?php if(isset($categoryName)&&isset($categoryDescription)){ ?>
			    <?php echo $categoryDescription;?>
			    <a title="<?php _e("Post Ad in");?> <?php echo $categoryName;?>" href="<?php echo SITE_URL.newURL();?>"><?php _e("Post Ad in");?> <?php echo $categoryName;?></a>
	        <?php }
	            else echo strftime("%A %e %B %Y");
	        ?>
	        <?php if (NEED_OFFER){?>
            <div style="float:right;"><b><?php _e("Filter");?></b>:
		    <?php generatePostType($currentCategory,$type); ?>
		    </div>
		    <?php }?>
    </div><!--info-->
    <div id="content">
    <div id="lfefbar">