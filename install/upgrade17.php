<?php
/*
 * $Id: upgrade.php,v 1.7.0 /04/23/2010 09:45:00 neo22s,arnaldohidalgo Exp $
 */

require_once('../includes/bootstrap.php');

echo "Upgrade script to v1.7.0<br/>";
echo "Executing changes...<br/>";
  	

//locations upgrade
	$query="CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."locations` (
	  `idLocation` int(10) unsigned NOT NULL auto_increment,
      `idLocationParent` int(10) unsigned NOT NULL default '0',
	  `name` varchar(64) NOT NULL,
      `friendlyName` varchar(64) NOT NULL,
	  PRIMARY KEY (`idLocation`)
	) ENGINE=InnoDB  DEFAULT CHARSET=".DB_CHARSET." AUTO_INCREMENT=1;";
	$ocdb->query($query);
    
    $query="ALTER TABLE ".TABLE_PREFIX."posts add idLocation int(10) unsigned NOT NULL DEFAULT '0'";
    $ocdb->query($query);
//end location upgrade

//accounts upgrade
	$query="CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."accounts` (
      `idAccount` int(11) NOT NULL auto_increment,
      `name` varchar(250) NOT NULL,
      `email` varchar(145) NOT NULL,
      `password` varchar(145) NOT NULL,
      `active` int(1) NOT NULL default '0',
      `idLocation` int(10) unsigned default NULL,
      `createdDate` timestamp NOT NULL default CURRENT_TIMESTAMP,
      `lastModifiedDate` datetime default NULL,
      `lastSigninDate` datetime default NULL,
      `activationToken` varchar(225) NOT NULL,
      PRIMARY KEY  (`idAccount`)
	) ENGINE=InnoDB  DEFAULT CHARSET=".DB_CHARSET." AUTO_INCREMENT=1;";
    $ocdb->query($query);
//end account upgrade

//prices upgrade
	 $query="ALTER TABLE ".TABLE_PREFIX."posts MODIFY COLUMN `price` FLOAT  NOT NULL DEFAULT '0'; ";
	 $ocdb->query($query);
//end prices upgrade
    
echo "Modifications on Data Base done. <br /><br />";


//images upgrade

$query="ALTER TABLE ".TABLE_PREFIX."posts ADD COLUMN `hasImages` BOOLEAN  NOT NULL DEFAULT '0';";
$ocdb->query($query);


	//images database loop
	$query="select p.idPost,p.insertDate from ".TABLE_PREFIX."posts p 
					inner join ".TABLE_PREFIX."postsimages i
					on p.idPost=i.idPost
			group by  p.idPost,p.insertDate";//echo $query;
	$result=$ocdb->getRows($query,"assoc","none");//var_dump ($result);
	if ($result){
		$types=split(",",IMG_TYPES);//creating array with the allowed images types
		$imgPath=SITE_ROOT.IMG_UPLOAD;//path of the images
		
		foreach ( $result as $row ){
			$idPost=$row['idPost'];
			$date=explode('-',setDate($row['insertDate']));
			
			$imgDirNew=$date[2].'/'.$date[1].'/'.$date[0].'/'.$idPost.'/';	//new pics path
			echo 'new path for pics: '.$imgDirNew.'<br />';
			umask(0000);
			mkdir($imgPath.$imgDirNew, 0755,true);//creates new folder
			$imgDirOld=$idPost.'/';//old pics are here
		
			$files = scandir($imgPath.$imgDirOld);//scanning folder with old files
			foreach($files as $img){//searching for images
				$file_ext  = strtolower(substr(strrchr($img, "."), 1 ));//get file ext
				if (in_array($file_ext,$types)){//we only want images with allowed ext
					echo $imgPath.$imgDirOld.$img.' moved to '.$imgPath.$imgDirNew.$img.'<br />';
					rename($imgPath.$imgDirOld.$img,$imgPath.$imgDirNew.$img);//move images to new folder
				}
			}
			removeRessource($imgPath.$imgDirOld);//remove old dir
			
			//update table if they have images in images table update hasImages=1
			$ocdb->update(TABLE_PREFIX."posts","hasImages=1","idPost=$idPost");
		}
	}
	else echo 'No images to move';
	
	$ocdb->query('DROP TABLE IF EXISTS '.TABLE_PREFIX.'postsimages;');//delete table images
//end images upgrade	

	
?>