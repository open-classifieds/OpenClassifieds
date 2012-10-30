<?php require_once('../includes/config.php');//configuration file?>
<?php 
if (MAP_KEY!=""){//google maps

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/map.php')){//map from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/map.php'); 
}
else{//not found in theme

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo SITE_NAME.SEPARATOR;?>Map <?php echo $_GET["address"].", ". MAP_INI_POINT;?></title>
<meta name="title" content="<?php echo SITE_NAME;?>, Map <?php echo $_GET["address"].", ". MAP_INI_POINT;?>" />
<meta name="description" content="<?php echo SITE_NAME;?>, Map <?php echo $_GET["address"].", ". MAP_INI_POINT;?>" />
<meta name="keywords" content="<?php echo SITE_NAME;?>, Map <?php echo $_GET["address"].", ". MAP_INI_POINT;?>" />
<meta name="revisit-after" content="1 days" />
<meta name="robots" content="all,index,follow" />
<meta name="distribution" content="Global" />
<meta name="rating" content="General" />
<meta name="generator" content="Open Classifieds <?php echo VERSION;?>" />
<link rel="shortcut icon" href="<?php echo SITE_URL;?>/favicon.ico" />
<style type="text/css">
v\:* {behavior:url(#default#VML);}
html, body {width: 100%; height: 100%}
body {margin-top: 0px; margin-right: 0px; margin-left: 0px; margin-bottom: 0px}
</style>		
<?php if ($_GET["address"]!=""){?>
	<script type="text/javascript">
		var init_street="<?php echo $_GET["address"];?>";
		var zoom=16;
	</script>
<?php }else{?>
	<script type="text/javascript">
		var init_street="<?php echo MAP_INI_POINT;?>";
		var zoom=13;
	</script>
<?php }?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo MAP_KEY;?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/content/js/map.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/content/js/mapBig.js"></script>
<?php if ($_GET["address"]==""){?>
<script type="text/javascript">
GDownloadUrl("<?php echo SITE_URL;?>/rss/?category=<?php echo $_GET["category"];?>&type=<?php echo $_GET["type"];?>", 
	function(data, responseCode) {
      if(responseCode == 200) {
    	  	var texts = [];
    	  	var addresses = [];       
	        var xml = GXml.parse(data);
	        var markers = xml.documentElement.getElementsByTagName("item");
	
	        for (var i = 0; i < markers.length; i++) { 	
	        	var address=markers[i].getElementsByTagName('address').item(0).childNodes.item(0).nodeValue;
	        	if (address!=null){
	        		//alert (address);
					var title=markers[i].getElementsByTagName('title').item(0).childNodes.item(0).nodeValue;
					var link=markers[i].getElementsByTagName('link').item(0).childNodes.item(0).nodeValue;
					var desc=markers[i].getElementsByTagName('description').item(0).childNodes.item(0).nodeValue;
					desc=desc.substr(0,220);//limit
					addresses.push(address);
					texts.push("<div style='width: 200px'><a target='_blank' href='" +link+"'>"+title+"</a><br />"+desc+"</div>");
	        	}//if
	   		 }//for
	
	   		for (var i = 0; i < addresses.length; i++) { 
	   		    geocoder.getLatLng(addresses[i], function (current) { 
	   		        return function(point) { 
	   		            if (point) map.addOverlay(createMarker(point,texts[current]));    
	   		        } 
	   		    }(i)); 
	   		}
		 
      }//if
 });//function
</script>
<?php }?>
</head>
<body onload="load()" onunload="GUnload()">
<div id="map" style="width: 100%; height: 100%;"></div>
<?php if ($_GET["address"]==""){?>
<div style="text-align:center;top:7px; left:75px;height: 25px; width:220px; position:absolute;background-color:white;border:solid 1px;">
	<a href="<?php echo SITE_URL;?>"><?php echo SITE_NAME;?></a>
</div>
<?php }?>
<?php if (ANALYTICS!=""){?>
<script type="text/javascript">
window.google_analytics_uacct = "<?php echo ANALYTICS;?>";
</script>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("<?php echo ANALYTICS;?>");
pageTracker._trackPageview();
} catch(err) {}</script>
<?php }?>
</body>
</html>
<?php 
}//if else

}
?>
