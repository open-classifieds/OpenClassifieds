<?php

/**
 * sitemap generator class
 *
 * @package     Open Classifieds
 * @subpackage  Core
 * @category    Helper
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 */

class Sitemap{
	
	public static function renew()
	{
		// the sitemap is expired then generates it
		if (time()>(filemtime(SITEMAP_FILE)+SITEMAP_EXPIRE) && SITEMAP_EXPIRE!=false)
		{
			self::generate();
		}
	}


	public static function generate(){//generates the sitemap returns the xml
		if (function_exists('gzencode')){//the function needs to exist!
			$ocdb=phpMyDB::GetInstance();
			$file=SITEMAP_FILE;
		
			$sitemap='<?xml version="1.0" encoding="UTF-8"?>
		 <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
		  xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
		  
		    $sitemap.=self::makeUrlTag (SITE_URL, date('Y-m-d H:i:s'), "daily", "1.0"); 
		    
			$query="SELECT name,friendlyName,
						(SELECT insertDate FROM ".TABLE_PREFIX."posts p where idCategory=c.idCategory and isConfirmed=1 order by 1 desc limit 1 ) time,
						(select friendlyName from ".TABLE_PREFIX."categories where idCategory=c.idCategoryParent limit 1) parent
					FROM ".TABLE_PREFIX."categories c order by idCategoryParent,`order`";
			$result =	$ocdb->query($query);
			while ($row=mysql_fetch_assoc($result)){
				if ($row['parent']=="")$sitemap.=self::makeUrlTag (SITE_URL.catURL($row['friendlyName']), $row['time'], "daily", "0.8"); 
				else $sitemap.=self::makeUrlTag (SITE_URL.catURL($row['friendlyName'],$row['parent']), $row['time'], "daily", "0.7"); 
			}
			
			$query="SELECT p.idPost,p.title,p.description,p.insertDate,
							c.Name category,c.idCategoryParent,p.type,p.price, friendlyName,p.hasImages,
							(select friendlyName from ".TABLE_PREFIX."categories where idCategory=c.idCategoryParent limit 1) parent
							FROM ".TABLE_PREFIX."posts p
								inner join ".TABLE_PREFIX."categories c
						on c.idCategory=p.idCategory
					where p.isAvailable=1 and p.isConfirmed=1
					order by p.insertDate Desc limit 250";
			//echo $query;
			
			$result =	$ocdb->query($query);
			while ($row=mysql_fetch_assoc($result)){
				$idPost=$row['idPost'];
				$postType=$row['type'];
				$postTypeName=getTypeName($postType);
				$postTitle=html_entity_decode($row['title'],ENT_COMPAT,CHARSET);
				$postPrice=$row['price'];
				$category=$row['category'];//real category name
				$fcategory=$row['friendlyName'];//frienfly name category
				$idCategoryParent=$row['idCategoryParent'];
				$fCategoryParent=$row['parent'];
				$insertDate=$row['insertDate'];
				$categoryUrl=$fcategory;    
			    if ($row["hasImages"]==1){
					$postImage=getPostImages($idPost,$insertDate,true,true);
				}
				else $postImage="";//there's no image
				$postUrl=itemURL($idPost,$fcategory,$postTypeName,$postTitle,$fCategoryParent);
				
				$sitemap.=self::makeUrlTag (SITE_URL.$postUrl,$insertDate, "weekly", "0.6",$postImage);
			}
			
			if (LOCATION){
				$query="SELECT * FROM ".TABLE_PREFIX."locations C order by idLocationParent,idLocation";			
				$result =	$ocdb->query($query);
				while ($row=mysql_fetch_assoc($result)){				
					$sitemap.=self::makeUrlTag (SITE_URL.catURL('','',$row['friendlyName']),$insertDate, "weekly", "0.6");
				}
			}
			
			//$sitemap.=self::makeUrlTag (SITE_URL."/rss/","", "hourly", "0.6");
		    
		    if (FRIENDLY_URL){
		        $sitemap.=self::makeUrlTag (SITE_URL."/".u(T_("Advanced Search")).".htm","", "monthly", "0.3");
			    $sitemap.=self::makeUrlTag (SITE_URL."/".u(T_("Sitemap")).".htm","", "monthly", "0.3");
			    $sitemap.=self::makeUrlTag (SITE_URL."/".u(T_("Privacy Policy")).".htm","", "monthly", "0.3");
		    }
		    else{
		        $sitemap.=self::makeUrlTag (SITE_URL."/content/search.php","", "monthly", "0.3");
			    $sitemap.=self::makeUrlTag (SITE_URL."/content/site-map.php","", "monthly", "0.3");
			    $sitemap.=self::makeUrlTag (SITE_URL."/content/privacy.php","", "monthly", "0.3");
			}
		    $sitemap.=self::makeUrlTag (SITE_URL."/".contactURL,"", "monthly", "0.3");
		    $sitemap.=self::makeUrlTag (SITE_URL.newURL(),"", "monthly", "0.1");
			
			$sitemap.="</urlset>";
			
			if (file_exists($file))  unlink ($file);
			$gzdata = gzencode($sitemap, 9);
		    $fp = fopen($file, "w");
		    fwrite($fp, $gzdata);
		    fclose($fp);
		    
			if (SITEMAP_PING) file_get_contents('http://www.google.com/webmasters/sitemaps/ping?sitemap='.SITE_URL.'/sitemap.xml.gz');
			
			return $sitemap;
		}//if exist gzencode
		return false;
	}


