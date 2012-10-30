<?php
require_once('access.php');
require_once('header.php');
?>

<div class="page-header">
	<h1><?php _e("Site Usage Statistics");?></h1>	
</div>

<table class="table table-bordered table-condensed">
	<thead>
		<tr>
			<th></th>
			<th><?php _e("Ads Views");?></th>
			<th><?php _e("Ads");?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><b><?php _e("Yesterday");?></b></td>
			<td><?php echo totalViews("all",1);?></td>
			<td><?php echo totalAds("all",1);?></td>
		</tr>
		<tr>
			<td><b><?php _e("Last week");?></b></td>
			<td><?php echo totalViews("all",8);?></td>
			<td><?php echo totalAds("all",8);?></td>
		</tr>
		<tr>
			<td><b><?php _e("Last month");?></b></td>
			<td><?php echo totalViews("all",10);?></td>
			<td><?php echo totalAds("all",30);?></td>
		</tr>
		<tr>
			<td><b><?php _e("Total");?></b></td>
			<td><?php echo totalViews();?></td>
			<td><?php echo totalAds();?></td>
		</tr>
	</tbody>
</table>

<?

//Ads last 30 days
$sql =  "SELECT DATE( insertDate ) , COUNT( idPost ) Ads
			FROM  `".TABLE_PREFIX."posts` 
			WHERE TIMESTAMPDIFF(DAY,insertDate,now())<=30
			GROUP BY DATE( insertDate ) 
			ORDER BY 1 ASC 
			";
$result = $ocdb->getRows($sql,'assoc');
if ($result){
    
    $data = array();
    foreach($result as $r) $data[]=$r;
    
	echo Chart::column($data,array('title'=>'Ads last 30 days','height'=>400,'width'=>700));
}

//total monthly ads
$sql =  "SELECT CONCAT( MONTH(  `insertDate` ) ,  ' - ', YEAR(  `insertDate` ) ) month , COUNT( idPost) Ads
		FROM  `".TABLE_PREFIX."posts` 
		GROUP BY MONTH(  `insertDate` ) , YEAR(  `insertDate` ) 
		ORDER BY YEAR(  `insertDate` ) ASC , MONTH(  `insertDate` ) ASC ";
$result = $ocdb->getRows($sql,'assoc');
if ($result){
    
    $data = array();
    foreach($result as $r) $data[]=$r;
    
	echo Chart::column($data,array('title'=>'Total monthly ads','height'=>400,'width'=>700));
}

?>

<?

//visits last 30 days
$sql =  "SELECT DATE( hitTime ) , COUNT( idHit ) visits
			FROM  `".TABLE_PREFIX."postshits` 
			WHERE TIMESTAMPDIFF(DAY,hitTime,now())<=30
			GROUP BY DATE( hitTime ) 
			ORDER BY 1 ASC 
			";
$result = $ocdb->getRows($sql,'assoc');
if ($result){
    
    $data = array();
    foreach($result as $r) $data[]=$r;
    
	echo Chart::column($data,array('title'=>'Visits last 30 days','height'=>400,'width'=>700));
}

//total monthly visits
$sql =  "SELECT CONCAT( MONTH(  `hitTime` ) ,  ' - ', YEAR(  `hitTime` ) ) month , COUNT( idHit) visits
		FROM  `".TABLE_PREFIX."postshits` 
		GROUP BY MONTH(  `hitTime` ) , YEAR(  `hitTime` ) 
		ORDER BY YEAR(  `hitTime` ) ASC , MONTH(  `hitTime` ) ASC ";
$result = $ocdb->getRows($sql,'assoc');
if ($result){
    
    $data = array();
    foreach($result as $r) $data[]=$r;
    
	echo Chart::column($data,array('title'=>'Total monthly visits','height'=>400,'width'=>700));
}

?>



<?php
require_once('footer.php');
?>