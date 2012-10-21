<?php
ignore_user_abort(true);
set_time_limit(0);
        
require_once('../includes/bootstrap.php');

echo "Upgrade script to v1.8<br/>";
echo "Executing changes...please wait...<br/>";
  	

	$query="ALTER TABLE ".TABLE_PREFIX."postshits DROP FOREIGN KEY `FK_PostsHits_idPost` ;";
	$ocdb->query($query);
	
	$query="ALTER TABLE ".TABLE_PREFIX."postshits ENGINE = MyISAM ;";
	$ocdb->query($query);


echo "Modifications on Data Base done. <br /><br />";
	
?>