	//from http://www.smart-it-consulting.com/article.htm?node=133&page=37
	public static function makeUrlString ($urlString) {
	    return htmlentities($urlString, ENT_QUOTES, CHARSET);
	}

	public static function makeIso8601TimeStamp ($dateTime) {
	    if (!$dateTime) {
	        $dateTime = date('Y-m-d H:i:s');
	    }
	    if (is_numeric(substr($dateTime, 11, 1))) {
	        $isoTS = substr($dateTime, 0, 10) ."T"
	                 .substr($dateTime, 11, 8) ."+00:00";
	    }
	    else {
	        $isoTS = substr($dateTime, 0, 10);
	    }
	    return $isoTS;
	}
	public static function makeUrlTag ($url, $modifiedDateTime, $changeFrequency, $priority,$image="") {
	    $newLine=PHP_EOL;
	    $indent="    ";
	    $isoLastModifiedSite="";
	    $urlOpen = "$indent<url>$newLine";
	    $urlValue = "";
	    $urlClose = "$indent</url>$newLine";
	    $locOpen = "$indent$indent<loc>";
	    $locValue = "";
	    $locClose = "</loc>$newLine";
	    $lastmodOpen = "$indent$indent<lastmod>";
	    $lastmodValue = "";
	    $lastmodClose = "</lastmod>$newLine";
	    $changefreqOpen = "$indent$indent<changefreq>";
	    $changefreqValue = "";
	    $changefreqClose = "</changefreq>$newLine";
	    $priorityOpen = "$indent$indent<priority>";
	    $priorityValue = "";
	    $priorityClose = "</priority>$newLine";
	    
	    $urlTag = $urlOpen;
	    $urlValue = $locOpen .self::makeUrlString("$url") .$locClose;
	    
	   if ($image!="") {
	        $image=$indent.'<image:image>'.$newLine.
	                    $indent.$indent.'<image:loc>'.self::makeUrlString($image).'</image:loc>'.$newLine.
	               $indent.'</image:image>'.$newLine;
	        $urlValue.=$image;
	    }
	    
	    if ($modifiedDateTime) {
	     $urlValue .= $lastmodOpen .self::makeIso8601TimeStamp($modifiedDateTime) .$lastmodClose;
	     if (!$isoLastModifiedSite) { // last modification of web site
	         $isoLastModifiedSite = self::makeIso8601TimeStamp($modifiedDateTime);
	     }
	    }
	    if ($changeFrequency) {
	     $urlValue .= $changefreqOpen .$changeFrequency .$changefreqClose;
	    }
	    if ($priority) {
	     $urlValue .= $priorityOpen .$priority .$priorityClose;
	    }
	    $urlTag .= $urlValue;
	    $urlTag .= $urlClose;
	    return $urlTag;
	}
		
}