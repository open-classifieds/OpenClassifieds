<?php
/*
 * $Id: upgrade175.php,v 1.7.5 06/11/2011 09:45:00 neo22s$
 */

require_once('../includes/bootstrap.php');

echo "Upgrade script to v1.7.5<br/>";
echo "Executing changes...<br/>";
  	

//categories prices
	$query="ALTER TABLE  `".TABLE_PREFIX."categories` ADD  `price` FLOAT NOT NULL;";
	$ocdb->query($query);
//end categories prices

    
echo "Modifications on Data Base done. <br /><br />";
	
?>